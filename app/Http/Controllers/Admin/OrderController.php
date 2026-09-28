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

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter courier
        if ($request->filled('courier')) {
            $query->where('shipping_courier', $request->courier);
        }

        $orders = $query
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => Order::count(),

            'pending_payment' => Order::where(
                'status',
                'pending_payment'
            )->count(),

            'paid' => Order::where(
                'status',
                'paid'
            )->count(),

            'processing' => Order::where(
                'status',
                'processing'
            )->count(),

            'packing' => Order::where(
                'status',
                'packing'
            )->count(),

            'shipped' => Order::where(
                'status',
                'shipped'
            )->count(),

            'completed' => Order::where(
                'status',
                'completed'
            )->count(),

            'cancelled' => Order::where(
                'status',
                'cancelled'
            )->count(),
        ];

        return view('admin.orders.index', compact(
            'orders',
            'stats'
        ));
    }

    public function show(Order $order)
    {
        $order->load([
            'user:id,name,email,phone',
            'items:id,order_id,product_variant_id,product_name,variant_name,quantity,unit_price,subtotal',
            'statusHistories.changedBy:id,name',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(
        Request $request,
        Order $order
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending_payment,paid,processing,packing,shipped,completed,cancelled',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        if ($oldStatus === $newStatus) {
            return back()->with(
                'error',
                'Status order tidak berubah.'
            );
        }

        DB::transaction(function () use (
            $order,
            $oldStatus,
            $newStatus,
            $validated
        ) {
            $order->update([
                'status' => $newStatus,
            ]);

            $order->statusHistories()->create([
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => auth()->id,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return back()->with(
            'success',
            'Status order berhasil diperbarui.'
        );
    }

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
            'shipping_courier' => $validated['shipping_courier'],
            'resi_number' => $validated['resi_number'] ?? null,
            'shipping_cost' => $validated['shipping_cost'],
            'final_amount' => (
                $order->subtotal
                - $order->discount_date_cantik
                - $order->discount_voucher
                + $validated['shipping_cost']
            ),
        ]);

        return back()->with(
            'success',
            'Informasi pengiriman berhasil diperbarui.'
        );
    }
}
