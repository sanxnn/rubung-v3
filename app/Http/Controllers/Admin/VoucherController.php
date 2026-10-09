<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DatePromo;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::query()
            ->latest()
            ->paginate(10);

        $allVouchers = Voucher::query()->get();

        $stats = [
            'total' => $allVouchers->count(),

            'active' => $allVouchers
                ->filter(fn ($voucher) => $voucher->isValid())
                ->count(),

            'upcoming' => $allVouchers
                ->filter(fn ($voucher) =>
                    $voucher->is_active &&
                    $voucher->start_date->isFuture()
                )
                ->count(),

            'expired' => $allVouchers
                ->filter(fn ($voucher) =>
                    $voucher->end_date->isPast()
                )
                ->count(),

            'used' => $allVouchers->sum('used_count'),

            'quota_full' => $allVouchers
                ->filter(fn ($voucher) =>
                    !is_null($voucher->usage_limit) &&
                    $voucher->used_count >= $voucher->usage_limit
                )
                ->count(),
        ];

        return view('admin.voucher', compact(
            'vouchers',
            'stats'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('vouchers', 'code'),
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
                'after_or_equal:today',
            ],

            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
                'after_or_equal:today',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'code.required' => 'Kode voucher wajib diisi.',
            'code.max' => 'Kode voucher tidak boleh lebih dari 50 karakter.',
            'code.regex' => 'Kode voucher hanya boleh menggunakan huruf, angka, garis bawah (_) dan tanda strip (-).',
            'code.unique' => 'Kode voucher sudah digunakan.',

            'discount_type.required' => 'Jenis diskon wajib dipilih.',
            'discount_type.in' => 'Jenis diskon tidak valid.',

            'discount_value.required' => 'Nilai diskon wajib diisi.',
            'discount_value.integer' => 'Nilai diskon harus berupa angka.',
            'discount_value.min' => 'Nilai diskon minimal 1.',

            'min_purchase.integer' => 'Minimal pembelian harus berupa angka.',
            'min_purchase.min' => 'Minimal pembelian tidak boleh negatif.',

            'max_discount.required_if' => 'Maksimal diskon wajib diisi untuk diskon persentase.',
            'max_discount.integer' => 'Maksimal diskon harus berupa angka.',
            'max_discount.min' => 'Maksimal diskon minimal 1.',

            'usage_limit.integer' => 'Batas penggunaan harus berupa angka.',
            'usage_limit.min' => 'Batas penggunaan minimal 1.',

            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Format tanggal mulai tidak valid.',
            'start_date.after_or_equal' => 'Tanggal mulai voucher tidak boleh berada di masa lalu.',

            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Format tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai voucher tidak boleh sebelum tanggal mulai atau sudah lewat.',

            'is_active.boolean' => 'Status voucher tidak valid.',
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

    public function update(Request $request, Voucher $voucher)
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
                'after_or_equal:today',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'code.required' => 'Kode voucher wajib diisi.',
            'code.max' => 'Kode voucher tidak boleh lebih dari 50 karakter.',
            'code.regex' => 'Kode voucher hanya boleh menggunakan huruf, angka, garis bawah (_) dan tanda strip (-).',
            'code.unique' => 'Kode voucher sudah digunakan.',

            'discount_type.required' => 'Jenis diskon wajib dipilih.',
            'discount_type.in' => 'Jenis diskon tidak valid.',

            'discount_value.required' => 'Nilai diskon wajib diisi.',
            'discount_value.integer' => 'Nilai diskon harus berupa angka.',
            'discount_value.min' => 'Nilai diskon minimal 1.',

            'min_purchase.integer' => 'Minimal pembelian harus berupa angka.',
            'min_purchase.min' => 'Minimal pembelian tidak boleh negatif.',

            'max_discount.required_if' => 'Maksimal diskon wajib diisi untuk diskon persentase.',
            'max_discount.integer' => 'Maksimal diskon harus berupa angka.',
            'max_discount.min' => 'Maksimal diskon minimal 1.',

            'usage_limit.integer' => 'Batas penggunaan harus berupa angka.',
            'usage_limit.min' => 'Batas penggunaan minimal 1.',

            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Format tanggal mulai tidak valid.',

            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Format tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai voucher tidak boleh sebelum tanggal mulai atau sudah lewat.',

            'is_active.boolean' => 'Status voucher tidak valid.',
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

    public function destroy(Voucher $voucher)
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
