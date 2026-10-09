<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DatePromo;
use App\Models\Voucher;
use Carbon\Carbon;
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

        /*
        |--------------------------------------------------------------------------
        | Date Promo Statistics
        |--------------------------------------------------------------------------
        */

        $allDatePromos = DatePromo::query()->get();

        $activeDatePromos = $allDatePromos
            ->filter(fn ($promo) => $promo->isActiveNow())
            ->count();

        $upcomingDatePromos = $allDatePromos
            ->filter(fn ($promo) => $promo->isUpcoming())
            ->count();

        $finishedDatePromos = $allDatePromos
            ->filter(fn ($promo) => $promo->isFinished())
            ->count();

        return view('admin.promo', compact(
            'datePromos',
            'vouchers',
            'activeDatePromos',
            'upcomingDatePromos',
            'finishedDatePromos'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s.-]+$/',
                Rule::unique('date_promos', 'name'),
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
                'after_or_equal:now',
            ],

            'end_datetime' => [
                'required',
                'date',
                'after_or_equal:start_datetime',
                'after_or_equal:now',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.regex' => 'Nama promo hanya boleh menggunakan huruf, angka, spasi, titik (.) dan tanda strip (-).',

            'name.unique' => 'Nama promo sudah digunakan.',

            'start_datetime.after_or_equal' => 'Tanggal mulai promo tidak boleh berada di masa lalu.',

            'end_datetime.after_or_equal' => 'Tanggal selesai promo tidak boleh berada di masa lalu.',
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

    public function update(Request $request, DatePromo $datePromo)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s.-]+$/',
                Rule::unique('date_promos', 'name')->ignore($datePromo->id),
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
                'after_or_equal:now',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.regex' => 'Nama promo hanya boleh menggunakan huruf, angka, spasi, titik (.) dan tanda strip (-).',

            'name.unique' => 'Nama promo sudah digunakan.',

            'end_datetime.after_or_equal' => 'Tanggal selesai promo tidak boleh berada di masa lalu.',
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

    public function destroy(DatePromo $datePromo)
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
}
