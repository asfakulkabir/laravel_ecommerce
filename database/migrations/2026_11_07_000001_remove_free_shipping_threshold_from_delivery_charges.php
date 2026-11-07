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
        // Free shipping is now a single global setting (settings table),
        // not a per-zone field. Remove the per-zone column added earlier.
        if (Schema::hasTable('delivery_charges') && Schema::hasColumn('delivery_charges', 'free_shipping_threshold')) {
            Schema::table('delivery_charges', function (Blueprint $table) {
                $table->dropColumn('free_shipping_threshold');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('delivery_charges') && ! Schema::hasColumn('delivery_charges', 'free_shipping_threshold')) {
            Schema::table('delivery_charges', function (Blueprint $table) {
                $table->decimal('free_shipping_threshold', 10, 2)->nullable()->after('charge');
            });
        }
    }
};
