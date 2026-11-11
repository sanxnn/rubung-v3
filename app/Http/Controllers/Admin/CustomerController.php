<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->where('role', 'customer')
            ->with([
                'primaryAddress',
            ])
            ->withCount('orders');

        // Search pelanggan
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Statistik pelanggan
        $stats = [
            'total' => User::where('role', 'customer')->count(),

            'with_orders' => User::where('role', 'customer')
                ->whereHas('orders')
                ->count(),

            'with_addresses' => User::where('role', 'customer')
                ->whereHas('addresses')
                ->count(),
        ];

        return view('admin.customer', compact(
            'customers',
            'stats'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            ...$validated,
            'role' => 'customer',
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Pelanggan dibuat. Pemilik email perlu memverifikasi alamatnya sebelum login.');
    }

    // Memperbarui data pelanggan
    public function update(Request $request, User $customer)
    {
        abort_if($customer->role !== 'customer', 404);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z\s-]+$/',
            ],

            'email' => 'required|string|email|max:100|unique:users,email,'.$customer->id,

            'phone' => 'nullable|string|max:20',

            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.regex' => 'Nama hanya boleh menggunakan huruf, spasi, dan tanda strip (-). Angka dan simbol lainnya tidak diperbolehkan.',
        ]);

        // Hanya update password jika diisi
        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $emailChanged = strcasecmp($customer->email, $validated['email']) !== 0;
        $passwordChanged = isset($validated['password']);

        DB::transaction(function () use ($customer, $validated, $emailChanged, $passwordChanged) {
            if ($emailChanged) {
                EmailOtp::where('user_id', $customer->id)->delete();
            }

            $customer->fill($validated);
            if ($emailChanged) {
                $customer->email_verified_at = null;
            }
            $customer->save();

            if ($emailChanged || $passwordChanged) {
                $customer->tokens()->delete();
            }
        });

        return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    // Menghapus pelanggan
    public function destroy(User $customer)
    {
        abort_if($customer->role !== 'customer', 404);

        if ($customer->orders()->exists()) {
            return back()->withErrors([
                'delete' => 'Pelanggan tidak dapat dihapus karena masih memiliki data pesanan.',
            ]);
        }

        if ($customer->addresses()->exists()) {
            return back()->withErrors([
                'delete' => 'Pelanggan tidak dapat dihapus karena masih memiliki alamat tersimpan.',
            ]);
        }

        if ($customer->customOrders()->exists()) {
            return back()->withErrors([
                'delete' => 'Pelanggan tidak dapat dihapus karena masih memiliki custom order.',
            ]);
        }

        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}
