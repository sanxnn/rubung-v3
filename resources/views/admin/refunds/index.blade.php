@extends('admin.layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pengajuan Refund</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $pendingCount }} pengajuan menunggu tinjauan.</p>
    </div>

    <form method="GET" class="mb-5 flex gap-3">
        <select name="status" class="rounded-xl border border-gray-200 px-3 py-2 text-sm">
            <option value="">Semua status</option>
            @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'processing' => 'Diproses Midtrans', 'completed' => 'Selesai', 'rejected' => 'Ditolak'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-xl bg-amber-400 px-4 py-2 text-sm font-semibold text-gray-900">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-600"><tr>
                <th class="px-5 py-3">Pesanan</th><th class="px-5 py-3">Customer</th>
                <th class="px-5 py-3">Nominal</th><th class="px-5 py-3">Status</th>
                <th class="px-5 py-3">Diajukan</th><th class="px-5 py-3"></th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($refunds as $refund)
                    <tr>
                        <td class="px-5 py-4 font-medium">{{ $refund->order?->order_number }}</td>
                        <td class="px-5 py-4">{{ $refund->user?->name }}</td>
                        <td class="px-5 py-4">Rp {{ number_format($refund->amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-4">{{ ucfirst($refund->status) }}</td>
                        <td class="px-5 py-4">{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-4"><a href="{{ route('admin.refunds.show', $refund) }}" class="font-semibold text-amber-700 hover:underline">Tinjau</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">Belum ada pengajuan refund.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $refunds->links() }}</div>
</main>
@endsection
