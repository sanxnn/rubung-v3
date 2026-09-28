<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DatePromo;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoController extends Controller
{
    public function index()
    {
        $datePromos = DatePromo::query()
            ->latest()
            ->paginate(10, ['*'], 'date_promos_page');

        $vouchers = Voucher::query()
            ->latest()
            ->paginate(10, ['*'], 'vouchers_page');

        return view('admin.promo', compact(
            'datePromos',
            'vouchers'
        ));
    }

    public function storeDatePromo(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'discount_type' => [
                'required',
                Rule::in(['percentage', 'fixed']),
            ],
            'discount_value' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        $request->discount_type === 'percentage'
                        && $value > 100
                    ) {
                        $fail('Diskon persentase maksimal 100%.');
                    }
                },
            ],
            'start_datetime' => [
                'required',
                'date',
            ],
            'end_datetime' => [
                'required',
                'date',
                'after_or_equal:start_datetime',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        DatePromo::create([
            'name' => $validated['name'],
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'start_datetime' => $validated['start_datetime'],
            'end_datetime' => $validated['end_datetime'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with(
            'success',
            'Promo tanggal berhasil ditambahkan.'
        );
    }

    public function updateDatePromo(Request $request, DatePromo $datePromo)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'discount_type' => [
                'required',
                Rule::in(['percentage', 'fixed']),
            ],
            'discount_value' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        $request->discount_type === 'percentage'
                        && $value > 100
                    ) {
                        $fail('Diskon persentase maksimal 100%.');
                    }
                },
            ],
            'start_datetime' => [
                'required',
                'date',
            ],
            'end_datetime' => [
                'required',
                'date',
                'after_or_equal:start_datetime',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $datePromo->update([
            'name' => $validated['name'],
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'start_datetime' => $validated['start_datetime'],
            'end_datetime' => $validated['end_datetime'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with(
            'success',
            'Promo tanggal berhasil diperbarui.'
        );
    }

    public function destroyDatePromo(DatePromo $datePromo)
    {
        $datePromo->delete();

        return back()->with(
            'success',
            'Promo tanggal berhasil dihapus.'
        );
    }

    public function toggleDatePromo(DatePromo $datePromo)
    {
        $datePromo->update([
            'is_active' => !$datePromo->is_active,
        ]);

        return back()->with(
            'success',
            'Status promo tanggal berhasil diperbarui.'
        );
    }

    public function storeVoucher(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9_-]+$/',
                'unique:vouchers,code',
            ],
            'discount_type' => [
                'required',
                Rule::in(['percentage', 'fixed']),
            ],
            'discount_value' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        $request->discount_type === 'percentage'
                        && $value > 100
                    ) {
                        $fail('Diskon persentase maksimal 100%.');
                    }
                },
            ],
            'min_purchase' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'max_discount' => [
                'nullable',
                'integer',
                'min:1',
                'required_if:discount_type,percentage',
            ],
            'usage_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        Voucher::create([
            'code' => strtoupper($validated['code']),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_purchase' => $validated['min_purchase'] ?? 0,
            'max_discount' => $validated['discount_type'] === 'percentage'
                ? ($validated['max_discount'] ?? null)
                : null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'used_count' => 0,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with(
            'success',
            'Voucher berhasil ditambahkan.'
        );
    }

    public function updateVoucher(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('vouchers', 'code')
                    ->ignore($voucher->id),
            ],
            'discount_type' => [
                'required',
                Rule::in(['percentage', 'fixed']),
            ],
            'discount_value' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        $request->discount_type === 'percentage'
                        && $value > 100
                    ) {
                        $fail('Diskon persentase maksimal 100%.');
                    }
                },
            ],
            'min_purchase' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'max_discount' => [
                'nullable',
                'integer',
                'min:1',
                'required_if:discount_type,percentage',
            ],
            'usage_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $voucher->update([
            'code' => strtoupper($validated['code']),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_purchase' => $validated['min_purchase'] ?? 0,
            'max_discount' => $validated['discount_type'] === 'percentage'
                ? ($validated['max_discount'] ?? null)
                : null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with(
            'success',
            'Voucher berhasil diperbarui.'
        );
    }

    public function destroyVoucher(Voucher $voucher)
    {
        $voucher->delete();

        return back()->with(
            'success',
            'Voucher berhasil dihapus.'
        );
    }

    public function toggleVoucher(Voucher $voucher)
    {
        $voucher->update([
            'is_active' => !$voucher->is_active,
        ]);

        return back()->with(
            'success',
            'Status voucher berhasil diperbarui.'
        );
    }
}
