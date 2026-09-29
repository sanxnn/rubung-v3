<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with([
            'user:id,name,email,phone',
            'items:id,order_id,product_variant_id,product_name,variant_name,quantity,unit_price,subtotal',
        ])->latest();

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where(
                    'order_number',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'recipient_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'email',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'phone',
                                'like',
                                "%{$search}%"
                            );
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER COURIER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('courier')) {
            $query->where(
                'shipping_courier',
                $request->courier
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ORDERS
        |--------------------------------------------------------------------------
        */

        $orders = $query
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | STATISTICS
        |--------------------------------------------------------------------------
        */

        $stats = [
            'total' =>
                Order::count(),

            'pending_payment' =>
                Order::where(
                    'status',
                    'pending_payment'
                )->count(),

            'paid' =>
                Order::where(
                    'status',
                    'paid'
                )->count(),

            'processing' =>
                Order::where(
                    'status',
                    'processing'
                )->count(),

            'packing' =>
                Order::where(
                    'status',
                    'packing'
                )->count(),

            'shipped' =>
                Order::where(
                    'status',
                    'shipped'
                )->count(),

            'completed' =>
                Order::where(
                    'status',
                    'completed'
                )->count(),

            'cancelled' =>
                Order::where(
                    'status',
                    'cancelled'
                )->count(),
        ];

        return view(
            'admin.orders.index',
            compact(
                'orders',
                'stats'
            )
        );
    }

    public function show(Order $order)
{
    $order->load([
        'user:id,name,email,phone',

        'address:id,user_id,label,recipient_name,phone,province,city,district,postal_code,detail',

        'items:id,order_id,product_variant_id,product_name,variant_name,quantity,unit_price,subtotal',

        'statusHistories.changedBy:id,name',
    ]);

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
    | Status berikutnya
    |--------------------------------------------------------------------------
    |
    | pending_payment -> paid
    | paid            -> processing
    | processing      -> packing
    | packing         -> shipped
    | shipped         -> completed
    |
    | pending_payment tidak bisa diubah manual oleh admin.
    | paid didapat dari Midtrans/webhook.
    |
    */

    $nextStatuses = [
        'pending_payment' => null,
        'paid' => 'processing',
        'processing' => 'packing',
        'packing' => 'shipped',
        'shipped' => 'completed',
        'completed' => null,
        'cancelled' => null,
    ];

    $allowedStatusOptions = [];

    $nextStatus = $nextStatuses[$order->status] ?? null;

    if ($nextStatus) {
        $allowedStatusOptions[$nextStatus] = $statusLabels[$nextStatus];
    }

    return view('admin.orders.show', compact(
        'order',
        'statusLabels',
        'allowedStatusOptions'
    ));
}

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        Request $request,
        Order $order
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:processing,packing,shipped,completed,cancelled',
            ],

            'resi_number' => [
                'nullable',
                'string',
                'min:3',
                'max:100',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $newStatus =
            $validated['status'];

        $oldStatus =
            $order->status;

        /*
        |--------------------------------------------------------------------------
        | STATUS TRANSITION
        |--------------------------------------------------------------------------
        */

        $allowedTransitions = [
            'pending_payment' => [
                'cancelled',
            ],

            'paid' => [
                'processing',
                'cancelled',
            ],

            'processing' => [
                'packing',
                'cancelled',
            ],

            'packing' => [
                'shipped',
                'cancelled',
            ],

            'shipped' => [
                'completed',
            ],

            'completed' => [],

            'cancelled' => [],
        ];

        $allowedStatuses =
            $allowedTransitions[$oldStatus]
            ?? [];

        /*
        |--------------------------------------------------------------------------
        | INVALID TRANSITION
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $newStatus,
                $allowedStatuses,
                true
            )
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    "Status tidak dapat diubah dari {$oldStatus} menjadi {$newStatus}."
                );
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS SHIPPED
        |--------------------------------------------------------------------------
        */

        if (
            $newStatus === 'shipped'
        ) {
            $resi =
                trim(
                    $validated['resi_number']
                    ?? ''
                );

            if (
                $resi === ''
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Nomor resi J&T wajib diisi sebelum order dikirim.'
                    );
            }

            $validated['resi_number'] =
                $resi;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $order,
                $oldStatus,
                $newStatus,
                $validated
            ) {
                $updateData = [
                    'status' =>
                        $newStatus,
                ];

                /*
                |--------------------------------------------------------------------------
                | J&T + RESI
                |--------------------------------------------------------------------------
                */

                if (
                    $newStatus === 'shipped'
                ) {
                    $updateData[
                        'shipping_courier'
                    ] = 'jnt';

                    $updateData[
                        'resi_number'
                    ] =
                        $validated[
                            'resi_number'
                        ];
                }

                /*
                |--------------------------------------------------------------------------
                | UPDATE ORDER
                |--------------------------------------------------------------------------
                */

                $order->update(
                    $updateData
                );

                /*
                |--------------------------------------------------------------------------
                | STATUS HISTORY
                |--------------------------------------------------------------------------
                */

                $order
                    ->statusHistories()
                    ->create([
                        'old_status' =>
                            $oldStatus,

                        'new_status' =>
                            $newStatus,

                        'changed_by' =>
                            auth()->id(),

                        'notes' =>
                            $validated[
                                'notes'
                            ] ?? null,
                    ]);
            }
        );

        return back()->with(
            'success',
            'Status order berhasil diperbarui.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE SHIPPING
    |--------------------------------------------------------------------------
    */

    public function updateShipping(
        Request $request,
        Order $order
    ) {
        $validated = $request->validate([
            'shipping_courier' => [
                'required',
                'string',
                'max:50',
            ],

            'resi_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'shipping_cost' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $order->update([
            'shipping_courier' =>
                $validated[
                    'shipping_courier'
                ],

            'resi_number' =>
                $validated[
                    'resi_number'
                ] ?? null,

            'shipping_cost' =>
                $validated[
                    'shipping_cost'
                ],

            'final_amount' =>
                $order->subtotal
                - $order->discount_date_cantik
                - $order->discount_voucher
                + $validated[
                    'shipping_cost'
                ],
        ]);

        return back()->with(
            'success',
            'Informasi pengiriman berhasil diperbarui.'
        );
    }
}
