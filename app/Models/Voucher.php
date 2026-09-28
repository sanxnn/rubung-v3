<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = [
        'code', 'discount_type', 'discount_value', 'min_purchase', 'max_discount',
        'usage_limit', 'used_count', 'start_date', 'end_date', 'is_active'
    ];
    protected $casts = [
        'discount_value' => 'integer', 'min_purchase' => 'integer', 'max_discount' => 'integer',
        'usage_limit' => 'integer', 'used_count' => 'integer',
        'start_date' => 'date', 'end_date' => 'date', 'is_active' => 'boolean'
    ];

    public function orders() { return $this->hasMany(Order::class); }

    public function isValid(): bool
    {
        $today = Carbon::today();

        $isWithinDate = $today->between(
            $this->start_date,
            $this->end_date
        );

        $hasQuota = is_null($this->usage_limit)
            || $this->used_count < $this->usage_limit;

        return $this->is_active
            && $isWithinDate
            && $hasQuota;
    }

    public function calculateDiscount(int $subtotal): int {
        if (!$this->isValid() || $subtotal < $this->min_purchase) return 0;

        $discount = $this->discount_type === 'percentage'
            ? (int) round($subtotal * ($this->discount_value / 100))
            : $this->discount_value;

        if ($this->discount_type === 'percentage' && $this->max_discount) {
            $discount = min($discount, $this->max_discount);
        }

        return min($discount, $subtotal); // Diskon tidak boleh lebih besar dari subtotal
    }
}
