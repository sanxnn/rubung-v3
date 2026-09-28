<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,

            'status' => $this->status,

            'is_custom_order' => $this->isCustomOrder(),

            'pricing' => [
                'subtotal' => $this->subtotal,
                'discount_date_cantik' => $this->discount_date_cantik,
                'discount_voucher' => $this->discount_voucher,
                'total_discount' => $this->total_discount,
                'shipping_cost' => $this->shipping_cost,
                'final_amount' => $this->final_amount,
            ],

            'promo' => [
                'date_promo' => $this->whenLoaded('datePromo', function () {
                    return $this->datePromo ? [
                        'id' => $this->datePromo->id,
                        'name' => $this->datePromo->name,
                    ] : null;
                }),

                'voucher' => $this->whenLoaded('voucher', function () {
                    return $this->voucher ? [
                        'id' => $this->voucher->id,
                        'code' => $this->voucher->code,
                    ] : null;
                }),
            ],

            'shipping' => [
                'courier' => $this->shipping_courier,
                'resi_number' => $this->resi_number,
                'cost' => $this->shipping_cost,
            ],

            'address' => [
                'recipient_name' => $this->snapshot_recipient_name,
                'phone' => $this->snapshot_phone,
                'province' => $this->snapshot_province,
                'city' => $this->snapshot_city,
                'postal_code' => $this->snapshot_postal_code,
                'detail' => $this->snapshot_detail,
            ],

            'notes' => $this->notes,

            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_variant_id' => $item->product_variant_id,
                        'product_name' => $item->product_name,
                        'variant_name' => $item->variant_name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->subtotal,
                    ];
                });
            }),

            'custom_order' => $this->whenLoaded('customOrder', function () {
                return $this->customOrder ? [
                    'id' => $this->customOrder->id,
                    'reference_number' => $this->customOrder->reference_number,
                    'status' => $this->customOrder->status,
                    'description' => $this->customOrder->description,
                    'estimated_price' => $this->customOrder->estimated_price,
                    'estimated_time' => $this->customOrder->estimated_time,
                ] : null;
            }),

            'payment' => $this->whenLoaded('payment', function () {
                return $this->payment ? [
                    'payment_type' => $this->payment->payment_type,
                    'payment_status' => $this->payment->payment_status,
                    'amount' => $this->payment->amount,
                    'paid_at' => $this->payment->paid_at,
                ] : null;
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
