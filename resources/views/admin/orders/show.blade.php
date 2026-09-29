@extends('admin.layouts.app')

@section('content')
    @php
        /*
        |--------------------------------------------------------------------------
        | STATUS LABEL
        |--------------------------------------------------------------------------
        */

        $statusLabels = [
            'pending_payment' => 'Menunggu Pembayaran',
            'paid' => 'Dibayar',
            'processing' => 'Diproses',
            'packing' => 'Dikemas',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        /*
        |--------------------------------------------------------------------------
        | STATUS COLOR
        |--------------------------------------------------------------------------
        */

        $statusClasses = [
            'pending_payment' => 'bg-yellow-100 text-yellow-700',
            'paid' => 'bg-green-100 text-green-700',
            'processing' => 'bg-blue-100 text-blue-700',
            'packing' => 'bg-purple-100 text-purple-700',
            'shipped' => 'bg-indigo-100 text-indigo-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
        ];

        /*
        |--------------------------------------------------------------------------
        | STATUS TRANSITION
        |--------------------------------------------------------------------------
        |
        | Alur normal:
        |
        | pending_payment
        |       ↓
        |     paid
        |       ↓
        |   processing
        |       ↓
        |    packing
        |       ↓
        |    shipped
        |       ↓
        |   completed
        |
        | pending_payment -> paid dilakukan oleh sistem/Midtrans.
        |
        | Admin tidak boleh:
        | - mengubah ke pending_payment
        | - lompat status
        | - mengubah paid langsung menjadi packing/shipped/completed
        | - mengubah processing langsung menjadi shipped/completed
        | - mengubah packing langsung menjadi completed
        |
        */

        $allowedTransitions = [
            'pending_payment' => [],

            'paid' => ['processing'],

            'processing' => ['packing'],

            'packing' => ['shipped'],

            'shipped' => ['completed'],

            'completed' => [],

            'cancelled' => [],
        ];

        $nextStatuses = $allowedTransitions[$order->status] ?? [];
    @endphp


    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

            <div>

                <div class="flex items-center gap-2 mb-2">

                    <a href="{{ route('admin.orders.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-900 transition">

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


            {{-- CURRENT STATUS --}}

            <span
                class="self-start inline-flex rounded-full px-3 py-1.5 text-sm font-medium {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-700' }}">

                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}

            </span>

        </div>


        {{-- ========================================================= --}}
        {{-- FLASH MESSAGE --}}
        {{-- ========================================================= --}}

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


        {{-- ========================================================= --}}
        {{-- MAIN GRID --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


            {{-- ===================================================== --}}
            {{-- LEFT --}}
            {{-- ===================================================== --}}

            <div class="lg:col-span-2 space-y-6">


                {{-- ================================================= --}}
                {{-- PRODUCTS --}}
                {{-- ================================================= --}}

                <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-200">

                        <h3 class="font-semibold text-gray-900">
                            Produk
                        </h3>

                    </div>


                    <div class="divide-y divide-gray-100">

                        @forelse ($order->items as $item)

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

                                        {{ $item->quantity }}

                                        ×

                                        Rp{{ number_format($item->unit_price, 0, ',', '.') }}

                                    </p>

                                </div>


                                <p class="font-semibold text-gray-900 whitespace-nowrap">

                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}

                                </p>

                            </div>

                        @empty

                            <div class="p-5">

                                <p class="text-sm text-gray-400">

                                    Tidak ada produk.

                                </p>

                            </div>

                        @endforelse

                    </div>

                </section>


                {{-- ================================================= --}}
                {{-- SHIPPING ADDRESS --}}
                {{-- ================================================= --}}

                <section class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">

                    <div class="flex items-center gap-3 mb-6">

                        <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">

                            <i class="fa-solid fa-location-dot text-yellow-600"></i>

                        </div>


                        <div>

                            <h3 class="text-base font-bold text-gray-900">

                                Alamat Pengiriman

                            </h3>


                            <p class="text-xs text-gray-500">

                                Alamat yang tersimpan pada saat order dibuat

                            </p>

                        </div>

                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">


                        {{-- PENERIMA --}}

                        <div>

                            <p class="text-xs font-medium text-gray-500 mb-1">

                                Penerima

                            </p>


                            <p class="text-sm font-semibold text-gray-900">

                                {{ $order->snapshot_recipient_name ?: '-' }}

                            </p>

                        </div>


                        {{-- NOMOR HP --}}

                        <div>

                            <p class="text-xs font-medium text-gray-500 mb-1">

                                Nomor HP

                            </p>


                            <p class="text-sm font-semibold text-gray-900">

                                {{ $order->snapshot_phone ?: '-' }}

                            </p>

                        </div>


                        {{-- PROVINSI --}}

                        <div>

                            <p class="text-xs font-medium text-gray-500 mb-1">

                                Provinsi

                            </p>


                            <p class="text-sm font-semibold text-gray-900">

                                {{ $order->snapshot_province ?: '-' }}

                            </p>

                        </div>


                        {{-- KOTA --}}

                        <div>

                            <p class="text-xs font-medium text-gray-500 mb-1">

                                Kota / Kabupaten

                            </p>


                            <p class="text-sm font-semibold text-gray-900">

                                {{ $order->snapshot_city ?: '-' }}

                            </p>

                        </div>


                        {{-- KECAMATAN --}}

                        <div>

                            <p class="text-xs font-medium text-gray-500 mb-1">

                                Kecamatan

                            </p>


                            <p class="text-sm font-semibold text-gray-900">

                                {{ $order->address?->district ?: '-' }}

                            </p>

                        </div>


                        {{-- KODE POS --}}

                        <div>

                            <p class="text-xs font-medium text-gray-500 mb-1">

                                Kode Pos

                            </p>


                            <p class="text-sm font-semibold text-gray-900">

                                {{ $order->snapshot_postal_code ?: '-' }}

                            </p>

                        </div>


                        {{-- DETAIL ALAMAT --}}

                        <div class="md:col-span-2">

                            <p class="text-xs font-medium text-gray-500 mb-1">

                                Detail Alamat

                            </p>


                            <p class="text-sm font-semibold text-gray-900 whitespace-pre-line">

                                {{ $order->snapshot_detail ?: '-' }}

                            </p>

                        </div>

                    </div>

                </section>


                {{-- ================================================= --}}
                {{-- TRANSACTION INFO --}}
                {{-- ================================================= --}}

                <section class="bg-white rounded-xl border border-gray-200">

                    <div class="px-5 py-4 border-b border-gray-200">

                        <h3 class="font-semibold text-gray-900">

                            Informasi Transaksi

                        </h3>

                    </div>


                    <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">


                        {{-- SUBTOTAL --}}

                        <div>

                            <p class="text-xs text-gray-400">

                                Subtotal

                            </p>


                            <p class="font-medium">

                                Rp{{ number_format($order->subtotal, 0, ',', '.') }}

                            </p>

                        </div>


                        {{-- SHIPPING --}}

                        <div>

                            <p class="text-xs text-gray-400">

                                Ongkir (J&T)

                            </p>


                            <p class="font-medium">

                                Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}

                            </p>

                        </div>


                        {{-- DATE DISCOUNT --}}

                        <div>

                            <p class="text-xs text-gray-400">

                                Diskon Tanggal

                            </p>


                            <p class="font-medium">

                                Rp{{ number_format($order->discount_date_cantik, 0, ',', '.') }}

                            </p>

                        </div>


                        {{-- VOUCHER --}}

                        <div>

                            <p class="text-xs text-gray-400">

                                Diskon Voucher

                            </p>


                            <p class="font-medium">

                                Rp{{ number_format($order->discount_voucher, 0, ',', '.') }}

                            </p>

                        </div>


                        {{-- TOTAL --}}

                        <div>

                            <p class="text-xs text-gray-400">

                                Total

                            </p>


                            <p class="font-bold text-gray-900">

                                Rp{{ number_format($order->final_amount, 0, ',', '.') }}

                            </p>

                        </div>


                        {{-- COURIER --}}

                        <div>

                            <p class="text-xs text-gray-400">

                                Ekspedisi

                            </p>


                            <p class="font-medium">

                                J&T

                            </p>

                        </div>


                        {{-- RESI --}}

                        <div>

                            <p class="text-xs text-gray-400">

                                No. Resi

                            </p>


                            <p class="font-medium">

                                {{ $order->resi_number ?: '-' }}

                            </p>

                        </div>


                        {{-- STATUS --}}

                        <div>

                            <p class="text-xs text-gray-400">

                                Status

                            </p>


                            <p class="font-medium">

                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}

                            </p>

                        </div>

                    </div>

                </section>


                {{-- ================================================= --}}
                {{-- STATUS HISTORY --}}
                {{-- ================================================= --}}

                <section class="bg-white rounded-xl border border-gray-200">

                    <div class="px-5 py-4 border-b border-gray-200">

                        <h3 class="font-semibold text-gray-900">

                            Riwayat Status

                        </h3>

                    </div>


                    <div class="p-5">

                        @forelse ($order->statusHistories->sortByDesc('created_at') as $history)

                            <div class="relative pl-6 pb-6 last:pb-0">


                                {{-- DOT --}}

                                <div class="absolute left-0 top-1 w-2.5 h-2.5 rounded-full bg-gray-900"></div>


                                {{-- LINE --}}

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


            {{-- ===================================================== --}}
            {{-- RIGHT --}}
            {{-- ===================================================== --}}

            <div class="space-y-6">


                {{-- ================================================= --}}
                {{-- CUSTOMER --}}
                {{-- ================================================= --}}

                <section class="bg-white rounded-xl border border-gray-200">

                    <div class="px-5 py-4 border-b border-gray-200">

                        <h3 class="font-semibold text-gray-900">

                            Pelanggan

                        </h3>

                    </div>


                    <div class="p-5">

                        <p class="font-medium text-gray-900">

                            {{ $order->user?->name ?: '-' }}

                        </p>


                        <p class="text-sm text-gray-500 mt-1">

                            {{ $order->user?->email ?: '-' }}

                        </p>


                        <p class="text-sm text-gray-500 mt-1">

                            {{ $order->user?->phone ?: '-' }}

                        </p>

                    </div>

                </section>


                {{-- ================================================= --}}
                {{-- PAYMENT SUMMARY --}}
                {{-- ================================================= --}}

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


                {{-- ================================================= --}}
                {{-- UPDATE STATUS --}}
                {{-- ================================================= --}}

                <section class="bg-white rounded-xl border border-gray-200">

                    <div class="px-5 py-4 border-b border-gray-200">

                        <h3 class="font-semibold text-gray-900">

                            Update Status

                        </h3>

                    </div>


                    <div class="p-5">


                        @if (count($nextStatuses) > 0)

                            <form id="statusForm"
                                action="{{ route('admin.orders.status.update', $order) }}"
                                method="POST"
                                onsubmit="return handleStatusSubmit(event)">

                                @csrf

                                @method('PATCH')


                                {{-- STATUS --}}

                                <div>

                                    <label for="statusSelect"
                                        class="block text-sm font-medium text-gray-700 mb-1">

                                        Status

                                    </label>


                                    <select id="statusSelect"
                                        name="status"
                                        required
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                                        onchange="handleStatusChange(this.value)">

                                        <option value="" selected disabled>

                                            Pilih status berikutnya

                                        </option>


                                        @foreach ($nextStatuses as $value)

                                            <option value="{{ $value }}">

                                                {{ $statusLabels[$value] ?? ucfirst($value) }}

                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                {{-- NOTES --}}

                                <div class="mt-4">

                                    <label for="status_notes"
                                        class="block text-sm font-medium text-gray-700 mb-1">

                                        Catatan

                                    </label>


                                    <textarea id="status_notes"
                                        name="notes"
                                        rows="3"
                                        placeholder="Catatan perubahan status..."
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>

                                </div>


                                {{-- BUTTON --}}

                                <button type="submit"
                                    class="w-full mt-4 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800 transition">

                                    Update Status

                                </button>

                            </form>

                        @else

                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-1">

                                    Status

                                </label>


                                <div
                                    class="rounded-lg bg-gray-50 border border-gray-200 px-3 py-3 text-sm text-gray-500">

                                    @if ($order->status === 'pending_payment')

                                        Status pembayaran dikelola otomatis oleh sistem.
                                        Admin tidak dapat mengubah status ini secara manual.

                                    @elseif ($order->status === 'completed')

                                        Order sudah selesai dan tidak dapat diubah lagi.

                                    @elseif ($order->status === 'cancelled')

                                        Order sudah dibatalkan dan tidak dapat diubah lagi.

                                    @else

                                        Tidak ada perubahan status yang tersedia.

                                    @endif

                                </div>

                            </div>

                        @endif

                    </div>

                </section>


                {{-- ================================================= --}}
                {{-- CUSTOMER NOTES --}}
                {{-- ================================================= --}}

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


    {{-- ============================================================= --}}
    {{-- RESI MODAL --}}
    {{-- ============================================================= --}}

    <div id="resiModal"
        class="fixed inset-0 z-50 hidden"
        aria-modal="true"
        role="dialog">

        {{-- OVERLAY --}}

        <div class="absolute inset-0 bg-black/50"
            onclick="closeResiModal()"></div>


        {{-- MODAL --}}

        <div
            class="absolute top-1/2 left-1/2 w-[calc(100%-2rem)] max-w-md -translate-x-1/2 -translate-y-1/2 rounded-2xl bg-white p-6 shadow-2xl">


            <div class="mb-5">

                <h3 class="text-lg font-bold text-gray-900">

                    Konfirmasi Pengiriman

                </h3>


                <p class="mt-1 text-sm text-gray-600">

                    Masukkan nomor resi J&T sebelum order diubah menjadi Dikirim.

                </p>

            </div>


            <form action="{{ route('admin.orders.status.update', $order) }}"
                method="POST"
                id="resiForm">

                @csrf

                @method('PATCH')


                {{-- STATUS --}}

                <input type="hidden"
                    name="status"
                    value="shipped">


                {{-- NOTES --}}

                <input type="hidden"
                    name="notes"
                    id="modalNotes">


                {{-- RESI --}}

                <div>

                    <label for="resi_input"
                        class="block text-sm font-medium text-gray-700 mb-1">

                        Nomor Resi J&T

                    </label>


                    <input type="text"
                        id="resi_input"
                        name="resi_number"
                        required
                        minlength="3"
                        maxlength="100"
                        autocomplete="off"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                        placeholder="Masukkan nomor resi J&T">


                    <p id="resi_error"
                        class="hidden mt-2 text-sm text-red-600">

                        Nomor resi wajib diisi.

                    </p>

                </div>


                {{-- BUTTONS --}}

                <div class="mt-5 flex gap-3">

                    <button type="button"
                        onclick="closeResiModal()"
                        class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">

                        Batal

                    </button>


                    <button type="submit"
                        class="flex-1 rounded-lg bg-yellow-500 px-4 py-2.5 text-sm font-medium text-gray-900 hover:bg-yellow-400">

                        Konfirmasi Pengiriman

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection


