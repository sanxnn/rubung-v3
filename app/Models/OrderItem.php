<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 'product_variant_id', 'product_name', 'variant_name',
        'quantity', 'unit_price', 'subtotal', 'stock_quantity',
    ];
    protected $casts = ['quantity' => 'integer', 'unit_price' => 'integer', 'subtotal' => 'integer', 'stock_quantity'];

    public function order() { return $this->belongsTo(Order::class); }
    public function productVariant() { return $this->belongsTo(ProductVariant::class); }
}
