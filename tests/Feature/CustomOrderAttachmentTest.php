<?php

namespace Tests\Feature;

use App\Models\CustomOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomOrderAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_upload_images_when_creating_and_before_review(): void
    {
        Storage::fake('public');
        $customer = User::factory()->create();

        $created = $this->actingAs($customer, 'sanctum')
            ->postJson('/api/custom-orders', [
                'description' => 'Batik motif bunga warna biru',
                'attachments' => [UploadedFile::fake()->image('motif.jpg')],
            ])
            ->assertCreated()
            ->assertJsonCount(1, 'data.attachments');

        $order = CustomOrder::findOrFail($created->json('data.id'));
        $firstPath = $created->json('data.attachments.0.file_path');
        Storage::disk('public')->assertExists($firstPath);
        $this->assertSame($customer->id, $order->attachments()->firstOrFail()->uploaded_by);

        $this->postJson('/api/custom-orders/'.$order->id.'/attachments', [
            'attachments' => [UploadedFile::fake()->image('referensi.png')],
        ])->assertCreated()->assertJsonCount(2, 'data.attachments');

        $this->getJson('/api/custom-orders/'.$order->id)
            ->assertOk()
            ->assertJsonCount(2, 'data.attachments');
        Storage::disk('public')->assertExists($order->attachments()->pluck('file_path')->all());
    }

    public function test_attachment_upload_checks_owner_status_count_and_file_type(): void
    {
        Storage::fake('public');
        $customer = User::factory()->create();
        $other = User::factory()->create();
        $order = CustomOrder::factory()->create(['user_id' => $customer->id, 'status' => 'pending_review']);
        $url = '/api/custom-orders/'.$order->id.'/attachments';

        $this->actingAs($other, 'sanctum')
            ->postJson($url, ['attachments' => [UploadedFile::fake()->image('other.jpg')]])
            ->assertNotFound();

        $this->actingAs($customer, 'sanctum')
            ->postJson($url, ['attachments' => [UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('attachments.0');

        $this->postJson($url, [
            'attachments' => array_fill(0, 5, UploadedFile::fake()->image('motif.jpg')),
        ])->assertCreated()->assertJsonCount(5, 'data.attachments');

        $this->postJson($url, ['attachments' => [UploadedFile::fake()->image('extra.jpg')]])
            ->assertUnprocessable();
        $this->assertSame(5, $order->attachments()->count());

        $order->update(['status' => 'quoted']);
        $this->postJson($url, ['attachments' => [UploadedFile::fake()->image('late.jpg')]])
            ->assertUnprocessable();
        $this->assertSame(5, $order->attachments()->count());
    }
}
