<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Services\RajaOngkirService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RajaOngkirServiceTest extends TestCase
{
    public function test_shipping_request_uses_configured_courier_and_weight(): void
    {
        config()->set('services.rajongkir.api_key', 'test-key');
        config()->set('services.rajongkir.courier', 'jnt');
        config()->set('services.rajongkir.weight', 2500);
        config()->set('services.rajongkir.origin_search', 'Menteng,Jakarta');

        Http::fake([
            '*domestic-destination*' => Http::response([
                'data' => [
                    ['id' => 9, 'label' => 'Cempaka Putih Jakarta', 'zip_code' => '10640'],
                    ['id' => 10, 'label' => 'Menteng Jakarta', 'zip_code' => '68121'],
                ],
            ]),
            '*domestic-cost*' => Http::response([
                'data' => [['cost' => 20000, 'service' => 'REG']],
            ]),
        ]);

        $address = new Address([
            'postal_code' => '68121',
            'district' => 'Sumbersari',
            'city' => 'Jember',
        ]);

        $cost = app(RajaOngkirService::class)->getCost($address);

        $this->assertSame(20000, $cost['cost']);
        $this->assertSame(2500, $cost['weight']);
        $this->assertSame(10, $cost['origin_id']);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'domestic-cost')
            && $request['weight'] === 2500
            && $request['courier'] === 'jnt');
    }
}
