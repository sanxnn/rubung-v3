<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'reference_number' =>
                $this->reference_number,

            'description' =>
                $this->description,

            'status' =>
                $this->status,

            'estimated_price' =>
                $this->estimated_price,

            'estimated_time' =>
                $this->estimated_time,

            'admin_notes' =>
                $this->admin_notes,

            'attachments' =>
                $this->whenLoaded('attachments', function () {
                    return $this->attachments->map(function ($attachment) {
                        return [
                            'id' => $attachment->id,
                            'file_path' => $attachment->file_path,
                            'created_at' => $attachment->created_at,
                        ];
                    });
                }),

            'order' =>
                $this->whenLoaded('order', function () {
                    return $this->order ? [
                        'id' => $this->order->id,
                        'order_number' => $this->order->order_number,
                        'status' => $this->order->status,
                        'final_amount' => $this->order->final_amount,
                    ] : null;
                }),

            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at,
        ];
    }
}
