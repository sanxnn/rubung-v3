@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8" style="font-family: 'Poppins', sans-serif;">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Manajemen Pelanggan</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data pelanggan yang terdaftar dalam sistem aplikasi Anda.</p>
            </div>

            {{-- TOMBOL CREATE --}}
            {{-- <button type="button" onclick="MicroModal.show('modal-create-customer')"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm hover:shadow-md transition-all duration-200">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Pelanggan</span>
            </button> --}}
        </div>

        {{-- Alert Section --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 shadow-sm flex items-start gap-3">
                <i class="fa-solid fa-circle-check mt-0.5 text-lg text-green-600"></i>
                <div class="flex-1 text-sm font-medium text-green-800">{{ session('success') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation mt-0.5 text-lg text-red-600"></i>
                <div class="flex-1">
                    <p class="text-sm font-bold text-red-800 mb-1">Terjadi kesalahan:</p>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            {{-- Card 1: Total --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Pelanggan</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</h3>
                        <p class="mt-1 text-xs text-gray-400">Seluruh pelanggan terdaftar</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-users text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Card 2: Dengan Pesanan --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Sudah Bertransaksi</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['with_orders'] ?? 0 }}</h3>
                        <p class="mt-1 text-xs text-indigo-600"><i class="fa-solid fa-shopping-bag mr-1"></i> Memiliki riwayat pesanan</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <i class="fa-solid fa-shopping-bag text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Card 3: Dengan Alamat --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Memiliki Alamat</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['with_addresses'] ?? 0 }}</h3>
                        <p class="mt-1 text-xs text-emerald-600"><i class="fa-solid fa-map-location-dot mr-1"></i> Data alamat lengkap</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-map-location-dot text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer List --}}
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            {{-- Toolbar (Search) --}}
            <div class="border-b border-gray-100 p-5">
                <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative w-full sm:max-w-md">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama, email, atau telepon..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm text-gray-700 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                    </div>
                    <button type="submit"
                        class="hidden sm:inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm transition-all">
                        <i class="fa-solid fa-filter"></i>
                        <span>Filter</span>
                    </button>
                </form>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Nama & Email</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Telepon</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Alamat Utama</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Pesanan</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($customers as $customer)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                            <i class="fa-solid fa-user-circle text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $customer->name }}</p>
                                            <p class="mt-1 text-xs text-gray-500">{{ $customer->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-700">{{ $customer->phone ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-500 line-clamp-2">
                                        {{ $customer->primaryAddress ? $customer->primaryAddress->full_address : '<span class="italic text-gray-400">Belum ada alamat</span>' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex min-w-10 items-center justify-center rounded-lg bg-indigo-50 px-3 py-1.5 text-sm font-semibold text-indigo-700">
                                        {{ $customer->orders->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" onclick="MicroModal.show('modal-detail-{{ $customer->id }}')"
                                            title="Lihat Detail"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </button>
                                        <button type="button" onclick="MicroModal.show('modal-edit-{{ $customer->id }}')"
                                            title="Edit"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-yellow-300 hover:bg-yellow-50 hover:text-yellow-700">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>
                                        <button type="button" onclick="MicroModal.show('modal-delete-{{ $customer->id }}')"
                                            title="Hapus"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fa-solid fa-users-slash text-3xl mb-3 text-gray-300"></i>
                                    <p class="text-sm">Belum ada data pelanggan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="divide-y divide-gray-100 md:hidden">
                @forelse($customers as $customer)
                    <div class="p-5">
                        <div class="flex items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <i class="fa-solid fa-user-circle"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $customer->name }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ $customer->email }}</p>
                                        <p class="mt-2 text-xs text-gray-500 line-clamp-2">
                                            {{ $customer->primaryAddress ? $customer->primaryAddress->full_address : 'Belum ada alamat' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3">
                                    <div class="flex gap-3 text-xs font-medium text-gray-500">
                                        <span><i class="fa-solid fa-phone mr-1 text-gray-400"></i> {{ $customer->phone ?? '-' }}</span>
                                        <span><i class="fa-solid fa-shopping-bag mr-1 text-indigo-500"></i> {{ $customer->orders->count() }} Pesanan</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" onclick="MicroModal.show('modal-detail-{{ $customer->id }}')"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </button>
                                        <button type="button" onclick="MicroModal.show('modal-edit-{{ $customer->id }}')"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-yellow-300 hover:bg-yellow-50 hover:text-yellow-700">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </button>
                                        <button type="button" onclick="MicroModal.show('modal-delete-{{ $customer->id }}')"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <p class="text-sm">Belum ada data pelanggan.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Info --}}
            <div class="border-t border-gray-100 px-5 py-4">
                <p class="text-sm text-gray-500">Menampilkan <span class="font-medium text-gray-700">{{ $customers->count() }}</span> pelanggan</p>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL 1: CREATE CUSTOMER --}}
        {{-- ========================================================= --}}
        {{-- <div class="modal micromodal-slide" id="modal-create-customer" aria-hidden="true">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                <div class="modal__container w-full max-w-md bg-white rounded-2xl shadow-xl" role="dialog" aria-modal="true" aria-labelledby="modal-create-title">
                    <header class="p-6 border-b border-gray-100 flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900" id="modal-create-title">Tambah Pelanggan Baru</h2>
                            <p class="text-xs text-blue-600 mt-1 font-semibold uppercase tracking-wider">Input data akun pelanggan</p>
                        </div>
                        <button class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 transition-colors" aria-label="Close modal" data-micromodal-close>
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </header>
                    <form action="{{ route('admin.customers.store') }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all" placeholder="Contoh: Muhammad Hasan">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all" placeholder="contoh@email.com">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Telepon <span class="text-gray-400 font-normal normal-case tracking-normal">(Opsional)</span></label>
                            <input type="text" name="phone" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all" placeholder="0812xxxxxxx">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Konfirmasi <span class="text-red-500">*</span></label>
                                <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                        </div>

                        <footer class="pt-4 flex gap-3">
                            <button type="button" data-micromodal-close class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold transition-all hover:bg-gray-200">Batal</button>
                            <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-200 transition-all">
                                <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Pelanggan
                            </button>
                        </footer>
                    </form>
                </div>
            </div>
        </div> --}}

        {{-- ========================================================= --}}
        {{-- MODAL 2, 3, 4: DETAIL, EDIT & DELETE (Generated per Customer) --}}
        {{-- ========================================================= --}}
        @foreach ($customers as $customer)

            {{-- Detail Modal --}}
            <div class="modal micromodal-slide" id="modal-detail-{{ $customer->id }}" aria-hidden="true">
                <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                    <div class="modal__container w-full max-w-md bg-white rounded-2xl shadow-xl" role="dialog" aria-modal="true" aria-labelledby="modal-detail-title-{{ $customer->id }}">
                        <header class="p-6 border-b border-gray-100 flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900" id="modal-detail-title-{{ $customer->id }}">Detail Pelanggan</h2>
                                <p class="text-xs text-blue-600 mt-1 font-semibold uppercase tracking-wider">Informasi lengkap akun</p>
                            </div>
                            <button class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 transition-colors" aria-label="Close modal" data-micromodal-close>
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </header>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                                <div class="h-14 w-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl font-bold">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-lg">{{ $customer->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $customer->email }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4 text-sm">
                                <div>
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Telepon</p>
                                    <p class="text-gray-800 font-medium">{{ $customer->phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Alamat Utama</p>
                                    <p class="text-gray-800 font-medium">{{ $customer->primaryAddress ? $customer->primaryAddress->full_address : 'Belum ada alamat' }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pesanan</p>
                                    <span class="inline-flex items-center justify-center rounded-lg bg-indigo-50 px-3 py-1.5 text-sm font-semibold text-indigo-700">
                                        {{ $customer->orders->count() }} Pesanan
                                    </span>
                                </div>
                            </div>
                        </div>
                        <footer class="p-6 pt-0 flex gap-3">
                            <button type="button" data-micromodal-close class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold transition-all hover:bg-gray-200">Tutup</button>
                            <button type="button" onclick="MicroModal.close('modal-detail-{{ $customer->id }}'); MicroModal.show('modal-edit-{{ $customer->id }}')" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-200 transition-all">
                                <i class="fa-solid fa-pen mr-2"></i> Edit Data
                            </button>
                        </footer>
                    </div>
                </div>
            </div>

            {{-- Edit Modal --}}
            <div class="modal micromodal-slide" id="modal-edit-{{ $customer->id }}" aria-hidden="true">
                <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                    <div class="modal__container w-full max-w-md bg-white rounded-2xl shadow-xl" role="dialog" aria-modal="true" aria-labelledby="modal-edit-title-{{ $customer->id }}">
                        <header class="p-6 border-b border-gray-100 flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900" id="modal-edit-title-{{ $customer->id }}">Edit Pelanggan</h2>
                                <p class="text-xs text-blue-600 mt-1 font-semibold uppercase tracking-wider">Perbarui informasi akun</p>
                            </div>
                            <button class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 transition-colors" aria-label="Close modal" data-micromodal-close>
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </header>
                        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="p-6 space-y-5">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $customer->email) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Telepon</label>
                                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Password <span class="text-gray-400 font-normal normal-case tracking-normal">(Kosongkan jika tidak diubah)</span></label>
                                    <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Konfirmasi</label>
                                    <input type="password" name="password_confirmation" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                                </div>
                            </div>

                            <footer class="pt-4 flex gap-3">
                                <button type="button" data-micromodal-close class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold transition-all hover:bg-gray-200">Batal</button>
                                <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-200 transition-all">
                                    <i class="fa-solid fa-floppy-disk mr-2"></i> Perbarui Data
                                </button>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Delete Modal --}}
            <div class="modal micromodal-slide" id="modal-delete-{{ $customer->id }}" aria-hidden="true">
                <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                    <div class="modal__container w-full max-w-sm bg-white rounded-2xl shadow-xl text-center" role="dialog" aria-modal="true" aria-labelledby="modal-delete-title-{{ $customer->id }}">
                        <div class="p-8 pb-4">
                            <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-red-50">
                                <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-2" id="modal-delete-title-{{ $customer->id }}">Hapus Pelanggan?</h2>
                            <p class="text-sm text-gray-500 leading-relaxed">
                                Akun <b class="text-gray-900">{{ $customer->name }}</b> akan dihapus permanen beserta data terkait. Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                        <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="p-6 pt-0 flex gap-3">
                            @csrf
                            @method('DELETE')
                            <button type="button" data-micromodal-close class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold transition-all hover:bg-gray-200">Batal</button>
                            <button type="submit" class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-lg shadow-red-200 transition-all">
                                <i class="fa-regular fa-trash-can mr-2"></i> Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        @endforeach

    </main>
@endsection
