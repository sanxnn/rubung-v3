<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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


    // Menyimpan pelanggan baru
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users,email',
    //         'phone' => 'nullable|string|max:20',
    //         'password' => 'required|string|min:8|confirmed',
    //     ]);

    //     $validated['password'] = Hash::make($validated['password']);
    //     $validated['role'] = 'customer';

    //     User::create($validated);

    //     return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    // }


    // Memperbarui data pelanggan
    public function update(Request $request, User $customer)
    {
        abort_if($customer->role !== 'customer', 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Hanya update password jika diisi
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    // Menghapus pelanggan
    public function destroy(User $customer)
    {
        abort_if($customer->role !== 'customer', 404);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
