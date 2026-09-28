@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Daftar Produk</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola produk, varian, dan bundling.</p>
            </div>
            <div class="flex gap-2">
                {{-- TOMBOL BARU: Kelola Paket --}}
                <a href="{{ route('admin.bundles.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-600 text-white font-semibold text-sm shadow-sm transition-all">
                    <i class="fa-solid fa-gift"></i> Kelola Paket
                </a>
                {{-- Tombol lama --}}
                <button type="button" onclick="MicroModal.show('modal-create-product')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#F4C430] hover:bg-[#E0B320] text-[#5A4300] font-semibold text-sm shadow-sm transition-all">
                    <i class="fa-solid fa-plus"></i> Tambah Produk
                </button>
            </div>
        </div>

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

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 p-5">
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..."
                        class="w-full sm:max-w-md rounded-xl border border-gray-200 bg-gray-50 py-3 px-4 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm transition-all"><i
                            class="fa-solid fa-search"></i></button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Produk</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Kategori</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase text-gray-500">Varian</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase text-gray-500">Stok Min.</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $product)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-12 w-12 shrink-0 rounded-lg bg-gray-100 overflow-hidden border border-gray-200">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-gray-400"><i
                                                        class="fa-solid fa-image"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-500 line-clamp-1">
                                                {{ Str::limit($product->description, 50) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $product->category->name }}</p>
                                    @if ($product->subcategory)
                                        <p class="text-xs text-gray-500">{{ $product->subcategory->name }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center justify-center rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-700">
                                        {{ $product->variants->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php $lowStock = $product->lowest_stock; @endphp
                                    <span
                                        class="inline-flex items-center justify-center rounded-lg {{ $lowStock < 5 ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }} px-3 py-1.5 text-sm font-semibold">
                                        {{ $lowStock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.variants.index', $product) }}"
                                            title="Kelola Varian & Paket"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-purple-300 hover:bg-purple-50 hover:text-purple-700">
                                            <i class="fa-solid fa-boxes-stacked text-sm"></i>
                                        </a>
                                        <button type="button"
                                            onclick="MicroModal.show('modal-edit-product-{{ $product->id }}')"
                                            title="Edit"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-yellow-300 hover:bg-yellow-50 hover:text-yellow-700">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>
                                        <button type="button"
                                            onclick="MicroModal.show('modal-delete-product-{{ $product->id }}')"
                                            title="Hapus"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada data produk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ========================================================= --}}
            {{-- PAGINATION SECTION (BARU) --}}
            {{-- ========================================================= --}}
            <div class="border-t border-gray-100 px-5 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-500">
                    Menampilkan <span class="font-medium text-gray-700">{{ $products->firstItem() ?? 0 }}</span>
                    sampai <span class="font-medium text-gray-700">{{ $products->lastItem() ?? 0 }}</span>
                    dari <span class="font-medium text-gray-700">{{ $products->total() }}</span> produk
                </p>
                <div>
                    {{-- appends menjaga parameter search saat pindah halaman --}}
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL 1: CREATE PRODUCT --}}
        {{-- ========================================================= --}}
        <div class="modal micromodal-slide" id="modal-create-product" aria-hidden="true">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                <div class="modal__container w-full max-w-lg bg-white rounded-2xl shadow-xl" role="dialog"
                    aria-modal="true" aria-labelledby="modal-create-product-title">

                    <header class="p-6 border-b border-gray-100 flex justify-between items-start">
                        <h3 class="text-lg font-bold text-gray-900" id="modal-create-product-title">
                            Tambah Produk Baru
                        </h3>

                        <button
                            class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 transition-colors"
                            aria-label="Close modal" data-micromodal-close>
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </header>

                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
                        class="p-6 space-y-4" x-data="{
                            categoryId: '',
                            subcategories: [],
                        
                            categories: {{ Js::from(
                                $categories->map(function ($category) {
                                        return [
                                            'id' => $category->id,
                                            'has_subcategories' => $category->has_subcategories,
                                            'subcategories' => $category->subcategories->map(function ($subcategory) {
                                                    return [
                                                        'id' => $subcategory->id,
                                                        'name' => $subcategory->name,
                                                    ];
                                                })->values(),
                                        ];
                                    })->values(),
                            ) }},
                        
                            changeCategory() {
                                const category = this.categories.find(
                                    category => category.id == this.categoryId
                                );
                        
                                if (category && category.has_subcategories) {
                                    this.subcategories = category.subcategories;
                                } else {
                                    this.subcategories = [];
                                }
                            }
                        }">

                        @csrf

                        <div class="grid grid-cols-2 gap-4">

                            {{-- KATEGORI --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                    Kategori
                                    <span class="text-red-500">*</span>
                                </label>

                                <select name="category_id" required x-model="categoryId" @change="changeCategory()"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">

                                    <option value="">Pilih Kategori</option>

                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            {{-- SUBKATEGORI --}}
                            <div x-show="subcategories.length > 0" x-cloak>
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                    Subkategori
                                </label>

                                <select name="subcategory_id"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">

                                    <option value="">
                                        Pilih Subkategori
                                    </option>

                                    <template x-for="subcategory in subcategories" :key="subcategory.id">
                                        <option :value="subcategory.id" x-text="subcategory.name">
                                        </option>
                                    </template>
                                </select>
                            </div>

                        </div>


                        {{-- NAMA PRODUK --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                Nama Produk
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="text" name="name" required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">
                        </div>


                        {{-- SLUG --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                Slug
                            </label>

                            <input type="text" name="slug"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-mono outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100"
                                placeholder="Kosongkan untuk otomatis">
                        </div>


                        {{-- DESKRIPSI --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                Deskripsi
                            </label>

                            <textarea name="description" rows="3"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100"></textarea>
                        </div>


                        {{-- GAMBAR --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                Gambar Utama
                            </label>

                            <input type="file" name="image" accept="image/*"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-[#B88900] hover:file:bg-yellow-100">
                        </div>


                        {{-- FOOTER --}}
                        <footer class="pt-4 flex gap-3">

                            <button type="button" data-micromodal-close
                                class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200">
                                Batal
                            </button>

                            <button type="submit"
                                class="flex-1 py-3 bg-[#D4A900] hover:bg-[#B88900] text-white rounded-xl font-bold shadow-lg">
                                Simpan Produk
                            </button>

                        </footer>

                    </form>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL EDIT & DELETE (Looping) --}}
        {{-- ========================================================= --}}
        @foreach ($products as $product)
            {{-- MODAL EDIT --}}
            <div class="modal micromodal-slide" id="modal-edit-product-{{ $product->id }}" aria-hidden="true">

                <div class="modal__overlay" tabindex="-1" data-micromodal-close>

                    <div class="modal__container w-full max-w-lg bg-white rounded-2xl shadow-xl" role="dialog"
                        aria-modal="true" aria-labelledby="modal-edit-product-title-{{ $product->id }}">

                        <header class="p-6 border-b border-gray-100 flex justify-between items-start">

                            <h3 class="text-lg font-bold text-gray-900"
                                id="modal-edit-product-title-{{ $product->id }}">
                                Edit Produk
                            </h3>

                            <button
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 transition-colors"
                                aria-label="Close modal" data-micromodal-close>

                                <i class="fa-solid fa-xmark text-lg"></i>

                            </button>

                        </header>


                        <form action="{{ route('admin.products.update', $product) }}" method="POST"
                            enctype="multipart/form-data" class="p-6 space-y-4" x-data="{
                                categoryId: '{{ old('category_id', $product->category_id) }}',
                                subcategoryId: '{{ old('subcategory_id', $product->subcategory_id) }}',
                            
                                categories: {{ Js::from(
                                    $categories->map(function ($category) {
                                            return [
                                                'id' => $category->id,
                                                'has_subcategories' => $category->has_subcategories,
                                                'subcategories' => $category->subcategories->map(function ($subcategory) {
                                                        return [
                                                            'id' => $subcategory->id,
                                                            'name' => $subcategory->name,
                                                        ];
                                                    })->values(),
                                            ];
                                        })->values(),
                                ) }},
                            
                                subcategories: [],
                            
                                init() {
                                    this.changeCategory();
                                },
                            
                                changeCategory() {
                                    const category = this.categories.find(
                                        category => category.id == this.categoryId
                                    );
                            
                                    if (category && category.has_subcategories) {
                                        this.subcategories = category.subcategories;
                                    } else {
                                        this.subcategories = [];
                                        this.subcategoryId = '';
                                    }
                                }
                            }">

                            @csrf
                            @method('PUT')


                            {{-- KATEGORI + SUBKATEGORI --}}
                            <div class="grid grid-cols-2 gap-4">

                                {{-- KATEGORI --}}
                                <div>

                                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                        Kategori
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <select name="category_id" required x-model="categoryId" @change="changeCategory()"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">

                                        <option value="">
                                            Pilih Kategori
                                        </option>

                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>


                                {{-- SUBKATEGORI --}}
                                <div x-show="subcategories.length > 0" x-cloak>

                                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                        Subkategori
                                    </label>

                                    <select name="subcategory_id" x-model="subcategoryId"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">

                                        <option value="">
                                            -- Tidak Ada / Umum --
                                        </option>

                                        <template x-for="subcategory in subcategories" :key="subcategory.id">

                                            <option :value="subcategory.id" x-text="subcategory.name">
                                            </option>

                                        </template>

                                    </select>

                                    <p class="text-[10px] text-gray-400 mt-1">
                                        * Subkategori mengikuti kategori yang dipilih.
                                    </p>

                                </div>

                            </div>


                            {{-- NAMA PRODUK --}}
                            <div>

                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                    Nama Produk
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">

                            </div>


                            {{-- SLUG --}}
                            <div>

                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                    Slug
                                </label>

                                <input type="text" name="slug" value="{{ old('slug', $product->slug) }}"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-mono outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">

                            </div>


                            {{-- DESKRIPSI --}}
                            <div>

                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                    Deskripsi
                                </label>

                                <textarea name="description" rows="3"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">{{ old('description', $product->description) }}</textarea>

                            </div>


                            {{-- GAMBAR --}}
                            <div>

                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                    Gambar Utama
                                    <span class="normal-case font-normal">
                                        (Biarkan kosong jika tidak diubah)
                                    </span>
                                </label>

                                <input type="file" name="image" accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-[#B88900] hover:file:bg-yellow-100">

                                @if ($product->image)
                                    <p class="text-[10px] text-gray-400 mt-1">
                                        Gambar saat ini:
                                        {{ basename($product->image) }}
                                    </p>
                                @endif

                            </div>


                            {{-- FOOTER --}}
                            <footer class="pt-4 flex gap-3">

                                <button type="button" data-micromodal-close
                                    class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200">

                                    Batal

                                </button>

                                <button type="submit"
                                    class="flex-1 py-3 bg-[#D4A900] hover:bg-[#B88900] text-white rounded-xl font-bold shadow-lg">

                                    Perbarui Produk

                                </button>

                            </footer>

                        </form>

                    </div>

                </div>

            </div>


            {{-- MODAL DELETE --}}
            <div class="modal micromodal-slide" id="modal-delete-product-{{ $product->id }}" aria-hidden="true">

                <div class="modal__overlay" tabindex="-1" data-micromodal-close>

                    <div class="modal__container w-full max-w-sm bg-white rounded-2xl shadow-xl text-center"
                        role="dialog" aria-modal="true"
                        aria-labelledby="modal-delete-product-title-{{ $product->id }}">

                        <div class="p-8 pb-4">

                            <div
                                class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-red-50">

                                <i class="fa-solid fa-triangle-exclamation text-3xl"></i>

                            </div>

                            <h2 class="text-xl font-bold text-gray-900 mb-2"
                                id="modal-delete-product-title-{{ $product->id }}">

                                Hapus Produk?

                            </h2>

                            <p class="text-sm text-gray-500">
                                Produk <b>{{ $product->name }}</b> dan semua variannya akan
                                dihapus permanen.
                            </p>

                        </div>


                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                            class="p-6 pt-0 flex gap-3">

                            @csrf
                            @method('DELETE')

                            <button type="button" data-micromodal-close
                                class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200">

                                Batal

                            </button>

                            <button type="submit"
                                class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-lg shadow-red-200">

                                Ya, Hapus

                            </button>

                        </form>

                    </div>

                </div>

            </div>
        @endforeach
    </main>
@endsection
