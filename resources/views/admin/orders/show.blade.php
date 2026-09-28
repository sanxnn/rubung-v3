@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-900">
                        <i class="fa-solid fa-arrow-left mr-1"></i>
                        Pesanan
                    </a>
                </div>

                <h2 class="text-xl font-bold text-gray-900">
                    {{ $order->order_number }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $order->created_at?->format('d F Y, H:i') }}
                </p>
            </div>

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

            <span
                class="self-start inline-flex rounded-full px-3 py-1.5 text-sm font-medium {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
            </span>

        </div>

        {{-- Flash --}}
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Items --}}
                <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">
                            Produk
                        </h3>
                    </div>

                    <div class="divide-y divide-gray-100">

                        @foreach ($order->items as $item)
                            <div class="p-5 flex items-start justify-between gap-4">

                                <div class="min-w-0">

                                    <p class="font-medium text-gray-900">
                                        {{ $item->product_name }}
                                    </p>

                                    @if ($item->variant_name)
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $item->variant_name }}
                                        </p>
                                    @endif

                                    <p class="text-xs text-gray-400 mt-2">
                                        {{ $item->quantity }} ×
                                        Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                                    </p>

                                </div>

                                <p class="font-semibold text-gray-900 whitespace-nowrap">
                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                </p>

                            </div>
                        @endforeach

                    </div>

                </section>

                {{-- Address --}}
                <section class="bg-white rounded-xl border border-gray-200">

                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">
                            Alamat Pengiriman
                        </h3>
                    </div>

                    <div class="p-5">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>
                                <p class="text-xs text-gray-400 uppercase">
                                    Penerima
                                </p>

                                <p class="font-medium text-gray-900 mt-1">
                                    {{ $order->recipient_name }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400 uppercase">
                                    Nomor HP
                                </p>

                                <p class="font-medium text-gray-900 mt-1">
                                    {{ $order->phone }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400 uppercase">
                                    Provinsi
                                </p>

                                <p class="text-gray-700 mt-1">
                                    {{ $order->province }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400 uppercase">
                                    Kota / Kabupaten
                                </p>

                                <p class="text-gray-700 mt-1">
                                    {{ $order->city }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400 uppercase">
                                    Kode Pos
                                </p>

                                <p class="text-gray-700 mt-1">
                                    {{ $order->postal_code }}
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <p class="text-xs text-gray-400 uppercase">
                                    Detail Alamat
                                </p>

                                <p class="text-gray-700 mt-1">
                                    {{ $order->detail }}
                                </p>
                            </div>

                        </div>

                    </div>

                </section>

                {{-- Shipping --}}
                <section class="bg-white rounded-xl border border-gray-200">

                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">
                            Pengiriman
                        </h3>
                    </div>

                    <form action="{{ route('admin.orders.shipping.update', $order) }}" method="POST" class="p-5">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kurir
                                </label>

                                <select name="shipping_courier" required
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    <option value="">Pilih Kurir</option>

                                    <option value="jne" @selected($order->shipping_courier === 'jne')>
                                        JNE
                                    </option>

                                    <option value="jnt" @selected($order->shipping_courier === 'jnt')>
                                        J&T
                                    </option>

                                    <option value="sicepat" @selected($order->shipping_courier === 'sicepat')>
                                        SiCepat
                                    </option>

                                    <option value="anteraja" @selected($order->shipping_courier === 'anteraja')>
                                        AnterAja
                                    </option>

                                    <option value="pos" @selected($order->shipping_courier === 'pos')>
                                        Pos Indonesia
                                    </option>

                                    <option value="gosend" @selected($order->shipping_courier === 'gosend')>
                                        GoSend
                                    </option>

                                    <option value="grab" @selected($order->shipping_courier === 'grab')>
                                        GrabExpress
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Ongkir
                                </label>

                                <input type="number" name="shipping_cost" min="0" required
                                    value="{{ $order->shipping_cost }}"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nomor Resi
                                </label>

                                <input type="text" name="resi_number" value="{{ $order->resi_number }}"
                                    placeholder="Masukkan nomor resi"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                            </div>

                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="submit"
                                class="rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800">
                                Simpan Pengiriman
                            </button>
                        </div>

                    </form>

                </section>

                {{-- Status History --}}
                <section class="bg-white rounded-xl border border-gray-200">

                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">
                            Riwayat Status
                        </h3>
                    </div>

                    <div class="p-5">

                        @forelse ($order->statusHistories->sortByDesc('created_at') as $history)
                            <div class="relative pl-6 pb-6 last:pb-0">

                                <div class="absolute left-0 top-1 w-2.5 h-2.5 rounded-full bg-gray-900"></div>

                                @unless ($loop->last)
                                    <div class="absolute left-[4px] top-4 bottom-0 w-px bg-gray-200"></div>
                                @endunless

                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">

                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $statusLabels[$history->new_status] ?? ucfirst($history->new_status) }}
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        {{ $history->created_at?->format('d M Y, H:i') }}
                                    </p>

                                </div>

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $history->changedBy?->name ?? 'System' }}
                                </p>

                                @if ($history->notes)
                                    <p class="text-sm text-gray-600 mt-2">
                                        {{ $history->notes }}
                                    </p>
                                @endif

                            </div>

                        @empty

                            <p class="text-sm text-gray-400">
                                Belum ada riwayat perubahan status.
                            </p>
                        @endforelse

                    </div>

                </section>

            </div>

            {{-- RIGHT --}}
            <div class="space-y-6">

                {{-- Customer --}}
                <section class="bg-white rounded-xl border border-gray-200">

                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">
                            Pelanggan
                        </h3>
                    </div>

                    <div class="p-5">

                        <p class="font-medium text-gray-900">
                            {{ $order->user?->name }}
                        </p>

                        <p class="text-sm text-gray-500 mt-1">
                            {{ $order->user?->email }}
                        </p>

                        <p class="text-sm text-gray-500 mt-1">
                            {{ $order->user?->phone }}
                        </p>

                    </div>

                </section>

                {{-- Summary --}}
                <section class="bg-white rounded-xl border border-gray-200">

                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">
                            Ringkasan Pembayaran
                        </h3>
                    </div>

                    <div class="p-5 space-y-3 text-sm">

                        <div class="flex justify-between">
                            <span class="text-gray-500">
                                Subtotal
                            </span>

                            <span class="font-medium text-gray-900">
                                Rp{{ number_format($order->subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">
                                Diskon Tanggal Cantik
                            </span>

                            <span class="font-medium text-red-600">
                                -Rp{{ number_format($order->discount_date_cantik, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">
                                Diskon Voucher
                            </span>

                            <span class="font-medium text-red-600">
                                -Rp{{ number_format($order->discount_voucher, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">
                                Ongkir
                            </span>

                            <span class="font-medium text-gray-900">
                                Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="border-t border-gray-200 pt-3 flex justify-between">

                            <span class="font-semibold text-gray-900">
                                Total
                            </span>

                            <span class="font-bold text-lg text-gray-900">
                                Rp{{ number_format($order->final_amount, 0, ',', '.') }}
                            </span>

                        </div>

                    </div>

                </section>

                {{-- Update Status --}}
                <section class="bg-white rounded-xl border border-gray-200">

                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">
                            Update Status
                        </h3>
                    </div>

                    <form action="{{ route('admin.orders.status.update', $order) }}" method="POST" class="p-5">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Status
                            </label>

                            <select name="status" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500">

                                @foreach ($statusLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($order->status === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="mt-4">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Catatan
                            </label>

                            <textarea name="notes" rows="3" placeholder="Catatan perubahan status..."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>

                        </div>

                        <button type="submit"
                            class="w-full mt-4 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800">
                            Update Status
                        </button>

                    </form>

                </section>

                {{-- Notes --}}
                @if ($order->notes)
                    <section class="bg-white rounded-xl border border-gray-200">

                        <div class="px-5 py-4 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-900">
                                Catatan Pelanggan
                            </h3>
                        </div>

                        <div class="p-5">
                            <p class="text-sm text-gray-600 whitespace-pre-line">
                                {{ $order->notes }}
                            </p>
                        </div>

                    </section>
                @endif

            </div>

        </div>

    </main>
@endsection
