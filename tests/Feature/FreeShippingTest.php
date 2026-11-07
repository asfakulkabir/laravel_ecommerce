<?php

namespace Tests\Feature;

use App\Models\DeliveryCharge;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FreeShippingTest extends TestCase
{
    use RefreshDatabase;

    private DeliveryCharge $zone;

    protected function setUp(): void
    {
        parent::setUp();

        $this->zone = DeliveryCharge::create([
            'zone'             => 'Inside Dhaka',
            'charge'           => 80,
            'estimated_days'   => '2-3 Days',
        ]);
    }

    private function placeOrder(int $price = 1000, int $quantity = 1, ?string $deliveryZone = null)
    {
        $product = Product::create([
            'name'           => 'Test Product',
            'slug'           => 'test-product-' . uniqid(),
            'product_type'   => 'simple',
            'regular_price'  => $price,
            'stock_quantity' => 50,
            'is_active'      => true,
        ]);

        $cart = [
            'item-1' => [
                'product_id'        => $product->id,
                'variation_id'      => null,
                'name'              => $product->name,
                'price'             => $price,
                'quantity'          => $quantity,
                'variation_details' => null,
                'image'             => null,
            ],
        ];

        $data = [
            'customer_name'    => 'Test Customer',
            'customer_phone'   => '01712345678',
            'customer_address' => 'Dhaka, Bangladesh',
        ];

        if ($deliveryZone !== null) {
            $data['delivery_zone'] = $deliveryZone;
        }

        return $this->withSession(['cart' => $cart])
            ->post(route('checkout.place'), $data);
    }

    public function test_checkout_hides_paid_zones_when_free_shipping_qualifies(): void
    {
        \Illuminate\Support\Facades\DB::table('settings')->insertOrIgnore([
            'key'         => 'free_shipping_threshold',
            'value'       => '1000',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $product = Product::create([
            'name'           => 'T',
            'slug'           => 't-' . uniqid(),
            'product_type'   => 'simple',
            'regular_price'  => 1000,
            'stock_quantity' => 10,
            'is_active'      => true,
        ]);

        $response = $this->withSession(['cart' => [
            'item-1' => ['product_id' => $product->id, 'variation_id' => null, 'name' => 'T', 'price' => 1000, 'quantity' => 1, 'variation_details' => null, 'image' => null],
        ]])->get(route('checkout'));

        $response->assertOk();

        // Strip <script> tags — the JS computed property always contains the string.
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/is', '', $response->getContent());
        $this->assertStringContainsString('ফ্রি ডেলিভারি 🎉', $html);
        // Paid zone radio buttons must NOT be rendered when free shipping qualifies.
        $this->assertStringNotContainsString('value="Inside Dhaka"', $html);
    }

    public function test_checkout_shows_paid_zones_when_below_threshold(): void
    {
        \Illuminate\Support\Facades\DB::table('settings')->insertOrIgnore([
            'key'         => 'free_shipping_threshold',
            'value'       => '1000',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $product = Product::create([
            'name'           => 'T',
            'slug'           => 't-' . uniqid(),
            'product_type'   => 'simple',
            'regular_price'  => 500,
            'stock_quantity' => 10,
            'is_active'      => true,
        ]);

        $this->withSession(['cart' => [
            'item-1' => ['product_id' => $product->id, 'variation_id' => null, 'name' => 'T', 'price' => 500, 'quantity' => 1, 'variation_details' => null, 'image' => null],
        ]])->get(route('checkout'))
            ->assertOk()
            ->assertSee('Inside Dhaka')
            ->assertSee('৳80');
    }

    public function test_checkout_hides_free_banner_when_threshold_not_set(): void
    {
        \Illuminate\Support\Facades\DB::table('settings')->insertOrIgnore([
            'key'         => 'free_shipping_threshold',
            'value'       => '',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $product = Product::create([
            'name'           => 'T',
            'slug'           => 't-' . uniqid(),
            'product_type'   => 'simple',
            'regular_price'  => 5000,
            'stock_quantity' => 10,
            'is_active'      => true,
        ]);

        $response = $this->withSession(['cart' => [
            'item-1' => ['product_id' => $product->id, 'variation_id' => null, 'name' => 'T', 'price' => 5000, 'quantity' => 1, 'variation_details' => null, 'image' => null],
        ]])->get(route('checkout'));

        $response->assertOk();

        // Strip <script> tags — the JS computed property always contains the string.
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/is', '', $response->getContent());
        $this->assertStringNotContainsString('ফ্রি ডেলিভারি 🎉', $html);
    }

    public function test_order_below_threshold_charges_shipping(): void
    {
        \App\Models\Setting::setValue('free_shipping_threshold', 1000);

        $this->placeOrder(500, 1, 'Inside Dhaka');

        $order = Order::latest('id')->firstOrFail();

        $this->assertSame(580, $order->total_amount);
        $this->assertEquals(80, (float) $order->shipping_charge);
        $this->assertNotNull($order->delivery_charge_id);
    }

    public function test_order_at_threshold_gets_free_shipping(): void
    {
        \Illuminate\Support\Facades\DB::table('settings')->insertOrIgnore([
            'key'         => 'free_shipping_threshold',
            'value'       => '1000',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->placeOrder(1000, 1); // no delivery_zone — free shipping

        $order = Order::latest('id')->firstOrFail();

        $this->assertSame(1000, $order->total_amount);
        $this->assertEquals(0, (float) $order->shipping_charge);
        $this->assertNull($order->delivery_charge_id);
    }

    public function test_order_above_threshold_gets_free_shipping(): void
    {
        \Illuminate\Support\Facades\DB::table('settings')->insertOrIgnore([
            'key'         => 'free_shipping_threshold',
            'value'       => '1000',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->placeOrder(2000, 1);

        $order = Order::latest('id')->firstOrFail();

        $this->assertSame(2000, $order->total_amount);
        $this->assertEquals(0, (float) $order->shipping_charge);
    }

    public function test_order_success_page_shows_free_delivery(): void
    {
        \Illuminate\Support\Facades\DB::table('settings')->insertOrIgnore([
            'key'         => 'free_shipping_threshold',
            'value'       => '1000',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->placeOrder(2000, 1);

        $order = Order::latest('id')->firstOrFail();

        $this->get(route('order.success', $order->id))
            ->assertOk()
            ->assertSee('ফ্রি ডেলিভারি')
            ->assertSee(number_format($order->total_amount, 0));
    }

    public function test_zone_without_free_shipping_still_charges(): void
    {
        \Illuminate\Support\Facades\DB::table('settings')->insertOrIgnore([
            'key'         => 'free_shipping_threshold',
            'value'       => '',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->placeOrder(5000, 1, 'Inside Dhaka');

        $order = Order::latest('id')->firstOrFail();

        $this->assertSame(5080, $order->total_amount);
        $this->assertEquals(80, (float) $order->shipping_charge);
    }

    public function test_admin_can_save_free_shipping_threshold(): void
    {
        $admin = \App\Models\User::create([
            'name'     => 'Admin',
            'email'    => 'shipping-admin@example.com',
            'password' => 'secret',
            'is_admin' => true,
        ]);

        $this->actingAs($admin)->put('/admin/settings', [
            'site_name'                   => 'Outfitt',
            'site_description'            => '',
            'primary_color'               => '#7c3aed',
            'order_notification_emails'   => '',
            'meta_pixel_id'               => '',
            'meta_conversion_api_token'   => '',
            'meta_test_event_code'        => '',
            'whatsapp_number'             => '',
            'facebook_url'                => '',
            'instagram_url'               => '',
            'youtube_url'                 => '',
            'twitter_url'                 => '',
            'steadfast_api_key'           => '',
            'steadfast_secret_key'        => '',
            'free_shipping_threshold'     => '1500',
        ])->assertRedirect(route('admin.settings.edit'));

        $this->assertEquals('1500', \App\Models\Setting::getValue('free_shipping_threshold', ''));
    }

    public function test_blank_threshold_disables_free_shipping(): void
    {
        \Illuminate\Support\Facades\DB::table('settings')->insertOrIgnore([
            'key'         => 'free_shipping_threshold',
            'value'       => '',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->placeOrder(99999, 1, 'Inside Dhaka');

        $order = Order::latest('id')->firstOrFail();

        $this->assertSame(100079, $order->total_amount); // 99999 + 80
        $this->assertEquals(80, (float) $order->shipping_charge);
    }
}
