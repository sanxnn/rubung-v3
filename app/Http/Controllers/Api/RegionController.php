<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WilayahService;

class RegionController extends Controller
{
    public function provinces(WilayahService $wilayah)
    {
        return response()->json(['data' => $wilayah->list('provinces')]);
    }

    public function regencies(WilayahService $wilayah, string $provinceCode)
    {
        return response()->json(['data' => $wilayah->list('regencies', $provinceCode)]);
    }

    public function districts(WilayahService $wilayah, string $cityCode)
    {
        return response()->json(['data' => $wilayah->list('districts', $cityCode)]);
    }

    public function villages(WilayahService $wilayah, string $districtCode)
    {
        return response()->json(['data' => $wilayah->list('villages', $districtCode)]);
    }
}
