<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCharge;
use Illuminate\Http\Request;

class DeliveryChargeController extends Controller
{
    public function index()
    {
        $charges = DeliveryCharge::orderBy('zone')->get();
        return view('admin.delivery-charges.index', compact('charges'));
    }

    public function create()
    {
        return view('admin.delivery-charges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'zone'             => 'required|string|max:255|unique:delivery_charges,zone',
            'charge'           => 'required|numeric|min:0',
            'estimated_days'   => 'nullable|string|max:50',
        ]);

        DeliveryCharge::create($request->only(['zone', 'charge', 'estimated_days']));

        return redirect()->route('admin.delivery-charges.index')->with('success', 'Delivery charge created successfully.');
    }

    public function edit(DeliveryCharge $deliveryCharge)
    {
        return view('admin.delivery-charges.edit', compact('deliveryCharge'));
    }

    public function update(Request $request, DeliveryCharge $deliveryCharge)
    {
        $request->validate([
            'zone'             => 'required|string|max:255|unique:delivery_charges,zone,' . $deliveryCharge->id,
            'charge'           => 'required|numeric|min:0',
            'estimated_days'   => 'nullable|string|max:50',
        ]);

        $deliveryCharge->update($request->only(['zone', 'charge', 'estimated_days']));

        return redirect()->route('admin.delivery-charges.index')->with('success', 'Delivery charge updated successfully.');
    }

    public function destroy(DeliveryCharge $deliveryCharge)
    {
        $deliveryCharge->delete();
        return redirect()->route('admin.delivery-charges.index')->with('success', 'Delivery charge deleted successfully.');
    }
}
