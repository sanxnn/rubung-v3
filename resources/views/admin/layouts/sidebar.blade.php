<aside id="sidebar"
    class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
    <div class="h-full flex flex-col">

        {{-- Brand --}}
        <div class="h-20 px-6 flex items-center border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#F4C430] flex items-center justify-center">
                    <i class="fa-solid fa-leaf text-[#5A4300]"></i>
                </div>
                <div>
                    <h1 class="font-bold text-sm text-[#292929]">
                        Batik Rubung
                    </h1>
                    <p class="text-xs text-gray-400">
                        Kuning
                    </p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">

            <p class="px-3 mb-3 text-[10px] font-semibold uppercase tracking-widest text-gray-400">
                Main Menu
            </p>

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition text-sm
                {{ request()->routeIs('admin.dashboard') ? 'bg-[#FFF8D8] text-[#A47B00]' : '' }}">
                <i class="fa-solid fa-chart-bar w-5 text-center"></i>
                <span>Dashboard</span>
            </a>

            {{-- Pesanan (Orders) --}}
            <a href="{{ route('admin.orders.index') }}"
                class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm
                {{ request()->routeIs('admin.orders.index') ? 'bg-[#FFF8D8] text-[#A47B00]' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-bag-shopping w-5 text-center"></i>
                    <span>Pesanan</span>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-500 text-[10px] font-semibold">
                    8
                </span>
            </a>

            {{-- Custom Order (Custom PO) --}}
            <a href="{{ route('admin.custom-orders.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition text-sm
                {{ request()->routeIs('admin.custom-orders.index') ? 'bg-[#FFF8D8] text-[#A47B00]' : '' }}">
                <i class="fa-solid fa-wand-magic-sparkles w-5 text-center"></i>
                <span>Custom Order</span>
            </a>

            {{-- Kategori --}}
            <a href="{{ route('admin.categories.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition text-sm
                {{ request()->routeIs('admin.categories.index') ? 'bg-[#FFF8D8] text-[#A47B00]' : '' }}">
                <i class="fa-solid fa-layer-group w-5 text-center"></i>
                <span>Kategori</span>
            </a>

            {{-- Produk --}}
            <a href="{{ route('admin.products.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition text-sm
                {{ request()->routeIs('admin.products.index') ? 'bg-[#FFF8D8] text-[#A47B00]' : '' }}">
                <i class="fa-solid fa-shirt w-5 text-center"></i>
                <span>Kelola Produk</span>
            </a>

            {{-- Pelanggan --}}
            <a href="{{ route('admin.customers.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition text-sm
                {{ request()->routeIs('admin.customers.index') ? 'bg-[#FFF8D8] text-[#A47B00]' : '' }}">
                <i class="fa-solid fa-users w-5 text-center"></i>
                <span>Pelanggan</span>
            </a>

            <p class="px-3 pt-7 mb-3 text-[10px] font-semibold uppercase tracking-widest text-gray-400">
                Management
            </p>

            {{-- Promosi --}}
            <a href="{{ route('admin.promo.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition text-sm
                {{ request()->routeIs('admin.promo.index') ? 'bg-[#FFF8D8] text-[#A47B00]' : '' }}">
                <i class="fa-solid fa-tags w-5 text-center"></i>
                <span>Promosi</span>
            </a>

            {{-- Laporan --}}
            <a href=""
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition text-sm">
                <i class="fa-solid fa-chart-line w-5 text-center"></i>
                <span>Laporan</span>
            </a>
        </nav>
    </div>
</aside>
