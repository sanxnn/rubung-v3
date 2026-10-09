@extends('admin.layouts.app')

@section('title', 'Laporan')

@section('content')

<main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">

    {{-- Header --}}
    <div class="mb-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Laporan
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Laporan transaksi dan penjualan Batik Rubung Kuning.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                {{-- Export Excel --}}
                <a
                    href="{{ route('admin.reports.export', request()->query()) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700"
                >
                    <i class="fa-solid fa-file-excel"></i>
                    Export Excel
                </a>


            </div>

        </div>
    </div>


    {{-- Filter --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

        <div class="mb-4 flex items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100">
                <i class="fa-solid fa-filter text-sm text-gray-600"></i>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-gray-900">
                    Filter Laporan
                </h2>

                <p class="text-xs text-gray-500">
                    Tentukan periode dan status transaksi.
                </p>
            </div>
        </div>

        <form
            method="GET"
            action="{{ route('admin.reports.index') }}"
            class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
        >

            {{-- Start Date --}}
            <div>
                <label
                    for="start_date"
                    class="mb-1.5 block text-sm font-medium text-gray-700"
                >
                    Dari Tanggal
                </label>

                <input
                    type="date"
                    id="start_date"
                    name="start_date"
                    value="{{ request('start_date') }}"
                    class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                >
            </div>

            {{-- End Date --}}
            <div>
                <label
                    for="end_date"
                    class="mb-1.5 block text-sm font-medium text-gray-700"
                >
                    Sampai Tanggal
                </label>

                <input
                    type="date"
                    id="end_date"
                    name="end_date"
                    value="{{ request('end_date') }}"
                    class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                >
            </div>

            {{-- Status --}}
            <div>
                <label
                    for="status"
                    class="mb-1.5 block text-sm font-medium text-gray-700"
                >
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                >
                    <option value="">Semua Status</option>

                    <option
                        value="pending_payment"
                        {{ request('status') === 'pending_payment' ? 'selected' : '' }}
                    >
                        Menunggu Pembayaran
                    </option>

                    <option
                        value="paid"
                        {{ request('status') === 'paid' ? 'selected' : '' }}
                    >
                        Dibayar
                    </option>

                    <option
                        value="processing"
                        {{ request('status') === 'processing' ? 'selected' : '' }}
                    >
                        Diproses
                    </option>

                    <option
                        value="packing"
                        {{ request('status') === 'packing' ? 'selected' : '' }}
                    >
                        Packing
                    </option>

                    <option
                        value="shipped"
                        {{ request('status') === 'shipped' ? 'selected' : '' }}
                    >
                        Dikirim
                    </option>

                    <option
                        value="completed"
                        {{ request('status') === 'completed' ? 'selected' : '' }}
                    >
                        Selesai
                    </option>

                    <option
                        value="cancelled"
                        {{ request('status') === 'cancelled' ? 'selected' : '' }}
                    >
                        Dibatalkan
                    </option>
                </select>
            </div>

            {{-- Button --}}
            <div class="flex items-end gap-2">

                <button
                    type="submit"
                    class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                >
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Terapkan
                </button>

                <a
                    href="{{ route('admin.reports.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50"
                    title="Reset Filter"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                </a>

            </div>

        </form>

    </div>


    {{-- Summary Cards --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Total Transaksi --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total Transaksi
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ number_format($totalOrders, 0, ',', '.') }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Transaksi berdasarkan filter
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100">
                    <i class="fa-solid fa-receipt text-gray-700"></i>
                </div>

            </div>
        </div>


        {{-- Omzet --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total Omzet
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Tidak termasuk pesanan dibatalkan
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100">
                    <i class="fa-solid fa-money-bill-wave text-gray-700"></i>
                </div>

            </div>
        </div>


        {{-- Diskon --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total Diskon
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        Rp {{ number_format($totalDiscount, 0, ',', '.') }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Promo + voucher
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100">
                    <i class="fa-solid fa-tag text-gray-700"></i>
                </div>

            </div>
        </div>


        {{-- Ongkir --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total Ongkir
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        Rp {{ number_format($totalShipping, 0, ',', '.') }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Tidak termasuk pesanan dibatalkan
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100">
                    <i class="fa-solid fa-truck text-gray-700"></i>
                </div>

            </div>
        </div>

    </div>


    {{-- Status Summary --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

        <div class="mb-5">
            <h2 class="text-base font-semibold text-gray-900">
                Ringkasan Status
            </h2>

            <p class="mt-1 text-xs text-gray-500">
                Jumlah transaksi berdasarkan status.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-7">

            {{-- Pending --}}
            <div class="rounded-xl bg-gray-50 p-4">
                <p class="text-xs font-medium text-gray-500">
                    Menunggu
                </p>

                <p class="mt-2 text-xl font-bold text-gray-900">
                    {{ $statusCounts['pending_payment'] }}
                </p>
            </div>

            {{-- Paid --}}
            <div class="rounded-xl bg-gray-50 p-4">
                <p class="text-xs font-medium text-gray-500">
                    Dibayar
                </p>

                <p class="mt-2 text-xl font-bold text-gray-900">
                    {{ $statusCounts['paid'] }}
                </p>
            </div>

            {{-- Processing --}}
            <div class="rounded-xl bg-gray-50 p-4">
                <p class="text-xs font-medium text-gray-500">
                    Diproses
                </p>

                <p class="mt-2 text-xl font-bold text-gray-900">
                    {{ $statusCounts['processing'] }}
                </p>
            </div>

            {{-- Packing --}}
            <div class="rounded-xl bg-gray-50 p-4">
                <p class="text-xs font-medium text-gray-500">
                    Packing
                </p>

                <p class="mt-2 text-xl font-bold text-gray-900">
                    {{ $statusCounts['packing'] }}
                </p>
            </div>

            {{-- Shipped --}}
            <div class="rounded-xl bg-gray-50 p-4">
                <p class="text-xs font-medium text-gray-500">
                    Dikirim
                </p>

                <p class="mt-2 text-xl font-bold text-gray-900">
                    {{ $statusCounts['shipped'] }}
                </p>
            </div>

            {{-- Completed --}}
            <div class="rounded-xl bg-gray-50 p-4">
                <p class="text-xs font-medium text-gray-500">
                    Selesai
                </p>

                <p class="mt-2 text-xl font-bold text-gray-900">
                    {{ $statusCounts['completed'] }}
                </p>
            </div>

            {{-- Cancelled --}}
            <div class="rounded-xl bg-gray-50 p-4">
                <p class="text-xs font-medium text-gray-500">
                    Dibatalkan
                </p>

                <p class="mt-2 text-xl font-bold text-gray-900">
                    {{ $statusCounts['cancelled'] }}
                </p>
            </div>

        </div>

    </div>


    {{-- Transaction Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

        {{-- Table Header --}}
        <div class="flex flex-col gap-3 border-b border-gray-100 p-5 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-base font-semibold text-gray-900">
                    Detail Transaksi
                </h2>

                <p class="mt-1 text-xs text-gray-500">
                    Daftar transaksi berdasarkan filter yang dipilih.
                </p>
            </div>

            <div class="text-sm text-gray-500">
                {{ $orders->total() }} transaksi
            </div>

        </div>


        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="w-full min-w-[1100px] text-left">

                <thead class="bg-gray-50">
                    <tr class="border-b border-gray-100">

                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Order
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Customer
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Tanggal
                        </th>

                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Item
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Subtotal
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Diskon
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Ongkir
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Total
                        </th>

                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                    </tr>
                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse ($orders as $order)

                        @php

                            $discount =
                                (int) ($order->discount_date_cantik ?? 0)
                                +
                                (int) ($order->discount_voucher ?? 0);

                            $statusLabel = match ($order->status) {

                                'pending_payment' => 'Menunggu Pembayaran',

                                'paid' => 'Dibayar',

                                'processing' => 'Diproses',

                                'packing' => 'Packing',

                                'shipped' => 'Dikirim',

                                'completed' => 'Selesai',

                                'cancelled' => 'Dibatalkan',

                                default => ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $order->status
                                    )
                                ),

                            };

                            $statusClass = match ($order->status) {

                                'pending_payment' =>
                                    'bg-yellow-50 text-yellow-700',

                                'paid' =>
                                    'bg-blue-50 text-blue-700',

                                'processing' =>
                                    'bg-indigo-50 text-indigo-700',

                                'packing' =>
                                    'bg-purple-50 text-purple-700',

                                'shipped' =>
                                    'bg-cyan-50 text-cyan-700',

                                'completed' =>
                                    'bg-green-50 text-green-700',

                                'cancelled' =>
                                    'bg-red-50 text-red-700',

                                default =>
                                    'bg-gray-100 text-gray-700',

                            };

                            $itemCount = $order->items->sum('quantity');

                        @endphp

                        <tr class="transition hover:bg-gray-50">

                            {{-- Order --}}
                            <td class="px-5 py-4">

                                <div class="font-semibold text-gray-900">
                                    {{ $order->order_number }}
                                </div>

                                <div class="mt-1 text-xs text-gray-400">
                                    #{{ $order->id }}
                                </div>

                            </td>


                            {{-- Customer --}}
                            <td class="px-5 py-4">

                                <div class="font-medium text-gray-900">
                                    {{ $order->user?->name ?? '-' }}
                                </div>

                                @if ($order->user?->email)
                                    <div class="mt-1 text-xs text-gray-400">
                                        {{ $order->user->email }}
                                    </div>
                                @endif

                            </td>


                            {{-- Date --}}
                            <td class="px-5 py-4 whitespace-nowrap">

                                <div class="text-sm text-gray-700">
                                    {{ $order->created_at?->format('d M Y') ?? '-' }}
                                </div>

                                <div class="mt-1 text-xs text-gray-400">
                                    {{ $order->created_at?->format('H:i') ?? '-' }}
                                </div>

                            </td>


                            {{-- Item --}}
                            <td class="px-5 py-4 text-center">

                                <span class="inline-flex min-w-8 items-center justify-center rounded-lg bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">
                                    {{ $itemCount }}
                                </span>

                            </td>


                            {{-- Subtotal --}}
                            <td class="px-5 py-4 text-right whitespace-nowrap">

                                <span class="text-sm font-medium text-gray-700">
                                    Rp {{ number_format($order->subtotal ?? 0, 0, ',', '.') }}
                                </span>

                            </td>


                            {{-- Discount --}}
                            <td class="px-5 py-4 text-right whitespace-nowrap">

                                @if ($discount > 0)

                                    <span class="text-sm font-medium text-red-600">
                                        - Rp {{ number_format($discount, 0, ',', '.') }}
                                    </span>

                                @else

                                    <span class="text-sm text-gray-400">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- Shipping --}}
                            <td class="px-5 py-4 text-right whitespace-nowrap">

                                <span class="text-sm font-medium text-gray-700">
                                    Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}
                                </span>

                            </td>


                            {{-- Total --}}
                            <td class="px-5 py-4 text-right whitespace-nowrap">

                                <span class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($order->final_amount ?? 0, 0, ',', '.') }}
                                </span>

                            </td>


                            {{-- Status --}}
                            <td class="px-5 py-4 text-center">

                                <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="9"
                                class="px-5 py-16 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100">
                                        <i class="fa-solid fa-chart-column text-xl text-gray-400"></i>
                                    </div>

                                    <h3 class="mt-4 text-sm font-semibold text-gray-900">
                                        Tidak ada transaksi
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Belum ada transaksi yang sesuai dengan filter.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if ($orders->hasPages())

            <div class="border-t border-gray-100 px-5 py-4">
                {{ $orders->links() }}
            </div>

        @endif

    </div>

</main>



@endsection
