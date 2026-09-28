<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
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
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['required', 'digits:5'],
            'detail' => ['required', 'string'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();

        $isPrimary = $validated['is_primary'] ?? false;

        /*
         * Kalau customer belum punya alamat,
         * alamat pertama otomatis menjadi alamat utama.
         */
        if (!$user->addresses()->exists()) {
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
                'city' => $validated['city'],
                'district' => $validated['district'] ?? null,
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

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['required', 'digits:5'],
            'detail' => ['required', 'string'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

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
                'city' => $validated['city'],
                'district' => $validated['district'] ?? null,
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
}
