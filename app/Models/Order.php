<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';

    const STATUSES = [
        'processing' => '🔷 Processing',
        'shipped'    => '🚚 Shipped',
        'delivered'  => '✅ Delivered',
        'cancelled'  => '❌ Cancelled',
    ];

    protected $fillable = [
        'items_json',
        'payment_method',
        'customer_name',
        'customer_phone',
        'customer_address',
        'delivery_charge_id',
        'shipping_charge',
        'bkash_trx_id',
        'total_amount',
        'total',          // alias kept for backward compat in views
        'status',
        'is_sent_to_steadfast',
        'is_notification_sent',
        'steadfast_consignment_id',
    ];

    protected $casts = [
        'total_amount'           => 'integer',
        'shipping_charge'        => 'decimal:2',
        'is_sent_to_steadfast'   => 'boolean',
        'is_notification_sent'   => 'boolean',
    ];

    // Relationships
    public function deliveryCharge()
    {
        return $this->belongsTo(DeliveryCharge::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Helpers
    /**
     * The shipping cost actually charged for this order.
     *
     * Uses the snapshot stored at order time (0 when free shipping applied);
     * legacy orders fall back to the live zone rate.
     */
    public function actualShippingCharge(): float
    {
        if ($this->shipping_charge !== null) {
            return (float) $this->shipping_charge;
        }

        $deliveryCharge = $this->deliveryCharge;

        return $deliveryCharge ? (float) $deliveryCharge->charge : 0.0;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getItemsAttribute(): array
    {
        return json_decode($this->items_json ?: '[]', true);
    }
}
