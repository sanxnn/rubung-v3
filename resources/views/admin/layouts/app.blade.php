<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Dashboard — Batik Rubung Kuning</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<style>
    /* ========================================
       MICROMODAL BASE STYLES
       ======================================== */
    .modal {
        display: none;
    }

    .modal.is-open {
        display: block;
    }

    .modal__overlay {
        display: flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 9999;
        padding: 1rem;
    }

    .modal__container {
        background-color: #fff;
        border-radius: 1.5rem;
        padding: 0;
        max-width: 520px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        outline: none;
    }

    .modal__container--sm {
        max-width: 400px;
        padding: 2rem 0;
    }

    .modal__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .modal__title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .modal__close-btn {
        width: 2.25rem;
        height: 2.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        color: #9ca3af;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .modal__close-btn:hover {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .modal__content {
        padding: 1.5rem;
    }

    .modal__footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #f3f4f6;
        background-color: #fafafa;
        border-radius: 0 0 1.5rem 1.5rem;
    }

    .modal__footer--center {
        justify-content: center;
        background-color: #fafafa;
        border-radius: 0 0 1.5rem 1.5rem;
        margin-top: 1.5rem;
    }

    .modal__label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .modal__input {
        width: 100%;
        padding: 0.7rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        background-color: #f9fafb;
        font-size: 0.875rem;
        color: #374151;
        outline: none;
        transition: all 0.2s;
    }

    .modal__input:focus {
        border-color: #F4C430;
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(244, 196, 48, 0.1);
    }

    .modal__btn-cancel {
        padding: 0.65rem 1.25rem;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        background-color: #fff;
        color: #4b5563;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .modal__btn-cancel:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
    }

    .modal__btn-primary {
        padding: 0.65rem 1.25rem;
        border-radius: 0.75rem;
        border: none;
        background-color: #F4C430;
        color: #5A4300;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(244, 196, 48, 0.3);
    }

    .modal__btn-primary:hover {
        background-color: #E0B320;
        box-shadow: 0 4px 8px rgba(244, 196, 48, 0.4);
    }

    .modal__btn-update {
        padding: 0.65rem 1.25rem;
        border-radius: 0.75rem;
        border: none;
        background-color: #3b82f6;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }

    .modal__btn-update:hover {
        background-color: #2563eb;
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4);
    }

    .modal__btn-danger {
        padding: 0.65rem 1.25rem;
        border-radius: 0.75rem;
        border: none;
        background-color: #ef4444;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
    }

    .modal__btn-danger:hover {
        background-color: #dc2626;
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.4);
    }

    .modal__delete-icon-wrapper {
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 50%;
        background-color: #fef2f2;
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        border: 4px solid #fef2f2;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }

    /* ========================================
       ANIMASI MICRO MODAL
       ======================================== */
    .micromodal-slide {
        display: none;
    }

    .micromodal-slide.is-open {
        display: block;
    }

    .micromodal-slide .modal__overlay {
        opacity: 0;
        transition: opacity 0.3s cubic-bezier(0.0, 0.0, 0.2, 1);
    }

    .micromodal-slide.is-open .modal__overlay {
        opacity: 1;
    }

    .micromodal-slide .modal__container {
        transform: translateY(20px) scale(0.95);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.0, 0.0, 0.2, 1);
    }

    .micromodal-slide.is-open .modal__container {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    /* Animasi keluar */
    .micromodal-slide[aria-hidden="true"] .modal__overlay {
        opacity: 0;
    }

    .micromodal-slide[aria-hidden="true"] .modal__container {
        transform: translateY(20px) scale(0.95);
        opacity: 0;
    }

    /* ========================================
       SCROLLBAR CUSTOM UNTUK MODAL
       ======================================== */
    .modal__container::-webkit-scrollbar {
        width: 6px;
    }

    .modal__container::-webkit-scrollbar-track {
        background: transparent;
    }

    .modal__container::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    .modal__container::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>

<body class="bg-[#F8F8F6] text-[#292929]">

    <div class="min-h-screen flex">

        {{-- ========================================================= --}}
        {{-- SIDEBAR --}}
        {{-- ========================================================= --}}
        @include('admin.layouts.sidebar')

        {{-- Overlay Mobile --}}
        <div id="sidebarOverlay" class="fixed inset-0 z-40 bg-black/30 hidden lg:hidden"></div>

        {{-- ========================================================= --}}
        {{-- MAIN --}}
        {{-- ========================================================= --}}
        <div class="flex-1 min-w-0">

            {{-- TOPBAR --}}
            @include('admin.layouts.topbar')

            {{-- CONTENT --}}
            @yield('content')

        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- SCRIPTS --}}
    {{-- ============================================================= --}}

    @stack('script')

    {{-- Mobile Sidebar Script --}}
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuButton = document.getElementById('menuButton');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        menuButton?.addEventListener('click', openSidebar);
        overlay?.addEventListener('click', closeSidebar);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Dropdown Logic ---
            const profileDropdownBtn = document.getElementById('profileDropdownBtn');
            const profileDropdown = document.getElementById('profileDropdown');

            profileDropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Mencegah event bubbling
                profileDropdown.classList.toggle('hidden');
            });

            // Tutup dropdown jika klik di luar area dropdown
            document.addEventListener('click', function(e) {
                if (!profileDropdownBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });

            // --- Modal Logout Logic ---
            const openLogoutModalBtn = document.getElementById('openLogoutModal');
            const logoutModal = document.getElementById('logoutModal');
            const modalBackdrop = document.getElementById('modalBackdrop');
            const modalPanel = document.getElementById('modalPanel');
            const closeModalBtn = document.getElementById('closeModal');

            function openModal() {
                profileDropdown.classList.add('hidden'); // Tutup dropdown saat modal buka
                logoutModal.classList.remove('hidden');
                void logoutModal.offsetWidth; // Trigger reflow
                modalBackdrop.classList.remove('opacity-0');
                modalPanel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
                modalPanel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
            }

            function closeModal() {
                modalBackdrop.classList.add('opacity-0');
                modalPanel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
                modalPanel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
                setTimeout(() => {
                    logoutModal.classList.add('hidden');
                }, 300);
            }

            if (openLogoutModalBtn) openLogoutModalBtn.addEventListener('click', openModal);
            if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
            if (modalBackdrop) modalBackdrop.addEventListener('click', closeModal);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !logoutModal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        });
    </script>

    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>

    <script>
        // Inisialisasi MicroModal saat DOM siap
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof MicroModal !== 'undefined') {
                MicroModal.init({
                    openTrigger: 'data-micromodal-trigger',
                    closeTrigger: 'data-micromodal-close',
                    openClass: 'is-open',
                    disableScroll: true,
                    disableFocus: false,
                    debugMode: false
                });
            }
        });
    </script>

</body>

</html>
