<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 'midtrans_transaction_id', 'payment_type',
        'payment_status', 'amount', 'paid_at', 'snap_token'
    ];
    protected $casts = [
        'amount' => 'integer', 'paid_at' => 'datetime'
    ];

    public function order() { return $this->belongsTo(Order::class); }

    public function isSuccess(): bool { return $this->payment_status === 'success'; }
    public function isPending(): bool { return $this->payment_status === 'pending'; }
}
