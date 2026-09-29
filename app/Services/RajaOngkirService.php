<?php

namespace App\Services;

use App\Models\Address;
use Exception;
use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    private string $baseUrl = 'https://rajaongkir.komerce.id/api/v1';

    /**
     * Checkout selalu menggunakan berat 2 KG.
     */
    private int $weight = 2000;

    /**
     * Hitung ongkir J&T dari Sumbersari, Jember
     * ke alamat customer.
     */
    public function getCost(Address $address): array
    {
        $key = config('services.rajongkir.api_key');

        if (!$key) {
            throw new Exception(
                'RajaOngkir API key belum dikonfigurasi.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CARI ORIGIN TOKO
        |--------------------------------------------------------------------------
        */

        $origin = $this->findOrigin($key);

        if (!$origin) {
            throw new Exception(
                'Origin toko Sumbersari, Jember tidak ditemukan di RajaOngkir.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CARI DESTINATION CUSTOMER
        |--------------------------------------------------------------------------
        */

        $destination = $this->findDestination(
            $address->postal_code,
            $address->district,
            $address->city,
            $key
        );

        if (!$destination) {
            throw new Exception(
                'Lokasi pengiriman customer tidak ditemukan di RajaOngkir.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | HITUNG ONGKIR
        |--------------------------------------------------------------------------
        */

        $response = Http::timeout(15)
            ->withHeaders([
                'key' => $key,
            ])
            ->asForm()
            ->post(
                $this->baseUrl . '/calculate/domestic-cost',
                [
                    'origin' => $origin['id'],
                    'destination' => $destination['id'],
                    'weight' => $this->weight,
                    'courier' => 'jnt',
                    'price' => 'lowest',
                ]
            );

        if (!$response->successful()) {
            throw new Exception(
                'Gagal menghubungi RajaOngkir: ' .
                $response->body()
            );
        }

        $json = $response->json();

        $costs = $json['data'] ?? [];

        if (empty($costs)) {
            throw new Exception(
                'Layanan J&T tidak tersedia untuk alamat tersebut.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL ONGKIR TERMURAH
        |--------------------------------------------------------------------------
        */

        $selected = collect($costs)
            ->sortBy('cost')
            ->first();

        if (!$selected) {
            throw new Exception(
                'Ongkir J&T tidak ditemukan.'
            );
        }

        return [
            'cost' => (int) $selected['cost'],
            'courier' => 'jnt',
            'service' => $selected['service'] ?? 'REG',
            'etd' => $selected['etd'] ?? null,
            'weight' => $this->weight,

            'origin_id' => $origin['id'],
            'origin_label' => $origin['label'] ?? null,

            'destination_id' => $destination['id'],
            'destination_label' => $destination['label'] ?? null,
        ];
    }

    /**
     * Cari origin toko.
     *
     * Contoh:
     * Sumbersari, Jember
     */
    private function findOrigin(string $key): ?array
    {
        $search = config(
            'services.rajongkir.origin_search',
            'Sumbersari,Jember'
        );

        $result = $this->searchDestination(
            $search,
            $key
        );

        if (empty($result)) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | PRIORITAS HASIL YANG PALING SESUAI
        |--------------------------------------------------------------------------
        */

        $normalizedSearch = strtolower($search);

        foreach ($result as $item) {
            $label = strtolower($item['label'] ?? '');

            if (
                str_contains($label, 'sumbersari') &&
                str_contains($label, 'jember')
            ) {
                return $item;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        */

        return $result[0];
    }

    /**
     * Cari destination customer.
     *
     * Prioritas:
     * 1. Kode pos
     * 2. Kecamatan + kota
     * 3. Kota
     */
    private function findDestination(
        ?string $postalCode,
        ?string $district,
        ?string $city,
        string $key
    ): ?array {
        /*
        |--------------------------------------------------------------------------
        | PRIORITAS 1: KODE POS
        |--------------------------------------------------------------------------
        */

        if ($postalCode) {
            $result = $this->searchDestination(
                $postalCode,
                $key
            );

            if (!empty($result)) {
                /*
                 * Cari hasil yang kode posnya benar-benar sama.
                 */
                foreach ($result as $item) {
                    if (
                        isset($item['zip_code']) &&
                        (string) $item['zip_code'] === (string) $postalCode
                    ) {
                        return $item;
                    }
                }

                return $result[0];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PRIORITAS 2: KECAMATAN + KOTA
        |--------------------------------------------------------------------------
        */

        if ($district && $city) {
            $result = $this->searchDestination(
                $district . ' ' . $city,
                $key
            );

            if (!empty($result)) {
                return $result[0];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PRIORITAS 3: KOTA
        |--------------------------------------------------------------------------
        */

        if ($city) {
            $result = $this->searchDestination(
                $city,
                $key
            );

            if (!empty($result)) {
                return $result[0];
            }
        }

        return null;
    }

    /**
     * Search destination RajaOngkir.
     */
    private function searchDestination(
        string $search,
        string $key
    ): array {
        $response = Http::timeout(15)
            ->withHeaders([
                'key' => $key,
            ])
            ->get(
                $this->baseUrl . '/destination/domestic-destination',
                [
                    'search' => $search,
                    'limit' => 10,
                    'offset' => 0,
                ]
            );

        if (!$response->successful()) {
            return [];
        }

        return $response->json('data', []);
    }
}
