<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\WilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    public function __construct(private readonly WilayahService $wilayah) {}

    public function index(Request $request)
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_primary')
            ->latest()
            ->get();

        return AddressResource::collection($addresses)
            ->additional([
                'message' => 'Data alamat berhasil diambil.',
            ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAddress($request);

        $user = $request->user();

        $isPrimary = $validated['is_primary'] ?? false;

        /*
         * Kalau customer belum punya alamat,
         * alamat pertama otomatis menjadi alamat utama.
         */
        if (! $user->addresses()->exists()) {
            $isPrimary = true;
        }

        return DB::transaction(function () use ($user, $validated, $isPrimary) {

            /*
             * Kalau alamat baru dijadikan utama,
             * alamat utama sebelumnya dicabut.
             */
            if ($isPrimary) {
                $user->addresses()->update([
                    'is_primary' => false,
                ]);
            }

            $address = $user->addresses()->create([
                'label' => $validated['label'],
                'recipient_name' => $validated['recipient_name'],
                'phone' => $validated['phone'],
                'province' => $validated['province'],
                'province_code' => $validated['province_code'],
                'city' => $validated['city'],
                'city_code' => $validated['city_code'],
                'district' => $validated['district'] ?? null,
                'district_code' => $validated['district_code'],
                'village' => $validated['village'],
                'village_code' => $validated['village_code'],
                'postal_code' => $validated['postal_code'],
                'detail' => $validated['detail'],
                'is_primary' => $isPrimary,
            ]);

            return (new AddressResource($address))
                ->additional([
                    'message' => 'Alamat berhasil ditambahkan.',
                ]);
        });
    }

    public function show(Request $request, Address $address)
    {
        $this->authorizeAddress($request, $address);

        return (new AddressResource($address))
            ->additional([
                'message' => 'Detail alamat berhasil diambil.',
            ]);
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeAddress($request, $address);

        $validated = $this->validateAddress($request);

        $isPrimary = $validated['is_primary'] ?? $address->is_primary;

        return DB::transaction(function () use ($request, $address, $validated, $isPrimary) {

            if ($isPrimary) {
                $request->user()
                    ->addresses()
                    ->where('id', '!=', $address->id)
                    ->update([
                        'is_primary' => false,
                    ]);
            }

            $address->update([
                'label' => $validated['label'],
                'recipient_name' => $validated['recipient_name'],
                'phone' => $validated['phone'],
                'province' => $validated['province'],
                'province_code' => $validated['province_code'],
                'city' => $validated['city'],
                'city_code' => $validated['city_code'],
                'district' => $validated['district'] ?? null,
                'district_code' => $validated['district_code'],
                'village' => $validated['village'],
                'village_code' => $validated['village_code'],
                'postal_code' => $validated['postal_code'],
                'detail' => $validated['detail'],
                'is_primary' => $isPrimary,
            ]);

            return (new AddressResource($address->fresh()))
                ->additional([
                    'message' => 'Alamat berhasil diperbarui.',
                ]);
        });
    }

    public function destroy(Request $request, Address $address)
    {
        $this->authorizeAddress($request, $address);

        $wasPrimary = $address->is_primary;

        $address->delete();

        /*
         * Kalau alamat utama dihapus,
         * jadikan alamat terbaru sebagai utama.
         */
        if ($wasPrimary) {
            $newPrimary = $request->user()
                ->addresses()
                ->latest()
                ->first();

            if ($newPrimary) {
                $newPrimary->update([
                    'is_primary' => true,
                ]);
            }
        }

        return response()->json([
            'message' => 'Alamat berhasil dihapus.',
        ]);
    }

    public function setPrimary(Request $request, Address $address)
    {
        $this->authorizeAddress($request, $address);

        DB::transaction(function () use ($request, $address) {

            $request->user()
                ->addresses()
                ->update([
                    'is_primary' => false,
                ]);

            $address->update([
                'is_primary' => true,
            ]);
        });

        return (new AddressResource($address->fresh()))
            ->additional([
                'message' => 'Alamat utama berhasil diubah.',
            ]);
    }

    private function authorizeAddress(Request $request, Address $address): void
    {
        abort_unless(
            $address->user_id === $request->user()->id,
            404
        );
    }

    private function validateAddress(Request $request): array
    {
        $usingCodes = $request->hasAny(['province_code', 'city_code', 'district_code', 'village_code']);
        $requiredCode = Rule::requiredIf($usingCodes);

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'province' => [Rule::requiredIf(! $usingCodes), 'nullable', 'string', 'max:100'],
            'city' => [Rule::requiredIf(! $usingCodes), 'nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'province_code' => [$requiredCode, 'regex:/^[0-9]{2}$/'],
            'city_code' => [$requiredCode, 'regex:/^[0-9]{2}\.[0-9]{2}$/'],
            'district_code' => [$requiredCode, 'regex:/^[0-9]{2}\.[0-9]{2}\.[0-9]{2}$/'],
            'village_code' => [$requiredCode, 'regex:/^[0-9]{2}\.[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/'],
            'postal_code' => ['required', 'digits:5'],
            'detail' => ['required', 'string'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        if ($usingCodes) {
            $names = $this->wilayah->resolve($validated);
            $validated = array_merge($validated, $names);
        } else {
            $validated['province_code'] = null;
            $validated['city_code'] = null;
            $validated['district_code'] = null;
            $validated['village_code'] = null;
            $validated['village'] = null;
        }

        return $validated;
    }
}
