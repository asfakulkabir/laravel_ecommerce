<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Per-zone free shipping threshold (e.g. 1000 => ফ্রি ডেলিভারি).
        if (Schema::hasTable('delivery_charges') && ! Schema::hasColumn('delivery_charges', 'free_shipping_threshold')) {
            Schema::table('delivery_charges', function (Blueprint $table) {
                $table->decimal('free_shipping_threshold', 10, 2)->nullable()->after('charge');
            });
        }

        // Snapshot of the shipping cost actually charged on the order.
        // NULL for legacy orders (fall back to the live zone rate).
        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'shipping_charge')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('shipping_charge', 10, 2)->nullable()->after('delivery_charge_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('delivery_charges', 'free_shipping_threshold')) {
            Schema::table('delivery_charges', function (Blueprint $table) {
                $table->dropColumn('free_shipping_threshold');
            });
        }

        if (Schema::hasColumn('orders', 'shipping_charge')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('shipping_charge');
            });
        }
    }
};
