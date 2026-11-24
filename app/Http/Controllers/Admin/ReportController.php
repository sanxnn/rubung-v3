<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()
            ->with([
                'user',
                'items',
            ])
            ->withSum(['refundRequests as refunded_amount' => fn ($q) => $q->where('status', 'completed')], 'amount');

        // Filter tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->start_date
            );
        }

        // Filter tanggal selesai
        if ($request->filled('end_date')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->end_date
            );
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $summaryQuery = clone $query;

        $totalOrders = (clone $summaryQuery)->count();

        $paidStatuses = ['paid', 'processing', 'packing', 'shipped', 'completed'];

        $grossRevenue = (clone $summaryQuery)
            ->whereIn('status', $paidStatuses)
            ->sum('final_amount');
        $totalRefunded = RefundRequest::where('status', 'completed')
            ->whereIn('order_id', (clone $summaryQuery)
                ->whereIn('status', $paidStatuses)->select('id'))
            ->sum('amount');
        $totalRevenue = $grossRevenue - $totalRefunded;

        $totalDiscount = (clone $summaryQuery)
            ->whereIn('status', $paidStatuses)
            ->selectRaw(
                'COALESCE(SUM(discount_date_cantik), 0) +
                 COALESCE(SUM(discount_voucher), 0) as total'
            )
            ->value('total') ?? 0;

        $totalShipping = (clone $summaryQuery)
            ->whereIn('status', $paidStatuses)
            ->sum('shipping_cost');

        /*
        |--------------------------------------------------------------------------
        | Status Summary
        |--------------------------------------------------------------------------
        */

        $statusCounts = [
            'pending_payment' => (clone $query)
                ->where('status', 'pending_payment')
                ->count(),

            'paid' => (clone $query)
                ->where('status', 'paid')
                ->count(),

            'processing' => (clone $query)
                ->where('status', 'processing')
                ->count(),

            'packing' => (clone $query)
                ->where('status', 'packing')
                ->count(),

            'shipped' => (clone $query)
                ->where('status', 'shipped')
                ->count(),

            'completed' => (clone $query)
                ->where('status', 'completed')
                ->count(),

            'cancelled' => (clone $query)
                ->where('status', 'cancelled')
                ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */

        $orders = $query
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports', [
            'orders' => $orders,

            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'totalRefunded' => $totalRefunded,
            'totalDiscount' => $totalDiscount,
            'totalShipping' => $totalShipping,

            'statusCounts' => $statusCounts,
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(
            new ReportExport(
                $request->input('start_date'),
                $request->input('end_date'),
                $request->input('status')
            ),
            'laporan-penjualan-'.now()->format('Y-m-d-His').'.xlsx'
        );
    }
}
