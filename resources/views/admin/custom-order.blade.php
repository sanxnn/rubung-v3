@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                Custom Order
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola pengajuan custom order dan berikan estimasi kepada customer.
            </p>
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

        {{-- Main Container --}}
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">

            {{-- Toolbar --}}
            <form
                action="{{ route('admin.custom-orders.index') }}"
                method="GET"
                class="border-b border-gray-100 p-5"
            >
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

                    {{-- Search --}}
                    <div class="relative w-full xl:max-w-md">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nomor PO atau pelanggan..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm text-gray-700 outline-none transition focus:border-[#F4C430] focus:bg-white focus:ring-2 focus:ring-yellow-100"
                        >
                    </div>

                    {{-- Filter --}}
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <select
                            name="status"
                            onchange="this.form.submit()"
                            class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600 outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100"
                        >
                            <option value="">Semua Status</option>

                            <option
                                value="pending_review"
                                {{ request('status') === 'pending_review' ? 'selected' : '' }}
                            >
                                Menunggu Review
                            </option>

                            <option
                                value="quoted"
                                {{ request('status') === 'quoted' ? 'selected' : '' }}
                            >
                                Menunggu Persetujuan
                            </option>

                            <option
                                value="approved"
                                {{ request('status') === 'approved' ? 'selected' : '' }}
                            >
                                Disetujui
                            </option>

                            <option
                                value="rejected"
                                {{ request('status') === 'rejected' ? 'selected' : '' }}
                            >
                                Ditolak Admin
                            </option>

                            <option
                                value="cancelled"
                                {{ request('status') === 'cancelled' ? 'selected' : '' }}
                            >
                                Dibatalkan Customer
                            </option>
                        </select>

                        @if (request('search') || request('status'))
                            <a
                                href="{{ route('admin.custom-orders.index') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-600 transition hover:bg-red-100"
                            >
                                <i class="fa-solid fa-xmark"></i>
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- Desktop Table --}}
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

                                $customerName = trim($co->user->name ?? 'Guest');

                                $nameParts = preg_split('/\s+/', $customerName);

                                $initials = strtoupper(
                                    substr($nameParts[0] ?? 'G', 0, 1) .
                                    (count($nameParts) > 1
                                        ? substr($nameParts[count($nameParts) - 1], 0, 1)
                                        : '')
                                );
                            @endphp

                            <tr class="transition hover:bg-gray-50">

                                {{-- Custom Order --}}
                                <td class="px-6 py-5">
                                    <p class="font-semibold text-gray-900">
                                        {{ $co->reference_number }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ $co->created_at?->format('d M Y · H:i') }}
                                    </p>

                                    <p
                                        class="mt-2 line-clamp-2 max-w-xs text-xs leading-relaxed text-gray-600"
                                        title="{{ $co->description }}"
                                    >
                                        {{ Str::limit($co->description, 70) }}
                                    </p>
                                </td>

                                {{-- Customer --}}
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">

                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#FFF3C4] text-[10px] font-bold text-[#9A7200]">
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
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold {{ $status['color'] }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $status['dot'] }}"></span>
                                        {{ $status['label'] }}
                                    </span>
                                </td>

                                {{-- Related Order --}}
                                <td class="px-6 py-5">
                                    @if ($co->order)
                                        <a
                                            href="{{ route('admin.orders.index') }}?search={{ urlencode($co->order->order_number) }}"
                                            class="text-xs font-semibold text-blue-600 hover:underline"
                                        >
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

                                        @if (in_array($co->status, ['pending_review', 'quoted'], true))
                                            <button
                                                type="button"
                                                onclick="MicroModal.show('modal-quote-{{ $co->id }}')"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-[#F4C430] hover:bg-yellow-50 hover:text-[#D4A900]"
                                                title="{{ $co->status === 'quoted' ? 'Ubah Estimasi' : 'Beri Estimasi' }}"
                                            >
                                                <i class="fa-solid fa-tag text-sm"></i>
                                            </button>
                                        @endif

                                        <button
                                            type="button"
                                            onclick="MicroModal.show('modal-detail-{{ $co->id }}')"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50 hover:text-gray-700"
                                            title="Lihat Detail"
                                        >
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </button>

                                        @if ($co->status === 'pending_review')
                                            <button
                                                type="button"
                                                onclick="MicroModal.show('modal-reject-{{ $co->id }}')"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-red-300 hover:bg-red-50 hover:text-red-600"
                                                title="Tolak"
                                            >
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

                                        <p class="text-gray-400">
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
                            'label' => ucfirst(str_replace('_', ' ', $co->status)),
                            'color' => 'bg-gray-50 text-gray-700',
                        ];
                    @endphp

                    <div class="p-5">

                        <div class="flex items-start justify-between gap-4">

                            <div class="flex min-w-0 gap-3">

                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-yellow-50 text-[#D4A900]">
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
                                        {{ $co->created_at?->format('d M Y') }}
                                    </p>
                                </div>

                            </div>

                            <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $status['color'] }}">
                                {{ $status['label'] }}
                            </span>

                        </div>

                        <div class="mt-4 rounded-xl bg-gray-50 p-3">

                            <div class="grid grid-cols-2 gap-4">

                                <div>
                                    <p class="text-xs text-gray-400">
                                        Estimasi
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ $co->estimated_price
                                            ? 'Rp ' . number_format($co->estimated_price, 0, ',', '.')
                                            : '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400">
                                        Order
                                    </p>

                                    <p class="mt-1 truncate text-sm font-semibold text-gray-900">
                                        {{ $co->order?->order_number ?? '-' }}
                                    </p>
                                </div>

                            </div>

                            @if ($co->estimated_time)
                                <div class="mt-3 border-t border-gray-200 pt-3">
                                    <p class="text-xs text-gray-400">
                                        Estimasi Waktu
                                    </p>

                                    <p class="mt-1 text-sm font-medium text-gray-700">
                                        {{ $co->estimated_time }}
                                    </p>
                                </div>
                            @endif

                        </div>

                        <div class="mt-4 flex justify-end gap-2">

                            <button
                                type="button"
                                onclick="MicroModal.show('modal-detail-{{ $co->id }}')"
                                class="flex h-9 items-center gap-2 rounded-lg border border-gray-200 px-3 text-xs font-medium text-gray-600 transition hover:bg-gray-50"
                            >
                                <i class="fa-regular fa-eye"></i>
                                Detail
                            </button>

                            @if (in_array($co->status, ['pending_review', 'quoted'], true))
                                <button
                                    type="button"
                                    onclick="MicroModal.show('modal-quote-{{ $co->id }}')"
                                    class="flex h-9 items-center gap-2 rounded-lg border border-[#F4C430] bg-yellow-50 px-3 text-xs font-medium text-[#D4A900] transition hover:bg-yellow-100"
                                >
                                    <i class="fa-solid fa-tag"></i>

                                    {{ $co->status === 'quoted'
                                        ? 'Ubah Estimasi'
                                        : 'Estimasi' }}
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
                <div class="flex flex-col gap-4 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

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

                    <div>
                        {{ $customOrders->onEachSide(1)->links() }}
                    </div>

                </div>
            @endif

        </div>
    </main>


    {{-- ========================================================= --}}
    {{-- MODALS --}}
    {{-- ========================================================= --}}

    @foreach ($customOrders as $co)

        {{-- ========================================================= --}}
        {{-- DETAIL MODAL --}}
        {{-- ========================================================= --}}

        <div
            class="modal"
            id="modal-detail-{{ $co->id }}"
            aria-hidden="true"
        >
            <div
                class="modal__overlay"
                tabindex="-1"
                data-micromodal-close
            >

                <div
                    class="modal__container w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl"
                    role="dialog"
                    aria-modal="true"
                >

                    {{-- Modal Header --}}
                    <div class="border-b border-gray-100 bg-white px-6 py-5 sm:px-7">

                        <div class="flex items-start justify-between gap-4">

                            <div class="min-w-0">

                                <div class="mb-3 flex items-center gap-2">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-yellow-50 text-[#D4A900]">
                                        <i class="fa-solid fa-file-invoice text-sm"></i>
                                    </div>

                                    <span class="text-[11px] font-bold uppercase tracking-widest text-gray-400">
                                        Custom Order
                                    </span>
                                </div>

                                <h2 class="truncate text-xl font-bold text-gray-900">
                                    {{ $co->reference_number }}
                                </h2>

                                <p class="mt-1 text-xs text-gray-400">
                                    Diajukan {{ $co->created_at?->format('d M Y, H:i') }}
                                </p>

                            </div>

                            <button
                                type="button"
                                data-micromodal-close
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </button>

                        </div>

                    </div>


                    {{-- Modal Body --}}
                    <div class="max-h-[70vh] overflow-y-auto px-6 py-6 sm:px-7">

                        <div class="space-y-6">

                            {{-- Customer --}}
                            <section>

                                <div class="mb-3 flex items-center gap-2">
                                    <i class="fa-regular fa-user text-xs text-gray-400"></i>

                                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Customer
                                    </h3>
                                </div>

                                <div class="flex items-center gap-4 rounded-xl border border-gray-100 bg-gray-50 p-4">

                                    @php
                                        $customerName = trim($co->user->name ?? 'Guest');
                                        $parts = preg_split('/\s+/', $customerName);

                                        $customerInitials = strtoupper(
                                            substr($parts[0] ?? 'G', 0, 1) .
                                            (count($parts) > 1
                                                ? substr($parts[count($parts) - 1], 0, 1)
                                                : '')
                                        );
                                    @endphp

                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#FFF3C4] font-bold text-[#9A7200]">
                                        {{ $customerInitials }}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900">
                                            {{ $co->user->name ?? 'Guest' }}
                                        </p>

                                        <p class="mt-1 truncate text-xs text-gray-500">
                                            {{ $co->user->email ?? '-' }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-gray-500">
                                            {{ $co->user->phone ?? '-' }}
                                        </p>
                                    </div>

                                </div>

                            </section>


                            {{-- Description --}}
                            <section>

                                <div class="mb-3 flex items-center gap-2">
                                    <i class="fa-regular fa-message text-xs text-gray-400"></i>

                                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Deskripsi Kebutuhan
                                    </h3>
                                </div>

                                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                                    <p class="whitespace-pre-line text-sm leading-7 text-gray-700">
                                        {{ $co->description }}
                                    </p>
                                </div>

                            </section>


                            {{-- Attachments --}}
                            <section>

                                <div class="mb-3 flex items-center justify-between">

                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-paperclip text-xs text-gray-400"></i>

                                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">
                                            Lampiran
                                        </h3>
                                    </div>

                                    <span class="text-xs text-gray-400">
                                        {{ $co->attachments->count() }} file
                                    </span>

                                </div>

                                @if ($co->attachments->count())

                                    <div class="grid gap-2">

                                        @foreach ($co->attachments as $attachment)

                                            <a
                                                href="{{ asset('storage/' . $attachment->file_path) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="group flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 p-3 transition hover:border-[#F4C430] hover:bg-yellow-50"
                                            >

                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-[#D4A900] shadow-sm">
                                                    <i class="fa-solid fa-paperclip"></i>
                                                </div>

                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-medium text-gray-800">
                                                        {{ basename($attachment->file_path) }}
                                                    </p>

                                                    <p class="mt-1 text-[11px] text-gray-400">
                                                        {{ $attachment->created_at?->format('d M Y, H:i') }}
                                                    </p>
                                                </div>

                                                <i class="fa-solid fa-arrow-up-right-from-square text-xs text-gray-400 transition group-hover:text-[#D4A900]"></i>

                                            </a>

                                        @endforeach

                                    </div>

                                @else

                                    <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 px-4 py-6 text-center">
                                        <i class="fa-regular fa-image mb-2 text-xl text-gray-300"></i>

                                        <p class="text-xs text-gray-400">
                                            Tidak ada lampiran.
                                        </p>
                                    </div>

                                @endif

                            </section>


                            {{-- Estimate --}}
                            <section>

                                <div class="mb-3 flex items-center gap-2">
                                    <i class="fa-solid fa-tag text-xs text-gray-400"></i>

                                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Estimasi
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                        <p class="text-xs text-gray-400">
                                            Harga
                                        </p>

                                        <p class="mt-2 text-lg font-bold text-gray-900">
                                            @if ($co->estimated_price)
                                                Rp {{ number_format($co->estimated_price, 0, ',', '.') }}
                                            @else
                                                <span class="text-sm font-normal italic text-gray-400">
                                                    Belum diestimasi
                                                </span>
                                            @endif
                                        </p>
                                    </div>

                                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                        <p class="text-xs text-gray-400">
                                            Waktu Pengerjaan
                                        </p>

                                        <p class="mt-2 text-sm font-semibold text-gray-900">
                                            {{ $co->estimated_time ?: '-' }}
                                        </p>
                                    </div>

                                </div>

                            </section>


                            {{-- Status --}}
                            <section>

                                <div class="mb-3 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-info text-xs text-gray-400"></i>

                                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Status
                                    </h3>
                                </div>

                                @php
                                    $detailStatusMap = [
                                        'pending_review' => [
                                            'label' => 'Menunggu Review',
                                            'color' => 'bg-orange-50 text-orange-700 border-orange-100',
                                        ],
                                        'quoted' => [
                                            'label' => 'Menunggu Persetujuan Customer',
                                            'color' => 'bg-blue-50 text-blue-700 border-blue-100',
                                        ],
                                        'approved' => [
                                            'label' => 'Disetujui Customer',
                                            'color' => 'bg-green-50 text-green-700 border-green-100',
                                        ],
                                        'rejected' => [
                                            'label' => 'Ditolak Admin',
                                            'color' => 'bg-red-50 text-red-700 border-red-100',
                                        ],
                                        'cancelled' => [
                                            'label' => 'Dibatalkan Customer',
                                            'color' => 'bg-gray-100 text-gray-600 border-gray-200',
                                        ],
                                    ];

                                    $detailStatus = $detailStatusMap[$co->status] ?? [
                                        'label' => ucfirst(str_replace('_', ' ', $co->status)),
                                        'color' => 'bg-gray-50 text-gray-700 border-gray-100',
                                    ];
                                @endphp

                                <div class="rounded-xl border p-4 {{ $detailStatus['color'] }}">
                                    <div class="flex items-center gap-3">
                                        <span class="h-2 w-2 rounded-full bg-current"></span>

                                        <span class="text-sm font-semibold">
                                            {{ $detailStatus['label'] }}
                                        </span>
                                    </div>
                                </div>

                            </section>


                            {{-- Admin Notes --}}
                            @if ($co->admin_notes)

                                <section>

                                    <div class="mb-3 flex items-center gap-2">
                                        <i class="fa-regular fa-note-sticky text-xs text-gray-400"></i>

                                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">
                                            Catatan Admin
                                        </h3>
                                    </div>

                                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                                        <p class="whitespace-pre-line text-sm leading-6 text-blue-900">
                                            {{ $co->admin_notes }}
                                        </p>
                                    </div>

                                </section>

                            @endif


                            {{-- Related Order --}}
                            @if ($co->order)

                                <section class="border-t border-gray-100 pt-6">

                                    <div class="mb-3 flex items-center gap-2">
                                        <i class="fa-solid fa-cart-shopping text-xs text-gray-400"></i>

                                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">
                                            Order Terkait
                                        </h3>
                                    </div>

                                    <a
                                        href="{{ route('admin.orders.index') }}?search={{ urlencode($co->order->order_number) }}"
                                        class="group flex items-center justify-between rounded-xl border border-green-100 bg-green-50 p-4 transition hover:border-green-200 hover:bg-green-100"
                                    >

                                        <div>
                                            <p class="text-xs text-green-600">
                                                Nomor Order
                                            </p>

                                            <p class="mt-1 font-bold text-green-800">
                                                {{ $co->order->order_number }}
                                            </p>
                                        </div>

                                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-green-600 shadow-sm">
                                            <i class="fa-solid fa-arrow-right text-xs"></i>
                                        </div>

                                    </a>

                                </section>

                            @endif

                        </div>

                    </div>


                    {{-- Modal Footer --}}
                    <div class="border-t border-gray-100 bg-gray-50 px-6 py-4 sm:px-7">

                        <div class="flex justify-end">
                            <button
                                type="button"
                                data-micromodal-close
                                class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-100"
                            >
                                Tutup
                            </button>
                        </div>

                    </div>

                </div>

            </div>
        </div>


        {{-- ========================================================= --}}
        {{-- QUOTE MODAL --}}
        {{-- ========================================================= --}}

        @if (in_array($co->status, ['pending_review', 'quoted'], true))

            <div
                class="modal"
                id="modal-quote-{{ $co->id }}"
                aria-hidden="true"
            >
                <div
                    class="modal__overlay"
                    tabindex="-1"
                    data-micromodal-close
                >

                    <div
                        class="modal__container w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl"
                        role="dialog"
                        aria-modal="true"
                    >

                        {{-- Header --}}
                        <div class="border-b border-gray-100 px-6 py-5 sm:px-7">

                            <div class="flex items-start justify-between gap-4">

                                <div>

                                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-yellow-50 text-[#D4A900]">
                                        <i class="fa-solid fa-tag"></i>
                                    </div>

                                    <h2 class="text-xl font-bold text-gray-900">
                                        {{ $co->status === 'quoted'
                                            ? 'Ubah Estimasi'
                                            : 'Beri Estimasi' }}
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Tentukan harga dan waktu pengerjaan untuk customer.
                                    </p>

                                </div>

                                <button
                                    type="button"
                                    data-micromodal-close
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                                >
                                    <i class="fa-solid fa-xmark"></i>
                                </button>

                            </div>

                            <div class="mt-5 flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2.5">
                                <i class="fa-solid fa-file-invoice text-xs text-gray-400"></i>

                                <span class="text-xs font-semibold text-gray-600">
                                    {{ $co->reference_number }}
                                </span>
                            </div>

                        </div>


                        {{-- Form --}}
                        <form
                            action="{{ route('admin.custom-orders.quote', $co->id) }}"
                            method="POST"
                        >
                            @csrf
                            @method('PATCH')

                            <div class="space-y-5 px-6 py-6 sm:px-7">

                                {{-- Price --}}
                                <div>

                                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Estimasi Harga
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <div class="relative">

                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">
                                            Rp
                                        </span>

                                        <input
                                            type="number"
                                            name="estimated_price"
                                            value="{{ old('estimated_price', $co->estimated_price) }}"
                                            required
                                            min="1"
                                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3.5 pl-11 pr-4 text-sm font-semibold text-gray-800 outline-none transition focus:border-[#F4C430] focus:bg-white focus:ring-4 focus:ring-yellow-50"
                                            placeholder="0"
                                        >

                                    </div>

                                    <p class="mt-1.5 text-xs text-gray-400">
                                        Harga final yang akan dibayarkan customer.
                                    </p>

                                </div>


                                {{-- Time --}}
                                <div>

                                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Estimasi Waktu
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <div class="relative">

                                        <i class="fa-regular fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>

                                        <input
                                            type="text"
                                            name="estimated_time"
                                            value="{{ old('estimated_time', $co->estimated_time) }}"
                                            required
                                            maxlength="100"
                                            placeholder="Contoh: 7 hari kerja"
                                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3.5 pl-11 pr-4 text-sm text-gray-800 outline-none transition focus:border-[#F4C430] focus:bg-white focus:ring-4 focus:ring-yellow-50"
                                        >

                                    </div>

                                </div>


                                {{-- Notes --}}
                                <div>

                                    <div class="mb-2 flex items-center justify-between">

                                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500">
                                            Catatan untuk Customer
                                        </label>

                                        <span class="text-[10px] text-gray-400">
                                            Opsional
                                        </span>

                                    </div>

                                    <textarea
                                        name="admin_notes"
                                        rows="5"
                                        maxlength="5000"
                                        placeholder="Jelaskan detail harga, bahan, ukuran, ketentuan, atau informasi lainnya..."
                                        class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3.5 text-sm leading-6 text-gray-800 outline-none transition focus:border-[#F4C430] focus:bg-white focus:ring-4 focus:ring-yellow-50"
                                    >{{ old('admin_notes', $co->admin_notes) }}</textarea>

                                </div>


                                {{-- Info --}}
                                <div class="flex gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4">

                                    <i class="fa-solid fa-circle-info mt-0.5 text-sm text-blue-500"></i>

                                    <p class="text-xs leading-5 text-blue-700">
                                        Setelah estimasi dikirim, status akan menjadi
                                        <strong>Menunggu Persetujuan</strong>.
                                        Customer dapat menyetujui atau membatalkan pengajuan tersebut.
                                    </p>

                                </div>

                            </div>


                            {{-- Footer --}}
                            <div class="flex gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:px-7">

                                <button
                                    type="button"
                                    data-micromodal-close
                                    class="flex-1 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-100"
                                >
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    class="flex-1 rounded-xl bg-[#D4A900] px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#B88900] focus:outline-none focus:ring-4 focus:ring-yellow-100"
                                >
                                    <i class="fa-solid fa-paper-plane mr-2 text-xs"></i>

                                    {{ $co->status === 'quoted'
                                        ? 'Perbarui Estimasi'
                                        : 'Kirim Estimasi' }}
                                </button>

                            </div>

                        </form>

                    </div>

                </div>
            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- REJECT MODAL --}}
        {{-- ========================================================= --}}

        @if ($co->status === 'pending_review')

            <div
                class="modal"
                id="modal-reject-{{ $co->id }}"
                aria-hidden="true"
            >
                <div
                    class="modal__overlay"
                    tabindex="-1"
                    data-micromodal-close
                >

                    <div
                        class="modal__container w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl"
                        role="dialog"
                        aria-modal="true"
                    >

                        {{-- Header --}}
                        <div class="px-6 pt-7 text-center sm:px-7">

                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </div>

                            <h2 class="mt-5 text-xl font-bold text-gray-900">
                                Tolak Pengajuan?
                            </h2>

                            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-gray-500">
                                Pengajuan
                                <span class="font-semibold text-gray-700">
                                    {{ $co->reference_number }}
                                </span>
                                akan ditolak dan tidak dapat diproses lebih lanjut.
                            </p>

                        </div>


                        {{-- Form --}}
                        <form
                            action="{{ route('admin.custom-orders.reject', $co->id) }}"
                            method="POST"
                        >
                            @csrf
                            @method('PATCH')

                            <div class="px-6 py-6 sm:px-7">

                                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-gray-500">
                                    Alasan Penolakan
                                </label>

                                <textarea
                                    name="admin_notes"
                                    rows="4"
                                    maxlength="5000"
                                    placeholder="Contoh: Bahan atau motif yang diminta tidak tersedia..."
                                    class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3.5 text-sm leading-6 text-gray-800 outline-none transition focus:border-red-400 focus:bg-white focus:ring-4 focus:ring-red-50"
                                >{{ old('admin_notes') }}</textarea>

                                <div class="mt-3 flex gap-2 rounded-xl border border-red-100 bg-red-50 p-3">

                                    <i class="fa-solid fa-triangle-exclamation mt-0.5 text-xs text-red-500"></i>

                                    <p class="text-xs leading-5 text-red-700">
                                        Tindakan ini akan mengubah status menjadi
                                        <strong>Ditolak Admin</strong>.
                                    </p>

                                </div>

                            </div>


                            {{-- Footer --}}
                            <div class="flex gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:px-7">

                                <button
                                    type="button"
                                    data-micromodal-close
                                    class="flex-1 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-100"
                                >
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    class="flex-1 rounded-xl bg-red-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100"
                                >
                                    <i class="fa-solid fa-xmark mr-2 text-xs"></i>
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
