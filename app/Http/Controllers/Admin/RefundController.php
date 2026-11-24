<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Services\MidtransRefundService;
use App\Services\MidtransRefundUncertainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $query = RefundRequest::with(['order:id,order_number,status', 'user:id,name,email'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return view('admin.refunds.index', [
            'refunds' => $query->paginate(15)->withQueryString(),
            'pendingCount' => RefundRequest::where('status', 'pending')->count(),
        ]);
    }

    public function show(RefundRequest $refundRequest)
    {
        return view('admin.refunds.show', [
            'refund' => $refundRequest->load(['order.payment', 'order.items', 'user', 'reviewer']),
        ]);
    }

    public function evidence(RefundRequest $refundRequest)
    {
        abort_unless(Storage::disk('local')->exists($refundRequest->evidence_path), 404);

        return Storage::disk('local')->response($refundRequest->evidence_path);
    }

    public function approve(Request $request, RefundRequest $refundRequest, MidtransRefundService $midtrans)
    {
        $validated = $request->validate([
            'method' => ['required', 'in:manual,midtrans'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validated['method'] === 'midtrans'
            && ! $midtrans->supports($refundRequest->order->payment?->payment_type ?? '')) {
            throw ValidationException::withMessages([
                'method' => 'Metode pembayaran ini tidak mendukung refund Midtrans. Pilih pengembalian manual.',
            ]);
        }

        DB::transaction(function () use ($refundRequest, $validated) {
            $refund = RefundRequest::lockForUpdate()->findOrFail($refundRequest->id);
            if ($refund->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Pengajuan sudah ditinjau.']);
            }
            $refund->update([
                'status' => 'approved',
                'refund_method' => $validated['method'],
                'refund_key' => 'BRK-REF-'.$refund->id,
                'admin_notes' => $validated['admin_notes'] ?? null,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        if ($validated['method'] === 'midtrans') {
            return $this->processMidtrans($refundRequest, $midtrans);
        }

        return redirect()->route('admin.refunds.show', $refundRequest)
            ->with('success', 'Pengajuan disetujui. Catat transfer setelah dana dikembalikan.');
    }

    public function retry(RefundRequest $refundRequest, MidtransRefundService $midtrans)
    {
        abort_unless($refundRequest->refund_method === 'midtrans'
            && in_array($refundRequest->status, ['approved', 'processing'], true), 422);

        return $this->processMidtrans($refundRequest, $midtrans);
    }

    public function switchToManual(RefundRequest $refundRequest, MidtransRefundService $midtrans)
    {
        abort_unless($refundRequest->status === 'approved'
            && $refundRequest->refund_method === 'midtrans', 422);

        try {
            $midtrans->assertNoRefundRecorded($refundRequest->load('order'));
        } catch (Throwable $error) {
            return redirect()->route('admin.refunds.show', $refundRequest)
                ->with('error', $error->getMessage());
        }

        DB::transaction(function () use ($refundRequest) {
            $refund = RefundRequest::lockForUpdate()->findOrFail($refundRequest->id);
            if ($refund->status !== 'approved' || $refund->refund_method !== 'midtrans') {
                throw ValidationException::withMessages(['status' => 'Status refund sudah berubah.']);
            }
            $refund->update(['refund_method' => 'manual']);
        });

        return redirect()->route('admin.refunds.show', $refundRequest)
            ->with('success', 'Pengembalian dana dialihkan ke proses manual.');
    }

    private function processMidtrans(RefundRequest $refundRequest, MidtransRefundService $midtrans)
    {
        try {
            $result = $midtrans->process($refundRequest->fresh(['order.payment']));
        } catch (MidtransRefundUncertainException $error) {
            DB::transaction(function () use ($refundRequest) {
                $refund = RefundRequest::lockForUpdate()->findOrFail($refundRequest->id);
                if ($refund->status !== 'completed') {
                    $refund->update(['status' => 'processing']);
                }
            });

            return redirect()->route('admin.refunds.show', $refundRequest)
                ->with('error', $error->getMessage());
        } catch (RuntimeException $error) {
            return redirect()->route('admin.refunds.show', $refundRequest)
                ->with('error', $error->getMessage());
        } catch (Throwable $error) {
            DB::transaction(function () use ($refundRequest) {
                $refund = RefundRequest::lockForUpdate()->findOrFail($refundRequest->id);
                if ($refund->status !== 'completed') {
                    $refund->update(['status' => 'processing']);
                }
            });

            report($error);

            return redirect()->route('admin.refunds.show', $refundRequest)
                ->with('error', 'Status refund Midtrans belum pasti. Periksa ulang sebelum tindakan manual.');
        }

        DB::transaction(function () use ($refundRequest, $result) {
            $refund = RefundRequest::lockForUpdate()->findOrFail($refundRequest->id);
            if ($refund->status !== 'completed') {
                $refund->update([
                    'status' => $result,
                    'completed_at' => $result === 'completed' ? now() : null,
                ]);
            }
        });

        return redirect()->route('admin.refunds.show', $refundRequest)
            ->with('success', $result === 'completed'
                ? 'Refund telah dikonfirmasi Midtrans.'
                : 'Refund diterima Midtrans dan menunggu konfirmasi penyedia pembayaran.');
    }

    public function reject(Request $request, RefundRequest $refundRequest)
    {
        $validated = $request->validate([
            'admin_notes' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        DB::transaction(function () use ($refundRequest, $validated) {
            $refund = RefundRequest::lockForUpdate()->findOrFail($refundRequest->id);
            if ($refund->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Pengajuan sudah ditinjau.']);
            }
            $refund->update([
                'status' => 'rejected',
                'admin_notes' => $validated['admin_notes'],
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('admin.refunds.show', $refundRequest)
            ->with('success', 'Pengajuan refund ditolak.');
    }

    public function completeManual(Request $request, RefundRequest $refundRequest)
    {
        $validated = $request->validate([
            'manual_reference' => ['required', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($refundRequest, $validated) {
            $refund = RefundRequest::lockForUpdate()->findOrFail($refundRequest->id);
            if ($refund->status !== 'approved' || $refund->refund_method !== 'manual') {
                throw ValidationException::withMessages(['status' => 'Refund manual tidak dapat diselesaikan pada status ini.']);
            }
            $refund->update([
                'status' => 'completed',
                'manual_reference' => trim($validated['manual_reference']),
                'completed_at' => now(),
            ]);
        });

        return redirect()->route('admin.refunds.show', $refundRequest)
            ->with('success', 'Pengembalian dana manual dicatat selesai.');
    }
}
