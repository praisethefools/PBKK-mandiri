<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Portal Akademik FTEIC ITS</h2>
        <p class="text-xs text-gray-600 mt-1">Sistem Otentikasi Terverifikasi via Laravel Breeze</p>
    </div>

    <!-- Alert Notifikasi / Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- TANTANGAN NILAI PLUS A+ (Slide 41): Tombol Login Instan Kustom untuk Evaluasi Demo Dosen/Asdos --}}
    <div class="mb-6 p-4 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-900 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Demo Evaluasi Cepat (1-Klik)
            </span>
            <span class="text-[10px] bg-blue-600 text-white font-semibold px-2 py-0.5 rounded-full">Challenge A+</span>
        </div>
        <p class="text-xs text-slate-600 mb-3">Pilih salah satu peran di bawah untuk login langsung tanpa mengetik kredensial:</p>

        <div class="grid grid-cols-1 gap-2">
            <!-- 1. Admin (Akses Penuh) -->
            <a href="{{ route('quick.login', 'admin') }}"
               class="flex items-center justify-between px-3 py-2 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm">
                <span class="flex items-center gap-2">
                    <span class="text-base">👑</span>
                    <span class="text-left">
                        <strong class="block font-semibold">Login Instan: Admin (Akses Penuh)</strong>
                        <span class="text-[10px] text-indigo-200">admin@akademik.its.ac.id &bull; Hak CRUD & Hapus Data</span>
                    </span>
                </span>
                <span class="text-indigo-200 text-xs font-bold">&rarr;</span>
            </a>

            <!-- 2. Staf (Akses Terbatas) -->
            <a href="{{ route('quick.login', 'staf') }}"
               class="flex items-center justify-between px-3 py-2 text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                <span class="flex items-center gap-2">
                    <span class="text-base">🛡️</span>
                    <span class="text-left">
                        <strong class="block font-semibold">Login Instan: Staf (Akses Terbatas)</strong>
                        <span class="text-[10px] text-emerald-200">staf@akademik.its.ac.id &bull; CRUD tanpa tombol Hapus</span>
                    </span>
                </span>
                <span class="text-emerald-200 text-xs font-bold">&rarr;</span>
            </a>

            <!-- 3. Mahasiswa (Publik) -->
            <a href="{{ route('quick.login', 'mahasiswa') }}"
               class="flex items-center justify-between px-3 py-2 text-xs font-medium text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 rounded-lg transition-colors">
                <span class="flex items-center gap-2">
                    <span class="text-base">🎓</span>
                    <span class="text-left">
                        <strong class="block font-semibold">Login Instan: Mahasiswa (Publik)</strong>
                        <span class="text-[10px] text-slate-500">mahasiswa@student.its.ac.id &bull; Dialihkan ke Portal</span>
                    </span>
                </span>
                <span class="text-slate-400 text-xs font-bold">&rarr;</span>
            </a>
        </div>
    </div>

    <div class="relative flex py-2 items-center mb-4">
        <div class="flex-grow border-t border-gray-300"></div>
        <span class="flex-shrink mx-3 text-gray-400 text-xs uppercase font-medium">atau masuk secara manual</span>
        <div class="flex-grow border-t border-gray-300"></div>
    </div>

    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember" checked>
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-xs text-gray-600 hover:text-gray-900 rounded-md focus:outline-none" href="{{ route('beranda') }}">
                &larr; Kembali ke Portal
            </a>

            <x-primary-button class="ms-3 bg-blue-700 hover:bg-blue-800">
                {{ __('Masuk ke Akun') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
