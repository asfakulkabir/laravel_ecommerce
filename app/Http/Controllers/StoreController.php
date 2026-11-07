<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DeliveryCharge;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Setting;
use App\Models\Slider;
use App\Mail\NewOrderNotification;
use App\Services\MetaPixelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index()
    {
        $featuredCategories = Category::whereNull('parent_id')->limit(6)->get();
        $featuredProducts = Product::with(['images', 'categories'])->where('is_active', true)->where('is_featured', true)->orderBy('sort_order')->latest()->limit(8)->get();
        $latestProducts = Product::with(['images', 'categories'])->where('is_active', true)->orderBy('sort_order')->latest()->limit(8)->get();
        $sliders = Slider::active()->get();

        $freeShippingThreshold = (float) Setting::getValue('free_shipping_threshold', 0);

        return view('store.index', compact(
            'featuredCategories',
            'featuredProducts',
            'latestProducts',
            'sliders',
            'freeShippingThreshold'
        ));
    }

    public function shop(Request $request)
    {
        $query = Product::with(['images', 'categories'])->where('is_active', true);

        // Category filter
        if ($request->filled('category')) {
            $catSlug = $request->input('category');
            $category = Category::where('slug', $catSlug)->first();
            if ($category) {
                // Get all descendant category IDs (recursive)
                $categoryIds = $this->getDescendantCategoryIds($category);
                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                });
            }
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Price filter
        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                // For simple products
                $q->where('regular_price', '>=', $request->input('min_price'))
                  // For variable products, we'll check variations price in a subquery or do simple logic
                  ->orWhereHas('variations', function ($qv) use ($request) {
                      $qv->where('price', '>=', $request->input('min_price'));
                  });
            });
        }
        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where(function ($qs) use ($request) {
                    $qs->whereNotNull('regular_price')
                       ->where('regular_price', '<=', $request->input('max_price'));
                })->orWhereHas('variations', function ($qv) use ($request) {
                    $qv->where('price', '<=', $request->input('max_price'));
                });
            });
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'price_asc') {
            $query->orderByRaw('COALESCE(products.sale_price, products.regular_price, (SELECT MIN(price) FROM product_variations WHERE product_variations.product_id = products.id)) asc');
        } elseif ($sort === 'price_desc') {
            $query->orderByRaw('COALESCE(products.sale_price, products.regular_price, (SELECT MAX(price) FROM product_variations WHERE product_variations.product_id = products.id)) desc');
        } else {
            $query->orderBy('products.sort_order')->orderBy('products.created_at', 'desc');
        }

        $products = $query->paginate(30)->withQueryString();
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        $pixelSearchEventId = null;
        $pixelSearchEventData = null;

        if ($request->filled('search')) {
            $pixelSearchEventId = (string) Str::uuid();
            $pixelSearchEventData = [
                'search_string' => $request->input('search'),
                'content_type' => 'product',
            ];
            app(MetaPixelService::class)->sendEvent(
                'Search',
                $pixelSearchEventData,
                [],
                $pixelSearchEventId
            );
        }

        return view('store.shop', compact(
            'products',
            'categories',
            'pixelSearchEventId',
            'pixelSearchEventData'
        ));
    }

    public function product($slug)
    {
        $product = Product::with(['images', 'categories', 'variations'])->where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        // Find related products
        $categoryIds = $product->categories->pluck('id')->toArray();
        $relatedProducts = Product::with('images')
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->limit(12)
            ->get();

        // Group variations for dropdown rendering if product is variable
        $variationsJson = [];
        if ($product->product_type === 'variable') {
            $variationsJson = $product->variations->map(function ($v) {
                return [
                    'id' => $v->id,
                    'size' => $v->size,
                    'color' => $v->color,
                    'weight' => $v->weight,
                    'price' => $v->price ?: $v->product->getDisplayPrice() ?: 0,
                    'stock' => $v->stock,
                ];
            });
        }

        $pixelEventId = (string) Str::uuid();
        $pixelEventData = [
            'content_ids' => [$product->id],
            'content_name' => $product->name,
            'content_type' => 'product',
            'value' => $product->getDisplayPrice() ?: 0,
            'currency' => 'BDT',
        ];
        app(MetaPixelService::class)->sendEvent('ViewContent', $pixelEventData, [], $pixelEventId);

        $freeShippingThreshold = (float) Setting::getValue('free_shipping_threshold', 0);

        return view('store.product', compact(
            'product',
            'relatedProducts',
            'variationsJson',
            'freeShippingThreshold',
            'pixelEventId',
            'pixelEventData'
        ));
    }

    public function cart()
    {
        $cart = session()->get('cart', []);

        // Global free shipping threshold (separate field, not per-zone).
        $freeShippingMin = (float) Setting::getValue('free_shipping_threshold', 0);

        return view('store.cart', compact('cart', 'freeShippingMin'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'variation_id' => 'nullable|exists:product_variations,id',
        ]);

        $product = Product::findOrFail($request->input('product_id'));
        $qty = $request->input('quantity');
        $variationId = $request->input('variation_id');
        $variation = null;

        if ($product->product_type === 'variable') {
            if (!$variationId) {
                return back()->with('error', 'Please select a product option.');
            }
            $variation = ProductVariation::findOrFail($variationId);
            if ($variation->stock < $qty) {
                return back()->with('error', "Only {$variation->stock} items left in stock for this option.");
            }
            // Respect the product sale price when the variation has no own price.
            $price = $variation->price ?: $product->getDisplayPrice();
            $variationDetails = trim("{$variation->size} {$variation->color} {$variation->weight}");
            $cartKey = "product_{$product->id}_var_{$variationId}";
        } else {
            if ($product->stock_quantity < $qty) {
                return back()->with('error', "Only {$product->stock_quantity} items left in stock.");
            }
            $price = $product->getDisplayPrice();
            $variationDetails = null;
            $cartKey = "product_{$product->id}";
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $newQty = $cart[$cartKey]['quantity'] + $qty;
            // Validate stock again
            if ($variation && $variation->stock < $newQty) {
                return back()->with('error', "Cannot add more. Only {$variation->stock} items available in total.");
            } elseif (!$variation && $product->stock_quantity < $newQty) {
                return back()->with('error', "Cannot add more. Only {$product->stock_quantity} items available in total.");
            }
            $cart[$cartKey]['quantity'] = $newQty;
        } else {
            $featuredImage = $product->images->where('is_featured', true)->first();
            $imagePath = $featuredImage ? $featuredImage->image : null;

            $cart[$cartKey] = [
                'product_id' => $product->id,
                'variation_id' => $variationId,
                'name' => $product->name,
                'image' => $imagePath,
                'price' => $price,
                'quantity' => $qty,
                'variation_details' => $variationDetails,
            ];
        }

        session()->put('cart', $cart);

        $pixelEventId = (string) Str::uuid();
        $pixelEvent = [
            'event' => 'AddToCart',
            'data' => [
                'content_ids' => [$product->id],
                'content_name' => $product->name,
                'content_type' => 'product',
                'value' => (float) $price * $qty,
                'currency' => 'BDT',
                'contents' => [[
                    'id' => (string) $product->id,
                    'quantity' => $qty,
                    'item_price' => (float) $price,
                ]],
                'num_items' => $qty,
            ],
            'event_id' => $pixelEventId,
        ];
        session()->push('pixel_events', $pixelEvent);
        app(MetaPixelService::class)->sendEvent('AddToCart', $pixelEvent['data'], [], $pixelEventId);

        if ($request->input('buy_now') == '1') {
            return redirect()->route('checkout');
        }

        return redirect()->back()->with('success', 'Product added to cart successfully!')->with('open_side_cart', true);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $key = $request->input('key');
        $qty = $request->input('quantity');

        $cart = session()->get('cart', []);

        if (!isset($cart[$key])) {
            return response()->json(['success' => false, 'message' => 'Item not found in cart.'], 404);
        }

        $cartItem = $cart[$key];
        $product = Product::findOrFail($cartItem['product_id']);

        if ($cartItem['variation_id']) {
            $variation = ProductVariation::findOrFail($cartItem['variation_id']);
            if ($variation->stock < $qty) {
                return response()->json([
                    'success' => false, 
                    'message' => "Only {$variation->stock} items available for this option."
                ], 422);
            }
        } else {
            if ($product->stock_quantity < $qty) {
                return response()->json([
                    'success' => false, 
                    'message' => "Only {$product->stock_quantity} items available."
                ], 422);
            }
        }

        $cart[$key]['quantity'] = $qty;
        session()->put('cart', $cart);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully.',
            'item_total' => number_format($cart[$key]['price'] * $qty, 2),
            'cart_subtotal' => number_format($subtotal, 2),
            'cart_count' => count($cart),
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
        ]);

        $key = $request->input('key');
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        if ($request->ajax()) {
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart.',
                'cart_subtotal' => number_format($subtotal, 2),
                'cart_count' => count($cart),
            ]);
        }

        return redirect()->route('cart')->with('success', 'Item removed from cart.');
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
        $deliveryZones = DeliveryCharge::orderBy('zone')->get();

        // Global free shipping threshold (separate field, not per-zone).
        $freeShippingThreshold = (float) Setting::getValue('free_shipping_threshold', 0);
        $qualifiesForFreeShipping = $freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold;

        $pixelEventId = (string) Str::uuid();
        $pixelEventData = [
            'content_ids' => array_values(array_map(
                fn ($item) => (string) $item['product_id'],
                $cart
            )),
            'content_type' => 'product',
            'value' => (float) $subtotal,
            'currency' => 'BDT',
            'contents' => array_values(array_map(fn ($item) => [
                'id' => (string) $item['product_id'],
                'quantity' => (int) $item['quantity'],
                'item_price' => (float) $item['price'],
            ], $cart)),
            'num_items' => array_sum(array_column($cart, 'quantity')),
        ];
        app(MetaPixelService::class)->sendEvent('InitiateCheckout', $pixelEventData, [], $pixelEventId);

        return view('store.checkout', compact(
            'cart',
            'deliveryZones',
            'subtotal',
            'freeShippingThreshold',
            'qualifiesForFreeShipping',
            'pixelEventId',
            'pixelEventData'
        ));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => ['required', 'string', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/'],
            'customer_address' => 'required|string',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Global free shipping threshold (separate field, not per-zone).
        $freeShippingThreshold = (float) Setting::getValue('free_shipping_threshold', 0);
        $qualifiesForFreeShipping = $freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold;

        $deliveryChargeId = null;
        $shippingCharge = 0.0;

        if (!$qualifiesForFreeShipping) {
            $request->validate([
                'delivery_zone' => 'required|exists:delivery_charges,zone',
            ]);
            $delivery = DeliveryCharge::where('zone', $request->delivery_zone)->firstOrFail();
            $deliveryChargeId = $delivery->id;
            $shippingCharge = (float) $delivery->charge;
        }

        $totalAmount = (int) round($subtotal + $shippingCharge);

        $itemsArray = [];
        foreach ($cart as $item) {
            $itemsArray[] = [
                'product_id'        => $item['product_id'],
                'variation_id'      => $item['variation_id'],
                'name'              => $item['name'],
                'price'             => $item['price'],
                'quantity'          => $item['quantity'],
                'variation_details' => $item['variation_details'],
                'image'             => $item['image'] ?? null,
            ];
        }

        DB::beginTransaction();

        try {
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($item['variation_id']) {
                    $variation = ProductVariation::findOrFail($item['variation_id']);
                    if ($variation->stock < $item['quantity']) {
                        throw new \Exception("{$product->name} ({$item['variation_details']}) is out of stock.");
                    }
                } else {
                    if ($product->stock_quantity < $item['quantity']) {
                        throw new \Exception("{$product->name} is out of stock.");
                    }
                }
            }

            $order = Order::create([
                'items_json'          => json_encode($itemsArray),
                'payment_method'      => 'Cash on Delivery',
                'customer_name'       => $request->customer_name,
                'customer_phone'      => $request->customer_phone,
                'customer_address'    => $request->customer_address,
                'delivery_charge_id'  => $deliveryChargeId,
                'shipping_charge'     => $shippingCharge,
                'total_amount'        => $totalAmount,
                'total'               => $totalAmount,
                'status'              => 'processing',
                'is_sent_to_steadfast'=> false,
                'is_notification_sent'=> false,
            ]);

            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);

                OrderItem::create([
                    'order_id'             => $order->id,
                    'product_id'           => $product->id,
                    'product_variation_id' => $item['variation_id'],
                    'product_name'         => $product->name,
                    'quantity'             => $item['quantity'],
                    'price'                => $item['price'],
                    'variation_details'    => $item['variation_details'],
                ]);

                if ($item['variation_id']) {
                    $variation = ProductVariation::findOrFail($item['variation_id']);
                    $variation->decrement('stock', $item['quantity']);
                    $product->updateStockFromVariations();
                } else {
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }

            DB::commit();

            session()->forget('cart');

            $pixelEventId = (string) Str::uuid();
            $pixelContentIds = array_map(fn ($item) => (string) $item['product_id'], $cart);
            session()->flash('pixel_purchase', [
                'event_id' => $pixelEventId,
                'order_id' => (string) $order->id,
                'data' => [
                    'value' => (float) $totalAmount,
                    'currency' => 'BDT',
                    'content_ids' => array_values($pixelContentIds),
                    'content_type' => 'product',
                    'contents' => array_values(array_map(fn ($item) => [
                        'id' => (string) $item['product_id'],
                        'quantity' => (int) $item['quantity'],
                        'item_price' => (float) $item['price'],
                    ], $cart)),
                    'num_items' => array_sum(array_column($cart, 'quantity')),
                    'order_id' => (string) $order->id,
                ],
            ]);

            return redirect()->route('order.success', $order->id)
                ->with('success', 'Order placed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order: ' . $e->getMessage())->withInput();
        }
    }

    public function orderSuccess(Order $order)
    {
        // Load the items relationship explicitly (not the attribute)
        $order->load('items');

        $pixelPurchase = session()->get('pixel_purchase');
        if (
            is_array($pixelPurchase)
            && ($pixelPurchase['order_id'] ?? null) === (string) $order->id
            && isset($pixelPurchase['event_id'], $pixelPurchase['data'])
        ) {
            app(MetaPixelService::class)->sendEvent(
                'Purchase',
                $pixelPurchase['data'],
                [],
                $pixelPurchase['event_id']
            );
            session()->forget('pixel_purchase');
        } else {
            $pixelPurchase = null;
        }

        if (!$order->is_notification_sent) {
            try {
                $emails = Setting::getValue('order_notification_emails', '');
                \Log::info('Order notification emails from setting: ' . $emails);
                
                if ($emails) {
                    $recipients = array_map('trim', explode(',', $emails));
                    $recipients = array_filter($recipients);
                    \Log::info('Processing order #' . $order->id . ' notification for recipients: ' . implode(', ', $recipients));
                    
                    foreach ($recipients as $recipient) {
                        try {
                            \Log::info('Sending email to: ' . $recipient);
                            Mail::to($recipient)->send(new NewOrderNotification($order));
                            \Log::info('Email sent successfully to: ' . $recipient);
                        } catch (\Exception $emailError) {
                            \Log::error('Failed to send email to ' . $recipient . ': ' . $emailError->getMessage());
                        }
                    }
                    $order->update(['is_notification_sent' => true]);
                } else {
                    \Log::warning('Order notification emails not configured in settings for order #' . $order->id);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to process order notification for order ' . $order->id . ': ' . $e->getMessage());
            }
        }

        return view('store.order-success', compact('order', 'pixelPurchase'));
    }

    // Helpers
    private function getDescendantCategoryIds(Category $category)
    {
        $ids = [$category->id];
        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->getDescendantCategoryIds($child));
        }
        return $ids;
    }
}
