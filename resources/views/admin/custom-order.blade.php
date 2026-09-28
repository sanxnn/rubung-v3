@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">

        {{-- Header --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Custom Order</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola pengajuan custom order dan berikan estimasi harga.
                </p>
            </div>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-green-700">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <div class="text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <div class="text-sm font-medium">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3 text-red-700">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>

                    <div>
                        <p class="text-sm font-semibold">
                            Terdapat kesalahan pada form.
                        </p>

                        <ul class="mt-2 list-disc pl-5 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Total --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Total Pengajuan
                        </p>

                        <h3 class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $stats['total'] }}
                        </h3>

                        <p class="mt-1 text-xs text-gray-400">
                            Seluruh Custom Order
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-100 text-[#D4A900]">
                        <i class="fa-solid fa-file-invoice text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Pending --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Menunggu Review
                        </p>

                        <h3 class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $stats['pending'] }}
                        </h3>

                        <p class="mt-1 text-xs text-orange-600">
                            <i class="fa-solid fa-clock mr-1"></i>
                            Perlu ditinjau
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-500">
                        <i class="fa-solid fa-clock text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Quoted --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Menunggu Persetujuan
                        </p>

                        <h3 class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $stats['quoted'] }}
                        </h3>

                        <p class="mt-1 text-xs text-blue-600">
                            <i class="fa-solid fa-hourglass-half mr-1"></i>
                            Menunggu customer
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-hourglass-half text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Closed --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Selesai / Ditolak
                        </p>

                        <h3 class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $stats['completed'] }}
                        </h3>

                        <p class="mt-1 text-xs text-green-600">
                            <i class="fa-solid fa-circle-check mr-1"></i>
                            Kasus tertutup
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-600">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table --}}
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">

            {{-- Toolbar --}}
            <form action="{{ route('admin.custom-orders.index') }}" method="GET" class="border-b border-gray-100 p-5">

                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

                    {{-- Search --}}
                    <div class="relative w-full xl:max-w-md">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nomor PO atau pelanggan..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm text-gray-700 outline-none transition focus:border-[#F4C430] focus:bg-white focus:ring-2 focus:ring-yellow-100">
                    </div>

                    {{-- Filter --}}
                    <div class="flex flex-col gap-3 sm:flex-row">

                        <select name="status" onchange="this.form.submit()"
                            class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600 outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">

                            <option value="">Semua Status</option>

                            <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>
                                Menunggu Review
                            </option>

                            <option value="quoted" {{ request('status') === 'quoted' ? 'selected' : '' }}>
                                Menunggu Persetujuan
                            </option>

                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                                Disetujui
                            </option>

                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                                Ditolak Admin
                            </option>

                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                Dibatalkan Customer
                            </option>
                        </select>

                        @if (request('search') || request('status'))
                            <a href="{{ route('admin.custom-orders.index') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-600 transition hover:bg-red-100">

                                <i class="fa-solid fa-xmark"></i>
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- Desktop --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left">

                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-100">

                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Custom Order
                            </th>

                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Pelanggan
                            </th>

                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Estimasi
                            </th>

                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status
                            </th>

                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Order Terkait
                            </th>

                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Aksi
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($customOrders as $co)
                            @php
                                $statusMap = [
                                    'pending_review' => [
                                        'label' => 'Menunggu Review',
                                        'color' => 'bg-orange-50 text-orange-700',
                                        'dot' => 'bg-orange-500',
                                    ],
                                    'quoted' => [
                                        'label' => 'Menunggu Persetujuan',
                                        'color' => 'bg-blue-50 text-blue-700',
                                        'dot' => 'bg-blue-500',
                                    ],
                                    'approved' => [
                                        'label' => 'Disetujui',
                                        'color' => 'bg-green-50 text-green-700',
                                        'dot' => 'bg-green-500',
                                    ],
                                    'rejected' => [
                                        'label' => 'Ditolak',
                                        'color' => 'bg-red-50 text-red-700',
                                        'dot' => 'bg-red-500',
                                    ],
                                    'cancelled' => [
                                        'label' => 'Dibatalkan',
                                        'color' => 'bg-gray-100 text-gray-600',
                                        'dot' => 'bg-gray-400',
                                    ],
                                ];

                                $status = $statusMap[$co->status] ?? [
                                    'label' => ucfirst(str_replace('_', ' ', $co->status)),
                                    'color' => 'bg-gray-50 text-gray-700',
                                    'dot' => 'bg-gray-500',
                                ];

                                $nameParts = preg_split('/\s+/', trim($co->user->name ?? 'Guest'));

                                $initials = strtoupper(
                                    substr($nameParts[0] ?? 'G', 0, 1) .
                                        (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''),
                                );
                            @endphp

                            <tr class="transition hover:bg-gray-50">

                                {{-- Custom Order --}}
                                <td class="px-6 py-5">
                                    <p class="font-semibold text-gray-900">
                                        {{ $co->reference_number }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ $co->created_at->format('d M Y · H:i') }}
                                    </p>

                                    <p class="mt-2 line-clamp-2 max-w-xs text-xs text-gray-600"
                                        title="{{ $co->description }}">
                                        {{ Str::limit($co->description, 70) }}
                                    </p>
                                </td>

                                {{-- Customer --}}
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#FFF3C4] text-[10px] font-bold text-[#9A7200]">
                                            {{ $initials }}
                                        </div>

                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ $co->user->name ?? 'Guest' }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-400">
                                                {{ $co->user->phone ?? ($co->user->email ?? '-') }}
                                            </p>
                                        </div>

                                    </div>
                                </td>

                                {{-- Estimate --}}
                                <td class="px-6 py-5">

                                    @if ($co->estimated_price)
                                        <p class="font-semibold text-gray-900">
                                            Rp {{ number_format($co->estimated_price, 0, ',', '.') }}
                                        </p>

                                        <p class="mt-1 text-xs text-gray-500">
                                            <i class="fa-regular fa-clock mr-1"></i>
                                            {{ $co->estimated_time }}
                                        </p>
                                    @else
                                        <span class="text-xs italic text-gray-400">
                                            Belum diestimasi
                                        </span>
                                    @endif

                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-5">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold {{ $status['color'] }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $status['dot'] }}"></span>
                                        {{ $status['label'] }}
                                    </span>
                                </td>

                                {{-- Order --}}
                                <td class="px-6 py-5">

                                    @if ($co->order)
                                        <a href="{{ route('admin.orders.index') }}?search={{ urlencode($co->order->order_number) }}"
                                            class="text-xs font-semibold text-blue-600 hover:underline">

                                            {{ $co->order->order_number }}

                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">
                                            -
                                        </span>
                                    @endif

                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-5">

                                    <div class="flex justify-end gap-2">

                                        {{-- Quote --}}
                                        @if (in_array($co->status, ['pending_review', 'quoted']))
                                            <button type="button"
                                                onclick="MicroModal.show('modal-quote-{{ $co->id }}')"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-[#F4C430] hover:bg-yellow-50 hover:text-[#D4A900]"
                                                title="{{ $co->status === 'quoted' ? 'Ubah Estimasi' : 'Beri Estimasi' }}">

                                                <i class="fa-solid fa-tag text-sm"></i>

                                            </button>
                                        @endif

                                        {{-- Detail --}}
                                        <button type="button"
                                            onclick="MicroModal.show('modal-detail-{{ $co->id }}')"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-700"
                                            title="Lihat Detail">

                                            <i class="fa-regular fa-eye text-sm"></i>

                                        </button>

                                        {{-- Reject --}}
                                        @if ($co->status === 'pending_review')
                                            <button type="button"
                                                onclick="MicroModal.show('modal-reject-{{ $co->id }}')"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-red-300 hover:bg-red-50 hover:text-red-600"
                                                title="Tolak">

                                                <i class="fa-solid fa-xmark text-sm"></i>

                                            </button>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">

                                    <div class="flex flex-col items-center justify-center">

                                        <i class="fa-solid fa-inbox mb-4 text-5xl text-gray-300"></i>

                                        <p class="text-gray-400 italic">
                                            Tidak ada pengajuan custom order.
                                        </p>

                                    </div>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            {{-- Mobile --}}
            <div class="divide-y divide-gray-100 md:hidden">

                @forelse ($customOrders as $co)
                    @php
                        $statusMap = [
                            'pending_review' => [
                                'label' => 'Menunggu Review',
                                'color' => 'bg-orange-50 text-orange-700',
                            ],
                            'quoted' => [
                                'label' => 'Menunggu Persetujuan',
                                'color' => 'bg-blue-50 text-blue-700',
                            ],
                            'approved' => [
                                'label' => 'Disetujui',
                                'color' => 'bg-green-50 text-green-700',
                            ],
                            'rejected' => [
                                'label' => 'Ditolak',
                                'color' => 'bg-red-50 text-red-700',
                            ],
                            'cancelled' => [
                                'label' => 'Dibatalkan',
                                'color' => 'bg-gray-100 text-gray-600',
                            ],
                        ];

                        $status = $statusMap[$co->status] ?? [
                            'label' => $co->status,
                            'color' => 'bg-gray-50 text-gray-700',
                        ];
                    @endphp

                    <div class="p-5">

                        <div class="flex items-start justify-between gap-4">

                            <div class="flex min-w-0 gap-3">

                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>

                                <div class="min-w-0">

                                    <p class="font-semibold text-gray-900">
                                        {{ $co->reference_number }}
                                    </p>

                                    <p class="mt-1 truncate text-sm text-gray-500">
                                        {{ $co->user->name ?? 'Guest' }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ $co->created_at->format('d M Y') }}
                                    </p>

                                </div>

                            </div>

                            <span
                                class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $status['color'] }}">
                                {{ $status['label'] }}
                            </span>

                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4 rounded-xl bg-gray-50 p-3">

                            <div>
                                <p class="text-xs text-gray-400">
                                    Estimasi
                                </p>

                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $co->estimated_price ? 'Rp ' . number_format($co->estimated_price, 0, ',', '.') : '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">
                                    Order
                                </p>

                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $co->order ? $co->order->order_number : '-' }}
                                </p>
                            </div>

                        </div>

                        <div class="mt-4 flex justify-end gap-2">

                            <button type="button" onclick="MicroModal.show('modal-detail-{{ $co->id }}')"
                                class="flex h-9 items-center gap-2 rounded-lg border border-gray-200 px-3 text-xs font-medium text-gray-600">

                                <i class="fa-regular fa-eye"></i>
                                Detail

                            </button>

                            @if (in_array($co->status, ['pending_review', 'quoted']))
                                <button type="button" onclick="MicroModal.show('modal-quote-{{ $co->id }}')"
                                    class="flex h-9 items-center gap-2 rounded-lg border border-[#F4C430] bg-yellow-50 px-3 text-xs font-medium text-[#D4A900]">

                                    <i class="fa-solid fa-tag"></i>
                                    {{ $co->status === 'quoted' ? 'Ubah Estimasi' : 'Estimasi' }}

                                </button>
                            @endif

                        </div>

                    </div>

                @empty

                    <div class="p-8 text-center">

                        <i class="fa-solid fa-inbox mb-4 text-5xl text-gray-300"></i>

                        <p class="text-xs italic text-gray-400">
                            Tidak ada pengajuan custom order.
                        </p>

                    </div>
                @endforelse

            </div>

            {{-- Pagination --}}
            @if ($customOrders->hasPages())
                <div
                    class="flex flex-col gap-4 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                    <p class="text-sm text-gray-500">

                        Menampilkan

                        <span class="font-medium text-gray-700">
                            {{ $customOrders->firstItem() }}–{{ $customOrders->lastItem() }}
                        </span>

                        dari

                        <span class="font-medium text-gray-700">
                            {{ $customOrders->total() }}
                        </span>

                        Custom Order

                    </p>

                    <div class="flex items-center gap-1">
                        {{ $customOrders->onEachSide(1)->links() }}
                    </div>

                </div>
            @endif

        </div>
    </main>

    {{-- ================================================= --}}
    {{-- MODALS --}}
    {{-- ================================================= --}}

    @foreach ($customOrders as $co)
        {{-- ================================================= --}}
        {{-- DETAIL --}}
        {{-- ================================================= --}}

        <div class="modal" id="modal-detail-{{ $co->id }}" aria-hidden="true">

            <div class="modal__overlay" tabindex="-1" data-micromodal-close>

                <div class="modal__container w-full max-w-lg rounded-2xl bg-white p-8 shadow-xl" role="dialog"
                    aria-modal="true">

                    <header class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">

                        <div>
                            <h2 class="text-xl font-bold text-gray-900">
                                Detail Pengajuan
                            </h2>

                            <p class="mt-1 text-[11px] font-bold uppercase tracking-wider text-[#D4A900]">
                                {{ $co->reference_number }}
                            </p>
                        </div>

                        <button type="button"
                            class="flex h-9 w-9 items-center justify-center rounded-xl text-gray-400 transition-colors hover:bg-gray-100"
                            data-micromodal-close>

                            <i class="fa-solid fa-times"></i>

                        </button>

                    </header>

                    <div class="space-y-5 text-sm">

                        {{-- Customer --}}
                        <div>

                            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-500">
                                Customer
                            </p>

                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">

                                <p class="font-semibold text-gray-900">
                                    {{ $co->user->name ?? 'Guest' }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $co->user->email ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $co->user->phone ?? '-' }}
                                </p>

                            </div>

                        </div>

                        {{-- Description --}}
                        <div>

                            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-500">
                                Deskripsi Kebutuhan
                            </p>

                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 leading-relaxed text-gray-800">
                                {!! nl2br(e($co->description)) !!}
                            </div>

                        </div>

                        {{-- Attachments --}}
                        <div>

                            <div class="mb-2 flex items-center justify-between">

                                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">
                                    Lampiran
                                </p>

                                <span class="text-xs text-gray-400">
                                    {{ $co->attachments->count() }} file
                                </span>

                            </div>

                            @if ($co->attachments->count())
                                <div class="space-y-2">

                                    @foreach ($co->attachments as $attachment)
                                        <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 p-3 transition hover:border-[#F4C430] hover:bg-yellow-50">

                                            <div
                                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-[#D4A900]">
                                                <i class="fa-solid fa-paperclip"></i>
                                            </div>

                                            <div class="min-w-0 flex-1">

                                                <p class="truncate text-sm font-medium text-gray-800">
                                                    {{ basename($attachment->file_path) }}
                                                </p>

                                                <p class="mt-1 text-[11px] text-gray-400">
                                                    {{ $attachment->created_at?->format('d M Y · H:i') }}
                                                </p>

                                            </div>

                                            <i class="fa-solid fa-arrow-up-right-from-square text-xs text-gray-400"></i>

                                        </a>
                                    @endforeach

                                </div>
                            @else
                                <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-4 text-center">

                                    <i class="fa-regular fa-image mb-2 text-xl text-gray-300"></i>

                                    <p class="text-xs text-gray-400">
                                        Tidak ada lampiran.
                                    </p>

                                </div>
                            @endif

                        </div>

                        {{-- Estimate --}}
                        <div class="grid grid-cols-2 gap-4">

                            <div>
                                <p class="mb-1 text-xs font-bold uppercase tracking-wider text-gray-500">
                                    Estimasi Harga
                                </p>

                                @if ($co->estimated_price)
                                    <p class="font-semibold text-gray-900">
                                        Rp {{ number_format($co->estimated_price, 0, ',', '.') }}
                                    </p>
                                @else
                                    <p class="italic text-gray-400">
                                        Belum diestimasi
                                    </p>
                                @endif

                            </div>

                            <div>
                                <p class="mb-1 text-xs font-bold uppercase tracking-wider text-gray-500">
                                    Estimasi Waktu
                                </p>

                                <p class="font-semibold text-gray-900">
                                    {{ $co->estimated_time ?: '-' }}
                                </p>
                            </div>

                        </div>

                        {{-- Admin Notes --}}
                        @if ($co->admin_notes)
                            <div>

                                <p class="mb-1 text-xs font-bold uppercase tracking-wider text-gray-500">
                                    Catatan Admin
                                </p>

                                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-gray-800">
                                    {!! nl2br(e($co->admin_notes)) !!}
                                </div>

                            </div>
                        @endif

                        {{-- Related Order --}}
                        @if ($co->order)
                            <div class="border-t border-gray-100 pt-4">

                                <p class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-500">
                                    Order Terkait
                                </p>

                                <a href="{{ route('admin.orders.index') }}?search={{ urlencode($co->order->order_number) }}"
                                    class="inline-flex items-center gap-2 rounded-xl bg-green-50 px-4 py-2 font-semibold text-green-700 transition hover:bg-green-100">

                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>

                                    {{ $co->order->order_number }}

                                </a>

                            </div>
                        @endif

                    </div>

                    <div class="mt-8 flex justify-end">

                        <button type="button" data-micromodal-close
                            class="rounded-xl bg-gray-100 px-6 py-3 font-bold text-gray-600 transition-all hover:bg-gray-200">

                            Tutup

                        </button>

                    </div>

                </div>

            </div>

        </div>

        {{-- ================================================= --}}
        {{-- QUOTE --}}
        {{-- ================================================= --}}

        @if (in_array($co->status, ['pending_review', 'quoted']))
            <div class="modal" id="modal-quote-{{ $co->id }}" aria-hidden="true">

                <div class="modal__overlay" tabindex="-1" data-micromodal-close>

                    <div class="modal__container w-full max-w-md rounded-2xl bg-white p-8 shadow-xl" role="dialog"
                        aria-modal="true">

                        <header class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">

                            <div>

                                <h2 class="text-xl font-bold text-gray-900">
                                    {{ $co->status === 'quoted' ? 'Ubah Estimasi' : 'Beri Estimasi' }}
                                </h2>

                                <p class="mt-1 text-[11px] font-bold uppercase tracking-wider text-[#D4A900]">
                                    {{ $co->reference_number }}
                                </p>

                            </div>

                            <button type="button"
                                class="flex h-9 w-9 items-center justify-center rounded-xl text-gray-400 transition-colors hover:bg-gray-100"
                                data-micromodal-close>

                                <i class="fa-solid fa-times"></i>

                            </button>

                        </header>

                        <form action="{{ route('admin.custom-orders.quote', $co->id) }}" method="POST"
                            class="space-y-5 text-left">

                            @csrf
                            @method('PATCH')

                            {{-- Price --}}
                            <div>

                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                    Estimasi Harga (Rp)
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="number" name="estimated_price"
                                    value="{{ old('estimated_price', $co->estimated_price) }}" required min="1"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 outline-none transition-all focus:border-[#F4C430] focus:bg-white focus:ring-2 focus:ring-[#F4C430]/30">

                            </div>

                            {{-- Time --}}
                            <div>

                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                    Estimasi Waktu
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" name="estimated_time"
                                    value="{{ old('estimated_time', $co->estimated_time) }}" required maxlength="100"
                                    placeholder="Contoh: 7 hari kerja"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 outline-none transition-all focus:border-[#F4C430] focus:bg-white focus:ring-2 focus:ring-[#F4C430]/30">

                            </div>

                            {{-- Notes --}}
                            <div>

                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                    Catatan untuk Customer
                                </label>

                                <textarea name="admin_notes" rows="4" maxlength="5000"
                                    placeholder="Jelaskan detail harga, bahan, syarat, atau informasi lainnya..."
                                    class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 outline-none transition-all focus:border-[#F4C430] focus:bg-white focus:ring-2 focus:ring-[#F4C430]/30">{{ old('admin_notes', $co->admin_notes) }}</textarea>

                            </div>

                            <div class="mt-8 flex gap-3">

                                <button type="button" data-micromodal-close
                                    class="flex-1 rounded-xl bg-gray-100 py-3 font-bold text-gray-600 transition-all hover:bg-gray-200">

                                    Batal

                                </button>

                                <button type="submit"
                                    class="flex-1 rounded-xl bg-[#D4A900] py-3 font-bold text-white shadow-lg shadow-[#F4C430]/30 transition-all hover:bg-[#B88900]">

                                    {{ $co->status === 'quoted' ? 'Perbarui Estimasi' : 'Kirim Estimasi' }}

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        @endif

        {{-- ================================================= --}}
        {{-- REJECT --}}
        {{-- ================================================= --}}

        @if ($co->status === 'pending_review')
            <div class="modal" id="modal-reject-{{ $co->id }}" aria-hidden="true">

                <div class="modal__overlay" tabindex="-1" data-micromodal-close>

                    <div class="modal__container w-full max-w-sm rounded-2xl bg-white p-8 text-center shadow-xl"
                        role="dialog" aria-modal="true">

                        <div
                            class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full border-4 border-red-50 bg-red-100 text-red-600">

                            <i class="fa-solid fa-xmark text-3xl"></i>

                        </div>

                        <h2 class="mb-2 text-xl font-bold text-gray-900">
                            Tolak Pengajuan?
                        </h2>

                        <p class="mb-6 text-sm leading-relaxed text-gray-500">
                            Pengajuan
                            <b>{{ $co->reference_number }}</b>
                            akan ditolak.
                        </p>

                        <form action="{{ route('admin.custom-orders.reject', $co->id) }}" method="POST"
                            class="space-y-4 text-left">

                            @csrf
                            @method('PATCH')

                            <div>

                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                    Alasan Penolakan
                                </label>

                                <textarea name="admin_notes" rows="3" maxlength="5000" placeholder="Contoh: Bahan tidak tersedia..."
                                    class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 outline-none transition-all focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/30">{{ old('admin_notes') }}</textarea>

                            </div>

                            <div class="mt-6 flex gap-3">

                                <button type="button" data-micromodal-close
                                    class="flex-1 rounded-xl bg-gray-100 py-3 font-bold text-gray-600 transition-all hover:bg-gray-200">

                                    Batal

                                </button>

                                <button type="submit"
                                    class="flex-1 rounded-xl bg-red-600 py-3 font-bold text-white shadow-lg shadow-red-200 transition-all hover:bg-red-700">

                                    Ya, Tolak

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        @endif
    @endforeach
@endsection
