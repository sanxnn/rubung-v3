@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
            <a href="{{ route('admin.categories.index') }}" class="hover:text-[#D4A900] transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kategori
            </a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="font-semibold text-gray-900">{{ $category->name }}</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Subkategori: {{ $category->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola subkategori untuk kategori "{{ $category->name }}"</p>
            </div>
            <button type="button" onclick="MicroModal.show('modal-create-subcategory')"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#F4C430] hover:bg-[#E0B320] text-[#5A4300] font-semibold text-sm shadow-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Subkategori</span>
            </button>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 shadow-sm">
                <div class="text-sm font-medium text-green-800">{{ session('success') }}</div>
            </div>
        @endif

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr class="border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Slug</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Produk</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subcategories as $sub)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">{{ $sub->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($sub->description, 60) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs font-mono text-gray-500 bg-gray-50 inline-block px-2 py-1 rounded">
                                    {{ $sub->slug }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex min-w-10 items-center justify-center rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-700">
                                    {{ $sub->products_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <button onclick="MicroModal.show('modal-edit-{{ $sub->id }}')"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-yellow-300 hover:bg-yellow-50 hover:text-yellow-700">
                                        <i class="fa-solid fa-pen text-sm"></i>
                                    </button>
                                    <button onclick="MicroModal.show('modal-delete-{{ $sub->id }}')"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                        <i class="fa-regular fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <i class="fa-solid fa-folder-open text-3xl mb-3 text-gray-300"></i>
                                <p class="text-sm">Belum ada subkategori.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Modal Create Subcategory --}}
        <div class="modal micromodal-slide" id="modal-create-subcategory" aria-hidden="true">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                <div class="modal__container w-full max-w-md bg-white rounded-2xl shadow-xl" role="dialog">
                    <header class="p-6 border-b border-gray-100 flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Tambah Subkategori</h2>
                            <p class="text-xs text-[#D4A900] mt-1 font-semibold uppercase">Untuk {{ $category->name }}</p>
                        </div>
                        <button data-micromodal-close class="w-9 h-9 rounded-xl text-gray-400 hover:bg-gray-100">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </header>
                    <form action="{{ route('admin.subcategories.store', $category) }}" method="POST"
                        class="p-6 space-y-5">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase">Nama Subkategori <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" id="create_sub_name" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#F4C430]/50 outline-none">
                        </div>

                        {{-- FIELD SLUG BARU --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase">
                                Slug <span class="text-gray-400 font-normal normal-case">(Opsional)</span>
                            </label>
                            <input type="text" name="slug" id="create_sub_slug"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 font-mono text-sm focus:ring-2 focus:ring-[#F4C430]/50 outline-none"
                                placeholder="Kosongkan untuk generate otomatis">
                        </div>

 
                        <footer class="pt-4 flex gap-3">
                            <button type="button" data-micromodal-close
                                class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200">Batal</button>
                            <button type="submit"
                                class="flex-1 py-3 bg-[#D4A900] hover:bg-[#B88900] text-white rounded-xl font-bold shadow-lg">Simpan</button>
                        </footer>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Edit & Delete per subcategory --}}
        @foreach ($subcategories as $sub)
            <div class="modal micromodal-slide" id="modal-edit-{{ $sub->id }}" aria-hidden="true">
                <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                    <div class="modal__container w-full max-w-md bg-white rounded-2xl shadow-xl" role="dialog">
                        <header class="p-6 border-b border-gray-100 flex justify-between items-start">
                            <h2 class="text-xl font-bold text-gray-900">Edit Subkategori</h2>
                            <button data-micromodal-close class="w-9 h-9 rounded-xl text-gray-400 hover:bg-gray-100">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </header>
                        <form action="{{ route('admin.subcategories.update', ['category' => $category, 'subcategory' => $sub]) }}" method="POST"
                            class="p-6 space-y-5">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase">Nama <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ $sub->name }}" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#F4C430]/50 outline-none">
                            </div>

                            {{-- FIELD SLUG BARU --}}
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase">
                                    Slug <span class="text-gray-400 font-normal normal-case">(Opsional)</span>
                                </label>
                                <input type="text" name="slug" value="{{ $sub->slug }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 font-mono text-sm focus:ring-2 focus:ring-[#F4C430]/50 outline-none"
                                    placeholder="Kosongkan untuk generate otomatis">
                            </div>

                            <footer class="pt-4 flex gap-3">
                                <button type="button" data-micromodal-close
                                    class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold">Batal</button>
                                <button type="submit"
                                    class="flex-1 py-3 bg-[#D4A900] hover:bg-[#B88900] text-white rounded-xl font-bold">Perbarui</button>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal micromodal-slide" id="modal-delete-{{ $sub->id }}" aria-hidden="true">
                <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                    <div class="modal__container w-full max-w-sm bg-white rounded-2xl shadow-xl text-center"
                        role="dialog">
                        <div class="p-8 pb-4">
                            <div
                                class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-2">Hapus Subkategori?</h2>
                            <p class="text-sm text-gray-500">Subkategori <b>{{ $sub->name }}</b> akan dihapus permanen.
                            </p>
                        </div>
                        <form action="{{ route('admin.subcategories.destroy', ['category' => $category, 'subcategory' => $sub]) }}" method="POST"
                            class="p-6 pt-0 flex gap-3">
                            @csrf
                            @method('DELETE')
                            <button type="button" data-micromodal-close
                                class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold">Batal</button>
                            <button type="submit"
                                class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold">Ya,
                                Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </main>
@endsection
