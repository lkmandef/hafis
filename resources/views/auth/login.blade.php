<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk - HAFIS RSUD dr. H. Moch. Ansari Saleh</title>

    <!-- Favicon: HAFIS Mark SVG -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/brand/hafis-mark.svg') }}">

    <!-- Typography: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-center py-8 sm:py-12 px-4 sm:px-6 lg:px-8 relative selection:bg-slate-900 selection:text-white"
      style="background-color: #f8fafc; background-image: radial-gradient(rgba(148, 163, 184, 0.28) 1px, #f8fafc 1px); background-size: 20px 20px;">
    
    <div class="w-full max-w-[420px] mx-auto relative z-10">
        <!-- Logo & Identitas Institusi -->
        <div class="text-center">
            <a href="{{ url('/') }}" class="inline-block focus:outline-none focus:ring-2 focus:ring-slate-900 rounded-xl p-1 transition-transform hover:scale-105" title="HAFIS">
                <img src="{{ asset('images/brand/hafis-logo.svg') }}"
                     alt="Logo HAFIS"
                     class="h-14 sm:h-16 w-auto mx-auto object-contain drop-shadow-xs" />
            </a>
            <div class="mt-3.5 space-y-2 flex flex-col items-center">
                <h1 class="text-xs font-bold tracking-widest text-slate-700 uppercase">
                    RSUD dr. H. Moch. Ansari Saleh
                </h1>
                <span class="inline-flex items-center bg-slate-200/70 text-slate-700 text-xs px-3 py-1 rounded-full font-medium border border-slate-300/40">
                    Unit Keselamatan dan Kesehatan Kerja Rumah Sakit (K3RS)
                </span>
            </div>
        </div>

        <!-- Kartu Form Login -->
        <div class="mt-6 sm:mt-7 bg-white rounded-2xl border border-slate-200/80 shadow-xl shadow-slate-900/5 overflow-hidden">
            <!-- Top Accent Bar: Dark Navy with Subtle Fire-Safety Crimson Gradient -->
            <div class="h-1.5 w-full bg-gradient-to-r from-slate-900 via-rose-700 to-amber-600"></div>

            <div class="p-6 sm:p-7">
                <div class="mb-5">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Masuk ke Sistem</h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Gunakan akun K3RS yang terdaftar untuk mengakses aplikasi.
                    </p>
                </div>

                <!-- Notifikasi Informasi / Keluar -->
                @if(session('info'))
                    <div class="mb-4 p-3.5 rounded-xl border border-sky-200 bg-sky-50 text-sky-900 text-xs flex items-start gap-2.5" role="alert">
                        <svg class="w-4 h-4 text-sky-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @if(session('status'))
                    <div class="mb-4 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-start gap-2.5" role="alert">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Notifikasi Kesalahan -->
                @if($errors->has('login'))
                    <div class="mb-4 p-3.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs flex items-start gap-2.5" role="alert">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>{{ $errors->first('login') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate>
                    @csrf

                    <!-- Input Identitas (Username / Email) -->
                    <div>
                        <label for="login" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Username atau Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="login"
                                id="login"
                                value="{{ old('login') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Contoh: k3_petugas"
                                class="w-full pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white placeholder:text-slate-400 placeholder:text-sm border @error('login') border-rose-300 ring-2 ring-rose-200/50 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:border-slate-800 focus:ring-4 focus:ring-slate-900/10 min-h-[46px] transition-all duration-200" />
                        </div>
                    </div>

                    <!-- Input Kata Sandi -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-slate-700">
                                Kata Sandi
                            </label>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi akun Anda"
                                class="w-full pl-10 pr-11 py-2.5 text-sm text-slate-900 bg-white placeholder:text-slate-400 placeholder:text-sm border @error('password') border-rose-300 ring-2 ring-rose-200/50 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:border-slate-800 focus:ring-4 focus:ring-slate-900/10 min-h-[46px] transition-all duration-200" />

                            <!-- Tombol Show/Hide Password -->
                            <button
                                type="button"
                                id="toggle-password"
                                aria-label="Tampilkan atau sembunyikan kata sandi"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer transition-colors">
                                <svg id="icon-eye" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="icon-eye-off" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-[11px] text-rose-600 mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Tombol Submit -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="group w-full min-h-[48px] px-4 py-3 bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow focus:outline-none focus:ring-4 focus:ring-slate-900/20 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                            <span>Masuk ke HAFIS</span>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-white group-hover:translate-x-0.5 transition-all duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Official Security Notice Footer -->
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <div class="flex items-start gap-2.5 p-3 rounded-lg bg-slate-50 border border-slate-200/70 text-slate-600 text-[11px] leading-relaxed">
                        <svg class="w-4 h-4 text-slate-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>
                            Sistem ini khusus diperuntukkan bagi petugas K3RS dan Kepala K3RS RSUD dr. H. Moch. Ansari Saleh Banjarmasin.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Show/Hide Password Interaktif -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');
            const iconEye = document.getElementById('icon-eye');
            const iconEyeOff = document.getElementById('icon-eye-off');

            if (toggleBtn && passwordInput && iconEye && iconEyeOff) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';
                    iconEye.classList.toggle('hidden', isPassword);
                    iconEyeOff.classList.toggle('hidden', !isPassword);
                });
            }
        });
    </script>
</body>

</html>
