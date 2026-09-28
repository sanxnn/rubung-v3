@extends('admin.layouts.app')

@section('content')
    <main class="p-5 sm:p-8">

        {{-- Welcome --}}
        <div class="mb-7">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                Selamat datang kembali, {{ auth()->user()->name }} 👋
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Berikut ringkasan aktivitas Batik Rubung Kuning hari ini.
            </p>
        </div>

        {{-- ================================================= --}}
        {{-- STAT CARDS --}}
        {{-- ================================================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

            {{-- Revenue --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400">Total Pendapatan</p>
                        <h3 class="mt-2 text-2xl font-bold tracking-tight text-gray-900">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-[#FFF8D8] flex items-center justify-center text-[#B88900]">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-xs text-gray-400">Dari pesanan yang sudah dibayar/diproses</span>
                </div>
            </div>

            {{-- Orders --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400">Total Pesanan</p>
                        <h3 class="mt-2 text-2xl font-bold tracking-tight text-gray-900">
                            {{ $stats['total_orders'] }}
                        </h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-xs font-semibold text-blue-600">{{ $stats['pending_orders'] }} menunggu</span>
                    <span class="text-xs text-gray-400">pembayaran</span>
                </div>
            </div>

            {{-- Custom Order / PO --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400">Custom Order (PO)</p>
                        <h3 class="mt-2 text-2xl font-bold tracking-tight text-gray-900">
                            {{ $stats['pending_custom_orders'] }}
                        </h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-xs font-semibold text-purple-600">Perlu review</span>
                    <span class="text-xs text-gray-400">dari customer</span>
                </div>
            </div>

            {{-- Products / Low Stock --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400">Stok Menipis</p>
                        <h3 class="mt-2 text-2xl font-bold tracking-tight text-gray-900">
                            {{ $stats['low_stock_items'] }}
                        </h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-xs font-semibold text-gray-600">Varian</span>
                    <span class="text-xs text-gray-400">dengan stok < 5</span>
                </div>
            </div>
        </div>

        {{-- ================================================= --}}
        {{-- SECOND ROW: CHART & STATUS --}}
        {{-- ================================================= --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">

            {{-- Revenue Chart --}}
            <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-semibold text-gray-900">Pendapatan</h3>
                        <p class="text-xs text-gray-400 mt-1">Performa pendapatan 6 bulan terakhir</p>
                    </div>
                </div>

                {{-- Canvas untuk Chart.js --}}
                <div class="relative h-64 w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            {{-- Order Status --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-sm">
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-900">Status Pesanan</h3>
                    <p class="text-xs text-gray-400 mt-1">Ringkasan pesanan saat ini</p>
                </div>

                @php
                    $total = max(1, $stats['total_orders']); // Hindari division by zero
                    $pctPending = round(($stats['pending_orders'] / $total) * 100);
                    $pctProcessing = round(($stats['processing_orders'] / $total) * 100);
                    $pctShipped = round(($stats['shipped_orders'] / $total) * 100);
                    $pctCompleted = round(($stats['completed_orders'] / $total) * 100);
                @endphp

                <div class="space-y-5">
                    {{-- Pending --}}
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-xs text-gray-500">Menunggu Pembayaran</span>
                            <span class="text-xs font-semibold">{{ $stats['pending_orders'] }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-[#F4C430] rounded-full transition-all duration-500"
                                style="width: {{ $pctPending }}%"></div>
                        </div>
                    </div>

                    {{-- Processing --}}
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-xs text-gray-500">Diproses / Packing</span>
                            <span class="text-xs font-semibold">{{ $stats['processing_orders'] }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-400 rounded-full transition-all duration-500"
                                style="width: {{ $pctProcessing }}%"></div>
                        </div>
                    </div>

                    {{-- Shipping --}}
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-xs text-gray-500">Dikirim</span>
                            <span class="text-xs font-semibold">{{ $stats['shipped_orders'] }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-400 rounded-full transition-all duration-500"
                                style="width: {{ $pctShipped }}%"></div>
                        </div>
                    </div>

                    {{-- Completed --}}
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-xs text-gray-500">Selesai</span>
                            <span class="text-xs font-semibold">{{ $stats['completed_orders'] }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-green-400 rounded-full transition-all duration-500"
                                style="width: {{ $pctCompleted }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-7 pt-5 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Total pesanan</span>
                        <span class="font-bold text-sm text-gray-900">{{ $stats['total_orders'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================= --}}
        {{-- THIRD ROW: RECENT ORDERS & ACTIVITY --}}
        {{-- ================================================= --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Recent Orders --}}
            <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="p-5 sm:p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900">Pesanan Terbaru</h3>
                        <p class="text-xs text-gray-400 mt-1">Aktivitas pesanan terbaru</p>
                    </div>
                    {{-- PERBAIKAN: Menambahkan route yang benar --}}
                    <a href="{{ route('admin.orders.index') }}"
                        class="text-xs font-medium text-[#B88900] hover:text-[#8A6500] transition-colors">
                        Lihat semua <i class="fa-solid fa-arrow-right ml-1 text-[9px]"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    @if ($recent_orders->isEmpty())
                        <div class="p-8 text-center text-sm text-gray-400">
                            Belum ada pesanan terbaru.
                        </div>
                    @else
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-gray-50">
                                    <th class="px-5 sm:px-6 py-3 text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Order</th>
                                    <th class="px-5 sm:px-6 py-3 text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Pelanggan</th>
                                    <th class="px-5 sm:px-6 py-3 text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Total</th>
                                    <th class="px-5 sm:px-6 py-3 text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($recent_orders as $order)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 sm:px-6 py-4">
                                            <span class="text-xs font-semibold text-gray-900">{{ $order->order_number }}</span>
                                            <p class="text-[10px] text-gray-400 mt-1">{{ $order->created_at->format('d M Y') }}</p>
                                        </td>
                                        <td class="px-5 sm:px-6 py-4">
                                            <p class="text-xs font-medium text-gray-900">{{ $order->user->name ?? 'Guest' }}</p>
                                            <p class="text-[10px] text-gray-400 mt-1">{{ $order->snapshot_city ?? '-' }}</p>
                                        </td>
                                        <td class="px-5 sm:px-6 py-4">
                                            <span class="text-xs font-semibold text-gray-900">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-5 sm:px-6 py-4">
                                            @php
                                                $statusColors = [
                                                    'pending_payment' => 'bg-yellow-50 text-yellow-700',
                                                    'paid' => 'bg-blue-50 text-blue-700',
                                                    'processing' => 'bg-blue-50 text-blue-700',
                                                    'packing' => 'bg-blue-50 text-blue-700',
                                                    'shipped' => 'bg-purple-50 text-purple-700',
                                                    'completed' => 'bg-green-50 text-green-700',
                                                    'cancelled' => 'bg-red-50 text-red-700',
                                                ];
                                                $statusLabels = [
                                                    'pending_payment' => 'Menunggu',
                                                    'paid' => 'Dibayar',
                                                    'processing' => 'Diproses',
                                                    'packing' => 'Packing',
                                                    'shipped' => 'Dikirim',
                                                    'completed' => 'Selesai',
                                                    'cancelled' => 'Dibatalkan',
                                                ];
                                                $colorClass = $statusColors[$order->status] ?? 'bg-gray-50 text-gray-700';
                                                $label = $statusLabels[$order->status] ?? $order->status;
                                            @endphp
                                            <span class="inline-flex px-2.5 py-1 rounded-full {{ $colorClass }} text-[10px] font-medium">
                                                {{ $label }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Quick Info / Activity --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-sm">
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-900">Aktivitas Terbaru</h3>
                    <p class="text-xs text-gray-400 mt-1">Custom order & sistem</p>
                </div>

                <div class="space-y-6">
                    @if ($recent_activities->isEmpty())
                        <p class="text-xs text-gray-400 text-center py-4">Belum ada aktivitas.</p>
                    @else
                        @foreach ($recent_activities as $activity)
                            <div class="flex gap-3">
                                <div class="relative">
                                    <div class="w-8 h-8 rounded-full bg-[#FFF8D8] text-[#B88900] flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </div>
                                    @if (!$loop->last)
                                        <div class="absolute top-8 left-1/2 -translate-x-1/2 w-px h-7 bg-gray-100"></div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-900">Custom Order baru</p>
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $activity->reference_number }}</p>
                                    <p class="text-[10px] text-gray-300 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100">
                    {{-- PERBAIKAN: Menambahkan route yang benar --}}
                    <a href="{{ route('admin.custom-orders.index') }}"
                        class="block w-full text-center py-2 text-xs font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        Kelola Custom Order
                    </a>
                </div>
            </div>

        </div>
    </main>
@endsection

@push('script')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Chart Initialization --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');

            // Data dari Controller
            const months = @json($chartMonths);
            const revenues = @json($chartRevenues);

            // Format angka ke Rupiah untuk tooltip
            const formatRupiah = (value) => {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
            };

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Pendapatan',
                        data: revenues,
                        backgroundColor: [
                            'rgba(244, 196, 48, 0.6)', // #F4C430 with opacity
                            'rgba(244, 196, 48, 0.6)',
                            'rgba(244, 196, 48, 0.6)',
                            'rgba(244, 196, 48, 0.6)',
                            'rgba(244, 196, 48, 0.6)',
                            'rgba(212, 169, 0, 0.8)' // #D4A900 (lebih gelap untuk bulan ini)
                        ],
                        borderColor: [
                            'rgba(244, 196, 48, 1)',
                            'rgba(244, 196, 48, 1)',
                            'rgba(244, 196, 48, 1)',
                            'rgba(244, 196, 48, 1)',
                            'rgba(244, 196, 48, 1)',
                            'rgba(212, 169, 0, 1)'
                        ],
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return formatRupiah(context.raw);
                                }
                            },
                            backgroundColor: '#1f2937',
                            padding: 10,
                            titleFont: {
                                size: 12
                            },
                            bodyFont: {
                                size: 12
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                borderDash: [5, 5]
                            },
                            ticks: {
                                font: {
                                    size: 10
                                },
                                color: '#9ca3af',
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000) + ' Jt';
                                    if (value >= 1000) return (value / 1000) + ' Rb';
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    weight: '500'
                                },
                                color: '#6b7280'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
