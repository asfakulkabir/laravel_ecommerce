<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartSalePriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_simple_product_uses_sale_price_in_cart(): void
    {
        $product = Product::create([
            'name'           => 'Sale T-Shirt',
            'slug'           => 'sale-t-shirt',
            'product_type'   => 'simple',
            'regular_price'  => 1000,
            'sale_price'     => 700,
            'stock_quantity' => 10,
            'is_active'      => true,
        ]);

        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $cart = session('cart');
        $this->assertArrayHasKey("product_{$product->id}", $cart);
        $this->assertEquals(700, (float) $cart["product_{$product->id}"]['price']);
        $this->assertEquals(1400, (float) array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart)));
    }

    public function test_simple_product_without_sale_uses_regular_price(): void
    {
        $product = Product::create([
            'name'           => 'Regular T-Shirt',
            'slug'           => 'regular-t-shirt',
            'product_type'   => 'simple',
            'regular_price'  => 1000,
            'sale_price'     => null,
            'stock_quantity' => 10,
            'is_active'      => true,
        ]);

        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $cart = session('cart');
        $this->assertEquals(1000, (float) $cart["product_{$product->id}"]['price']);
    }

    public function test_variable_product_falls_back_to_sale_price_when_variation_has_no_price(): void
    {
        $product = Product::create([
            'name'           => 'Sale Dress',
            'slug'           => 'sale-dress',
            'product_type'   => 'variable',
            'regular_price'  => 1500,
            'sale_price'     => 1100,
            'stock_quantity' => 0,
            'is_active'      => true,
        ]);

        // Variation with NO price set -> should fall back to product sale price.
        ProductVariation::create([
            'product_id' => $product->id,
            'size'       => 'S',
            'color'      => 'Red',
            'price'      => null,
            'stock'      => 5,
        ]);

        $this->post(route('cart.add'), [
            'product_id'  => $product->id,
            'quantity'    => 1,
            'variation_id' => ProductVariation::first()->id,
        ]);

        $cart = session('cart');
        $this->assertCount(1, $cart);
        $this->assertEquals(1100, (float) array_values($cart)[0]['price']);
    }

    public function test_variable_product_uses_own_variation_price_when_set(): void
    {
        $product = Product::create([
            'name'           => 'Dress',
            'slug'           => 'dress',
            'product_type'   => 'variable',
            'regular_price'  => 1500,
            'sale_price'     => 1100,
            'stock_quantity' => 0,
            'is_active'      => true,
        ]);

        // Variation WITH its own price -> variation price wins.
        ProductVariation::create([
            'product_id' => $product->id,
            'size'       => 'M',
            'color'      => 'Blue',
            'price'      => 1300,
            'stock'      => 5,
        ]);

        $this->post(route('cart.add'), [
            'product_id'  => $product->id,
            'quantity'    => 1,
            'variation_id' => ProductVariation::first()->id,
        ]);

        $cart = session('cart');
        $this->assertEquals(1300, (float) array_values($cart)[0]['price']);
    }
}
