<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RefundRequestResource;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        return RefundRequestResource::collection(
            RefundRequest::with('order:id,order_number')
                ->where('user_id', $request->user()->id)
                ->latest()->paginate(10)
        );
    }

    public function show(Request $request, RefundRequest $refundRequest)
    {
        abort_unless($refundRequest->user_id === $request->user()->id, 404);

        return new RefundRequestResource($refundRequest->load('order:id,order_number'));
    }

    public function store(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:20', 'max:2000'],
            'evidence' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ]);

        $path = $validated['evidence']->store('refunds', 'local');
        if ($path === false) {
            abort(500, 'Bukti refund gagal disimpan.');
        }

        try {
            $refund = DB::transaction(function () use ($order, $request, $validated, $path) {
                $order = Order::lockForUpdate()->findOrFail($order->id);
                $payment = $order->payment;

                if (! in_array($order->status, ['paid', 'processing', 'packing', 'shipped', 'completed'], true)
                    || $payment?->payment_status !== 'success') {
                    throw ValidationException::withMessages(['order' => 'Pesanan belum memenuhi syarat refund.']);
                }

                if ($order->refundRequests()->whereNotIn('status', ['rejected', 'cancelled'])->exists()) {
                    throw ValidationException::withMessages(['order' => 'Pesanan sudah memiliki pengajuan refund aktif atau selesai.']);
                }

                return RefundRequest::create([
                    'order_id' => $order->id,
                    'user_id' => $request->user()->id,
                    'reason' => trim($validated['reason']),
                    'evidence_path' => $path,
                    'amount' => $payment->amount,
                    'status' => 'pending',
                ]);
            });
        } catch (Throwable $error) {
            Storage::disk('local')->delete($path);
            throw $error;
        }

        return (new RefundRequestResource($refund->load('order:id,order_number')))
            ->response()->setStatusCode(201);
    }

    public function cancel(Request $request, RefundRequest $refundRequest)
    {
        abort_unless($refundRequest->user_id === $request->user()->id, 404);

        $refund = DB::transaction(function () use ($refundRequest) {
            $refund = RefundRequest::lockForUpdate()->findOrFail($refundRequest->id);
            if (! in_array($refund->status, ['pending', 'cancelled'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Hanya pengajuan yang menunggu tinjauan yang dapat dibatalkan.',
                ]);
            }

            if ($refund->status === 'pending') {
                $refund->update(['status' => 'cancelled']);
            }

            return $refund;
        });

        return new RefundRequestResource($refund->load('order:id,order_number'));
    }

    public function evidence(Request $request, RefundRequest $refundRequest)
    {
        abort_unless($refundRequest->user_id === $request->user()->id, 404);
        abort_unless(Storage::disk('local')->exists($refundRequest->evidence_path), 404);

        return Storage::disk('local')->response($refundRequest->evidence_path);
    }
}
