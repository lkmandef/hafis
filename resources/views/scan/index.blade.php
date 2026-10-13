<x-layouts.app title="HAFIS : Scan APAR">
    <!-- 1. Header Halaman & Navigation -->
    <div class="pb-5 mb-6 border-b border-slate-200">
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
                @if($bulan && $tahun)
                    <x-button variant="secondary" href="{{ route('pemeriksaan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="min-h-[44px]">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Hasil Inspeksi</span>
                    </x-button>
                @else
                    <x-button variant="secondary" href="{{ url('/') }}" class="min-h-[44px]">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali ke Dashboard</span>
                    </x-button>
                @endif
            </div>
        </div>
    </div>

    @if($bulan && $tahun)
        <!-- Active Monthly Inspection Period Indicator (Tahap 4) -->
        <div class="max-w-xl mx-auto mb-4 p-3 bg-white border border-slate-200 rounded-xl flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-xs text-slate-500 font-medium">Periode Inspeksi:</span>
                <span class="text-xs font-bold text-slate-900 font-mono">{{ \App\Models\Pemeriksaan::NAMA_BULAN[$bulan] ?? $bulan }} {{ $tahun }}</span>
            </div>
            <a href="{{ route('pemeriksaan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition inline-flex items-center gap-1">
                <span>Rekap Bulanan</span>
                <span>&rarr;</span>
            </a>
        </div>
    @endif

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
                @if($bulan)
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                @endif
                @if($tahun)
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                @endif
                @if(request('jadwal_id'))
                    <input type="hidden" name="jadwal_id" value="{{ request('jadwal_id') }}">
                @endif
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
                                placeholder="Contoh: APAR-EMR1-01"
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

                <!-- Contoh Format Kode Unit -->
                <div class="pt-3 border-t border-slate-100 hidden sm:block">
                    <span class="text-[11px] text-slate-400 block mb-1.5 font-medium">Contoh kode unit yang terdaftar:</span>
                    <div class="flex flex-wrap items-center gap-1.5 text-xs">
                        <a href="{{ route('scan.cari', array_filter(['kode' => 'APAR-IGD1-01', 'bulan' => $bulan, 'tahun' => $tahun])) }}" 
                           class="font-mono text-slate-700 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded border border-slate-200 transition min-h-[36px] inline-flex items-center">
                            APAR-IGD1-01
                        </a>
                        <a href="{{ route('scan.cari', array_filter(['kode' => 'APAR-EMR1-01', 'bulan' => $bulan, 'tahun' => $tahun])) }}" 
                           class="font-mono text-slate-700 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded border border-slate-200 transition min-h-[36px] inline-flex items-center">
                            APAR-EMR1-01
                        </a>
                        <a href="{{ route('scan.cari', array_filter(['kode' => 'APAR-ITD-01', 'bulan' => $bulan, 'tahun' => $tahun])) }}" 
                           class="font-mono text-slate-700 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded border border-slate-200 transition min-h-[36px] inline-flex items-center">
                            APAR-ITD-01
                        </a>
                    </div>
                </div>
            </form>
        </x-card>
    </div>

    <!-- Hidden Form untuk submission hasil deteksi kamera -->
    <form id="auto-scan-form" action="{{ route('scan.cari') }}" method="POST" class="hidden">
        @csrf
        @if($bulan)
            <input type="hidden" name="bulan" value="{{ $bulan }}">
        @endif
        @if($tahun)
            <input type="hidden" name="tahun" value="{{ $tahun }}">
        @endif
        @if(request('jadwal_id'))
            <input type="hidden" name="jadwal_id" value="{{ request('jadwal_id') }}">
        @endif
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
            const manualInput = document.getElementById('kode-manual-input');

            let mediaStream = null;
            let scanTimer = null;
            let isProcessing = false;
            let barcodeDetector = null;

            // Inisialisasi canvas offscreen untuk ekstraksi frame jsQR
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d', { willReadFrequently: true });

            // Inisialisasi BarcodeDetector jika browser mendukung
            if ('BarcodeDetector' in window) {
                try {
                    barcodeDetector = new BarcodeDetector({ formats: ['qr_code'] });
                } catch(e) {
                    barcodeDetector = null;
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

            function updateScanStatus(text, isSuccess) {
                if (!activeBadge) return;
                if (isSuccess) {
                    activeBadge.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400"></span><span class="text-emerald-300 font-semibold">' + text + '</span>';
                } else {
                    activeBadge.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span><span>' + text + '</span>';
                }
                activeBadge.classList.remove('hidden');
            }

            async function startCamera() {
                // Periksa secure context (HTTPS / localhost)
                const isSecureContext = window.isSecureContext ||
                    ['localhost', '127.0.0.1'].includes(window.location.hostname);

                // Periksa ketersediaan WebRTC mediaDevices API
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    if (!isSecureContext) {
                        showError(
                            'Koneksi Aman (HTTPS) Diperlukan',
                            'Browser membatasi akses kamera pada jaringan HTTP biasa. Silakan akses melalui HTTPS atau gunakan pencarian kode APAR manual di bawah.'
                        );
                    } else {
                        showError(
                            'Kamera Tidak Didukung',
                            'Browser ini tidak mendukung akses kamera perangkat. Silakan gunakan pencarian kode APAR secara manual.'
                        );
                    }
                    return;
                }

                // Periksa kesiapan decoder (BarcodeDetector native atau jsQR fallback)
                const hasJsQr = typeof window.jsQR === 'function';
                if (!barcodeDetector && !hasJsQr) {
                    showError(
                        'Decoder QR Belum Siap',
                        'Pustaka decoder QR sedang dimuat atau tidak didukung browser ini. Silakan gunakan input kode APAR manual di bawah.'
                    );
                    return;
                }

                try {
                    updateScanStatus('Membuka kamera...', false);

                    const constraints = {
                        audio: false,
                        video: {
                            facingMode: { ideal: 'environment' },
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        }
                    };

                    let stream = null;
                    try {
                        stream = await navigator.mediaDevices.getUserMedia(constraints);
                    } catch (constraintErr) {
                        // Fallback ke orientasi kamera lingkungan tanpa batasan resolusi
                        try {
                            stream = await navigator.mediaDevices.getUserMedia({
                                audio: false,
                                video: { facingMode: 'environment' }
                            });
                        } catch (e2) {
                            // Fallback terakhir ke kamera video default apapun yang tersedia
                            stream = await navigator.mediaDevices.getUserMedia({
                                audio: false,
                                video: true
                            });
                        }
                    }

                    mediaStream = stream;
                    video.srcObject = mediaStream;
                    video.setAttribute('playsinline', 'true');
                    video.setAttribute('muted', 'true');
                    video.playsInline = true;
                    video.muted = true;

                    await video.play();

                    video.classList.remove('hidden');
                    overlay.classList.add('hidden');
                    controls.classList.remove('hidden');

                    updateScanStatus('Mencari QR Code...', false);

                    startScanningLoop();
                } catch (err) {
                    const name = err.name || '';
                    if (name === 'NotAllowedError' || name === 'PermissionDeniedError') {
                        showError(
                            'Izin Kamera Ditolak',
                            'Akses kamera tidak diizinkan. Silakan berikan izin kamera pada browser Anda atau masukkan kode APAR secara manual di bawah.'
                        );
                    } else if (name === 'NotFoundError' || name === 'DevicesNotFoundError') {
                        showError(
                            'Kamera Tidak Ditemukan',
                            'Perangkat kamera tidak ditemukan. Silakan masukkan kode APAR secara manual di bawah.'
                        );
                    } else if (name === 'NotReadableError' || name === 'TrackStartError') {
                        showError(
                            'Kamera Sedang Digunakan',
                            'Kamera sedang dipakai oleh aplikasi lain. Tutup aplikasi tersebut atau gunakan pencarian kode APAR manual.'
                        );
                    } else if (name === 'SecurityError') {
                        showError(
                            'Koneksi Aman (HTTPS) Diperlukan',
                            'Akses kamera diblokir browser pada koneksi HTTP tidak aman. Silakan gunakan HTTPS atau masukkan kode APAR manual.'
                        );
                    } else {
                        showError(
                            'Kamera Tidak Dapat Dibuka',
                            'Kendala membuka kamera perangkat. Silakan masukkan kode APAR secara manual di bawah.'
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
                    mediaStream.getTracks().forEach(track => {
                        try {
                            track.stop();
                        } catch (e) {}
                    });
                    mediaStream = null;
                }
                if (video) {
                    video.srcObject = null;
                    video.classList.add('hidden');
                }
                activeBadge.classList.add('hidden');
                controls.classList.add('hidden');
            }

            function onQrCodeDetected(rawCode) {
                if (isProcessing) return;
                isProcessing = true;

                const code = (rawCode || '').trim();
                if (!code) {
                    isProcessing = false;
                    return;
                }

                stopCamera();
                updateScanStatus('QR Code terdeteksi: ' + code + '. Membuka APAR...', true);

                autoPayload.value = code;
                autoForm.submit();
            }

            function startScanningLoop() {
                if (scanTimer) {
                    clearInterval(scanTimer);
                    scanTimer = null;
                }
                isProcessing = false;

                scanTimer = setInterval(async () => {
                    if (isProcessing || !video || video.readyState < 2) return;

                    const vw = video.videoWidth;
                    const vh = video.videoHeight;
                    if (!vw || !vh) return;

                    let detectedCode = null;

                    // 1. Coba BarcodeDetector native jika browser mendukung
                    if (barcodeDetector) {
                        try {
                            const barcodes = await barcodeDetector.detect(video);
                            if (barcodes && barcodes.length > 0 && barcodes[0].rawValue) {
                                detectedCode = barcodes[0].rawValue;
                            }
                        } catch (e) {
                            // Abaikan dan lanjut ke decoder jsQR fallback
                        }
                    }

                    // 2. Fallback ke jsQR jika belum terdeteksi dan jsQR tersedia
                    if (!detectedCode && typeof window.jsQR === 'function') {
                        try {
                            let tw = vw;
                            let th = vh;
                            const maxDim = 800;
                            if (tw > maxDim || th > maxDim) {
                                const scale = Math.min(maxDim / tw, maxDim / th);
                                tw = Math.round(tw * scale);
                                th = Math.round(th * scale);
                            }

                            if (canvas.width !== tw || canvas.height !== tw) {
                                canvas.width = tw;
                                canvas.height = th;
                            }

                            ctx.drawImage(video, 0, 0, tw, th);
                            const imageData = ctx.getImageData(0, 0, tw, th);
                            const result = window.jsQR(imageData.data, imageData.width, imageData.height, {
                                inversionAttempts: 'attemptBoth'
                            });

                            if (result && result.data) {
                                detectedCode = result.data;
                            }
                        } catch (e) {
                            // Abaikan error individual frame
                        }
                    }

                    if (detectedCode && !isProcessing) {
                        onQrCodeDetected(detectedCode);
                    }
                }, 200);
            }

            btnStart.addEventListener('click', startCamera);

            btnStop.addEventListener('click', () => {
                isProcessing = false;
                stopCamera();
                statePrompt.classList.remove('hidden');
                stateError.classList.add('hidden');
                overlay.classList.remove('hidden');
            });
        })();
    </script>
</x-layouts.app>