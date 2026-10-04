<x-layouts.app title="HAFIS : Checklist Pemeriksaan Fisik {{ $apar->kode }}">
    <!-- 1. Header with Breadcrumb & Navigation -->
    <div class="pb-5 mb-6 border-b border-slate-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            @if($jadwal)
                <a href="{{ route('jadwal.index') }}" class="hover:text-slate-800 transition-colors">Jadwal Inspeksi</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('jadwal.show', $jadwal->id) }}" class="hover:text-slate-800 transition-colors">{{ $jadwal->nama }}</a>
                <span class="text-slate-300">/</span>
            @else
                <a href="{{ route('apar.index') }}" class="hover:text-slate-800 transition-colors">Data APAR</a>
                <span class="text-slate-300">/</span>
            @endif
            <a href="{{ route('apar.show', $apar->id) }}" class="hover:text-slate-800 transition-colors">{{ $apar->kode }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Checklist Fisik</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Checklist Pemeriksaan Fisik APAR
                    </h1>
                    <span class="font-mono text-base font-extrabold text-slate-900 bg-white border border-slate-200 px-3 py-0.5 rounded-lg tabular-nums shadow-2xs">
                        {{ $apar->kode }}
                    </span>
                    <x-badge :variant="$apar->status_badge_variant" :dot="true">
                        {{ $apar->status_label }}
                    </x-badge>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Verifikasi kondisi visual dan fisik komponen tabung pemadam di lapangan.
                </p>
            </div>

            <!-- Header Action -->
            <div class="flex items-center gap-2">
                <x-button variant="secondary" href="{{ route('pemeriksaan.mulai', ['apar_id' => $apar->id, 'jadwal_id' => $jadwal?->id]) }}" class="min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali</span>
                </x-button>
            </div>
        </div>
    </div>

    <!-- Error Validation Feedback -->
    @if ($errors->any())
        <div class="max-w-4xl mx-auto mb-6 p-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs shadow-2xs" role="alert">
            <div class="flex items-start gap-2.5">
                <span class="w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 mt-0.5 font-bold">!</span>
                <div>
                    <h4 class="font-bold text-sm text-rose-950 mb-1">Terdapat kesalahan pengisian formulir:</h4>
                    <ul class="list-disc pl-4 space-y-0.5 text-rose-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-4xl mx-auto space-y-6 pb-20 sm:pb-0">
        <!-- 2. Ringkasan Informasi Unit & Sesi (Section 4) -->
        <x-card class="!p-4 sm:!p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 mb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Unit Diperiksa:</span>
                    <span class="font-mono text-sm font-extrabold text-slate-900 bg-slate-100 px-2.5 py-0.5 rounded border border-slate-200 tabular-nums">
                        {{ $apar->kode }}
                    </span>
                    @if($apar->nomor_register)
                        <span class="text-xs text-slate-400 font-mono">({{ $apar->nomor_register }})</span>
                    @endif
                </div>

                <div class="text-xs text-slate-600 flex items-center gap-1.5">
                    <span class="font-semibold text-slate-700">Penugasan:</span>
                    @if($jadwal)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-sky-50 text-sky-800 border border-sky-200 font-medium">
                            {{ $jadwal->nama }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 font-medium">
                            Pemeriksaan Mandiri (Ad-Hoc)
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <span class="block text-slate-400 text-[11px] font-medium">Media & Kapasitas</span>
                    <span class="font-semibold text-slate-800 text-xs">{{ $apar->jenis_media }}</span>
                    <span class="block font-mono text-[11px] text-slate-500">{{ $apar->kapasitas }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 text-[11px] font-medium">Lokasi Penempatan</span>
                    <span class="font-semibold text-slate-800 text-xs">{{ $apar->gedung?->nama ?? '-' }} &bull; {{ $apar->lantai?->nama ?? '-' }}</span>
                    <span class="block text-[11px] text-slate-500 truncate">{{ $apar->ruangan?->nama ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 text-[11px] font-medium">Petugas Pemeriksa</span>
                    <span class="font-semibold text-slate-800 text-xs">{{ $petugas }}</span>
                    <span class="block text-[11px] text-slate-500 font-mono">{{ $waktuSekarang->format('d/m/Y H:i') }} WIB</span>
                </div>
                <div>
                    <span class="block text-slate-400 text-[11px] font-medium">Kondisi Tabung Awal</span>
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
            <input type="hidden" name="pemeriksa_nama" value="{{ $petugas }}">

            <!-- 3. Checklist Pemeriksaan Fisik (Section 4, 5, 6, 7) -->
            <x-card
                title="Checklist Pemeriksaan Fisik"
                subtitle="Verifikasi setiap kriteria kelaikan tabung di lokasi. Hasil dapat berupa Baik, Tidak Baik, atau Tidak Diperiksa.">

                <div class="divide-y divide-slate-100 border border-slate-200 rounded-lg overflow-hidden bg-white">
                    @foreach ($checklistItems as $index => $itemNama)
                    @php
                        $oldHasil = old("items.{$index}.hasil", 'baik');
                        $oldCatatan = old("items.{$index}.catatan", '');
                    @endphp
                    <div class="p-3.5 sm:p-4 hover:bg-slate-50/60 transition-colors space-y-2.5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start gap-2.5 flex-1 min-w-0">
                                <span class="font-mono text-xs text-slate-400 font-bold w-5 shrink-0 mt-0.5">
                                    {{ sprintf('%02d', $index + 1) }}.
                                </span>
                                <div class="min-w-0">
                                    <span class="text-xs font-semibold text-slate-900 block leading-snug">
                                        {{ $itemNama }}
                                    </span>
                                    <input type="hidden" name="items[{{ $index }}][nama]" value="{{ $itemNama }}">
                                </div>
                            </div>

                            <!-- Pilihan Hasil: Baik / Tidak Baik / Tidak Diperiksa (Section 7) -->
                            <div class="grid grid-cols-3 sm:flex sm:flex-wrap items-center gap-1.5 pl-7 sm:pl-0 shrink-0">
                                <label class="cursor-pointer flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold hover:bg-slate-50 transition-colors has-checked:bg-emerald-100 has-checked:border-emerald-600 has-checked:text-emerald-950 has-checked:ring-2 has-checked:ring-emerald-500/30 has-checked:font-bold min-h-[44px]">
                                    <input type="radio" 
                                           name="items[{{ $index }}][hasil]" 
                                           value="baik" 
                                           {{ $oldHasil === 'baik' ? 'checked' : '' }} 
                                           class="accent-emerald-600 radio-item-checklist"
                                           data-index="{{ $index }}"
                                           onchange="evaluateOverallHealth()">
                                    <span>&check; Baik</span>
                                </label>

                                <label class="cursor-pointer flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold hover:bg-slate-50 transition-colors has-checked:bg-rose-100 has-checked:border-rose-600 has-checked:text-rose-950 has-checked:ring-2 has-checked:ring-rose-500/30 has-checked:font-bold min-h-[44px]">
                                    <input type="radio" 
                                           name="items[{{ $index }}][hasil]" 
                                           value="tidak_baik" 
                                           {{ $oldHasil === 'tidak_baik' ? 'checked' : '' }} 
                                           class="accent-rose-600 radio-item-checklist"
                                           data-index="{{ $index }}"
                                           onchange="evaluateOverallHealth()">
                                    <span>&times; Tidak Baik</span>
                                </label>

                                <label class="cursor-pointer flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg border border-slate-200 text-xs font-semibold hover:bg-slate-50 transition-colors has-checked:bg-slate-200/80 has-checked:border-slate-500 has-checked:text-slate-900 has-checked:ring-2 has-checked:ring-slate-400/30 has-checked:font-bold min-h-[44px]">
                                    <input type="radio" 
                                           name="items[{{ $index }}][hasil]" 
                                           value="tidak_diperiksa" 
                                           {{ $oldHasil === 'tidak_diperiksa' ? 'checked' : '' }} 
                                           class="accent-slate-600 radio-item-checklist"
                                           data-index="{{ $index }}"
                                           onchange="evaluateOverallHealth()">
                                    <span class="truncate">Lewati</span>
                                </label>
                            </div>
                        </div>

                        <!-- Catatan Opsional Item (Section 7) -->
                        <div class="pl-7 pt-1">
                            <input type="text" 
                                   name="items[{{ $index }}][catatan]" 
                                   value="{{ $oldCatatan }}"
                                   placeholder="Catatan temuan khusus item ini (opsional, misal: selang retak halus / kotor)..." 
                                   class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 placeholder-slate-400 focus:bg-white focus:border-slate-400 focus:outline-none transition min-h-[44px]">
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-card>

            <!-- 4. Hasil & Kesimpulan Pemeriksaan (Section 8) -->
            <x-card
                title="Hasil Keseluruhan & Catatan Lapangan"
                subtitle="Penetapan hasil evaluasi kelaikan tabung APAR berdasarkan checklist fisik.">

                <div class="space-y-4 text-xs">
                    <!-- Banner Rekomendasi Otomatis (Section 8) -->
                    <div id="banner-rekomendasi" class="p-3 rounded-lg border flex items-center justify-between gap-3 text-xs bg-emerald-50 border-emerald-200 text-emerald-900">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span id="rekomendasi-teks" class="font-medium">
                                Seluruh parameter checklist dalam kondisi Baik. Hasil keseluruhan: <strong>Baik</strong>.
                            </span>
                        </div>
                    </div>

                    <!-- Pilihan Kesimpulan Manual (Jika diperlukan penyesuaian) -->
                    <div>
                        <label for="kesimpulan_manual" class="block font-semibold text-slate-700 mb-1">
                            Hasil Keseluruhan Pemeriksaan
                        </label>
                        <select id="kesimpulan_manual" 
                                name="kesimpulan_manual" 
                                class="w-full sm:w-80 px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white font-semibold text-slate-800 focus:outline-none focus:border-slate-400 min-h-[44px]">
                            <option value="baik" {{ old('kesimpulan_manual', 'baik') === 'baik' ? 'selected' : '' }}>
                                Baik (Siap Digunakan)
                            </option>
                            <option value="perlu_tindakan" {{ old('kesimpulan_manual') === 'perlu_tindakan' ? 'selected' : '' }}>
                                Perlu Tindakan (Terdapat Temuan / Tidak Siap Pakai)
                            </option>
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">
                            Sistem otomatis menetapkan status <em>Perlu Tindakan</em> bila ditemukan minimal 1 parameter bernilai <strong>Tidak Baik</strong>.
                        </p>
                    </div>

                    <!-- Catatan Umum Pemeriksaan -->
                    <div>
                        <label for="catatan_pemeriksaan" class="block font-semibold text-slate-700 mb-1">
                            Catatan Umum Pemeriksaan (Opsional)
                        </label>
                        <textarea id="catatan_pemeriksaan" 
                                  name="catatan_pemeriksaan" 
                                  rows="3" 
                                  placeholder="Contoh: Tabung APAR dalam kondisi prima, segel utuh, dipasang kembali ke braket dinding dengan rapi..."
                                  class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white placeholder-slate-400 focus:outline-none focus:border-slate-400 transition">{{ old('catatan_pemeriksaan') }}</textarea>
                    </div>
                </div>
            </x-card>

            <!-- 5. Foto Dokumentasi Opsional (Section 9) -->
            <x-card
                title="Foto Dokumentasi Pemeriksaan (Opsional)"
                subtitle="Lampirkan foto visual tabung atau temuan fisik manometer jika diperlukan dokumentasi.">

                <div class="space-y-3 text-xs">
                    <div class="border-2 border-dashed border-slate-200 rounded-lg p-5 text-center bg-slate-50/50 hover:bg-slate-50 transition">
                        <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>

                        <div class="text-xs text-slate-600 mb-2">
                            <input type="file" 
                                   id="input-foto"
                                   name="foto" 
                                   accept="image/jpeg,image/png,image/jpg" 
                                   class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer min-h-[44px]">
                        </div>
                        <p class="text-[11px] text-slate-400">Format JPG, JPEG, atau PNG (Maksimal 5 MB)</p>
                    </div>
                </div>
            </x-card>

            <!-- 6. Tombol Aksi Submit Formulir (Section 10) - Sticky Bottom on Mobile -->
            <div class="fixed sm:static bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-xs border-t sm:border border-slate-200 sm:rounded-xl p-3.5 sm:p-4 shadow-lg sm:shadow-2xs flex flex-row items-center justify-between gap-3">
                <a href="{{ route('apar.show', $apar->id) }}" 
                   class="w-auto px-4 py-2.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs text-center transition min-h-[44px] flex items-center justify-center shrink-0">
                    Batal
                </a>

                <button type="submit" 
                        class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs text-center transition shadow-2xs min-h-[44px] inline-flex items-center justify-center gap-2">
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
            const teks = document.getElementById('rekomendasi-teks');
            const select = document.getElementById('kesimpulan_manual');

            if (hasTidakBaik) {
                banner.className = 'p-3 rounded-lg border flex items-center justify-between gap-3 text-xs bg-rose-50 border-rose-200 text-rose-900';
                teks.innerHTML = 'Terdapat temuan <strong>Tidak Baik</strong> pada checklist fisik. Hasil keseluruhan disarankan: <strong>Perlu Tindakan</strong>.';
                select.value = 'perlu_tindakan';
            } else {
                banner.className = 'p-3 rounded-lg border flex items-center justify-between gap-3 text-xs bg-emerald-50 border-emerald-200 text-emerald-900';
                teks.innerHTML = 'Seluruh parameter checklist dalam kondisi Baik. Hasil keseluruhan: <strong>Baik</strong>.';
                select.value = 'baik';
            }
        }

        document.addEventListener('DOMContentLoaded', evaluateOverallHealth);
    </script>
</x-layouts.app>