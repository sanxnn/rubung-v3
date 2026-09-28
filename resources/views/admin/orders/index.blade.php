@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Pesanan</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola pesanan pelanggan, pembayaran, dan pengiriman.
                </p>
            </div>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @php
            $statusLabels = [
                'pending_payment' => 'Menunggu Pembayaran',
                'paid' => 'Dibayar',
                'processing' => 'Diproses',
                'packing' => 'Dikemas',
                'shipped' => 'Dikirim',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];

            $statusClasses = [
                'pending_payment' => 'bg-yellow-100 text-yellow-700',
                'paid' => 'bg-green-100 text-green-700',
                'processing' => 'bg-blue-100 text-blue-700',
                'packing' => 'bg-purple-100 text-purple-700',
                'shipped' => 'bg-indigo-100 text-indigo-700',
                'completed' => 'bg-green-100 text-green-700',
                'cancelled' => 'bg-red-100 text-red-700',
            ];
        @endphp

        {{-- Statistics --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

            {{-- Total --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Total Pesanan
                        </p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">
                            {{ $stats['total'] }}
                        </p>
                    </div>

                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                        <i class="fa-solid fa-bag-shopping text-gray-600"></i>
                    </div>
                </div>
            </div>

            {{-- Pending --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Menunggu Pembayaran
                        </p>
                        <p class="text-2xl font-bold text-yellow-600 mt-2">
                            {{ $stats['pending_payment'] }}
                        </p>
                    </div>

                    <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center">
                        <i class="fa-solid fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>

            {{-- Processing --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Diproses
                        </p>
                        <p class="text-2xl font-bold text-blue-600 mt-2">
                            {{ $stats['processing'] + $stats['packing'] }}
                        </p>
                    </div>

                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-box text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Completed --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Selesai
                        </p>
                        <p class="text-2xl font-bold text-green-600 mt-2">
                            {{ $stats['completed'] }}
                        </p>
                    </div>

                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-green-600"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">

            <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">

                {{-- Search --}}
                <div class="md:col-span-2">
                    <label class="sr-only">Cari pesanan</label>

                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nomor pesanan / pelanggan..."
                            class="w-full rounded-lg border border-gray-300 pl-10 pr-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <select name="status"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Semua Status</option>

                        <option value="pending_payment" @selected(request('status') === 'pending_payment')}>
                            Menunggu Pembayaran
                        </option>

                        <option value="paid" @selected(request('status') === 'paid')}>
                            Dibayar
                        </option>

                        <option value="processing" @selected(request('status') === 'processing')}>
                            Diproses
                        </option>

                        <option value="packing" @selected(request('status') === 'packing')}>
                            Dikemas
                        </option>

                        <option value="shipped" @selected(request('status') === 'shipped')}>
                            Dikirim
                        </option>

                        <option value="completed" @selected(request('status') === 'completed')}>
                            Selesai
                        </option>

                        <option value="cancelled" @selected(request('status') === 'cancelled')}>
                            Dibatalkan
                        </option>
                    </select>
                </div>

                {{-- Button --}}
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800">
                        <i class="fa-solid fa-filter mr-1"></i>
                        Filter
                    </button>

                    @if (request()->hasAny(['search', 'status', 'courier']))
                        <a href="{{ route('admin.orders.index') }}"
                            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Reset
                        </a>
                    @endif
                </div>

            </form>

        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-gray-600">
                                Pesanan
                            </th>

                            <th class="px-5 py-4 text-left font-semibold text-gray-600">
                                Pelanggan
                            </th>

                            <th class="px-5 py-4 text-left font-semibold text-gray-600">
                                Total
                            </th>

                            <th class="px-5 py-4 text-left font-semibold text-gray-600">
                                Status
                            </th>

                            <th class="px-5 py-4 text-left font-semibold text-gray-600">
                                Pengiriman
                            </th>

                            <th class="px-5 py-4 text-right font-semibold text-gray-600">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($orders as $order)
                            <tr class="hover:bg-gray-50">

                                {{-- Order --}}
                                <td class="px-5 py-4">
                                    <div>
                                        <p class="font-semibold text-gray-900">
                                            {{ $order->order_number }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $order->created_at?->format('d M Y, H:i') }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Customer --}}
                                <td class="px-5 py-4">
                                    <p class="font-medium text-gray-900">
                                        {{ $order->user?->name ?? $order->recipient_name }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $order->phone }}
                                    </p>
                                </td>

                                {{-- Total --}}
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-900">
                                        Rp{{ number_format($order->final_amount, 0, ',', '.') }}
                                    </p>
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4">

                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                    </span>

                                </td>

                                {{-- Shipping --}}
                                <td class="px-5 py-4">

                                    @if ($order->shipping_courier)
                                        <p class="font-medium text-gray-900">
                                            {{ strtoupper($order->shipping_courier) }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $order->resi_number ?: 'Belum ada resi' }}
                                        </p>
                                    @else
                                        <span class="text-xs text-gray-400">
                                            Belum diatur
                                        </span>
                                    @endif

                                </td>

                                {{-- Action --}}
                                <td class="px-5 py-4 text-right">

                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                        <i class="fa-solid fa-eye mr-1"></i>
                                        Detail
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-bag-shopping text-3xl text-gray-300"></i>

                                        <p class="mt-3 font-medium text-gray-700">
                                            Belum ada pesanan
                                        </p>

                                        <p class="text-sm text-gray-400 mt-1">
                                            Pesanan pelanggan akan muncul di sini.
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
                <div class="border-t border-gray-200 px-5 py-4">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>

        {{-- Mobile --}}
        <div class="md:hidden space-y-3">

            @forelse ($orders as $order)
                <a href="{{ route('admin.orders.show', $order) }}"
                    class="block bg-white rounded-xl border border-gray-200 p-4">

                    <div class="flex items-start justify-between gap-3">

                        <div>
                            <p class="font-semibold text-gray-900">
                                {{ $order->order_number }}
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                {{ $order->user?->name ?? $order->recipient_name }}
                            </p>
                        </div>

                        <span
                            class="inline-flex rounded-full px-2 py-1 text-[11px] font-medium {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                        </span>

                    </div>

                    <div class="border-t border-gray-100 mt-4 pt-3 flex items-center justify-between">

                        <div>
                            <p class="text-xs text-gray-400">
                                Total
                            </p>

                            <p class="font-semibold text-gray-900">
                                Rp{{ number_format($order->final_amount, 0, ',', '.') }}
                            </p>
                        </div>

                        <i class="fa-solid fa-chevron-right text-gray-300"></i>

                    </div>

                </a>

            @empty

                <div class="bg-white rounded-xl border border-gray-200 p-10 text-center">
                    <i class="fa-solid fa-bag-shopping text-3xl text-gray-300"></i>

                    <p class="mt-3 font-medium text-gray-700">
                        Belum ada pesanan
                    </p>
                </div>
            @endforelse

            @if ($orders->hasPages())
                <div class="pt-2">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>

    </main>
@endsection
