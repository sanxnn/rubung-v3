<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WilayahService
{
    public function list(string $level, ?string $parentCode = null): array
    {
        $path = $level === 'provinces'
            ? 'provinces.json'
            : $level.'/'.$parentCode.'.json';

        return Cache::remember('wilayah-id:'.$path, now()->addDay(), function () use ($path) {
            $url = 'https://wilayah.id/api/'.$path;

            try {
                $response = Http::acceptJson()->timeout(10)->get($url);
            } catch (ConnectionException $error) {
                Log::warning('Wilayah.id tidak dapat dihubungi.', ['url' => $url, 'error' => $error->getMessage()]);
                throw new HttpResponseException(response()->json(['message' => 'Layanan wilayah sedang tidak tersedia.'], 503));
            }

            if ($response->notFound()) {
                throw new HttpResponseException(response()->json(['message' => 'Wilayah tidak ditemukan.'], 404));
            }

            $data = $response->json('data');

            if (! $response->successful() || ! is_array($data) || ! array_is_list($data) ||
                ! collect($data)->every(fn ($item) => is_array($item)
                    && is_string($item['code'] ?? null)
                    && is_string($item['name'] ?? null))) {
                Log::warning('Respons wilayah.id tidak valid.', ['url' => $url, 'status' => $response->status()]);
                throw new HttpResponseException(response()->json(['message' => 'Layanan wilayah sedang tidak tersedia.'], 503));
            }

            return $data;
        });
    }

    public function resolve(array $codes): array
    {
        $levels = [
            'province' => ['provinces', null],
            'city' => ['regencies', $codes['province_code']],
            'district' => ['districts', $codes['city_code']],
            'village' => ['villages', $codes['district_code']],
        ];

        $names = [];

        foreach ($levels as $field => [$level, $parent]) {
            $code = $codes[$field.'_code'];
            $region = collect($this->list($level, $parent))
                ->first(fn ($item) => is_array($item) && ($item['code'] ?? null) === $code);

            if (! $region) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Kode wilayah tidak valid atau tidak sesuai dengan wilayah induknya.',
                    'errors' => [$field.'_code' => ['Kode wilayah tidak valid atau tidak sesuai dengan wilayah induknya.']],
                ], 422));
            }

            $names[$field] = $region['name'];
        }

        return $names;
    }
}
