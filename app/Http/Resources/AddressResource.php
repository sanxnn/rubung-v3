<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'recipient_name' => $this->recipient_name,
            'phone' => $this->phone,

            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'postal_code' => $this->postal_code,

            'detail' => $this->detail,
            'full_address' => $this->full_address,

            'is_primary' => $this->is_primary,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
