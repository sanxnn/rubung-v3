@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Paket Bundling</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola paket hemat dan bundling produk.</p>
            </div>
            <button type="button" onclick="MicroModal.show('modal-create-bundle')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#F4C430] hover:bg-[#E0B320] text-[#5A4300] font-semibold text-sm shadow-sm transition-all">
                <i class="fa-solid fa-gift"></i> Buat Paket Baru
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
            <div class="border-b border-gray-100 p-5">
                <form method="GET" action="{{ route('admin.bundles.index') }}" class="flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama paket..."
                        class="w-full sm:max-w-md rounded-xl border border-gray-200 bg-gray-50 py-3 px-4 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm"><i
                            class="fa-solid fa-search"></i></button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Paket</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">SKU</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Harga</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase text-gray-500">Stok Tersedia
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($bundles as $bundle)
                            @php $bundleVariant = $bundle->variants->first(); @endphp
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-12 w-12 shrink-0 rounded-lg bg-purple-50 overflow-hidden border border-purple-100 flex items-center justify-center">
                                            @if ($bundle->image)
                                                <img src="{{ asset('storage/' . $bundle->image) }}"
                                                    class="h-full w-full object-cover">
                                            @else
                                                <i class="fa-solid fa-gift text-purple-400"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-bold text-purple-700">PAKET</span>
                                                <p class="font-bold text-gray-900">{{ $bundle->name }}</p>
                                            </div>
                                            <div class="mt-1 text-xs text-gray-500">
                                                <span class="font-semibold">Isi:</span>
                                                @if ($bundleVariant)
                                                    @foreach ($bundleVariant->bundleComponents as $comp)
                                                        {{ $comp->componentVariant->product->name }} -
                                                        {{ $comp->componentVariant->name }}
                                                        ({{ $comp->quantity }}x)
                                                        {{ !$loop->last ? ',' : '' }}
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-600">{{ $bundleVariant->sku ?? '-' }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900">Rp
                                    {{ number_format($bundleVariant->price ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php $stock = $bundleVariant ? $bundleVariant->available_bundle_stock : 0; @endphp
                                    <span
                                        class="inline-flex items-center justify-center rounded-lg {{ $stock < 5 ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }} px-3 py-1.5 text-sm font-semibold">
                                        {{ $stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                            onclick="MicroModal.show('modal-edit-bundle-{{ $bundle->id }}')"
                                            title="Edit"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-yellow-300 hover:bg-yellow-50 hover:text-yellow-700">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>
                                        <button type="button"
                                            onclick="MicroModal.show('modal-delete-bundle-{{ $bundle->id }}')"
                                            title="Hapus"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada paket bundling.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-5 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-500">
                    Menampilkan <span class="font-medium text-gray-700">{{ $bundles->firstItem() ?? 0 }}</span>
                    sampai <span class="font-medium text-gray-700">{{ $bundles->lastItem() ?? 0 }}</span>
                    dari <span class="font-medium text-gray-700">{{ $bundles->total() }}</span> paket
                </p>
                <div>{{ $bundles->appends(request()->query())->links() }}</div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL CREATE BUNDLE --}}
        {{-- ========================================================= --}}
        <div class="modal micromodal-slide" id="modal-create-bundle" aria-hidden="true">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                <div class="modal__container w-full max-w-2xl bg-white rounded-2xl shadow-xl max-h-[90vh] overflow-y-auto"
                    role="dialog" aria-modal="true">
                    <header
                        class="p-6 border-b border-gray-100 flex justify-between items-start sticky top-0 bg-white z-10">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Buat Paket Bundling Baru</h3>
                            <p class="text-xs text-gray-500 mt-1">Gabungkan beberapa varian produk menjadi satu paket.</p>
                        </div>
                        <button class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100"
                            data-micromodal-close>
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </header>
                    <form action="{{ route('admin.bundles.store') }}" method="POST" enctype="multipart/form-data"
                        class="p-6 space-y-4">
                        @csrf

                        {{-- Info Paket --}}
                        <div class="rounded-xl bg-purple-50 border border-purple-100 p-4">
                            <p class="text-xs font-bold text-purple-700 uppercase mb-3">Informasi Paket</p>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Nama Paket <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" required
                                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100"
                                        placeholder="Contoh: Paket Hemat Lebaran">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">SKU Paket <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="bundle_sku" required
                                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono uppercase outline-none focus:border-[#F4C430]"
                                            placeholder="PKT-HEMAT-01">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Harga Paket
                                            (Rp) <span class="text-red-500">*</span></label>
                                        <input type="number" name="bundle_price" required min="0"
                                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#F4C430]">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Slug</label>
                                    <input type="text" name="slug"
                                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono outline-none focus:border-[#F4C430]"
                                        placeholder="Kosongkan untuk otomatis">
                                </div>
                            </div>
                        </div>

                        {{-- Komponen --}}
                        <div class="rounded-xl bg-gray-50 border border-gray-200 p-4" x-data="bundleComponents()">

                            <div class="flex items-center justify-between mb-3">

                                <div>
                                    <p class="text-xs font-bold text-gray-700 uppercase">
                                        Isi Paket
                                    </p>

                                    <p class="text-[11px] text-gray-500 mt-1">
                                        Tambahkan bahan yang ingin dimasukkan ke dalam paket.
                                    </p>
                                </div>

                                {{-- TOMBOL + --}}
                                <button type="button" @click="addComponent()"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#F4C430] hover:bg-[#E0B320] text-[#5A4300] transition-colors"
                                    title="Tambah komponen">

                                    <i class="fa-solid fa-plus"></i>

                                </button>

                            </div>


                            {{-- LIST KOMPONEN --}}
                            <div class="space-y-2">

                                <template x-for="(component, index) in components" :key="component.id">

                                    <div class="flex gap-2 items-center">

                                        {{-- PRODUK / VARIAN --}}
                                        <select :name="`components[${index}][variant_id]`" x-model="component.variant_id"
                                            required
                                            class="flex-1 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">

                                            <option value="">
                                                -- Pilih Bahan --
                                            </option>

                                            @foreach ($availableComponents as $compVar)
                                                <option value="{{ $compVar->id }}">
                                                    {{ $compVar->product->name }}
                                                    → {{ $compVar->name }}
                                                    (Stok: {{ $compVar->stock }})
                                                </option>
                                            @endforeach

                                        </select>


                                        {{-- JUMLAH --}}
                                        <input type="number" :name="`components[${index}][quantity]`"
                                            x-model="component.quantity" min="1" required
                                            class="w-20 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-center outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">


                                        {{-- HAPUS --}}
                                        <button type="button" @click="removeComponent(index)"
                                            :disabled="components.length === 1"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-400 hover:border-red-200 hover:bg-red-50 hover:text-red-600 disabled:opacity-30 disabled:cursor-not-allowed"
                                            title="Hapus komponen">

                                            <i class="fa-solid fa-minus"></i>

                                        </button>

                                    </div>

                                </template>

                            </div>


                            {{-- EMPTY INFO --}}
                            <p class="text-[10px] text-gray-400 mt-3">
                                * Komponen hanya dapat dipilih dari kategori <b>Bahan</b>.
                                Klik <b>+</b> untuk menambah isi paket.
                            </p>

                        </div>

                        {{-- Opsional --}}
                        <details class="rounded-xl border border-gray-200">
                            <summary class="cursor-pointer px-4 py-3 text-xs font-bold text-gray-500 uppercase">Opsional
                                (Deskripsi, Gambar)</summary>
                            <div class="p-4 pt-0 space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Deskripsi</label>
                                    <textarea name="description" rows="2"
                                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#F4C430]"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Gambar
                                        Paket</label>
                                    <input type="file" name="image" accept="image/*"
                                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-[#B88900] hover:file:bg-yellow-100">
                                </div>
                            </div>
                        </details>

                        <footer class="pt-4 flex gap-3 sticky bottom-0 bg-white pb-2 border-t border-gray-100">
                            <button type="button" data-micromodal-close
                                class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200">Batal</button>
                            <button type="submit"
                                class="flex-1 py-3 bg-[#D4A900] hover:bg-[#B88900] text-white rounded-xl font-bold shadow-lg">
                                <i class="fa-solid fa-gift mr-2"></i> Buat Paket
                            </button>
                        </footer>
                    </form>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL EDIT & DELETE BUNDLE (Looping) --}}
        {{-- ========================================================= --}}
        @foreach ($bundles as $bundle)
            @php $bundleVariant = $bundle->variants->first(); @endphp

            {{-- Edit Modal --}}
            {{-- Edit Modal --}}
            <div class="modal micromodal-slide" id="modal-edit-bundle-{{ $bundle->id }}" aria-hidden="true">
                <div class="modal__overlay" tabindex="-1" data-micromodal-close>

                    <div class="modal__container w-full max-w-2xl bg-white rounded-2xl shadow-xl max-h-[90vh] overflow-y-auto"
                        role="dialog" aria-modal="true">

                        {{-- Header --}}
                        <header
                            class="p-6 border-b border-gray-100 flex justify-between items-start sticky top-0 bg-white z-10">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    Edit Paket Bundling
                                </h3>

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $bundle->name }}
                                </p>
                            </div>

                            <button type="button"
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100"
                                data-micromodal-close>

                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </header>

                        <form action="{{ route('admin.bundles.update', $bundle) }}" method="POST"
                            enctype="multipart/form-data" class="p-6 space-y-4">

                            @csrf
                            @method('PUT')

                            {{-- Informasi Paket --}}
                            <div class="rounded-xl bg-purple-50 border border-purple-100 p-4">

                                <p class="text-xs font-bold text-purple-700 uppercase mb-3">
                                    Informasi Paket
                                </p>

                                <div class="space-y-3">

                                    {{-- Nama --}}
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                            Nama Paket
                                            <span class="text-red-500">*</span>
                                        </label>

                                        <input type="text" name="name" value="{{ $bundle->name }}" required
                                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">
                                    </div>

                                    {{-- SKU + Harga --}}
                                    <div class="grid grid-cols-2 gap-3">

                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                                SKU Paket
                                                <span class="text-red-500">*</span>
                                            </label>

                                            <input type="text" name="bundle_sku"
                                                value="{{ $bundleVariant->sku ?? '' }}" required
                                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono uppercase outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100"
                                                placeholder="PKT-HEMAT-01">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                                Harga Paket (Rp)
                                                <span class="text-red-500">*</span>
                                            </label>

                                            <input type="number" name="bundle_price"
                                                value="{{ $bundleVariant->price ?? 0 }}" required min="0"
                                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">
                                        </div>

                                    </div>

                                    {{-- Slug --}}
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                            Slug
                                        </label>

                                        <input type="text" name="slug" value="{{ $bundle->slug }}"
                                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100"
                                            placeholder="Kosongkan untuk otomatis">
                                    </div>

                                </div>
                            </div>

                            {{-- Komponen Paket --}}
                            <div class="rounded-xl bg-gray-50 border border-gray-200 p-4" x-data="editBundleComponents(
                                {{ $bundleVariant
                                    ? $bundleVariant->bundleComponents->map(function ($comp) {
                                            return [
                                                'id' => $comp->id,
                                                'variant_id' => (string) $comp->component_variant_id,
                                                'quantity' => $comp->quantity,
                                            ];
                                        })->values()->toJson()
                                    : '[]' }}
                            )">

                                {{-- Header --}}
                                <div class="flex items-center justify-between mb-3">

                                    <div>
                                        <p class="text-xs font-bold text-gray-700 uppercase">
                                            Isi Paket
                                        </p>

                                        <p class="text-[11px] text-gray-500 mt-1">
                                            Atur bahan yang termasuk dalam paket.
                                        </p>
                                    </div>

                                    <button type="button" @click="addComponent()"
                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#F4C430] hover:bg-[#E0B320] text-[#5A4300] transition-colors"
                                        title="Tambah komponen">

                                        <i class="fa-solid fa-plus"></i>
                                    </button>

                                </div>

                                {{-- LIST KOMPONEN --}}
                                <div class="space-y-2">

                                    <template x-for="(component, index) in components" :key="component.id">

                                        <div class="flex gap-2 items-center">

                                            {{-- Bahan --}}
                                            <select :name="`components[${index}][variant_id]`"
                                                x-model="component.variant_id" required
                                                class="flex-1 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">

                                                <option value="">
                                                    -- Pilih Bahan --
                                                </option>

                                                @foreach ($availableComponents as $compVar)
                                                    <option value="{{ $compVar->id }}">
                                                        {{ $compVar->product->name }}
                                                        → {{ $compVar->name }}
                                                        (Stok: {{ $compVar->stock }})
                                                    </option>
                                                @endforeach

                                            </select>

                                            {{-- Quantity --}}
                                            <input type="number" :name="`components[${index}][quantity]`"
                                                x-model="component.quantity" min="1" required
                                                class="w-20 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-center outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">

                                            {{-- Hapus --}}
                                            <button type="button" @click="removeComponent(index)"
                                                :disabled="components.length === 1"
                                                class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-400 hover:border-red-200 hover:bg-red-50 hover:text-red-600 disabled:opacity-30 disabled:cursor-not-allowed"
                                                title="Hapus komponen">

                                                <i class="fa-solid fa-minus"></i>

                                            </button>

                                        </div>

                                    </template>

                                </div>

                                {{-- Info --}}
                                <p class="text-[10px] text-gray-400 mt-3">
                                    * Komponen hanya dapat dipilih dari kategori
                                    <b>Bahan</b>.
                                    Gunakan tombol <b>+</b> untuk menambah bahan.
                                </p>

                            </div>

                            {{-- Opsional --}}
                            <details class="rounded-xl border border-gray-200">

                                <summary class="cursor-pointer px-4 py-3 text-xs font-bold text-gray-500 uppercase">
                                    Opsional (Deskripsi, Gambar)
                                </summary>

                                <div class="p-4 pt-0 space-y-3">

                                    {{-- Deskripsi --}}
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                            Deskripsi
                                        </label>

                                        <textarea name="description" rows="3"
                                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#F4C430] focus:ring-2 focus:ring-yellow-100">{{ $bundle->description }}</textarea>
                                    </div>

                                    {{-- Gambar --}}
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">
                                            Gambar Paket
                                        </label>

                                        @if ($bundle->image)
                                            <div class="mb-3 flex items-center gap-3">

                                                <img src="{{ asset('storage/' . $bundle->image) }}"
                                                    class="h-16 w-16 rounded-xl object-cover border border-gray-200">

                                                <p class="text-xs text-gray-500">
                                                    Gambar saat ini
                                                </p>

                                            </div>
                                        @endif

                                        <input type="file" name="image" accept="image/*"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-[#B88900] hover:file:bg-yellow-100">
                                    </div>

                                </div>
                            </details>

                            {{-- Footer --}}
                            <footer class="pt-4 flex gap-3 sticky bottom-0 bg-white pb-2 border-t border-gray-100">

                                <button type="button" data-micromodal-close
                                    class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200">

                                    Batal

                                </button>

                                <button type="submit"
                                    class="flex-1 py-3 bg-[#D4A900] hover:bg-[#B88900] text-white rounded-xl font-bold shadow-lg">

                                    <i class="fa-solid fa-floppy-disk mr-2"></i>
                                    Simpan Perubahan

                                </button>

                            </footer>

                        </form>

                    </div>
                </div>
            </div>

            {{-- Delete Modal --}}
            <div class="modal micromodal-slide" id="modal-delete-bundle-{{ $bundle->id }}" aria-hidden="true">
                <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                    <div class="modal__container w-full max-w-sm bg-white rounded-2xl shadow-xl text-center"
                        role="dialog" aria-modal="true">
                        <div class="p-8 pb-4">
                            <div
                                class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-red-50">
                                <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-2">Hapus Paket?</h2>
                            <p class="text-sm text-gray-500">Paket <b>{{ $bundle->name }}</b> akan dihapus permanen.</p>
                        </div>
                        <form action="{{ route('admin.bundles.destroy', $bundle) }}" method="POST"
                            class="p-6 pt-0 flex gap-3">
                            @csrf @method('DELETE')
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

@push('script')
    <script>
        function bundleComponents() {
            return {
                components: [{
                    id: Date.now(),
                    variant_id: '',
                    quantity: 1
                }],

                addComponent() {
                    this.components.push({
                        id: Date.now() + Math.random(),
                        variant_id: '',
                        quantity: 1
                    });
                },

                removeComponent(index) {
                    if (this.components.length <= 1) {
                        return;
                    }

                    this.components.splice(index, 1);
                }
            }
        }

        function editBundleComponents(existingComponents) {
            return {
                components: existingComponents.length > 0 ?
                    existingComponents :
                    [{
                        id: Date.now(),
                        variant_id: '',
                        quantity: 1
                    }],

                addComponent() {
                    this.components.push({
                        id: Date.now() + Math.random(),
                        variant_id: '',
                        quantity: 1
                    });
                },

                removeComponent(index) {
                    if (this.components.length <= 1) {
                        return;
                    }

                    this.components.splice(index, 1);
                }
            }
        }
    </script>
@endpush
