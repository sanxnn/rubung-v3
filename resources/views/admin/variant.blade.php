@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('admin.products.index') }}" class="hover:text-[#D4A900] transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Produk
            </a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="font-semibold text-gray-900">{{ $product->name }}</span>
        </div>

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Varian Satuan (PCS)</h2>
                <p class="text-sm text-gray-500 mt-1">Atur ukuran, harga, dan stok untuk varian satuan produk ini.</p>
            </div>
            <button type="button" onclick="MicroModal.show('modal-create-variant')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#F4C430] hover:bg-[#E0B320] text-[#5A4300] font-semibold text-sm shadow-sm transition-all">
                <i class="fa-solid fa-plus"></i> Tambah Varian Satuan
            </button>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 shadow-sm flex items-start gap-3">
                <i class="fa-solid fa-circle-check mt-0.5 text-lg text-green-600"></i>
                <div class="flex-1 text-sm font-medium text-green-800">{{ session('success') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation mt-0.5 text-lg text-red-600"></i>
                <ul class="flex-1 list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Nama Varian</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">SKU</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Harga</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase text-gray-500">Stok Tersedia
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($variants as $variant)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700">PCS</span>
                                        <span class="font-semibold text-gray-900">{{ $variant->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-600">{{ $variant->sku }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900">Rp
                                    {{ number_format($variant->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php $stock = $variant->stock; @endphp
                                    <span
                                        class="inline-flex items-center justify-center rounded-lg {{ $stock < 5 ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }} px-3 py-1.5 text-sm font-semibold">
                                        {{ $stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                            onclick="MicroModal.show('modal-edit-variant-{{ $variant->id }}')"
                                            title="Edit"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-yellow-300 hover:bg-yellow-50 hover:text-yellow-700">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>
                                        <button type="button"
                                            onclick="MicroModal.show('modal-delete-variant-{{ $variant->id }}')"
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
                                    Belum ada varian satuan. Tambahkan varian pertama untuk produk ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="border-t border-gray-100 px-5 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-500">
                    Menampilkan <span class="font-medium text-gray-700">{{ $variants->firstItem() ?? 0 }}</span>
                    sampai <span class="font-medium text-gray-700">{{ $variants->lastItem() ?? 0 }}</span>
                    dari <span class="font-medium text-gray-700">{{ $variants->total() }}</span> varian
                </p>
                <div>
                    {{ $variants->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL 1: CREATE VARIANT (Hanya Satuan) --}}
        {{-- ========================================================= --}}
        <div class="modal micromodal-slide" id="modal-create-variant" aria-hidden="true">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                <div class="modal__container w-full max-w-lg bg-white rounded-2xl shadow-xl" role="dialog"
                    aria-modal="true">
                    <header class="p-6 border-b border-gray-100 flex justify-between items-start">
                        <h3 class="text-lg font-bold text-gray-900">Tambah Varian Satuan</h3>
                        <button
                            class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 transition-colors"
                            data-micromodal-close>
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </header>
                    <form action="{{ route('admin.variants.store', $product) }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Nama Varian <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100"
                                placeholder="Contoh: Ukuran L, Warna Merah">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">SKU <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="sku" required
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-mono uppercase outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100"
                                    placeholder="Cth: BRK-L">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Harga (Rp) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="price" required min="0"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Stok Awal <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="stock" required min="0" value="0"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">
                            <p class="text-[10px] text-gray-400 mt-1">* Jumlah stok fisik yang tersedia di gudang.</p>
                        </div>

                        <footer class="pt-4 flex gap-3">
                            <button type="button" data-micromodal-close
                                class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200">Batal</button>
                            <button type="submit"
                                class="flex-1 py-3 bg-[#D4A900] hover:bg-[#B88900] text-white rounded-xl font-bold shadow-lg">Simpan
                                Varian</button>
                        </footer>
                    </form>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL 2 & 3: EDIT & DELETE (Looping) --}}
        {{-- ========================================================= --}}
        @foreach ($variants as $variant)
            {{-- Edit Modal --}}
            <div class="modal micromodal-slide" id="modal-edit-variant-{{ $variant->id }}" aria-hidden="true">
                <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                    <div class="modal__container w-full max-w-lg bg-white rounded-2xl shadow-xl" role="dialog"
                        aria-modal="true">
                        <header class="p-6 border-b border-gray-100 flex justify-between items-start">
                            <h3 class="text-lg font-bold text-gray-900">Edit Varian Satuan</h3>
                            <button
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 transition-colors"
                                data-micromodal-close>
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </header>
                        <form action="{{ route('admin.variants.update', [$product, $variant]) }}" method="POST"
                            class="p-6 space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Nama Varian <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ $variant->name }}" required
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">SKU <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="sku" value="{{ $variant->sku }}" required
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-mono uppercase outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Harga (Rp) <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" name="price" value="{{ $variant->price }}" required
                                        min="0"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-4">
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Stok Awal <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="stock" value="{{ $variant->stock }}" required
                                    min="0"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">
                                <p class="text-[10px] text-gray-400 mt-1">* Jumlah stok fisik yang tersedia di gudang.</p>
                            </div>

                            <footer class="pt-4 flex gap-3">
                                <button type="button" data-micromodal-close
                                    class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200">Batal</button>
                                <button type="submit"
                                    class="flex-1 py-3 bg-[#D4A900] hover:bg-[#B88900] text-white rounded-xl font-bold shadow-lg">Perbarui
                                    Varian</button>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Delete Modal --}}
            <div class="modal micromodal-slide" id="modal-delete-variant-{{ $variant->id }}" aria-hidden="true">
                <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                    <div class="modal__container w-full max-w-sm bg-white rounded-2xl shadow-xl text-center"
                        role="dialog" aria-modal="true">
                        <div class="p-8 pb-4">
                            <div
                                class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-red-50">
                                <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-2">Hapus Varian?</h2>
                            <p class="text-sm text-gray-500">Varian <b>{{ $variant->name }}</b> akan dihapus permanen dari
                                produk ini.</p>
                        </div>
                        <form action="{{ route('admin.variants.destroy', [$product, $variant]) }}" method="POST"
                            class="p-6 pt-0 flex gap-3">
                            @csrf
                            @method('DELETE')
                            <button type="button" data-micromodal-close
                                class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200">Batal</button>
                            <button type="submit"
                                class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-lg shadow-red-200">Ya,
                                Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

    </main>
@endsection
