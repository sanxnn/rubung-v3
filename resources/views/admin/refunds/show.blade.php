@extends('admin.layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8 space-y-5">
    <a href="{{ route('admin.refunds.index') }}" class="text-sm text-amber-700 hover:underline">← Kembali ke refund</a>
    <div class="rounded-2xl border border-gray-200 bg-white p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Refund {{ $refund->order->order_number }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $refund->user->name }} · {{ $refund->user->email }}</p>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-sm text-amber-800">{{ ucfirst($refund->status) }}</span>
        </div>
        <dl class="mt-6 grid gap-5 sm:grid-cols-2 text-sm">
            <div><dt class="text-gray-500">Nominal refund penuh</dt><dd class="font-semibold">Rp {{ number_format($refund->amount, 0, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Pembayaran</dt><dd>{{ $refund->order->payment?->payment_type }} · {{ $refund->order->payment?->payment_status }}</dd></div>
            <div><dt class="text-gray-500">Status pesanan</dt><dd>{{ $refund->order->status }}</dd></div>
            <div><dt class="text-gray-500">Diajukan</dt><dd>{{ $refund->created_at->format('d/m/Y H:i') }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500">Alasan customer</dt><dd class="mt-1 whitespace-pre-wrap">{{ $refund->reason }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500">Catatan admin</dt><dd class="mt-1 whitespace-pre-wrap">{{ $refund->admin_notes ?: '—' }}</dd></div>
            @if ($refund->manual_reference)
                <div><dt class="text-gray-500">Referensi transfer</dt><dd>{{ $refund->manual_reference }}</dd></div>
            @endif
        </dl>
        <div class="mt-5 flex flex-wrap gap-4 text-sm">
            <a href="{{ route('admin.refunds.evidence', $refund) }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-amber-700 hover:underline">Lihat bukti customer</a>
            <a href="{{ route('admin.orders.show', $refund->order) }}" class="font-semibold text-amber-700 hover:underline">Lihat pesanan</a>
        </div>
    </div>

    @if (session('success')) <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div> @endif
    @if (session('error')) <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">{{ session('error') }}</div> @endif
    @if ($errors->any()) <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div> @endif

    @if ($refund->status === 'pending')
        <div class="grid gap-5 lg:grid-cols-2">
            <form method="POST" action="{{ route('admin.refunds.approve', $refund) }}" class="space-y-3 rounded-2xl border border-gray-200 bg-white p-6">
                @csrf
                <h2 class="font-semibold">Setujui refund</h2>
                <label for="method" class="block text-sm">Cara pengembalian dana</label>
                <select id="method" name="method" required class="w-full rounded-xl border border-gray-200 p-3 text-sm">
                    <option value="manual">Manual, catat transfer setelah selesai</option>
                    <option value="midtrans">Midtrans, kirim permintaan refund</option>
                </select>
                <p class="text-xs text-gray-500">Midtrans hanya mendukung metode tertentu dan transaksi settlement. Jika tidak memenuhi syarat, pengajuan tetap disetujui dan dapat diperiksa kembali.</p>
                <textarea name="admin_notes" rows="3" maxlength="2000" placeholder="Catatan untuk customer (opsional)" class="w-full rounded-xl border border-gray-200 p-3 text-sm"></textarea>
                <button class="rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white">Setujui</button>
            </form>
            <form method="POST" action="{{ route('admin.refunds.reject', $refund) }}" class="space-y-3 rounded-2xl border border-gray-200 bg-white p-6">
                @csrf
                <h2 class="font-semibold">Tolak refund</h2>
                <textarea name="admin_notes" rows="4" required minlength="10" maxlength="2000" placeholder="Alasan penolakan untuk customer" class="w-full rounded-xl border border-gray-200 p-3 text-sm"></textarea>
                <button class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white">Tolak</button>
            </form>
        </div>
    @elseif ($refund->status === 'approved' && $refund->refund_method === 'manual')
        <form method="POST" action="{{ route('admin.refunds.complete', $refund) }}" class="space-y-3 rounded-2xl border border-gray-200 bg-white p-6">
            @csrf
            <h2 class="font-semibold">Catat transfer selesai</h2>
            <input name="manual_reference" required maxlength="100" placeholder="Nomor referensi transfer" class="w-full rounded-xl border border-gray-200 p-3 text-sm">
            <button class="rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white">Tandai dana dikembalikan</button>
        </form>
    @elseif (in_array($refund->status, ['approved', 'processing'], true) && $refund->refund_method === 'midtrans')
        <form method="POST" action="{{ route('admin.refunds.retry', $refund) }}" class="rounded-2xl border border-gray-200 bg-white p-6">
            @csrf
            <p class="mb-3 text-sm text-gray-600">Periksa status Midtrans. Jika belum diterima, kirim ulang dengan kunci refund yang sama.</p>
            <button class="rounded-xl bg-amber-400 px-4 py-2 text-sm font-semibold">Periksa / ulangi</button>
        </form>
        @if ($refund->status === 'approved')
            <form method="POST" action="{{ route('admin.refunds.manual', $refund) }}" class="rounded-2xl border border-gray-200 bg-white p-6">
                @csrf
                <p class="mb-3 text-sm text-gray-600">Gunakan hanya setelah memastikan pengembalian belum berjalan di Midtrans. Sistem akan memeriksa status transaksi sebelum mengalihkan ke proses manual.</p>
                <button class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold">Alihkan ke manual</button>
            </form>
        @endif
    @endif
</main>
@endsection
