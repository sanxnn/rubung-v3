<?php

namespace Database\Factories;

use App\Models\CustomOrder;
use App\Models\CustomOrderAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomOrderAttachmentFactory extends Factory
{
    protected $model = CustomOrderAttachment::class;

    public function definition(): array
    {
        return [
            'custom_order_id' => CustomOrder::factory(),
            'file_path' => 'attachments/' . fake()->uuid() . '.jpg',
            'uploaded_by' => User::factory(),
        ];
    }
}
