<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Login Admin — Batik Rubung Kuning</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="min-h-screen bg-[#FAFAF8] text-[#2B2B2B]">
    <main class="min-h-screen flex">
        {{-- LEFT SIDE --}}
        <section class="relative hidden lg:flex lg:w-[55%] overflow-hidden bg-[#F4C430]">
            {{-- Decorative pattern --}}
            <div class="absolute inset-0 opacity-[0.08]">
                <svg class="w-full h-full" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="batikPattern" width="120" height="120" patternUnits="userSpaceOnUse">
                            <path d="M60 5
                                   C75 25 95 35 115 60
                                   C95 85 75 95 60 115
                                   C45 95 25 85 5 60
                                   C25 35 45 25 60 5Z" fill="none" stroke="#5A4300" stroke-width="3" />

                            <circle cx="60" cy="60" r="10" fill="none" stroke="#5A4300"
                                stroke-width="3" />
                            <circle cx="60" cy="20" r="3" fill="#5A4300" />
                            <circle cx="60" cy="100" r="3" fill="#5A4300" />
                            <circle cx="20" cy="60" r="3" fill="#5A4300" />
                            <circle cx="100" cy="60" r="3" fill="#5A4300" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#batikPattern)" />
                </svg>
            </div>

            <div class="relative z-10 flex flex-col justify-between w-full p-12 xl:p-16">
                {{-- Brand --}}
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-white/95 flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-leaf text-[#C89B00] text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold tracking-tight text-[#3D3000]">
                                Batik Rubung Kuning
                            </h1>
                            <p class="text-xs text-[#5A4300]/80">
                                Jember, Indonesia
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Hero --}}
                <div class="max-w-xl">
                    <span
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/70 border border-white/50 text-xs font-medium text-[#5A4300] mb-6">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#8A6500]"></span>
                        Admin Dashboard
                    </span>
                    <h2 class="text-4xl xl:text-5xl font-bold leading-tight tracking-tight text-[#302600]">
                        Kelola Batik,
                        <br>
                        <span class="text-[#6B4F00]">
                            Lestarikan Budaya.
                        </span>
                    </h2>
                    <p class="mt-6 max-w-md text-[#5A4300] leading-relaxed">
                        Kelola produk, pesanan, bahan, limbah, dan seluruh
                        aktivitas Batik Rubung Kuning melalui satu dashboard.
                    </p>
                </div>

                {{-- Footer --}}
                <div class="text-xs text-[#5A4300]/70">
                    © {{ date('Y') }} Batik Rubung Kuning.
                    All rights reserved.
                </div>
            </div>
        </section>


        {{-- RIGHT SIDE --}}
        <section class="w-full lg:w-[45%] flex items-center justify-center px-6 py-12 sm:px-10">
            <div class="w-full max-w-md">
                {{-- Mobile Brand --}}
                <div class="lg:hidden mb-10 text-center">
                    <div
                        class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-[#F4C430] flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-leaf text-[#5A4300] text-xl"></i>
                    </div>
                    <h1 class="text-xl font-bold text-[#292929]">
                        Batik Rubung Kuning
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Admin Dashboard
                    </p>
                </div>


                {{-- Login Header --}}
                <div class="mb-8">
                    <p class="text-sm font-medium text-[#B88900] mb-2">
                        Welcome back
                    </p>
                    <h2 class="text-3xl font-bold tracking-tight text-[#242424]">
                        Masuk ke Dashboard
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">
                        Silakan masuk menggunakan akun administrator Anda.
                    </p>
                </div>


                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif


                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                        <div class="flex gap-3">
                            <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5"></i>
                            <div class="text-sm text-red-700">
                                <p class="font-semibold mb-1">
                                    Login gagal
                                </p>
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif


                {{-- Login Form --}}
                <form method="POST" action="{{ route('authenticate') }}" class="space-y-5">
                    @csrf
                    {{-- Email --}}
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-[#333]">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fa-regular fa-envelope text-gray-400"></i>
                            </div>

                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                autocomplete="email" required autofocus placeholder="admin@example.com"
                                class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 outline-none transition focus:border-[#D4A900] focus:ring-4 focus:ring-[#F4C430]/15">
                        </div>
                    </div>


                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="text-sm font-medium text-[#333]">
                                Password
                            </label>
                            @if (Route::has('admin.password.request'))
                                <a href="{{ route('admin.password.request') }}"
                                    class="text-xs font-medium text-[#B88900] hover:text-[#8A6500] transition">
                                    Lupa password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" autocomplete="current-password"
                                required placeholder="Masukkan password"
                                class="w-full h-12 pl-11 pr-12 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 outline-none transition focus:border-[#D4A900] focus:ring-4 focus:ring-[#F4C430]/15">

                            <button type="button" id="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-600 transition"
                                aria-label="Tampilkan password">
                                <i id="passwordIcon" class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>


                    {{-- Remember Me --}}
                    <div class="flex items-center">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-gray-300 text-[#D4A900] focus:ring-[#F4C430]">
                            <span class="text-sm text-gray-500">
                                Ingat saya
                            </span>
                        </label>
                    </div>


                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full h-12 rounded-xl bg-[#D4A900] hover:bg-[#B88900] active:bg-[#9A7000] text-white font-semibold text-sm shadow-sm transition duration-200 flex items-center justify-center gap-2">
                        <span>Masuk ke Dashboard</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>


                {{-- Bottom --}}
                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-xs text-gray-400">
                        Batik Rubung Kuning
                        <span class="mx-1">•</span>
                        Admin Panel
                    </p>
                </div>
            </div>
        </section>
    </main>


    {{-- Password Toggle --}}
    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const passwordIcon = document.getElementById('passwordIcon');

        togglePassword?.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';

            passwordIcon.classList.toggle('fa-eye', !isPassword);
            passwordIcon.classList.toggle('fa-eye-slash', isPassword);
        });
    </script>
</body>

</html>
