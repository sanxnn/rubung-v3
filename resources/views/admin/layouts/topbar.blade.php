<header class="h-20 bg-white border-b border-gray-100 px-5 sm:px-8 flex items-center justify-between relative z-40">

    <div class="flex items-center gap-4">
        {{-- Mobile Menu --}}
        <button id="menuButton" class="lg:hidden w-10 h-10 rounded-xl hover:bg-gray-50 text-gray-600 transition">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div>
            <p class="text-xs text-gray-400">
                Monday, 21 September 2026
            </p>
            <h2 class="font-semibold text-lg text-gray-800">
                Dashboard
            </h2>
        </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-4">
        {{-- Search --}}
        <button
            class="hidden sm:flex w-10 h-10 items-center justify-center rounded-xl text-gray-400 hover:bg-gray-50 hover:text-gray-700 transition">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>

        {{-- Notification --}}
        <button
            class="relative w-10 h-10 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-50 hover:text-gray-700 transition">
            <i class="fa-regular fa-bell"></i>
            <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-red-500 border-2 border-white"></span>
        </button>

        {{-- Divider --}}
        <div class="hidden sm:block w-px h-7 bg-gray-100"></div>

        <div class="relative">
            {{-- Profile Trigger --}}
            <button id="profileDropdownBtn"
                class="flex items-center gap-2 hover:bg-gray-50 rounded-xl p-1.5 pr-3 transition group">
                <div
                    class="w-9 h-9 rounded-full bg-[#F4C430] flex items-center justify-center text-[#5A4300] font-semibold text-xs shadow-sm group-hover:shadow-md transition-shadow">
                    AR
                </div>
                <div class="hidden md:block text-left">
                    <p class="text-xs font-semibold text-gray-700 group-hover:text-gray-900">
                        Admin Rubung
                    </p>
                    <p class="text-[10px] text-gray-400">
                        Administrator
                    </p>
                </div>
                <i
                    class="fa-solid fa-chevron-down text-[10px] text-gray-400 hidden md:block ml-1 group-hover:text-gray-600 transition-colors"></i>
            </button>

            {{-- Dropdown Menu --}}
            <div id="profileDropdown"
                class="hidden absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50 transform origin-top-right transition-all">
                {{-- Header Dropdown --}}
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-800">Admin Rubung</p>
                    <p class="text-xs text-gray-500">admin@rubungkuning.com</p>
                </div>

                {{-- Menu Items --}}
                <div class="py-1">
                    <a href="{{ route('admin.profile') }}"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <i class="fa-regular fa-user w-4 text-center text-gray-400"></i>
                        Profil Saya
                    </a>
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-100 my-1"></div>

                {{-- Logout Button (Memicu Modal) --}}
                <button id="openLogoutModal"
                    class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                    <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                    Keluar
                </button>
            </div>
        </div>
    </div>

</header>

{{-- ============================================================ --}}
{{-- LOGOUT MODAL                                                  --}}
{{-- ============================================================ --}}
<div id="logoutModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div id="modalBackdrop"
        class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

            {{-- Modal Panel --}}
            <div id="modalPanel"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all duration-300 sm:my-8 sm:w-full sm:max-w-md opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        {{-- Icon --}}
                        <div
                            class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-50 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fa-solid fa-arrow-right-from-bracket text-red-500 text-lg"></i>
                        </div>

                        {{-- Text --}}
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                                Keluar dari Akun?
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin keluar? Anda perlu login kembali untuk mengakses dashboard.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-gray-50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                    <form method="POST" action="{{ route('admin.logout') }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit"
                            class="inline-flex w-full justify-center items-center gap-2 rounded-xl bg-red-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto transition-colors">
                            <i class="fa-solid fa-check"></i>
                            Ya, Keluar
                        </button>
                    </form>
                    <button type="button" id="closeModal"
                        class="mt-3 inline-flex w-full justify-center items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-200 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
