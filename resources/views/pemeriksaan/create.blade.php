<x-layouts.app title="HAFIS : Checklist Pemeriksaan Fisik {{ $apar->kode }}">
    <!-- 1. Header with Breadcrumb & Navigation -->
    <div class="pb-5 mb-6 border-b border-gray-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-2 font-medium" aria-label="Breadcrumb">
            @if($jadwal)
                <a href="{{ route('jadwal.index') }}" class="hover:text-gray-900 transition-colors">Jadwal Inspeksi</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('jadwal.show', $jadwal->id) }}" class="hover:text-gray-900 transition-colors">{{ $jadwal->nama }}</a>
                <span class="text-gray-300">/</span>
            @elseif(isset($periodeBulan) && isset($periodeTahun))
                <a href="{{ route('pemeriksaan.index', ['bulan' => $periodeBulan, 'tahun' => $periodeTahun]) }}" class="hover:text-gray-900 transition-colors">Hasil Inspeksi ({{ \App\Models\Pemeriksaan::NAMA_BULAN[$periodeBulan] ?? $periodeBulan }} {{ $periodeTahun }})</a>
                <span class="text-gray-300">/</span>
            @else
                <a href="{{ route('apar.index') }}" class="hover:text-gray-900 transition-colors">Data APAR</a>
                <span class="text-gray-300">/</span>
            @endif
            <a href="{{ route('apar.show', $apar->id) }}" class="hover:text-gray-900 transition-colors">{{ $apar->kode }}</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-semibold">Checklist Fisik</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">
                        Checklist Pemeriksaan Fisik APAR
                    </h1>
                    <span class="font-mono text-sm font-semibold text-gray-900 bg-white border border-gray-200 px-2.5 py-0.5 rounded-lg tabular-nums">
                        {{ $apar->kode }}
                    </span>
                    <x-badge :variant="$apar->status_badge_variant" :dot="true">
                        {{ $apar->status_label }}
                    </x-badge>
                    @if(isset($periodeBulan) && isset($periodeTahun))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                            Periode: {{ \App\Models\Pemeriksaan::NAMA_BULAN[$periodeBulan] ?? $periodeBulan }} {{ $periodeTahun }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Verifikasi kondisi visual dan fisik komponen tabung pemadam di lapangan.
                </p>
            </div>

            <!-- Header Action -->
            <div class="flex items-center gap-2">
                @if($apar->can_be_replaced)
                    <a href="{{ route('apar.ganti', $apar->id) }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg text-amber-800 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition min-h-[40px]">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Ganti APAR</span>
                    </a>
                @endif

                @if(isset($periodeBulan) && isset($periodeTahun) && !$jadwal)
                    <x-button variant="secondary" href="{{ route('pemeriksaan.index', ['bulan' => $periodeBulan, 'tahun' => $periodeTahun]) }}" class="min-h-[40px]">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali ke Hasil Inspeksi</span>
                    </x-button>
                @else
                    <x-button variant="secondary" href="{{ route('pemeriksaan.mulai', ['apar_id' => $apar->id, 'jadwal_id' => $jadwal?->id]) }}" class="min-h-[40px]">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali</span>
                    </x-button>
                @endif
            </div>
        </div>
    </div>

    <!-- Error Validation Feedback -->
    @if ($errors->any())
        <div class="max-w-4xl mx-auto mb-6 p-4 rounded-xl border border-red-200 bg-red-50 text-red-900 text-xs" role="alert">
            <div class="flex items-start gap-2.5">
                <span class="w-5 h-5 rounded-full bg-red-600 text-white flex items-center justify-center shrink-0 mt-0.5 font-bold">!</span>
                <div>
                    <h4 class="font-semibold text-sm text-red-950 mb-1">Terdapat kesalahan pengisian formulir:</h4>
                    <ul class="list-disc pl-4 space-y-0.5 text-red-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-4xl mx-auto space-y-6 pb-28 sm:pb-0">
        <!-- 2. Ringkasan Informasi Unit & Sesi -->
        <x-card class="!p-4 sm:!p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 mb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Diperiksa:</span>
                    <span class="font-mono text-sm font-semibold text-gray-900 bg-gray-50 px-2.5 py-0.5 rounded-lg border border-gray-200 tabular-nums">
                        {{ $apar->kode }}
                    </span>
                    @if($apar->nomor_register)
                        <span class="text-xs text-gray-400 font-mono">({{ $apar->nomor_register }})</span>
                    @endif
                </div>

                <div class="text-xs text-gray-600 flex items-center gap-1.5">
                    <span class="font-medium text-gray-500">Penugasan:</span>
                    @if($jadwal)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-800 border border-blue-200 font-medium text-[11px]">
                            {{ $jadwal->nama }}
                        </span>
                    @elseif(isset($periodeBulan) && isset($periodeTahun))
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 font-medium text-[11px]">
                            Inspeksi Bulanan ({{ \App\Models\Pemeriksaan::NAMA_BULAN[$periodeBulan] ?? $periodeBulan }} {{ $periodeTahun }})
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 font-medium text-[11px]">
                            Pemeriksaan Mandiri (Ad-Hoc)
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <span class="block text-gray-400 text-[11px] font-medium">Media & Kapasitas</span>
                    <span class="font-semibold text-gray-900 text-xs">{{ $apar->jenis_media }}</span>
                    <span class="block font-mono text-[11px] text-gray-500">{{ $apar->kapasitas }}</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-[11px] font-medium">Lokasi Penempatan</span>
                    <span class="font-semibold text-gray-900 text-xs">{{ $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? '-' }}</span>
                    <span class="block text-[11px] text-gray-500 truncate">
                        @if($apar->lokasi)
                            {{ $apar->lokasi->keterangan ?? $apar->lokasi->nama }}
                        @else
                            {{ $apar->gedung?->nama ?? '-' }} &bull; {{ $apar->lantai?->nama ?? '-' }}
                        @endif
                    </span>
                </div>
                <div>
                    <span class="block text-gray-400 text-[11px] font-medium">Petugas Pemeriksa</span>
                    <span class="font-semibold text-gray-900 text-xs">{{ $petugas }}</span>
                    <span class="block text-[11px] text-gray-500 font-mono">{{ $waktuSekarang->format('d/m/Y H:i') }} WITA</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-[11px] font-medium">Kondisi Tabung Awal</span>
                    <div class="mt-0.5">
                        <x-badge :variant="$apar->status_badge_variant">
                            {{ $apar->status_label }}
                        </x-badge>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Formulir Pemeriksaan Lapangan -->
        <form action="{{ route('pemeriksaan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 pb-20 sm:pb-0">
            @csrf
            <input type="hidden" name="apar_id" value="{{ $apar->id }}">
            <input type="hidden" name="jadwal_id" value="{{ $jadwal?->id }}">
            @if($isMonthlyWorkflow && isset($periodeTahun))
                <input type="hidden" name="periode_tahun" value="{{ $periodeTahun }}">
            @endif
            @if($isMonthlyWorkflow && isset($periodeBulan))
                <input type="hidden" name="periode_bulan" value="{{ $periodeBulan }}">
            @endif
            <input type="hidden" name="pemeriksa_nama" value="{{ $petugas }}">

            <!-- 3. Checklist Pemeriksaan Fisik (Kotak Bersih rounded-xl p-4) -->
            <x-card
                title="Checklist Pemeriksaan Fisik"
                subtitle="Verifikasi setiap kriteria kelaikan tabung di lokasi. Pastikan komponen fisik memenuhi standar K3RS.">

                <div class="space-y-3 sm:space-y-4">
                    @foreach ($checklistItems as $index => $itemNama)
                    @php
                        $oldHasil = old("items.{$index}.hasil", 'baik');
                        $oldCatatan = old("items.{$index}.catatan", '');
                    @endphp
                    <div class="p-4 sm:p-5 rounded-xl border border-gray-200 bg-white hover:border-gray-300 transition-colors space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <span class="font-mono text-xs text-gray-400 font-semibold w-5 shrink-0 mt-0.5">
                                    {{ sprintf('%02d', $loop->iteration) }}.
                                </span>
                                <div class="min-w-0">
                                    <span class="text-sm font-semibold text-gray-900 block leading-snug">
                                        {{ $itemNama }}
                                    </span>
                                    <input type="hidden" name="items[{{ $index }}][nama]" value="{{ $itemNama }}">
                                </div>
                            </div>

                            <!-- Pilihan Hasil: Baik / Tidak Baik / Lewati (Touch Target min-h-[44px] dengan Indikator Visual Tegas) -->
                            <div class="w-full sm:w-auto grid grid-cols-3 sm:flex sm:flex-wrap items-center gap-2 shrink-0">
                                <label class="cursor-pointer flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-gray-200 text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors has-checked:bg-emerald-50 has-checked:border-emerald-600 has-checked:text-emerald-900 has-checked:font-semibold min-h-[44px]">
                                    <input type="radio" 
                                           name="items[{{ $index }}][hasil]" 
                                           value="baik" 
                                           {{ $oldHasil === 'baik' ? 'checked' : '' }} 
                                           class="accent-emerald-600 radio-item-checklist shrink-0"
                                           data-index="{{ $index }}"
                                           onchange="evaluateOverallHealth()">
                                    <span>Baik</span>
                                </label>

                                <label class="cursor-pointer flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-gray-200 text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors has-checked:bg-red-50 has-checked:border-red-600 has-checked:text-red-900 has-checked:font-semibold min-h-[44px]">
                                    <input type="radio" 
                                           name="items[{{ $index }}][hasil]" 
                                           value="tidak_baik" 
                                           {{ $oldHasil === 'tidak_baik' ? 'checked' : '' }} 
                                           class="accent-red-600 radio-item-checklist shrink-0"
                                           data-index="{{ $index }}"
                                           onchange="evaluateOverallHealth()">
                                    <span>Tidak Baik</span>
                                </label>

                                <label class="cursor-pointer flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-gray-200 text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors has-checked:bg-gray-100 has-checked:border-gray-500 has-checked:text-gray-900 has-checked:font-semibold min-h-[44px]">
                                    <input type="radio" 
                                           name="items[{{ $index }}][hasil]" 
                                           value="tidak_diperiksa" 
                                           {{ $oldHasil === 'tidak_diperiksa' ? 'checked' : '' }} 
                                           class="accent-gray-600 radio-item-checklist shrink-0"
                                           data-index="{{ $index }}"
                                           onchange="evaluateOverallHealth()">
                                    <span>Lewati</span>
                                </label>
                            </div>
                        </div>

                        <!-- Catatan Opsional Item -->
                        <div class="sm:pl-7">
                            <input type="text" 
                                   name="items[{{ $index }}][catatan]" 
                                   value="{{ $oldCatatan }}"
                                   placeholder="{{ !empty($helperMap[$itemNama]) ? 'Catatan temuan (contoh: ' . $helperMap[$itemNama] . ')' : 'Catatan temuan khusus item ini (opsional)...' }}" 
                                   class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-gray-200 bg-gray-50/50 text-gray-900 placeholder-gray-400 focus:bg-white focus:border-gray-900 focus:outline-none transition min-h-[44px]">
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-card>

            <!-- 4. Hasil & Kesimpulan Pemeriksaan -->
            <x-card
                title="Hasil Keseluruhan & Catatan Lapangan"
                subtitle="Penetapan hasil evaluasi kelaikan tabung APAR berdasarkan checklist fisik.">

                @php
                    $initialHasTidakBaik = false;
                    foreach ($checklistItems as $idx => $name) {
                        if (old("items.{$idx}.hasil") === 'tidak_baik') {
                            $initialHasTidakBaik = true;
                            break;
                        }
                    }
                    if (old('kesimpulan_manual') === 'perlu_tindakan') {
                        $initialHasTidakBaik = true;
                    }
                @endphp

                <div class="space-y-4 text-xs">
                    <!-- Banner Rekomendasi Otomatis -->
                    <div id="banner-rekomendasi" class="p-3.5 rounded-xl border flex items-center justify-between gap-3 text-xs {{ $initialHasTidakBaik ? 'bg-red-50 border-red-200 text-red-900' : 'bg-emerald-50 border-emerald-200 text-emerald-900' }}">
                        <div class="flex items-center gap-2">
                            <span id="rekomendasi-dot" class="w-2 h-2 rounded-full shrink-0 {{ $initialHasTidakBaik ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                            <span id="rekomendasi-teks" class="font-medium">
                                @if($initialHasTidakBaik)
                                    Terdapat temuan <strong>Tidak Baik</strong> pada checklist fisik. Hasil keseluruhan disarankan: <strong>Perlu Tindakan</strong>.
                                @else
                                    Seluruh parameter checklist dalam kondisi Baik. Hasil keseluruhan: <strong>Baik</strong>.
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Pilihan Kesimpulan Manual (Jika diperlukan penyesuaian) -->
                    <div>
                        <label for="kesimpulan_manual" class="block font-medium text-gray-700 mb-1.5">
                            Hasil Keseluruhan Pemeriksaan
                        </label>
                        <select id="kesimpulan_manual" 
                                name="kesimpulan_manual" 
                                class="w-full sm:w-80 px-3 py-2 text-xs rounded-lg border border-gray-200 bg-white font-medium text-gray-900 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 min-h-[44px]">
                            <option value="baik" {{ old('kesimpulan_manual', 'baik') === 'baik' ? 'selected' : '' }}>
                                Baik (Siap Digunakan)
                            </option>
                            <option value="perlu_tindakan" {{ old('kesimpulan_manual') === 'perlu_tindakan' ? 'selected' : '' }}>
                                Perlu Tindakan (Terdapat Temuan / Tidak Siap Pakai)
                            </option>
                        </select>
                        <p class="text-[11px] text-gray-500 mt-1">
                            Sistem otomatis menetapkan status <em>Perlu Tindakan</em> bila ditemukan minimal 1 parameter bernilai <strong>Tidak Baik</strong>.
                        </p>
                    </div>

                    <!-- Catatan Umum Pemeriksaan -->
                    <div>
                        <label for="catatan_pemeriksaan" class="block font-medium text-gray-700 mb-1.5">
                            Catatan Umum Pemeriksaan (Opsional)
                        </label>
                        <textarea id="catatan_pemeriksaan" 
                                  name="catatan_pemeriksaan" 
                                  rows="3" 
                                  placeholder="Contoh: Tabung APAR dalam kondisi prima, segel utuh, dipasang kembali ke braket dinding dengan rapi..."
                                  class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition">{{ old('catatan_pemeriksaan') }}</textarea>
                    </div>
                </div>
            </x-card>

            <!-- 5. Foto Dokumentasi Opsional -->
            <x-card
                title="Foto Dokumentasi Pemeriksaan (Opsional)"
                subtitle="Lampirkan foto visual tabung atau temuan fisik manometer jika diperlukan dokumentasi.">

                <div class="space-y-3 text-xs">
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center bg-gray-50/50 hover:bg-gray-50 transition">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>

                        <div class="text-xs text-gray-600 mb-2">
                            <input type="file" 
                                   id="input-foto"
                                   name="foto" 
                                   accept="image/jpeg,image/png,image/jpg" 
                                   class="text-xs text-gray-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#111827] file:text-white hover:file:bg-gray-800 cursor-pointer min-h-[44px]">
                        </div>
                        <p class="text-[11px] text-gray-400">Format JPG, JPEG, atau PNG (Maksimal 5 MB)</p>
                    </div>
                </div>
            </x-card>

            <!-- 6. Tombol Aksi Submit Formulir - Sticky Bottom on Mobile with Safe Area Support -->
            <div class="fixed sm:static bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-md border-t sm:border border-gray-200 sm:rounded-xl px-3.5 pt-3.5 pb-[max(0.875rem,calc(0.875rem+env(safe-area-inset-bottom,0px)))] sm:p-4 shadow-lg sm:shadow-none flex flex-row items-center justify-between gap-3">
                <a href="{{ route('apar.show', $apar->id) }}" 
                   class="w-auto px-4 py-2.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-medium text-xs text-center transition min-h-[44px] flex items-center justify-center shrink-0">
                    Batal
                </a>

                <button type="submit" 
                        class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg bg-[#111827] hover:bg-gray-800 text-white font-medium text-xs text-center transition min-h-[44px] inline-flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Pemeriksaan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Script Evaluasi Dinamis Otomatis -->
    <script>
        function evaluateOverallHealth() {
            const radios = document.querySelectorAll('.radio-item-checklist:checked');
            let hasTidakBaik = false;

            radios.forEach(r => {
                if (r.value === 'tidak_baik') {
                    hasTidakBaik = true;
                }
            });

            const banner = document.getElementById('banner-rekomendasi');
            const dot = document.getElementById('rekomendasi-dot');
            const teks = document.getElementById('rekomendasi-teks');
            const select = document.getElementById('kesimpulan_manual');

            if (hasTidakBaik) {
                banner.className = 'p-3.5 rounded-xl border flex items-center justify-between gap-3 text-xs bg-red-50 border-red-200 text-red-900';
                if (dot) dot.className = 'w-2 h-2 rounded-full shrink-0 bg-red-500';
                teks.innerHTML = 'Terdapat temuan <strong>Tidak Baik</strong> pada checklist fisik. Hasil keseluruhan disarankan: <strong>Perlu Tindakan</strong>.';
                if (select) select.value = 'perlu_tindakan';
            } else {
                banner.className = 'p-3.5 rounded-xl border flex items-center justify-between gap-3 text-xs bg-emerald-50 border-emerald-200 text-emerald-900';
                if (dot) dot.className = 'w-2 h-2 rounded-full shrink-0 bg-emerald-500';
                teks.innerHTML = 'Seluruh parameter checklist dalam kondisi Baik. Hasil keseluruhan: <strong>Baik</strong>.';
                if (select) select.value = 'baik';
            }
        }

        document.addEventListener('DOMContentLoaded', evaluateOverallHealth);
    </script>
</x-layouts.app>