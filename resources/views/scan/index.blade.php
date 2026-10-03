<x-layouts.app title="HAFIS : Scan APAR">
    <!-- 1. Header with Breadcrumb & Navigation -->
    <div class="pb-5 mb-6 border-b border-slate-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-slate-800 transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Scan APAR</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Scan APAR
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Arahkan kamera ke QR Code tabung APAR atau gunakan input kode manual.
                </p>
            </div>

            <!-- Header Action: Kembali -->
            <div class="flex items-center gap-2">
                <x-button variant="secondary" href="{{ url('/') }}" class="min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Dashboard</span>
                </x-button>
            </div>
        </div>
    </div>

    <!-- Feedback Alerts: Error, Warning, Info -->
    <div class="max-w-xl mx-auto mb-5 space-y-3">
        @if(session('error'))
            <div class="p-3.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
                <div class="flex items-center gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="font-bold block text-rose-900">APAR tidak ditemukan.</span>
                        <span class="text-rose-700 text-[11px]">{{ session('error') }}</span>
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-bold text-sm leading-none focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Tutup alert">
                    &times;
                </button>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-3.5 rounded-xl border border-amber-200 bg-amber-50 text-amber-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
                <div class="flex items-center gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-amber-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </span>
                    <span class="font-medium">{{ session('warning') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-900 font-bold text-sm leading-none focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Tutup alert">
                    &times;
                </button>
            </div>
        @endif

        @if(session('info'))
            <div class="p-3.5 rounded-xl border border-sky-200 bg-sky-50 text-sky-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
                <div class="flex items-center gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-sky-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    <span class="font-medium">{{ session('info') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-sky-700 hover:text-sky-900 font-bold text-sm leading-none focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Tutup alert">
                    &times;
                </button>
            </div>
        @endif
    </div>

    <div class="max-w-xl mx-auto space-y-6">
        <!-- ==================================================================
             AREA SCANNER KAMERA (Section 4 & 10)
             ================================================================== -->
        <x-card class="!p-5 sm:!p-6 text-center">
            <h2 class="text-sm font-bold text-slate-900 mb-1">
                Scan QR Code APAR
            </h2>
            <p class="text-xs text-slate-500 mb-4">
                Posisikan QR Code yang tertera pada tabung di dalam area bingkai kamera.
            </p>

            <!-- Viewfinder Area -->
            <div class="relative w-full aspect-[4/3] max-w-md mx-auto bg-slate-900 rounded-xl overflow-hidden flex flex-col items-center justify-center border border-slate-800 shadow-2xs">
                <!-- Live Camera Video Element -->
                <video id="camera-video" class="absolute inset-0 w-full h-full object-cover hidden" playsinline autoplay muted></video>

                <!-- Corner Viewfinder Marks (Clean hairline) -->
                <div id="scanner-viewfinder" class="relative w-52 h-52 flex items-center justify-center pointer-events-none z-10">
                    <div class="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-slate-300"></div>
                    <div class="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-slate-300"></div>
                    <div class="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 border-slate-300"></div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 border-slate-300"></div>

                    <!-- Inner Frame -->
                    <div class="w-40 h-40 border border-slate-600/40 rounded flex flex-col items-center justify-center text-slate-400">
                        <svg id="qr-scan-icon" class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Overlay Status & Messages -->
                <div id="scanner-overlay" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center p-6 text-center z-20 transition-opacity">
                    <!-- State: Menunggu Izin / Memulai -->
                    <div id="state-prompt" class="space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-800 text-slate-300 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">Aktifkan Kamera Pemindai</p>
                            <p class="text-[11px] text-slate-400 mt-1 max-w-xs leading-relaxed">
                                Klik tombol di bawah untuk memberikan izin kamera pada browser perangkat Anda.
                            </p>
                        </div>
                        <button type="button" id="btn-start-camera" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-900 rounded-lg text-xs font-bold transition shadow-xs min-h-[44px]">
                            Mulai Kamera
                        </button>
                    </div>

                    <!-- State: Izin Ditolak / Kamera Gagal (Section 11) -->
                    <div id="state-error" class="hidden space-y-3">
                        <div class="w-12 h-12 rounded-full bg-rose-950/80 text-rose-400 border border-rose-800 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                        </div>
                        <div>
                            <p id="camera-error-title" class="text-xs font-bold text-white">Kamera Tidak Tersedia</p>
                            <p id="camera-error-desc" class="text-[11px] text-slate-300 mt-1 max-w-xs leading-relaxed">
                                Izin kamera ditolak atau perangkat tidak memiliki kamera. Silakan gunakan pencarian kode manual di bawah.
                            </p>
                        </div>
                        <button type="button" onclick="document.getElementById('kode-manual-input').focus()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg text-xs font-semibold transition min-h-[44px]">
                            Gunakan Pencarian Kode APAR
                        </button>
                    </div>
                </div>

                <!-- Scanner Status Indicator Saat Aktif -->
                <div id="active-scanning-badge" class="hidden absolute bottom-3 z-20 bg-slate-900/80 backdrop-blur-xs px-3 py-1 rounded-full border border-slate-700 text-[11px] text-slate-300 flex items-center gap-1.5 font-mono">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Kamera aktif, memindai...</span>
                </div>
            </div>

            <!-- Kamera Controls: Stop Kamera jika menyala -->
            <div id="camera-controls" class="hidden mt-3 flex items-center justify-center gap-2">
                <button type="button" id="btn-stop-camera" class="text-[11px] text-slate-500 hover:text-slate-800 underline min-h-[44px] px-2 flex items-center">
                    Matikan Kamera
                </button>
            </div>
        </x-card>

        <!-- ==================================================================
             PENCARIAN KODE APAR MANUAL (FALLBACK DESKTOP & OFFLINE)
             (Section 4 & 10)
             ================================================================== -->
        <x-card title="Masukkan Kode APAR" subtitle="Fallback pencarian jika kamera tidak tersedia atau kode fisik diinput manual.">
            <form action="{{ route('scan.cari') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="kode-manual-input" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kode APAR / Identifier QR
                    </label>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 font-mono text-xs">
                                #
                            </span>
                            <input
                                type="text"
                                id="kode-manual-input"
                                name="kode"
                                placeholder="Contoh: APAR-IGD-01"
                                value="{{ old('kode') }}"
                                required
                                class="w-full pl-8 pr-3 py-2 text-xs font-mono font-medium text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none transition min-h-[44px] uppercase" />
                        </div>

                        <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition-colors min-h-[44px] shrink-0 focus:outline-none focus:ring-2 focus:ring-slate-900 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Cari APAR</span>
                        </button>
                    </div>
                </div>

                <!-- Opsi Cepat Pengujian Desktop -->
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-[11px] text-slate-400 block mb-1.5 font-medium">Contoh kode unit aktif untuk simulasi desktop:</span>
                    <div class="flex flex-wrap items-center gap-1.5 text-xs">
                        <a href="{{ route('scan.cari', ['kode' => 'APAR-IGD-01']) }}" 
                           class="font-mono text-slate-700 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded border border-slate-200 transition min-h-[36px] inline-flex items-center">
                            APAR-IGD-01
                        </a>
                        <a href="{{ route('scan.cari', ['kode' => 'APAR-GIZ-01']) }}" 
                           class="font-mono text-slate-700 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded border border-slate-200 transition min-h-[36px] inline-flex items-center">
                            APAR-GIZ-01
                        </a>
                        <a href="{{ route('scan.cari', ['kode' => 'APAR-OK-01']) }}" 
                           class="font-mono text-slate-700 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded border border-slate-200 transition min-h-[36px] inline-flex items-center">
                            APAR-OK-01
                        </a>
                        <a href="{{ route('scan.cari', ['kode' => 'APAR-999-INVALID']) }}" 
                           class="font-mono text-rose-600 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded border border-rose-200 transition min-h-[36px] inline-flex items-center"
                           title="Uji kondisi QR tidak ditemukan">
                            Uji Invalid (APAR-999)
                        </a>
                    </div>
                </div>
            </form>
        </x-card>
    </div>

    <!-- Hidden Form untuk submission hasil deteksi kamera -->
    <form id="auto-scan-form" action="{{ route('scan.cari') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="kode" id="auto-scan-payload">
    </form>

    <!-- Client-side Camera & Scanner Handling Script -->
    <script>
        (function() {
            const video = document.getElementById('camera-video');
            const overlay = document.getElementById('scanner-overlay');
            const statePrompt = document.getElementById('state-prompt');
            const stateError = document.getElementById('state-error');
            const errorTitle = document.getElementById('camera-error-title');
            const errorDesc = document.getElementById('camera-error-desc');
            const btnStart = document.getElementById('btn-start-camera');
            const btnStop = document.getElementById('btn-stop-camera');
            const controls = document.getElementById('camera-controls');
            const activeBadge = document.getElementById('active-scanning-badge');
            const autoForm = document.getElementById('auto-scan-form');
            const autoPayload = document.getElementById('auto-scan-payload');

            let mediaStream = null;
            let scanTimer = null;
            let barcodeDetector = null;

            // Inisialisasi BarcodeDetector jika browser mendukung
            if ('BarcodeDetector' in window) {
                try {
                    barcodeDetector = new BarcodeDetector({ formats: ['qr_code'] });
                } catch(e) {
                    console.log('BarcodeDetector QR not supported, fallback to manual', e);
                }
            }

            function showError(title, desc) {
                statePrompt.classList.add('hidden');
                stateError.classList.remove('hidden');
                errorTitle.textContent = title;
                errorDesc.textContent = desc;
                overlay.classList.remove('hidden');
                video.classList.add('hidden');
                controls.classList.add('hidden');
                activeBadge.classList.add('hidden');
                stopCamera();
            }

            async function startCamera() {
                // Periksa dukungan browser (Section 11)
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    showError(
                        'Browser Tidak Mendukung Kamera',
                        'Browser ini tidak mendukung WebRTC Camera API. Silakan gunakan pencarian kode APAR manual.'
                    );
                    return;
                }

                try {
                    // Coba buka kamera belakang (environment) terlebih dahulu
                    const constraints = {
                        video: {
                            facingMode: { ideal: 'environment' },
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        },
                        audio: false
                    };

                    mediaStream = await navigator.mediaDevices.getUserMedia(constraints);
                    video.srcObject = mediaStream;
                    video.classList.remove('hidden');
                    overlay.classList.add('hidden');
                    controls.classList.remove('hidden');
                    activeBadge.classList.remove('hidden');

                    // Mulai deteksi barcode berkala
                    startScanningLoop();
                } catch (err) {
                    console.error('Camera error:', err);
                    if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                        showError(
                            'Izin Kamera Ditolak',
                            'Akses kamera tidak diizinkan oleh pengguna atau pengaturan browser. Gunakan pencarian kode APAR manual di bawah.'
                        );
                    } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                        showError(
                            'Kamera Tidak Ditemukan',
                            'Tidak ada perangkat kamera yang terhubung pada sistem ini. Gunakan pencarian kode APAR manual.'
                        );
                    } else {
                        showError(
                            'Kamera Tidak Dapat Dibuka',
                            'Terjadi kendala saat mengakses kamera (' + (err.message || err.name) + '). Gunakan pencarian kode APAR manual.'
                        );
                    }
                }
            }

            function stopCamera() {
                if (scanTimer) {
                    clearInterval(scanTimer);
                    scanTimer = null;
                }
                if (mediaStream) {
                    mediaStream.getTracks().forEach(track => track.stop());
                    mediaStream = null;
                }
                video.classList.add('hidden');
                activeBadge.classList.add('hidden');
                controls.classList.add('hidden');
            }

            function onQrCodeDetected(code) {
                if (!code) return;
                stopCamera();
                activeBadge.innerHTML = '<span class="text-emerald-400 font-bold">&check; Kode terdeteksi: ' + code + '</span>';
                activeBadge.classList.remove('hidden');
                
                autoPayload.value = code;
                autoForm.submit();
            }

            function startScanningLoop() {
                if (!barcodeDetector) return;

                scanTimer = setInterval(async () => {
                    if (video.readyState >= 2) {
                        try {
                            const barcodes = await barcodeDetector.detect(video);
                            if (barcodes.length > 0) {
                                const detected = barcodes[0].rawValue;
                                onQrCodeDetected(detected);
                            }
                        } catch (e) {
                            // Abaikan error frame individual
                        }
                    }
                }, 300);
            }

            btnStart.addEventListener('click', startCamera);
            btnStop.addEventListener('click', () => {
                stopCamera();
                statePrompt.classList.remove('hidden');
                stateError.classList.add('hidden');
                overlay.classList.remove('hidden');
            });
        })();
    </script>
</x-layouts.app>