<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order_number' => $this->whenLoaded('order', fn () => $this->order->order_number),
            'reason' => $this->reason,
            'evidence_url' => route('api.refunds.evidence', $this->id),
            'amount' => $this->amount,
            'status' => $this->status,
            'refund_method' => $this->refund_method,
            'admin_notes' => $this->admin_notes,
            'manual_reference' => $this->manual_reference,
            'reviewed_at' => $this->reviewed_at,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
        ];
    }
}
