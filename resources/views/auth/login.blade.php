<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk ke Sistem - HAFIS RSUD dr. H. Moch. Ansari Saleh</title>

    <!-- Favicon: HAFIS Mark SVG -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/brand/hafis-mark.svg') }}">
    <link rel="alternate icon" href="{{ asset('images/brand/hafis-mark.svg') }}">

    <!-- Preload Critical Typography (Root-relative to prevent port/origin mismatch) -->
    <link rel="preload" href="/fonts/inter/inter-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/inter/inter-latin-500-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/inter/inter-latin-600-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/inter/inter-latin-700-normal.woff2" as="font" type="font/woff2" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full min-h-screen min-h-dvh bg-white text-gray-900 font-sans antialiased selection:bg-gray-900 selection:text-white">
    <div class="min-h-screen min-h-dvh grid grid-cols-1 lg:grid-cols-12">

        <!-- ================================================================== -->
        <!-- Sisi Kiri: Hero Panel Brand & Edukasi Keselamatan K3RS (Desktop)   -->
        <!-- ================================================================== -->
        <div class="hidden lg:flex lg:col-span-5 xl:col-span-5 bg-gray-950 text-white flex-col justify-between p-8 xl:p-12 relative overflow-hidden border-r border-gray-800">
            <!-- Subtle Radial Gradient & Motif Background -->
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-gray-800/40 via-gray-950 to-black pointer-events-none" aria-hidden="true"></div>
            
            <!-- Siluet Watermark Mark APAR di Sudut Bawah -->
            <div class="absolute -right-16 -bottom-16 w-80 h-80 opacity-[0.04] pointer-events-none select-none text-white" aria-hidden="true">
                <img src="{{ asset('images/brand/hafis-mark.svg') }}" alt="" class="w-full h-full object-contain filter invert" />
            </div>

            <!-- Content Body: Brand Lockup + Tagline, Kesiapsiagaan, & Pilar Nilai -->
            <div class="relative z-10 my-auto py-4">
                <!-- Brand Lockup Terintegrasi -->
                <div class="flex items-center gap-3.5 mb-8">
                    <div class="w-11 h-11 rounded-xl bg-white/10 backdrop-blur-xs border border-white/15 p-2 flex items-center justify-center shrink-0 shadow-xs">
                        <img src="{{ asset('images/brand/hafis-mark.svg') }}" alt="Mark HAFIS" class="w-full h-full object-contain filter invert" />
                    </div>
                    <div>
                        <div class="text-base font-bold tracking-tight text-white leading-tight">HAFIS</div>
                        <div class="text-xs font-medium text-gray-400 leading-tight mt-0.5">RSUD dr. H. Moch. Ansari Saleh</div>
                    </div>
                </div>

                <h2 class="text-2xl xl:text-3xl font-bold tracking-tight text-white leading-snug mb-4">
                    Kesiapsiagaan & Proteksi Kebakaran Rumah Sakit
                </h2>
                    Kesiapsiagaan & Proteksi Kebakaran Rumah Sakit
                </h2>
                <p class="text-sm text-gray-400 leading-relaxed mb-8">
                    Platform inspeksi terpadu untuk monitoring kondisi fisik, masa berlaku media, dan kelayakan titik APAR di seluruh gedung rumah sakit.
                </p>

                <!-- 3 Pilar Keandalan K3RS -->
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center shrink-0 mt-0.5 text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-white">Inspeksi Berbasis QR Code</div>
                            <div class="text-[11px] text-gray-400">Verifikasi fisik real-time langsung di setiap titik penempatan gedung.</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center shrink-0 mt-0.5 text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-white">Standar Keselamatan Pasien & Kemenkes</div>
                            <div class="text-[11px] text-gray-400">Mendukung akreditasi KARS dengan data kepatuhan inspeksi transparan.</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center shrink-0 mt-0.5 text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-white">Peringatan Dini Kadaluarsa</div>
                            <div class="text-[11px] text-gray-400">Notifikasi proaktif untuk pengisian ulang media dan perbaikan kerusakan.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Panel Kiri -->
            <div class="relative z-10 pt-6 border-t border-gray-800/80 flex items-center justify-between text-[11px] text-gray-400">
                <span>Unit K3RS Banjarmasin</span>
                <span>© {{ date('Y') }} RSUD Ansari Saleh</span>
            </div>
        </div>

        <!-- ================================================================== -->
        <!-- Sisi Kanan: Form Login Interaktif                                  -->
        <!-- ================================================================== -->
        <div class="col-span-12 lg:col-span-7 xl:col-span-7 flex flex-col justify-between bg-white px-5 py-6 sm:px-10 sm:py-10 lg:px-14 xl:px-18 min-h-screen min-h-dvh overflow-y-auto">
            
            <!-- Top Header Mobile (Hanya tampil di mobile/tablet kecil) -->
            <div class="w-full max-w-md mx-auto pt-2 sm:pt-4 lg:hidden">
                <div class="text-center mb-6">
                    <a href="{{ url('/') }}" class="inline-block focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 rounded-xl p-1" title="HAFIS">
                        <img src="{{ asset('images/brand/hafis-logo.svg') }}"
                             alt="Logo HAFIS"
                             width="130"
                             height="57"
                             class="h-10 w-auto mx-auto object-contain" />
                    </a>
                    <div class="mt-2 text-xs font-semibold text-gray-700">
                        RSUD dr. H. Moch. Ansari Saleh
                    </div>
                    <div class="text-[11px] text-gray-500">
                        Unit Keselamatan dan Kesehatan Kerja Rumah Sakit (K3RS)
                    </div>
                </div>
            </div>

            <!-- Container Kartu Form -->
            <div class="w-full max-w-md mx-auto my-auto py-4">
                <div class="mb-6">
                    <h1 class="text-2xl sm:text-[26px] font-bold text-gray-900 tracking-tight">Masuk ke Sistem</h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1.5 leading-relaxed">
                        Masukkan kredensial akun K3RS Anda untuk mengakses dashboard inspeksi dan manajemen APAR.
                    </p>
                </div>

                <!-- Notifikasi Informasi / Keluar Sesi -->
                @if(session('info'))
                    <div class="mb-5 p-3.5 rounded-xl border border-blue-200/80 bg-blue-50/70 text-blue-900 text-xs flex items-start gap-3 shadow-2xs" role="alert">
                        <div class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="leading-relaxed font-medium pt-0.5">{{ session('info') }}</div>
                    </div>
                @endif

                <!-- Notifikasi Status Berhasil -->
                @if(session('status'))
                    <div class="mb-5 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-start gap-3 shadow-2xs" role="alert">
                        <div class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="leading-relaxed font-medium pt-0.5">{{ session('status') }}</div>
                    </div>
                @endif

                <!-- Notifikasi Kesalahan Kredensial / Rate Limit -->
                @if($errors->has('login'))
                    <div class="mb-5 p-3.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs flex items-start gap-3 shadow-2xs" role="alert">
                        <div class="w-5 h-5 rounded-md bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="leading-relaxed font-medium pt-0.5">{{ $errors->first('login') }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate id="login-form">
                    @csrf

                    <!-- Input Identitas (Username / Email) -->
                    <div>
                        <label for="login" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Username atau Email Kedinasan
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
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
                                placeholder="k3_petugas"
                                class="w-full pl-10 pr-3.5 py-2.5 text-sm text-gray-900 bg-white placeholder:text-gray-400 border @error('login') border-red-300 ring-2 ring-red-100 @else border-gray-300 @enderror rounded-xl focus:outline-none focus:border-gray-900 focus:ring-4 focus:ring-gray-900/10 min-h-[44px] transition-colors" />
                        </div>
                    </div>

                    <!-- Input Kata Sandi -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-gray-700">
                                Kata Sandi
                            </label>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi akun"
                                class="w-full pl-10 pr-11 py-2.5 text-sm text-gray-900 bg-white placeholder:text-gray-400 border @error('password') border-red-300 ring-2 ring-red-100 @else border-gray-300 @enderror rounded-xl focus:outline-none focus:border-gray-900 focus:ring-4 focus:ring-gray-900/10 min-h-[44px] transition-colors" />

                            <!-- Tombol Show/Hide Password Interaktif -->
                            <button
                                type="button"
                                id="toggle-password"
                                aria-label="Tampilkan atau sembunyikan kata sandi"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none min-h-[44px] min-w-[44px] justify-center cursor-pointer transition-colors">
                                <svg id="icon-eye" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="icon-eye-off" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-[11px] text-red-600 mt-1.5 flex items-center gap-1 font-medium">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Pilihan Ingat Saya & Bantuan Akses -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember" class="flex items-center gap-2 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                                class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900/20 focus:ring-offset-0 cursor-pointer" />
                            <span class="text-xs text-gray-600">Ingat sesi saya</span>
                        </label>

                        <button
                            type="button"
                            onclick="alert('Untuk kendala lupa kata sandi atau aktivasi akun baru, silakan hubungi Administrator K3RS di Ruang K3RS Gedung Utama.')"
                            class="text-xs font-medium text-gray-600 hover:text-gray-900 focus:outline-none focus-visible:underline cursor-pointer">
                            Butuh bantuan?
                        </button>
                    </div>

                    <!-- Tombol Masuk Utama -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            id="btn-submit"
                            class="w-full min-h-[44px] px-4 py-2.5 bg-gray-900 hover:bg-black text-white text-sm font-semibold rounded-xl shadow-xs focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2 motion-safe:active:scale-[0.99] transition flex items-center justify-center gap-2 cursor-pointer group">
                            <span id="btn-text">Masuk</span>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Catatan Keamanan Resmi -->
                <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                    <p class="text-[11px] text-gray-500 leading-relaxed">
                        Sistem resmi RSUD dr. H. Moch. Ansari Saleh. Akses hanya diperuntukkan bagi petugas K3RS dan pimpinan rumah sakit yang berwenang.
                    </p>
                </div>
            </div>

            <!-- Footer Bawah Sisi Kanan -->
            <div class="w-full max-w-md mx-auto pt-4 text-center">
                <p class="text-[10px] text-gray-400">
                    Hospital APAR & Fire Inspection System • Dilindungi Enkripsi Sesi
                </p>
            </div>
        </div>

    </div>

    <!-- Script Interaktif Show/Hide Password & Submit State -->
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

            const loginForm = document.getElementById('login-form');
            const submitBtn = document.getElementById('btn-submit');
            const btnText = document.getElementById('btn-text');

            if (loginForm && submitBtn && btnText) {
                loginForm.addEventListener('submit', function () {
                    submitBtn.classList.add('opacity-80', 'pointer-events-none');
                    btnText.textContent = 'Memverifikasi...';
                });
            }
        });
    </script>
</body>

</html>
