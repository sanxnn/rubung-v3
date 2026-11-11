@extends('admin.layouts.app')

@section('content')
    <main class="p-6 lg:p-8 space-y-6">
        <a href="{{ route('admin.custom-orders.index') }}" class="text-sm text-amber-700 hover:underline">← Kembali ke Custom Order</a>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $customOrder->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $customOrder->user?->name }} · {{ $customOrder->user?->email }}</p>
                </div>
                <span class="rounded-full bg-amber-50 px-3 py-1 text-sm text-amber-800">{{ str_replace('_', ' ', ucfirst($customOrder->status)) }}</span>
            </div>

            <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                <div><dt class="text-xs uppercase text-gray-500">Deskripsi</dt><dd class="mt-1 whitespace-pre-wrap text-sm text-gray-800">{{ $customOrder->description }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Catatan admin</dt><dd class="mt-1 whitespace-pre-wrap text-sm text-gray-800">{{ $customOrder->admin_notes ?: '—' }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Estimasi harga</dt><dd class="mt-1 text-sm text-gray-800">{{ $customOrder->estimated_price ? 'Rp '.number_format($customOrder->estimated_price, 0, ',', '.') : '—' }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Estimasi waktu</dt><dd class="mt-1 text-sm text-gray-800">{{ $customOrder->estimated_time ?: '—' }}</dd></div>
            </dl>

            @if ($customOrder->order)
                <a href="{{ route('admin.orders.show', $customOrder->order) }}" class="mt-6 inline-block text-sm text-amber-700 hover:underline">Lihat pesanan terkait →</a>
            @endif
        </div>

        @if ($customOrder->attachments->isNotEmpty())
            <section class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h2 class="font-semibold text-gray-900">Lampiran</h2>
                <ul class="mt-3 space-y-2">
                    @foreach ($customOrder->attachments as $attachment)
                        <li><a href="{{ Storage::disk('public')->url($attachment->file_path) }}" class="text-sm text-amber-700 hover:underline" target="_blank" rel="noopener noreferrer">Lampiran {{ $loop->iteration }}</a></li>
                    @endforeach
                </ul>
            </section>
        @endif
    </main>
@endsection
