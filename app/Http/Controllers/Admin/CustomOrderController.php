<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomOrderController extends Controller
{
    /**
     * Menampilkan daftar custom order.
     */
    public function index(Request $request)
    {
        $query = CustomOrder::with([
            'user:id,name,email,phone',
            'order:id,order_number',
            'attachments:id,custom_order_id,file_path,uploaded_by,created_at',
        ]);

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
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

        $customOrders = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Statistik
        $stats = [
            'total' => CustomOrder::count(),

            'pending' => CustomOrder::where('status', 'pending_review')
                ->count(),

            'quoted' => CustomOrder::where('status', 'quoted')
                ->count(),

            'completed' => CustomOrder::whereIn('status', [
                'approved',
                'rejected',
                'cancelled',
            ])->count(),
        ];

        return view('admin.custom-order', compact(
            'customOrders',
            'stats'
        ));
    }

    /**
     * Menampilkan detail custom order.
     *
     * Saat ini detail masih menggunakan modal di index.
     * Method ini tetap disediakan agar nanti mudah dipisahkan
     * menjadi halaman detail jika diperlukan.
     */
    public function show(CustomOrder $customOrder)
    {
        $customOrder->load([
            'user',
            'order',
            'attachments.uploadedBy',
        ]);

        return view('admin.custom-orders.show', compact('customOrder'));
    }

    /**
     * Memberikan / memperbarui estimasi custom order.
     *
     * pending_review -> quoted
     * quoted         -> quoted
     */
    public function quote(Request $request, CustomOrder $customOrder)
    {
        // Admin hanya boleh memberikan estimasi
        if (!in_array($customOrder->status, [
            'pending_review',
            'quoted',
        ])) {
            return back()->with('error', 'Custom Order ini tidak dapat diberikan estimasi.');
        }

        $validated = $request->validate([
            'estimated_price' => [
                'required',
                'integer',
                'min:1',
            ],

            'estimated_time' => [
                'required',
                'string',
                'max:100',
            ],

            'admin_notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ], [
            'estimated_price.required' => 'Estimasi harga wajib diisi.',
            'estimated_price.integer' => 'Estimasi harga harus berupa angka.',
            'estimated_price.min' => 'Estimasi harga minimal Rp1.',

            'estimated_time.required' => 'Estimasi waktu wajib diisi.',
            'estimated_time.max' => 'Estimasi waktu maksimal 100 karakter.',

            'admin_notes.max' => 'Catatan admin maksimal 5000 karakter.',
        ]);

        $customOrder->update([
            'estimated_price' => $validated['estimated_price'],
            'estimated_time' => $validated['estimated_time'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'status' => 'quoted',
        ]);

        return redirect()
            ->route('admin.custom-orders.index')
            ->with('success', 'Estimasi Custom Order berhasil dikirim ke customer.');
    }

    /**
     * Menolak custom order.
     *
     * pending_review -> rejected
     */
    public function reject(Request $request, CustomOrder $customOrder)
    {
        if ($customOrder->status !== 'pending_review') {
            return back()->with('error', 'Custom Order ini tidak dapat ditolak pada status sekarang.');
        }

        $validated = $request->validate([
            'admin_notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ], [
            'admin_notes.max' => 'Alasan penolakan maksimal 5000 karakter.',
        ]);

        $customOrder->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.custom-orders.index')
            ->with('success', 'Custom Order berhasil ditolak.');
    }

    /**
     * Update status secara manual.
     *
     * Sengaja dibatasi karena:
     *
     * - Admin tidak boleh mengubah menjadi approved.
     * - approved merupakan hasil persetujuan customer.
     * - cancelled merupakan pembatalan customer.
     */
    public function updateStatus(Request $request, CustomOrder $customOrder)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending_review,quoted,rejected',
            ],
        ]);

        $allowedTransitions = [
            'pending_review' => [
                'pending_review',
                'quoted',
                'rejected',
            ],

            'quoted' => [
                'quoted',
            ],

            'approved' => [],

            'rejected' => [],

            'cancelled' => [],
        ];

        $currentStatus = $customOrder->status;
        $newStatus = $validated['status'];

        if (!in_array(
            $newStatus,
            $allowedTransitions[$currentStatus] ?? []
        )) {
            return back()->with(
                'error',
                'Perubahan status tidak diperbolehkan.'
            );
        }

        $customOrder->update([
            'status' => $newStatus,
        ]);

        return back()->with(
            'success',
            'Status Custom Order berhasil diperbarui.'
        );
    }
}
