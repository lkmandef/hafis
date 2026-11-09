<x-layouts.app title="HAFIS : Koreksi Pemeriksaan Fisik {{ $apar->kode }}">
    <!-- 1. Header with Breadcrumb & Navigation -->
    <div class="pb-5 mb-6 border-b border-gray-200">
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ route('pemeriksaan.index', ['bulan' => $periodeBulan, 'tahun' => $periodeTahun]) }}" class="hover:text-gray-900 transition-colors">Pemeriksaan Bulanan</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('pemeriksaan.show', $pemeriksaan->id) }}" class="hover:text-gray-900 transition-colors">{{ $apar->kode }}</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-semibold">Koreksi Data</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">
                        Koreksi Pemeriksaan Fisik APAR
                    </h1>
                    <span class="font-mono text-sm font-semibold text-gray-900 bg-white border border-gray-200 px-2.5 py-0.5 rounded-lg tabular-nums">
                        {{ $apar->kode }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                        Periode: {{ \App\Models\Pemeriksaan::NAMA_BULAN[$periodeBulan] ?? $periodeBulan }} {{ $periodeTahun }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Pembaruan catatan evaluasi fisik bulanan tanpa membuat duplikasi riwayat inspeksi.
                </p>
            </div>

            <!-- Header Action -->
            <div class="flex items-center gap-2">
                <x-button variant="secondary" href="{{ route('pemeriksaan.show', $pemeriksaan->id) }}" class="min-h-[40px]">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Batal</span>
                </x-button>
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

    <div class="max-w-4xl mx-auto space-y-6 pb-20 sm:pb-0">
        <!-- Banner Mode Koreksi Data -->
        <div class="p-4 rounded-xl border border-blue-200 bg-blue-50 text-blue-950 text-xs">
            <div class="flex items-start gap-3">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">i</span>
                <div>
                    <h4 class="font-semibold text-sm text-blue-900 mb-0.5">Mode Koreksi Periode {{ \App\Models\Pemeriksaan::NAMA_BULAN[$periodeBulan] ?? $periodeBulan }} {{ $periodeTahun }}</h4>
                    <p class="text-blue-800 leading-relaxed text-xs">
                        Unit APAR ini telah diperiksa sebelumnya. Formulir ini digunakan untuk memperbaiki temuan, mengubah hasil evaluasi kelaikan, atau memperbarui dokumentasi. 
                        Nomor identitas riwayat tetap dipertahankan (ID #{{ $pemeriksaan->id }}).
                    </p>
                </div>
            </div>
        </div>

        <!-- Ringkasan Unit -->
        <x-card class="!p-4 sm:!p-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <span class="block text-gray-400 text-[11px] font-medium">Unit APAR</span>
                    <span class="font-semibold font-mono text-gray-900 text-xs">{{ $apar->kode }}</span>
                    <span class="block text-[11px] text-gray-500">{{ $apar->jenis_media }} ({{ $apar->kapasitas }})</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-[11px] font-medium">Lokasi K3RS</span>
                    <span class="font-semibold text-gray-900 text-xs">{{ $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? '-' }}</span>
                    <span class="block text-[11px] text-gray-500 truncate">
                        @if($apar->lokasi)
                            {{ $apar->lokasi->keterangan ?? $apar->lokasi->nama }}
                        @else
                            {{ $apar->ruangan?->nama ?? '-' }}
                        @endif
                    </span>
                </div>
                <div>
                    <span class="block text-gray-400 text-[11px] font-medium">Petugas Awal</span>
                    <span class="font-semibold text-gray-900 text-xs">{{ $pemeriksaan->pemeriksa_nama }}</span>
                    <span class="block text-[11px] text-gray-500 font-mono">{{ $pemeriksaan->tanggal_pemeriksaan?->format('d/m/Y H:i') ?? '-' }} WITA</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-[11px] font-medium">Hasil Tersimpan</span>
                    <div class="mt-0.5">
                        <x-badge :variant="$pemeriksaan->hasil_badge_variant">
                            {{ $pemeriksaan->hasil_label }}
                        </x-badge>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Formulir Koreksi -->
        <form action="{{ route('pemeriksaan.update', $pemeriksaan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Checklist Items (Kotak Bersih rounded-xl p-4) -->
            <x-card
                title="Koreksi Checklist Fisik (5 Parameter K3RS)"
                subtitle="Perbarui evaluasi setiap parameter fisik tabung APAR di bawah ini.">

                <div class="space-y-3 sm:space-y-4">
                    @foreach ($checklistItems as $index => $itemNama)
                        @php
                            $savedDetail = $savedDetails->get($index);
                            $oldHasil = old("items.{$index}.hasil", $savedDetail?->hasil ?? 'baik');
                            $oldCatatan = old("items.{$index}.catatan", $savedDetail?->catatan ?? '');
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

                                <!-- Radio choices with min-h-[44px] -->
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
                                       placeholder="Catatan temuan khusus item ini (opsional)..." 
                                       class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-gray-200 bg-gray-50/50 text-gray-900 placeholder-gray-400 focus:bg-white focus:border-gray-900 focus:outline-none transition min-h-[44px]">
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>

            <!-- Kesimpulan & Petugas -->
            <x-card
                title="Kesimpulan & Petugas Pengoreksi"
                subtitle="Verifikasi nama petugas dan catatan perbaikan.">

                @php
                    $initialHasTidakBaik = false;
                    foreach ($checklistItems as $idx => $name) {
                        $detail = $savedDetails->get($idx);
                        if (old("items.{$idx}.hasil", $detail?->hasil) === 'tidak_baik') {
                            $initialHasTidakBaik = true;
                            break;
                        }
                    }
                    if (old('kesimpulan_manual', $pemeriksaan->hasil_keseluruhan) === 'perlu_tindakan') {
                        $initialHasTidakBaik = true;
                    }
                @endphp

                <div class="space-y-4 text-xs">
                    <!-- Banner Status Otomatis -->
                    <div id="banner-rekomendasi" class="p-3.5 rounded-xl border flex items-center justify-between gap-3 text-xs {{ $initialHasTidakBaik ? 'bg-red-50 border-red-200 text-red-900' : 'bg-emerald-50 border-emerald-200 text-emerald-900' }}">
                        <div class="flex items-center gap-2">
                            <span id="rekomendasi-dot" class="w-2 h-2 rounded-full shrink-0 {{ $initialHasTidakBaik ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                            <span id="rekomendasi-teks" class="font-medium">
                                @if($initialHasTidakBaik)
                                    Ditemukan parameter dalam kondisi <strong>Tidak Baik</strong>. Hasil otomatis: <strong>Perlu Tindakan</strong>.
                                @else
                                    Seluruh parameter checklist dalam kondisi Baik. Hasil keseluruhan: <strong>Baik</strong>.
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-gray-700 mb-1.5">
                                Petugas Pengoreksi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="pemeriksa_nama" 
                                   value="{{ old('pemeriksa_nama', auth()->user()?->name ?? $pemeriksaan->pemeriksa_nama) }}" 
                                   required 
                                   class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-gray-200 bg-white text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 transition min-h-[44px]">
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700 mb-1.5">
                                Kesimpulan Manual (Opsional)
                            </label>
                            <select name="kesimpulan_manual" id="kesimpulan_manual" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-200 bg-white font-medium text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 transition min-h-[44px]">
                                <option value="baik" {{ old('kesimpulan_manual', $pemeriksaan->hasil_keseluruhan) === 'baik' ? 'selected' : '' }}>Baik</option>
                                <option value="perlu_tindakan" {{ old('kesimpulan_manual', $pemeriksaan->hasil_keseluruhan) === 'perlu_tindakan' ? 'selected' : '' }}>Perlu Tindakan</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1.5">
                            Catatan Koreksi / Alasan Perubahan
                        </label>
                        <textarea name="catatan_pemeriksaan" rows="3" placeholder="Tuliskan catatan perbaikan fisik atau alasan perubahan checklist..." class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition">{{ old('catatan_pemeriksaan', $pemeriksaan->catatan) }}</textarea>
                    </div>

                    <!-- Upload Foto Dokumentasi Tambahan -->
                    <div>
                        <label class="block font-medium text-gray-700 mb-1.5">
                            Perbarui Foto Dokumentasi Fisik (Opsional)
                        </label>
                        @if($pemeriksaan->foto_path)
                            <div class="mb-2 flex items-center gap-2 text-[11px] text-gray-500">
                                <span>Foto tersimpan saat ini:</span>
                                <a href="{{ asset('storage/' . $pemeriksaan->foto_path) }}" target="_blank" class="text-blue-600 hover:underline font-mono">Lihat Foto</a>
                            </div>
                        @endif
                        <input type="file" name="foto" accept="image/*" class="w-full text-xs file:mr-4 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-gray-100 file:text-gray-800 hover:file:bg-gray-200 cursor-pointer">
                    </div>
                </div>
            </x-card>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('pemeriksaan.show', $pemeriksaan->id) }}" class="px-4 py-2.5 text-xs font-medium text-gray-600 hover:text-gray-900 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-xs font-medium text-white bg-[#111827] rounded-lg hover:bg-gray-800 transition min-h-[44px] cursor-pointer">
                    Simpan Koreksi Data
                </button>
            </div>
        </form>
    </div>

    <!-- Client-side Logic Evaluasi -->
    <script>
        function evaluateOverallHealth() {
            const radios = document.querySelectorAll('.radio-item-checklist:checked');
            let hasTidakBaik = false;

            radios.forEach(radio => {
                if (radio.value === 'tidak_baik') {
                    hasTidakBaik = true;
                }
            });

            const banner = document.getElementById('banner-rekomendasi');
            const dot = document.getElementById('rekomendasi-dot');
            const teks = document.getElementById('rekomendasi-teks');
            const selectManual = document.getElementById('kesimpulan_manual');

            if (hasTidakBaik) {
                banner.className = 'p-3.5 rounded-xl border flex items-center justify-between gap-3 text-xs bg-red-50 border-red-200 text-red-900';
                if (dot) dot.className = 'w-2 h-2 rounded-full shrink-0 bg-red-500';
                teks.innerHTML = 'Ditemukan parameter dalam kondisi <strong>Tidak Baik</strong>. Hasil otomatis: <strong>Perlu Tindakan</strong>.';
                if (selectManual) {
                    selectManual.value = 'perlu_tindakan';
                }
            } else {
                banner.className = 'p-3.5 rounded-xl border flex items-center justify-between gap-3 text-xs bg-emerald-50 border-emerald-200 text-emerald-900';
                if (dot) dot.className = 'w-2 h-2 rounded-full shrink-0 bg-emerald-500';
                teks.innerHTML = 'Seluruh parameter checklist dalam kondisi Baik. Hasil keseluruhan: <strong>Baik</strong>.';
                if (selectManual) {
                    selectManual.value = 'baik';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', evaluateOverallHealth);
    </script>
</x-layouts.app>