@push('script')

    <script>

        /*
        |--------------------------------------------------------------------------
        | STATUS CHANGE
        |--------------------------------------------------------------------------
        */

        function handleStatusChange(status) {

            if (status === 'shipped') {

                openResiModal();

            }

        }


        /*
        |--------------------------------------------------------------------------
        | STATUS FORM SUBMIT
        |--------------------------------------------------------------------------
        */

        function handleStatusSubmit(event) {

            const statusSelect =
                document.getElementById('statusSelect');


            if (!statusSelect) {

                return false;

            }


            const status =
                statusSelect.value;


            /*
            |--------------------------------------------------------------------------
            | SHIPPED
            |--------------------------------------------------------------------------
            |
            | Jangan submit form utama.
            | Buka modal untuk memasukkan resi.
            |
            */

            if (status === 'shipped') {

                event.preventDefault();

                openResiModal();

                return false;

            }


            return true;

        }


        /*
        |--------------------------------------------------------------------------
        | OPEN RESI MODAL
        |--------------------------------------------------------------------------
        */

        function openResiModal() {

            const modal =
                document.getElementById('resiModal');

            const resiInput =
                document.getElementById('resi_input');

            const modalNotes =
                document.getElementById('modalNotes');


            if (!modal || !resiInput) {

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | COPY NOTES
            |--------------------------------------------------------------------------
            */

            const notes =
                document.querySelector(
                    '#statusForm textarea[name="notes"]'
                );


            if (notes && modalNotes) {

                modalNotes.value =
                    notes.value;

            }


            /*
            |--------------------------------------------------------------------------
            | CLEAR OLD ERROR
            |--------------------------------------------------------------------------
            */

            const error =
                document.getElementById('resi_error');


            if (error) {

                error.classList.add('hidden');

            }


            /*
            |--------------------------------------------------------------------------
            | SHOW MODAL
            |--------------------------------------------------------------------------
            */

            modal.classList.remove('hidden');

            document.body.style.overflow = 'hidden';


            /*
            |--------------------------------------------------------------------------
            | FOCUS
            |--------------------------------------------------------------------------
            */

            setTimeout(function () {

                resiInput.focus();

            }, 100);

        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE RESI MODAL
        |--------------------------------------------------------------------------
        */

        function closeResiModal() {

            const modal =
                document.getElementById('resiModal');


            if (!modal) {

                return;

            }


            modal.classList.add('hidden');

            document.body.style.overflow = '';


            /*
            |--------------------------------------------------------------------------
            | RESET RESI INPUT
            |--------------------------------------------------------------------------
            */

            const resiInput =
                document.getElementById('resi_input');


            if (resiInput) {

                resiInput.value = '';

            }


            /*
            |--------------------------------------------------------------------------
            | RESET ERROR
            |--------------------------------------------------------------------------
            */

            const error =
                document.getElementById('resi_error');


            if (error) {

                error.classList.add('hidden');

            }


            /*
            |--------------------------------------------------------------------------
            | RESET STATUS SELECT
            |--------------------------------------------------------------------------
            */

            const statusSelect =
                document.getElementById('statusSelect');


            if (statusSelect) {

                statusSelect.value = '';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | RESI FORM
        |--------------------------------------------------------------------------
        */

        const resiForm =
            document.getElementById('resiForm');


        if (resiForm) {

            resiForm.addEventListener(
                'submit',
                function (event) {

                    const input =
                        document.getElementById('resi_input');

                    const error =
                        document.getElementById('resi_error');

                    const modalNotes =
                        document.getElementById('modalNotes');


                    if (!input) {

                        event.preventDefault();

                        return;

                    }


                    const resi =
                        input.value.trim();


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDATE RESI
                    |--------------------------------------------------------------------------
                    */

                    if (!resi) {

                        event.preventDefault();


                        if (error) {

                            error.classList.remove('hidden');

                        }


                        input.focus();

                        return;

                    }


                    if (error) {

                        error.classList.add('hidden');

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | COPY LATEST NOTES
                    |--------------------------------------------------------------------------
                    */

                    const notes =
                        document.querySelector(
                            '#statusForm textarea[name="notes"]'
                        );


                    if (notes && modalNotes) {

                        modalNotes.value =
                            notes.value;

                    }

                }
            );

        }

    </script>

@endpush
