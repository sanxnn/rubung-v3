<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'reviewed_by', 'reason', 'evidence_path',
        'amount', 'status', 'refund_method', 'refund_key',
        'manual_reference', 'admin_notes', 'reviewed_at', 'completed_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'reviewed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
