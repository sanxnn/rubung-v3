<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatePromo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'discount_type',
        'discount_value',
        'start_datetime',
        'end_datetime',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'integer',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Cek apakah promo sedang aktif sekarang.
     */
    public function isActiveNow(): bool
    {
        return $this->is_active
            && Carbon::now()->between(
                $this->start_datetime,
                $this->end_datetime
            );
    }

    /**
     * Cek apakah promo akan datang.
     */
    public function isUpcoming(): bool
    {
        return $this->is_active
            && Carbon::now()->lt($this->start_datetime);
    }

    /**
     * Cek apakah promo sudah selesai.
     */
    public function isFinished(): bool
    {
        return Carbon::now()->gt($this->end_datetime);
    }

    /**
     * Hitung nominal diskon berdasarkan subtotal.
     */
    public function calculateDiscount(int $subtotal): int
    {
        if (!$this->isActiveNow()) {
            return 0;
        }

        $discount = $this->discount_type === 'percentage'
            ? (int) round(
                $subtotal * ($this->discount_value / 100)
            )
            : $this->discount_value;

        return min($discount, $subtotal);
    }
}
