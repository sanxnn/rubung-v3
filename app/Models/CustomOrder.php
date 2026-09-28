<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_number',
        'description',
        'estimated_price',
        'estimated_time',
        'status',
        'admin_notes',
        'order_id',
    ];

    protected $casts = [
        'estimated_price' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function attachments()
    {
        return $this->hasMany(CustomOrderAttachment::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending_review';
    }

    public function isQuoted(): bool
    {
        return $this->status === 'quoted';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
