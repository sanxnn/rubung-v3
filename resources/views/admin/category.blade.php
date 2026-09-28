@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Kategori Produk</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola kategori utama untuk mengelompokkan katalog produk Anda.</p>
            </div>

            {{-- TOMBOL CREATE --}}
            <button type="button" onclick="MicroModal.show('modal-create-category')"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#F4C430] hover:bg-[#E0B320] text-[#5A4300] font-semibold text-sm shadow-sm hover:shadow-md transition-all duration-200">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Kategori</span>
            </button>
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

        {{-- Summary Cards (Disesuaikan tanpa 'type') --}}
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            {{-- Card 1: Total --}}
            <div
                class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Kategori</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total'] }}</h3>
                        <p class="mt-1 text-xs text-gray-400">Seluruh kategori tersedia</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-100 text-[#D4A900]">
                        <i class="fa-solid fa-layer-group text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Card 2: Dengan Produk --}}
            <div
                class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Kategori Berproduk</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['with_products'] }}</h3>
                        <p class="mt-1 text-xs text-blue-600"><i class="fa-solid fa-box-open mr-1"></i> Sudah memiliki item
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-box-open text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Card 3: Dengan Subkategori --}}
            <div
                class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Kategori Bersubkategori</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['with_subcategories'] }}</h3>
                        <p class="mt-1 text-xs text-purple-600"><i class="fa-solid fa-sitemap mr-1"></i> Memiliki pembagian
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                        <i class="fa-solid fa-sitemap text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Category List --}}
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            {{-- Toolbar (Filter type dihapus) --}}
            <div class="border-b border-gray-100 p-5">
                <form method="GET" action="{{ route('admin.categories.index') }}"
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative w-full sm:max-w-md">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama atau deskripsi kategori..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm text-gray-700 outline-none transition focus:border-[#F4C430] focus:bg-white focus:ring-2 focus:ring-yellow-100">
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
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Nama &
                                Deskripsi</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Slug</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Produk</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Subkategori</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($categories as $category)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-yellow-50 text-[#D4A900]">
                                            <i class="fa-solid fa-layer-group"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $category->name }}</p>
                                            <p class="mt-1 text-xs text-gray-500 line-clamp-2">
                                                {{ Str::limit($category->description, 70, '...') ?: '<span class="italic text-gray-400">Tidak ada deskripsi</span>' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p
                                        class="text-xs font-mono text-gray-500 bg-gray-50 inline-block px-2 py-1 rounded border border-gray-100">
                                        {{ $category->slug }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex min-w-10 items-center justify-center rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-700">
                                        {{ $category->products_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex min-w-10 items-center justify-center rounded-lg bg-purple-50 px-3 py-1.5 text-sm font-semibold text-purple-700">
                                        {{ $category->subcategories_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        @if ($category->has_subcategories)
                                            <a href="{{ route('admin.subcategories.index', $category) }}"
                                                title="Kelola Subkategori"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-purple-300 hover:bg-purple-50 hover:text-purple-700">
                                                <i class="fa-solid fa-sitemap text-sm"></i>
                                            </a>
                                        @else
                                            <span title="Subkategori tidak diaktifkan"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-100 bg-gray-50 text-gray-300 cursor-not-allowed">
                                                <i class="fa-solid fa-sitemap text-sm"></i>
                                            </span>
                                        @endif

                                        <button type="button" onclick="MicroModal.show('modal-edit-{{ $category->id }}')"
                                            title="Edit"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-yellow-300 hover:bg-yellow-50 hover:text-yellow-700">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>
                                        <button type="button"
                                            onclick="MicroModal.show('modal-delete-{{ $category->id }}')" title="Hapus"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fa-solid fa-folder-open text-3xl mb-3 text-gray-300"></i>
                                    <p class="text-sm">Belum ada data kategori.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="divide-y divide-gray-100 md:hidden">
                @forelse($categories as $category)
                    <div class="p-5">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-yellow-50 text-[#D4A900]">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $category->name }}</p>
                                        <p class="mt-1 text-xs font-mono text-gray-400">{{ $category->slug }}</p>
                                        <p class="mt-2 text-xs text-gray-500 line-clamp-2">
                                            {{ Str::limit($category->description, 60, '...') ?: 'Tidak ada deskripsi' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3">
                                    <div class="flex gap-3 text-xs font-medium text-gray-500">
                                        <span><i class="fa-solid fa-box-open mr-1 text-blue-500"></i>
                                            {{ $category->products_count }} Produk</span>
                                        <span><i class="fa-solid fa-sitemap mr-1 text-purple-500"></i>
                                            {{ $category->subcategories_count }} Sub</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button"
                                            onclick="MicroModal.show('modal-edit-{{ $category->id }}')"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-yellow-300 hover:bg-yellow-50 hover:text-yellow-700">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </button>
                                        <button type="button"
                                            onclick="MicroModal.show('modal-delete-{{ $category->id }}')"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                        @if ($category->has_subcategories)
                                            <a href="{{ route('admin.subcategories.index', $category) }}"
                                                title="Kelola Subkategori"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-purple-300 hover:bg-purple-50 hover:text-purple-700">
                                                <i class="fa-solid fa-sitemap text-sm"></i>
                                            </a>
                                        @else
                                            <span title="Subkategori tidak diaktifkan"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-100 bg-gray-50 text-gray-300 cursor-not-allowed">
                                                <i class="fa-solid fa-sitemap text-sm"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <p class="text-sm">Belum ada data kategori.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Info --}}
            <div class="border-t border-gray-100 px-5 py-4">
                <p class="text-sm text-gray-500">Menampilkan <span
                        class="font-medium text-gray-700">{{ $categories->count() }}</span> kategori</p>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL 1: CREATE CATEGORY --}}
        {{-- ========================================================= --}}
        <div class="modal micromodal-slide" id="modal-create-category" aria-hidden="true">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                <div class="modal__container w-full max-w-md bg-white rounded-2xl shadow-xl" role="dialog"
                    aria-modal="true" aria-labelledby="modal-create-title">
                    <header class="p-6 border-b border-gray-100 flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900" id="modal-create-title">Tambah Kategori Baru</h2>
                            <p class="text-xs text-[#D4A900] mt-1 font-semibold uppercase tracking-wider">Input kategori
                                produk</p>
                        </div>
                        <button
                            class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 transition-colors"
                            aria-label="Close modal" data-micromodal-close>
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </header>
                    <form action="{{ route('admin.categories.store') }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Nama
                                Kategori <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="create_category_name" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-[#F4C430]/50 outline-none transition-all"
                                placeholder="Contoh: Batik Tulis">
                        </div>

                        {{-- FIELD SLUG BARU --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">
                                Slug <span class="text-gray-400 font-normal normal-case tracking-normal">(Opsional)</span>
                            </label>
                            <input type="text" name="slug" id="create_category_slug"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-[#F4C430]/50 outline-none transition-all font-mono text-sm"
                                placeholder="Kosongkan untuk generate otomatis dari Nama">
                            <p class="text-[10px] text-gray-400 mt-1">* Hanya huruf kecil, angka, dan tanda strip (-).</p>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">
                                Deskripsi <span class="text-red-500">*</span></label>
                            </label>
                            <textarea required name="description" rows="3"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-[#F4C430]/50 outline-none transition-all resize-none"
                                placeholder="Jelaskan singkat tentang kategori ini..."></textarea>
                        </div>

                        {{-- SWITCH TOGGLE SUBKATEGORI --}}
                        <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-widest">Aktifkan
                                    Subkategori</label>
                                <p class="text-xs text-gray-500 mt-1">Izinkan kategori ini memiliki subkategori.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="has_subcategories" value="1" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4A900]">
                                </div>
                            </label>
                        </div>
                        <footer class="pt-4 flex gap-3">
                            <button type="button" data-micromodal-close
                                class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold transition-all hover:bg-gray-200">Batal</button>
                            <button type="submit"
                                class="flex-1 py-3 bg-[#D4A900] hover:bg-[#B88900] text-white rounded-xl font-bold shadow-lg shadow-[#F4C430]/30 transition-all">
                                <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Kategori
                            </button>
                        </footer>
                    </form>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL 2 & 3: EDIT & DELETE (Generated per Category) --}}
        {{-- ========================================================= --}}
        @foreach ($categories as $category)
            {{-- Edit Modal --}}
            <div class="modal micromodal-slide" id="modal-edit-{{ $category->id }}" aria-hidden="true">
                <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                    <div class="modal__container w-full max-w-md bg-white rounded-2xl shadow-xl" role="dialog"
                        aria-modal="true" aria-labelledby="modal-edit-title-{{ $category->id }}">
                        <header class="p-6 border-b border-gray-100 flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900" id="modal-edit-title-{{ $category->id }}">
                                    Edit Kategori</h2>
                                <p class="text-xs text-[#D4A900] mt-1 font-semibold uppercase tracking-wider">Update
                                    informasi kategori</p>
                            </div>
                            <button
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 transition-colors"
                                aria-label="Close modal" data-micromodal-close>
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </header>
                        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST"
                            class="p-6 space-y-5">
                            @csrf
                            @method('PUT')
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Nama
                                    Kategori <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="edit_category_name_{{ $category->id }}"
                                    value="{{ $category->name }}" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-[#F4C430]/50 outline-none transition-all">
                            </div>

                            {{-- FIELD SLUG BARU --}}
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">
                                    Slug <span
                                        class="text-gray-400 font-normal normal-case tracking-normal">(Opsional)</span>
                                </label>
                                <input type="text" name="slug" id="edit_category_slug_{{ $category->id }}"
                                    value="{{ $category->slug }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-[#F4C430]/50 outline-none transition-all font-mono text-sm"
                                    placeholder="Kosongkan untuk generate otomatis dari Nama">
                                <p class="text-[10px] text-gray-400 mt-1">* Hanya huruf kecil, angka, dan tanda strip (-).
                                </p>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">
                                    Deskripsi <span class="text-red-500">*</span></label>
                                </label>
                                <textarea required name="description" rows="3"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-[#F4C430]/50 outline-none transition-all resize-none"
                                    placeholder="Jelaskan singkat tentang kategori ini...">{{ $category->description }}</textarea>
                            </div>

                            {{-- SWITCH TOGGLE SUBKATEGORI --}}
                            <div
                                class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-gray-700 uppercase tracking-widest">Aktifkan
                                        Subkategori</label>
                                    <p class="text-xs text-gray-500 mt-1">Izinkan kategori ini memiliki subkategori.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="has_subcategories" value="1" class="sr-only peer"
                                        @checked($category->has_subcategories)>
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4A900]">
                                    </div>
                                </label>
                            </div>
                            <footer class="pt-4 flex gap-3">
                                <button type="button" data-micromodal-close
                                    class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold transition-all hover:bg-gray-200">Batal</button>
                                <button type="submit"
                                    class="flex-1 py-3 bg-[#D4A900] hover:bg-[#B88900] text-white rounded-xl font-bold shadow-lg shadow-[#F4C430]/30 transition-all">
                                    <i class="fa-solid fa-floppy-disk mr-2"></i> Perbarui Kategori
                                </button>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Delete Modal --}}
            <div class="modal micromodal-slide" id="modal-delete-{{ $category->id }}" aria-hidden="true">
                <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                    <div class="modal__container w-full max-w-sm bg-white rounded-2xl shadow-xl text-center"
                        role="dialog" aria-modal="true" aria-labelledby="modal-delete-title-{{ $category->id }}">
                        <div class="p-8 pb-4">
                            <div
                                class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-red-50">
                                <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-2" id="modal-delete-title-{{ $category->id }}">
                                Hapus Kategori?</h2>
                            <p class="text-sm text-gray-500 leading-relaxed">
                                Kategori <b class="text-gray-900">{{ $category->name }}</b> akan dihapus permanen.
                                Pastikan tidak ada produk atau subkategori yang terhubung.
                            </p>
                        </div>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                            class="p-6 pt-0 flex gap-3">
                            @csrf
                            @method('DELETE')
                            <button type="button" data-micromodal-close
                                class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold transition-all hover:bg-gray-200">Batal</button>
                            <button type="submit"
                                class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-lg shadow-red-200 transition-all">
                                <i class="fa-regular fa-trash-can mr-2"></i> Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

    </main>
@endsection


