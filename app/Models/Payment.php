<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function saveSnapTokenForPendingOrder(Order $order, string $snapToken): ?self
    {
        return DB::transaction(function () use ($order, $snapToken) {
            $lockedOrder = Order::lockForUpdate()->findOrFail($order->id);
            if ($lockedOrder->status !== 'pending_payment') {
                return null;
            }

            $payment = $lockedOrder->payment()->first();
            if ($payment) {
                if (! $payment->isPending()) {
                    return null;
                }

                if (! $payment->snap_token) {
                    $payment->update(['snap_token' => $snapToken]);
                }

                return $payment;
            }

            return $lockedOrder->payment()->create([
                'snap_token' => $snapToken,
                'payment_type' => 'pending',
                'payment_status' => 'pending',
                'amount' => $lockedOrder->final_amount,
            ]);
        });
    }

    public function order() { return $this->belongsTo(Order::class); }

    public function isSuccess(): bool { return $this->payment_status === 'success'; }
    public function isPending(): bool { return $this->payment_status === 'pending'; }
}
