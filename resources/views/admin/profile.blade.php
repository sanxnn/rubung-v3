@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8"
        style="font-family: 'Poppins', sans-serif;">

        {{-- Header --}}
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900">
                Profile Admin
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Kelola informasi akun administrator Anda.
            </p>
        </div>

        {{-- Success Alert --}}
        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 shadow-sm">
                <i class="fa-solid fa-circle-check mt-0.5 text-lg text-green-600"></i>

                <div class="flex-1 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- Validation Error --}}
        @if ($errors->any())
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation mt-0.5 text-lg text-red-600"></i>

                <div class="flex-1">
                    <p class="mb-1 text-sm font-bold text-red-800">
                        Terjadi kesalahan:
                    </p>

                    <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Profile Summary --}}
            <div class="h-fit rounded-2xl border border-gray-100 bg-white shadow-sm">

                <div class="p-6 text-center">

                    {{-- Avatar --}}
                    <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-blue-50 text-blue-600 ring-8 ring-blue-50/50">
                        <span class="text-3xl font-bold">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </span>
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-gray-900">
                        {{ $admin->name }}
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $admin->email }}
                    </p>

                    <div class="mt-4">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-blue-700">
                            <i class="fa-solid fa-shield-halved"></i>
                            Administrator
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-100 px-6 py-5">

                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-50 text-gray-500">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                Email
                            </p>

                            <p class="mt-0.5 truncate text-sm font-medium text-gray-700">
                                {{ $admin->email }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-50 text-gray-500">
                            <i class="fa-solid fa-phone text-sm"></i>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                Nomor Telepon
                            </p>

                            <p class="mt-0.5 text-sm font-medium text-gray-700">
                                {{ $admin->phone ?? 'Belum diatur' }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>


            {{-- Edit Profile --}}
            <div class="lg:col-span-2 rounded-2xl border border-gray-100 bg-white shadow-sm">

                {{-- Form Header --}}
                <div class="border-b border-gray-100 p-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>

                        <div>
                            <h3 class="font-bold text-gray-900">
                                Informasi Profile
                            </h3>

                            <p class="mt-0.5 text-xs text-gray-500">
                                Perbarui informasi dasar akun Anda.
                            </p>
                        </div>
                    </div>
                </div>

                <form
                    action="{{ route('admin.profile.update') }}"
                    method="POST"
                    class="p-6"
                >
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        {{-- Name --}}
                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                Nama Lengkap
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative">
                                <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>

                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $admin->name) }}"
                                    required
                                    autocomplete="name"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm text-gray-800 outline-none transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                                    placeholder="Nama lengkap"
                                >
                            </div>

                            @error('name')
                                <p class="mt-1.5 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Email --}}
                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                Email
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative">
                                <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email', $admin->email) }}"
                                    required
                                    autocomplete="email"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm text-gray-800 outline-none transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                                    placeholder="admin@example.com"
                                >
                            </div>

                            @error('email')
                                <p class="mt-1.5 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Phone --}}
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                Nomor Telepon
                                <span class="font-normal normal-case tracking-normal text-gray-400">
                                    (Opsional)
                                </span>
                            </label>

                            <div class="relative">
                                <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>

                                <input
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone', $admin->phone) }}"
                                    autocomplete="tel"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm text-gray-800 outline-none transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                                    placeholder="0812xxxxxxx"
                                >
                            </div>

                            @error('phone')
                                <p class="mt-1.5 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>


                    {{-- Password Section --}}
                    <div class="my-7 border-t border-gray-100"></div>

                    <div class="mb-5">
                        <h3 class="font-bold text-gray-900">
                            Ubah Password
                        </h3>

                        <p class="mt-1 text-xs text-gray-500">
                            Kosongkan jika Anda tidak ingin mengubah password.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        {{-- Password --}}
                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                Password Baru
                            </label>

                            <div class="relative">
                                <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>

                                <input
                                    type="password"
                                    name="password"
                                    autocomplete="new-password"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm text-gray-800 outline-none transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                                    placeholder="Minimal 8 karakter"
                                >
                            </div>

                            @error('password')
                                <p class="mt-1.5 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Password Confirmation --}}
                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                Konfirmasi Password
                            </label>

                            <div class="relative">
                                <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>

                                <input
                                    type="password"
                                    name="password_confirmation"
                                    autocomplete="new-password"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm text-gray-800 outline-none transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                                    placeholder="Ulangi password baru"
                                >
                            </div>
                        </div>

                    </div>


                    {{-- Action --}}
                    <div class="mt-8 flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:justify-end">

                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-100 px-5 py-3 text-sm font-bold text-gray-600 transition-all hover:bg-gray-200"
                        >
                            <i class="fa-solid fa-arrow-left"></i>
                            Kembali
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-200 transition-all hover:bg-blue-700 hover:shadow-xl"
                        >
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
                        </button>

                    </div>

                </form>
            </div>

        </div>

    </main>
@endsection
