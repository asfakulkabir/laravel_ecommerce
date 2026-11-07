<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SteadfastService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_address', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $orders = $query->with('deliveryCharge')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'items.variation', 'deliveryCharge');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:processing,shipped,delivered,cancelled',
        ]);

        $order->update([
            'status' => $request->input('status'),
        ]);

        return redirect()->route('admin.orders.index')
            ->with('success', "Order #{$order->id} status updated to " . \App\Models\Order::STATUSES[$order->status] ?? ucfirst($order->status) . '.');
    }

    public function sendToSteadfast(Order $order, SteadfastService $steadfast)
    {
        if ($order->is_sent_to_steadfast) {
            return redirect()->back()->with('error', "Order #{$order->id} has already been sent to Steadfast.");
        }

        if (!$steadfast->isConfigured()) {
            return redirect()->back()->with('error', 'Steadfast API credentials are not configured. Please add them in Settings.');
        }

        $chargeAmount = $order->actualShippingCharge();

        $response = $steadfast->createOrder([
            'invoice' => 'ORDER-' . $order->id,
            'recipient_name' => $order->customer_name,
            'recipient_phone' => $order->customer_phone,
            'recipient_address' => $order->customer_address,
            'cod_amount' => $order->total_amount,
            'note' => $chargeAmount > 0
                ? 'Delivery charge: ৳' . $chargeAmount
                : 'Free delivery (ফ্রি ডেলিভারি)',
        ]);

        if (($response['status'] ?? 0) === 200) {
            $consignment = $response['consignment'] ?? [];
            $order->update([
                'is_sent_to_steadfast' => true,
                'steadfast_consignment_id' => $consignment['consignment_id'] ?? null,
                'status' => Order::STATUS_SHIPPED,
            ]);

            return redirect()->back()->with('success', "Order #{$order->id} sent to Steadfast successfully. Tracking: " . ($consignment['tracking_code'] ?? 'N/A'));
        }

        return redirect()->back()->with('error', 'Failed to send order to Steadfast. ' . ($response['message'] ?? 'Please try again.'));
    }

    public function exportCsv()
    {
        $orders = Order::with('deliveryCharge')->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-export.csv"',
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id', 'customer_name', 'customer_phone', 'customer_address', 'payment_method', 'bkash_trx_id', 'total_amount', 'status', 'delivery_zone', 'is_sent_to_steadfast', 'steadfast_consignment_id', 'is_notification_sent', 'created_at']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->id,
                    $order->customer_name,
                    $order->customer_phone,
                    $order->customer_address,
                    $order->payment_method,
                    $order->bkash_trx_id,
                    $order->total_amount,
                    $order->status,
                    $order->deliveryCharge?->zone ?? '',
                    $order->is_sent_to_steadfast ? 1 : 0,
                    $order->steadfast_consignment_id,
                    $order->is_notification_sent ? 1 : 0,
                    $order->created_at,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (!$header) {
            fclose($handle);
            return redirect()->back()->with('error', 'Invalid CSV file.');
        }

        $header = array_map('trim', $header);
        $imported = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            if (empty($data['customer_name']) || empty($data['customer_phone'])) {
                $errors[] = 'Row missing customer_name or customer_phone, skipped.';
                continue;
            }

            try {
                Order::create([
                    'customer_name' => $data['customer_name'],
                    'customer_phone' => $data['customer_phone'],
                    'customer_address' => $data['customer_address'] ?? '',
                    'payment_method' => $data['payment_method'] ?? 'Cash on Delivery',
                    'bkash_trx_id' => $data['bkash_trx_id'] ?? null,
                    'total_amount' => $data['total_amount'] ?? 0,
                    'total' => $data['total_amount'] ?? 0,
                    'status' => $data['status'] ?? 'processing',
                    'is_sent_to_steadfast' => filter_var($data['is_sent_to_steadfast'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'is_notification_sent' => filter_var($data['is_notification_sent'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Error importing order '{$data['customer_name']}': " . $e->getMessage();
            }
        }

        fclose($handle);

        $message = "Imported {$imported} orders successfully.";
        if (!empty($errors)) {
            $message .= ' ' . implode(' ', array_slice($errors, 0, 5));
        }

        return redirect()->route('admin.orders.index')->with('success', $message);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', "Order #{$order->id} deleted successfully.");
    }
}
