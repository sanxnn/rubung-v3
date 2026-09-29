<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'order_number',

        'subtotal',

        'discount_date_cantik',
        'date_promo_id',

        'discount_voucher',
        'voucher_id',

        'shipping_cost',
        'final_amount',

        'status',

        'shipping_courier',
        'resi_number',

        'snapshot_recipient_name',
        'snapshot_phone',
        'snapshot_province',
        'snapshot_city',
        'snapshot_postal_code',
        'snapshot_detail',

        'notes',

        'stock_reserved_at',
        'stock_released_at',
    ];

    protected $casts = [
        'subtotal' =>
            'integer',

        'discount_date_cantik' =>
            'integer',

        'discount_voucher' =>
            'integer',

        'shipping_cost' =>
            'integer',

        'final_amount' =>
            'integer',

        'stock_reserved_at' =>
            'datetime',

        'stock_released_at' =>
            'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function address()
    {
        return $this->belongsTo(
            Address::class
        );
    }

    public function datePromo()
    {
        return $this->belongsTo(
            DatePromo::class
        );
    }

    public function voucher()
    {
        return $this->belongsTo(
            Voucher::class
        );
    }

    public function items()
    {
        return $this->hasMany(
            OrderItem::class
        );
    }

    public function statusHistories()
    {
        return $this
            ->hasMany(
                OrderStatusHistory::class
            )
            ->latest();
    }

    public function payment()
    {
        return $this->hasOne(
            Payment::class
        );
    }

    public function customOrder()
    {
        return $this->hasOne(
            CustomOrder::class
        );
    }

    public function isCustomOrder(): bool
    {
        return $this->customOrder !== null;
    }

    public function getTotalDiscountAttribute(): int
    {
        return
            $this->discount_date_cantik +
            $this->discount_voucher;
    }
}
