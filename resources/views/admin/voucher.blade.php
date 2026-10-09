@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}
        <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Kelola Voucher</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola promo voucher diskon untuk pelanggan.
                </p>
            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- ALERT --}}
        {{-- ========================================================= --}}
        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 shadow-sm">
                <i class="fa-solid fa-circle-check mt-0.5 text-lg text-green-600"></i>

                <div class="flex-1 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation mt-0.5 text-lg text-red-600"></i>

                <div class="flex-1">
                    <p class="mb-1 text-sm font-bold text-red-800">
                        Terjadi kesalahan:
                    </p>

                    <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ========================================================= --}}
        {{-- SUMMARY CARDS --}}
        {{-- ========================================================= --}}
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

            {{-- Total Voucher --}}
            <div
                class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Total Voucher
                        </p>

                        <h3 class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $stats['total'] }}
                        </h3>

                        <p class="mt-1 text-xs text-gray-400">
                            Semua kode voucher
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-ticket text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Voucher Aktif --}}
            <div
                class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Voucher Aktif
                        </p>

                        <h3 class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $stats['active'] }}
                        </h3>

                        <p class="mt-1 text-xs text-green-600">
                            <i class="fa-solid fa-circle-check mr-1"></i>
                            Bisa digunakan
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-600">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Akan Datang --}}
            <div
                class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Akan Datang
                        </p>

                        <h3 class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $stats['upcoming'] }}
                        </h3>

                        <p class="mt-1 text-xs text-blue-600">
                            <i class="fa-solid fa-clock mr-1"></i>
                            Belum dimulai
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-calendar-plus text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Sudah Berakhir --}}
            <div
                class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Sudah Berakhir
                        </p>

                        <h3 class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $stats['expired'] }}
                        </h3>

                        <p class="mt-1 text-xs text-red-600">
                            <i class="fa-solid fa-calendar-xmark mr-1"></i>
                            Tidak berlaku
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600">
                        <i class="fa-solid fa-calendar-xmark text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Total Penggunaan --}}
            <div
                class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Total Penggunaan
                        </p>

                        <h3 class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $stats['used'] }}
                        </h3>

                        <p class="mt-1 text-xs text-purple-600">
                            <i class="fa-solid fa-chart-simple mr-1"></i>
                            Voucher telah digunakan
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                        <i class="fa-solid fa-chart-column text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Kuota Habis --}}
            <div
                class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Kuota Habis
                        </p>

                        <h3 class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $stats['quota_full'] }}
                        </h3>

                        <p class="mt-1 text-xs text-orange-600">
                            <i class="fa-solid fa-ban mr-1"></i>
                            Tidak dapat digunakan
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                        <i class="fa-solid fa-hourglass-end text-lg"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- VOUCHER --}}
        {{-- ========================================================= --}}
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">

            {{-- Section Header --}}
            <div class="border-b border-gray-100 p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <i class="fa-solid fa-ticket"></i>
                            </div>

                            <h3 class="text-base font-bold text-gray-900">
                                Voucher
                            </h3>
                        </div>

                        <p class="mt-2 text-xs text-gray-500">
                            Kelola kode voucher dan aturan penggunaannya.
                        </p>
                    </div>

                    <button type="button" onclick="MicroModal.show('modal-create-voucher')"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-100">
                        <i class="fa-solid fa-plus"></i>
                        Tambah Voucher
                    </button>
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-100">

                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Kode Voucher
                            </th>

                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Diskon
                            </th>

                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Minimum Belanja
                            </th>

                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Periode
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status
                            </th>

                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Aksi
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($vouchers as $voucher)
                            @php
                                $isValid = $voucher->isValid();
                                $isUpcoming = $voucher->start_date && today()->lt($voucher->start_date);
                            @endphp

                            <tr class="transition hover:bg-gray-50">

                                {{-- Code --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                            <i class="fa-solid fa-ticket"></i>
                                        </div>

                                        <div>
                                            <p class="font-mono font-bold tracking-wide text-gray-900">
                                                {{ $voucher->code }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-400">
                                                Digunakan {{ $voucher->used_count }}
                                                @if ($voucher->usage_limit)
                                                    / {{ $voucher->usage_limit }}
                                                @else
                                                    kali
                                                @endif
                                            </p>
                                        </div>

                                    </div>
                                </td>

                                {{-- Diskon --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-bold text-blue-700">
                                        @if ($voucher->discount_type === 'percentage')
                                            {{ $voucher->discount_value }}%
                                        @else
                                            Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}
                                        @endif
                                    </span>

                                    @if ($voucher->discount_type === 'percentage' && $voucher->max_discount)
                                        <p class="mt-1 text-[10px] text-gray-400">
                                            Maks. Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Minimum --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-700">
                                        Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}
                                    </p>
                                </td>

                                {{-- Periode --}}
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-regular fa-calendar w-4 text-gray-400"></i>
                                            {{ $voucher->start_date->format('d M Y') }}
                                        </div>

                                        <div class="my-1 ml-1.5 h-3 border-l border-gray-200"></div>

                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-flag-checkered w-4 text-gray-400"></i>
                                            {{ $voucher->end_date->format('d M Y') }}
                                        </div>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 text-center">

                                    @if (!$voucher->is_active)
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-500">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            Nonaktif
                                        </span>
                                    @elseif ($isValid)
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            Aktif
                                        </span>
                                    @elseif ($isUpcoming)
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                            Akan Datang
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Berakhir
                                        </span>
                                    @endif

                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">

                                        {{-- Toggle --}}
                                        <form action="{{ route('admin.voucher.toggle', $voucher) }}" method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                title="{{ $voucher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-green-300 hover:bg-green-50 hover:text-green-700">
                                                <i
                                                    class="fa-solid {{ $voucher->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} text-sm"></i>
                                            </button>
                                        </form>

                                        {{-- Edit --}}
                                        <button type="button"
                                            onclick="MicroModal.show('modal-edit-voucher-{{ $voucher->id }}')"
                                            title="Edit"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-yellow-300 hover:bg-yellow-50 hover:text-yellow-700">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>

                                        {{-- Delete --}}
                                        <button type="button"
                                            onclick="MicroModal.show('modal-delete-voucher-{{ $voucher->id }}')"
                                            title="Hapus"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>

                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fa-solid fa-ticket-slash mb-3 text-3xl text-gray-300"></i>
                                    <p class="text-sm">
                                        Belum ada voucher.
                                    </p>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="divide-y divide-gray-100 md:hidden">

                @forelse ($vouchers as $voucher)
                    @php
                        $isValid = $voucher->isValid();
                        $isUpcoming = $voucher->start_date && today()->lt($voucher->start_date);
                    @endphp

                    <div class="p-5">
                        <div class="flex items-start gap-3">

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <i class="fa-solid fa-ticket"></i>
                            </div>

                            <div class="min-w-0 flex-1">

                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-mono font-bold tracking-wide text-gray-900">
                                            {{ $voucher->code }}
                                        </p>

                                        <p class="mt-1 text-xs text-gray-400">
                                            Digunakan {{ $voucher->used_count }}
                                            @if ($voucher->usage_limit)
                                                / {{ $voucher->usage_limit }}
                                            @else
                                                kali
                                            @endif
                                        </p>
                                    </div>

                                    @if (!$voucher->is_active)
                                        <span
                                            class="rounded-full bg-gray-100 px-2.5 py-1 text-[10px] font-semibold text-gray-500">
                                            Nonaktif
                                        </span>
                                    @elseif ($isValid)
                                        <span
                                            class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold text-green-700">
                                            Aktif
                                        </span>
                                    @elseif ($isUpcoming)
                                        <span
                                            class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-700">
                                            Akan Datang
                                        </span>
                                    @else
                                        <span
                                            class="rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-semibold text-red-600">
                                            Berakhir
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2">

                                    <span class="rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">
                                        @if ($voucher->discount_type === 'percentage')
                                            {{ $voucher->discount_value }}%
                                        @else
                                            Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}
                                        @endif
                                    </span>

                                    <span class="rounded-lg bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-600">
                                        Min. Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}
                                    </span>

                                </div>

                                <div class="mt-3 space-y-1.5 text-xs text-gray-500">
                                    <p>
                                        <i class="fa-regular fa-calendar mr-1 text-gray-400"></i>
                                        {{ $voucher->start_date->format('d M Y') }}
                                    </p>

                                    <p>
                                        <i class="fa-solid fa-flag-checkered mr-1 text-gray-400"></i>
                                        {{ $voucher->end_date->format('d M Y') }}
                                    </p>
                                </div>

                                <div class="mt-4 flex justify-end gap-2 border-t border-gray-100 pt-3">

                                    <form action="{{ route('admin.voucher.toggle', $voucher) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-green-300 hover:bg-green-50 hover:text-green-700">
                                            <i
                                                class="fa-solid {{ $voucher->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} text-xs"></i>
                                        </button>
                                    </form>

                                    <button type="button"
                                        onclick="MicroModal.show('modal-edit-voucher-{{ $voucher->id }}')"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-yellow-300 hover:bg-yellow-50 hover:text-yellow-700">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>

                                    <button type="button"
                                        onclick="MicroModal.show('modal-delete-voucher-{{ $voucher->id }}')"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>

                                </div>

                            </div>
                        </div>
                    </div>

                @empty

                    <div class="p-8 text-center text-gray-500">
                        <i class="fa-solid fa-ticket-slash mb-3 text-2xl text-gray-300"></i>
                        <p class="text-sm">
                            Belum ada voucher.
                        </p>
                    </div>
                @endforelse

            </div>

            {{-- Pagination --}}
            @if ($vouchers->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $vouchers->links() }}
                </div>
            @endif
        </div>


        {{-- ========================================================= --}}
        {{-- MODAL CREATE VOUCHER --}}
        {{-- ========================================================= --}}
        <div class="modal micromodal-slide" id="modal-create-voucher" aria-hidden="true">

            <div class="modal__overlay" tabindex="-1" data-micromodal-close>

                <div class="modal__container w-full max-w-md rounded-2xl bg-white shadow-xl" role="dialog"
                    aria-modal="true" aria-labelledby="modal-create-voucher-title">

                    <header class="flex items-start justify-between border-b border-gray-100 p-6">
                        <div>
                            <h2 id="modal-create-voucher-title" class="text-xl font-bold text-gray-900">
                                Tambah Voucher
                            </h2>

                            <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-blue-600">
                                Kode voucher pelanggan
                            </p>
                        </div>

                        <button type="button" aria-label="Close modal" data-micromodal-close
                            class="flex h-9 w-9 items-center justify-center rounded-xl text-gray-400 transition hover:bg-gray-100">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </header>

                    <form action="{{ route('admin.voucher.store') }}" method="POST" class="space-y-5 p-6">

                        @csrf

                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                Kode Voucher <span class="text-red-500">*</span>
                            </label>

                            <input type="text" name="code" required maxlength="50" placeholder="Contoh: RUBUNG10"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-mono text-sm uppercase text-gray-800 outline-none transition-all focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div class="grid grid-cols-2 gap-3">

                            <div>
                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                    Tipe Diskon <span class="text-red-500">*</span>
                                </label>

                                <select name="discount_type" required
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                                    <option value="percentage">Persentase</option>
                                    <option value="fixed">Nominal</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                    Nilai Diskon <span class="text-red-500">*</span>
                                </label>

                                <input type="number" name="discount_value" min="1" required placeholder="10"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            </div>

                        </div>

                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                Minimal Belanja
                            </label>

                            <input type="number" name="min_purchase" min="0" value="0"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div class="grid grid-cols-2 gap-3">

                            <div>
                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                    Maks. Diskon
                                </label>

                                <input type="number" name="max_discount" min="0" placeholder="Opsional"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            </div>

                            <div>
                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                    Batas Penggunaan
                                </label>

                                <input type="number" name="usage_limit" min="1" placeholder="Unlimited"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            </div>

                        </div>

                        <div class="grid grid-cols-2 gap-3">

                            <div>
                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                    Mulai <span class="text-red-500">*</span>
                                </label>

                                <input type="date" name="start_date" required
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            </div>

                            <div>
                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                    Berakhir <span class="text-red-500">*</span>
                                </label>

                                <input type="date" name="end_date" required
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            </div>

                        </div>

                        <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-gray-700">
                                    Aktifkan Voucher
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    Voucher dapat langsung digunakan.
                                </p>
                            </div>

                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" name="is_active" value="1" checked class="peer sr-only">

                                <div
                                    class="after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 h-6 w-11 rounded-full bg-gray-200">
                                </div>
                            </label>
                        </div>

                        <footer class="flex gap-3 pt-4">
                            <button type="button" data-micromodal-close
                                class="flex-1 rounded-xl bg-gray-100 py-3 font-bold text-gray-600 transition hover:bg-gray-200">
                                Batal
                            </button>

                            <button type="submit"
                                class="flex-1 rounded-xl bg-blue-600 py-3 font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-700">
                                <i class="fa-solid fa-floppy-disk mr-2"></i>
                                Simpan Voucher
                            </button>
                        </footer>

                    </form>

                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- VOUCHER EDIT & DELETE --}}
        {{-- ========================================================= --}}

        @foreach ($vouchers as $voucher)
            {{-- EDIT VOUCHER --}}
            <div class="modal micromodal-slide" id="modal-edit-voucher-{{ $voucher->id }}" aria-hidden="true">

                <div class="modal__overlay" tabindex="-1" data-micromodal-close>

                    <div class="modal__container w-full max-w-md rounded-2xl bg-white shadow-xl" role="dialog"
                        aria-modal="true">

                        <header class="flex items-start justify-between border-b border-gray-100 p-6">

                            <div>
                                <h2 class="text-xl font-bold text-gray-900">
                                    Edit Voucher
                                </h2>

                                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-blue-600">
                                    Update informasi voucher
                                </p>
                            </div>

                            <button type="button" data-micromodal-close
                                class="flex h-9 w-9 items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>

                        </header>

                        <form action="{{ route('admin.voucher.update', $voucher) }}" method="POST"
                            class="space-y-5 p-6">

                            @csrf
                            @method('PUT')

                            <div>
                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                    Kode Voucher
                                </label>

                                <input type="text" name="code" value="{{ $voucher->code }}" maxlength="50"
                                    required
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-mono text-sm uppercase outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            </div>

                            <div class="grid grid-cols-2 gap-3">

                                <div>
                                    <label
                                        class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                        Tipe Diskon
                                    </label>

                                    <select name="discount_type"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">

                                        <option value="percentage" @selected($voucher->discount_type === 'percentage')>
                                            Persentase
                                        </option>

                                        <option value="fixed" @selected($voucher->discount_type === 'fixed')>
                                            Nominal
                                        </option>

                                    </select>
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                        Nilai Diskon
                                    </label>

                                    <input type="number" name="discount_value" min="1"
                                        value="{{ $voucher->discount_value }}" required
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                                </div>

                            </div>

                            <div>
                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                    Minimal Belanja
                                </label>

                                <input type="number" name="min_purchase" min="0"
                                    value="{{ $voucher->min_purchase }}"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            </div>

                            <div class="grid grid-cols-2 gap-3">

                                <div>
                                    <label
                                        class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                        Maks. Diskon
                                    </label>

                                    <input type="number" name="max_discount" min="0"
                                        value="{{ $voucher->max_discount }}"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                        Batas Penggunaan
                                    </label>

                                    <input type="number" name="usage_limit" min="1"
                                        value="{{ $voucher->usage_limit }}"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                                </div>

                            </div>

                            <div class="grid grid-cols-2 gap-3">

                                <div>
                                    <label
                                        class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                        Mulai
                                    </label>

                                    <input type="date" name="start_date"
                                        value="{{ $voucher->start_date->format('Y-m-d') }}" required
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                        Berakhir
                                    </label>

                                    <input type="date" name="end_date"
                                        value="{{ $voucher->end_date->format('Y-m-d') }}" required
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-widest text-gray-700">
                                        Status Voucher
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Aktifkan atau nonaktifkan voucher.
                                    </p>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" name="is_active" value="1" @checked($voucher->is_active)
                                        class="peer sr-only">
                                    <div
                                        class="after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white h-6 w-11 rounded-full bg-gray-200">
                                    </div>

                                </label>
                            </div>

                            <footer class="flex gap-3 pt-4">
                                <button type="button" data-micromodal-close
                                    class="flex-1 rounded-xl bg-gray-100 py-3 font-bold text-gray-600 hover:bg-gray-200">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="flex-1 rounded-xl bg-blue-600 py-3 font-bold text-white shadow-lg shadow-blue-100 hover:bg-blue-700">
                                    <i class="fa-solid fa-floppy-disk mr-2"></i>
                                    Perbarui
                                </button>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>

            {{-- DELETE VOUCHER --}}
            <div class="modal micromodal-slide" id="modal-delete-voucher-{{ $voucher->id }}" aria-hidden="true">

                <div class="modal__overlay" tabindex="-1" data-micromodal-close>

                    <div class="modal__container w-full max-w-sm rounded-2xl bg-white text-center shadow-xl"
                        role="dialog" aria-modal="true">

                        <div class="p-8 pb-4">

                            <div
                                class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full border-4 border-red-50 bg-red-100 text-red-600">
                                <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                            </div>

                            <h2 class="mb-2 text-xl font-bold text-gray-900">
                                Hapus Voucher?
                            </h2>

                            <p class="text-sm leading-relaxed text-gray-500">
                                Voucher
                                <b class="font-mono text-gray-900">
                                    {{ $voucher->code }}
                                </b>
                                akan dihapus secara permanen.
                            </p>

                        </div>

                        <form action="{{ route('admin.voucher.destroy', $voucher) }}" method="POST"
                            class="flex gap-3 p-6 pt-0">

                            @csrf
                            @method('DELETE')

                            <button type="button" data-micromodal-close
                                class="flex-1 rounded-xl bg-gray-100 py-3 font-bold text-gray-600 hover:bg-gray-200">
                                Batal
                            </button>

                            <button type="submit"
                                class="flex-1 rounded-xl bg-red-600 py-3 font-bold text-white shadow-lg shadow-red-200 hover:bg-red-700">
                                <i class="fa-regular fa-trash-can mr-2"></i>
                                Ya, Hapus
                            </button>

                        </form>

                    </div>
                </div>
            </div>
        @endforeach

    </main>
@endsection
