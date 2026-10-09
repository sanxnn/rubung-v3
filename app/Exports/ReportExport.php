<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportExport implements
    FromQuery,
    WithHeadings,
    WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct(
        $startDate = null,
        $endDate = null,
        $status = null
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function query(): Builder
    {
        $query = Order::query()
            ->with([
                'user',
                'items',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Filter Tanggal
        |--------------------------------------------------------------------------
        */

        if ($this->startDate) {
            $query->whereDate(
                'created_at',
                '>=',
                $this->startDate
            );
        }

        if ($this->endDate) {
            $query->whereDate(
                'created_at',
                '<=',
                $this->endDate
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */

        if ($this->status) {
            $query->where(
                'status',
                $this->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        |
        | created_at + id digunakan agar urutan export deterministic.
        |
        */

        return $query
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Order',
            'Tanggal',
            'Customer',
            'Jumlah Item',
            'Subtotal',
            'Diskon Date Promo',
            'Diskon Voucher',
            'Total Diskon',
            'Ongkir',
            'Total',
            'Status',
        ];
    }

    public function map($order): array
    {
        $discountDatePromo =
            (int) ($order->discount_date_cantik ?? 0);

        $discountVoucher =
            (int) ($order->discount_voucher ?? 0);

        $totalDiscount =
            $discountDatePromo +
            $discountVoucher;

        $statusLabel = match ($order->status) {

            'pending_payment' =>
                'Menunggu Pembayaran',

            'paid' =>
                'Dibayar',

            'processing' =>
                'Diproses',

            'packing' =>
                'Packing',

            'shipped' =>
                'Dikirim',

            'completed' =>
                'Selesai',

            'cancelled' =>
                'Dibatalkan',

            default =>
                ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        $order->status
                    )
                ),
        };

        return [
            $order->id,

            $order->order_number,

            $order->created_at
                ? $order->created_at->format('d/m/Y H:i')
                : '-',

            $order->user?->name ?? '-',

            $order->items->sum('quantity'),

            $order->subtotal ?? 0,

            $discountDatePromo,

            $discountVoucher,

            $totalDiscount,

            $order->shipping_cost ?? 0,

            $order->final_amount ?? 0,

            $statusLabel,
        ];
    }
}
