<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AddressRegionTest extends TestCase
{
    use RefreshDatabase;

    public function test_region_choices_and_code_based_address_creation_and_update(): void
    {
        $this->fakeWilayah();

        $this->getJson('/api/regions/provinces')->assertOk()->assertJsonPath('data.0.code', '31');
        $this->getJson('/api/regions/regencies/31')->assertOk()->assertJsonPath('data.0.code', '31.74');
        $this->getJson('/api/regions/districts/31.74')->assertOk()->assertJsonPath('data.0.code', '31.74.09');
        $this->getJson('/api/regions/villages/31.74.09')->assertOk()->assertJsonPath('data.0.code', '31.74.09.1002');

        $customer = User::factory()->create();
        $payload = $this->addressPayload();

        $created = $this->actingAs($customer, 'sanctum')->postJson('/api/addresses', $payload)
            ->assertCreated()
            ->assertJsonPath('data.province', 'DKI Jakarta')
            ->assertJsonPath('data.city', 'Kota Administrasi Jakarta Selatan')
            ->assertJsonPath('data.district', 'Jagakarsa')
            ->assertJsonPath('data.village', 'Srengseng Sawah')
            ->assertJsonPath('data.village_code', '31.74.09.1002')
            ->assertJsonPath('data.is_primary', true);

        $address = Address::findOrFail($created->json('data.id'));
        $this->assertStringContainsString('Srengseng Sawah', $address->full_address);

        $this->putJson('/api/addresses/'.$address->id, array_merge($payload, ['label' => 'Kantor']))
            ->assertOk()
            ->assertJsonPath('data.label', 'Kantor')
            ->assertJsonPath('data.province_code', '31');

        $this->putJson('/api/addresses/'.$address->id, [
            'label' => 'Kantor',
            'recipient_name' => 'Siti',
            'phone' => '081234567890',
            'province' => 'Jawa Timur',
            'city' => 'Jember',
            'district' => 'Sumbersari',
            'postal_code' => '68121',
            'detail' => 'Jl. Mawar No. 10',
        ])->assertOk()->assertJsonPath('data.province_code', null)
            ->assertJsonPath('data.village', null);

        Http::assertSentCount(4);
    }

    public function test_invalid_hierarchy_is_rejected_without_saving_address(): void
    {
        $this->fakeWilayah();
        $customer = User::factory()->create();

        $this->actingAs($customer, 'sanctum')
            ->postJson('/api/addresses', array_merge($this->addressPayload(), ['city_code' => '31.73']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('city_code');

        $this->assertDatabaseCount('addresses', 0);

        $this->postJson('/api/addresses', array_merge($this->addressPayload(), [
            'city_code' => null,
            'district_code' => null,
            'village_code' => null,
        ]))->assertUnprocessable()->assertJsonValidationErrors(['city_code', 'district_code', 'village_code']);
    }

    public function test_wilayah_failure_returns_service_unavailable(): void
    {
        Cache::flush();
        Http::fake(['https://wilayah.id/*' => Http::response([], 502)]);

        $this->getJson('/api/regions/provinces')->assertStatus(503)
            ->assertJsonPath('message', 'Layanan wilayah sedang tidak tersedia.');
    }

    public function test_legacy_address_payload_still_works_without_wilayah_service(): void
    {
        Http::preventStrayRequests();
        $customer = User::factory()->create();

        $created = $this->actingAs($customer, 'sanctum')->postJson('/api/addresses', [
            'label' => 'Rumah',
            'recipient_name' => 'Siti',
            'phone' => '081234567890',
            'province' => 'Jawa Timur',
            'city' => 'Jember',
            'district' => 'Sumbersari',
            'postal_code' => '68121',
            'detail' => 'Jl. Mawar No. 10',
        ])->assertCreated()->assertJsonPath('data.province_code', null);

        $this->assertSame('Jember', Address::findOrFail($created->json('data.id'))->city);
        Http::assertNothingSent();
    }

    private function addressPayload(): array
    {
        return [
            'label' => 'Rumah',
            'recipient_name' => 'Siti',
            'phone' => '081234567890',
            'province_code' => '31',
            'city_code' => '31.74',
            'district_code' => '31.74.09',
            'village_code' => '31.74.09.1002',
            'postal_code' => '12640',
            'detail' => 'Jl. Mawar No. 10',
        ];
    }

    private function fakeWilayah(): void
    {
        Cache::flush();
        Http::fake([
            'https://wilayah.id/api/provinces.json' => Http::response(['data' => [['code' => '31', 'name' => 'DKI Jakarta']]]),
            'https://wilayah.id/api/regencies/31.json' => Http::response(['data' => [['code' => '31.74', 'name' => 'Kota Administrasi Jakarta Selatan']]]),
            'https://wilayah.id/api/districts/31.74.json' => Http::response(['data' => [['code' => '31.74.09', 'name' => 'Jagakarsa']]]),
            'https://wilayah.id/api/villages/31.74.09.json' => Http::response(['data' => [['code' => '31.74.09.1002', 'name' => 'Srengseng Sawah']]]),
        ]);
    }
}
