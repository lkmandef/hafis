<x-layouts.app title="Checklist Pemeriksaan - Pengaturan Sistem - HAFIS">
    <!-- Header Halaman & Tab Navigasi Pengaturan -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center p-1.5 rounded-lg bg-[#111827] text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </span>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Pengaturan & Akses</h1>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Konfigurasi sistem HAFIS: manajemen akun petugas dan parameter checklist pemeriksaan fisik APAR.
                </p>
            </div>

            <!-- Tombol Tambah Parameter -->
            <div>
                <button type="button" onclick="openModal('modal-tambah-checklist')" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#111827] hover:bg-gray-800 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors min-h-[44px] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Kriteria Baru</span>
                </button>
            </div>
        </div>

        <!-- Tab Navigasi Pengaturan -->
        <div class="flex items-center gap-2 border-b border-gray-200">
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Pengguna & Akses</span>
            </a>
            <a href="{{ route('pengaturan.checklist.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold border-b-2 border-gray-900 text-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span>Checklist Pemeriksaan</span>
            </a>
        </div>
    </div>

    <!-- Notifikasi Sukses / Galat / Peringatan -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50/70 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 p-1 rounded" aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 p-4 rounded-xl border border-amber-200 bg-amber-50/70 text-amber-900 text-xs flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-950 p-1 rounded" aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl border border-rose-200 bg-rose-50/70 text-rose-900 text-xs flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-950 p-1 rounded" aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div role="alert" class="mb-6 p-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs shadow-xs">
            <div class="font-semibold mb-1 flex items-center gap-1.5 text-rose-950">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Terdapat kesalahan pengisian data:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-800 pl-5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Kartu Statistik Parameter -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Kriteria</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-xl sm:text-2xl font-bold font-mono text-gray-900 tabular-nums">{{ $stats['total'] }}</span>
                <span class="text-[10px] text-gray-400 font-medium">Parameter terdaftar</span>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Kriteria Aktif</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-xl sm:text-2xl font-bold font-mono text-emerald-700 tabular-nums">{{ $stats['aktif'] }}</span>
                <span class="text-[10px] text-emerald-600 font-medium">Tampil di form</span>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Nonaktif</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-xl sm:text-2xl font-bold font-mono text-gray-600 tabular-nums">{{ $stats['nonaktif'] }}</span>
                <span class="text-[10px] text-gray-400 font-medium">Dilewati</span>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Standar K3RS</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-xl sm:text-2xl font-bold font-mono text-gray-900 tabular-nums">5</span>
                <span class="text-[10px] text-gray-500 font-medium">Item bawaan</span>
            </div>
        </div>
    </div>

    <!-- Penjelasan Singkat Alur Dinamis -->
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 shadow-xs flex items-start gap-3">
        <svg class="w-5 h-5 text-slate-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="text-xs text-slate-700 leading-relaxed">
            <span class="font-semibold text-slate-900">Parameter Dinamis Formulir Inspeksi:</span>
            Setiap kriteria yang berstatus <strong class="text-emerald-700">Aktif</strong> akan otomatis tampil pada formulir checklist pemeriksaan fisik APAR di lapangan. Jika kriteria dinonaktifkan, data riwayat inspeksi sebelumnya tetap aman dan tidak terhapus.
        </div>
    </div>

    <!-- Tabel Master Checklist -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs">
        <!-- Mobile Card Reflow (sm:hidden) -->
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse($checklists as $item)
                <div class="p-4 space-y-3 bg-white">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-gray-100 text-gray-800 font-mono font-semibold text-xs border border-gray-200">
                                {{ sprintf('%02d', $item->urutan) }}
                            </span>
                            <div>
                                <div class="font-semibold text-gray-900 text-xs flex items-center gap-1.5">
                                    <span>{{ $item->nama }}</span>
                                    @if($item->is_default)
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 font-medium">Bawaan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div>
                            @if($item->is_active)
                                <x-badge variant="success" size="sm">Aktif</x-badge>
                            @else
                                <x-badge variant="neutral" size="sm">Nonaktif</x-badge>
                            @endif
                        </div>
                    </div>

                    @if($item->deskripsi_bantuan)
                        <div class="text-xs text-gray-600 bg-gray-50 p-2.5 rounded-lg border border-gray-100 leading-relaxed">
                            <span class="text-[10px] uppercase font-semibold text-gray-500 block mb-0.5">Panduan Temuan:</span>
                            {{ $item->deskripsi_bantuan }}
                        </div>
                    @endif

                    <div class="pt-1 flex items-center gap-2">
                        <button type="button" 
                                onclick="editChecklist({{ $item->toJson() }})" 
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 text-xs font-semibold shadow-2xs min-h-[40px] transition-colors cursor-pointer">
                            Edit
                        </button>

                        <form method="POST" action="{{ route('pengaturan.checklist.toggle-status', $item->id) }}"
                              data-confirm
                              data-confirm-title="{{ $item->is_active ? 'Nonaktifkan Kriteria' : 'Aktifkan Kembali Kriteria' }}"
                              data-confirm-message="Apakah Anda yakin ingin {{ $item->is_active ? 'menonaktifkan' : 'mengaktifkan kembali' }} kriteria pemeriksaan '{{ $item->nama }}'?"
                              data-confirm-type="{{ $item->is_active ? 'warning' : 'success' }}"
                              data-confirm-text="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                              class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-lg {{ $item->is_active ? 'text-amber-700 hover:bg-amber-50 border-amber-200 bg-amber-50/50' : 'text-emerald-700 hover:bg-emerald-50 border-emerald-200 bg-emerald-50/50' }} border min-h-[40px] inline-flex items-center justify-center text-xs font-semibold transition-colors cursor-pointer">
                                {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>

                        @if(!$item->is_default)
                            <form method="POST" action="{{ route('pengaturan.checklist.destroy', $item->id) }}"
                                  data-confirm
                                  data-confirm-title="Hapus Kriteria Pemeriksaan"
                                  data-confirm-message="Apakah Anda yakin ingin menghapus kriteria '{{ $item->nama }}'? Jika kriteria memiliki riwayat pemeriksaan masa lalu, statusnya akan dialihkan ke nonaktif."
                                  data-confirm-type="danger"
                                  data-confirm-text="Hapus Parameter"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-2 rounded-lg text-rose-700 hover:bg-rose-50 border-rose-200 bg-rose-50/50 border min-h-[40px] inline-flex items-center justify-center text-xs font-semibold transition-colors cursor-pointer">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white">
                    <p class="text-xs text-gray-500">Belum ada data kriteria pemeriksaan.</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table (hidden sm:block) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs min-w-[720px]">
                <thead>
                    <tr class="border-b border-gray-200 bg-[#f8f9fa] text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-3 pl-5 sm:pl-6 pr-3 w-16 text-center">Urutan</th>
                        <th class="py-3 px-4 w-56">Nama Kriteria</th>
                        <th class="py-3 px-4">Panduan / Contoh Catatan Temuan</th>
                        <th class="py-3 px-4 w-28 text-center">Status</th>
                        <th class="py-3 pl-4 pr-5 sm:pr-6 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($checklists as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3.5 pl-5 sm:pl-6 pr-3 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-gray-100 text-gray-800 font-mono font-semibold text-xs border border-gray-200">
                                    {{ sprintf('%02d', $item->urutan) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-gray-900">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $item->nama }}</span>
                                    @if($item->is_default)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200" title="Standar bawaan K3RS">
                                            Bawaan
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 leading-relaxed">
                                @if($item->deskripsi_bantuan)
                                    <span class="text-xs">{{ $item->deskripsi_bantuan }}</span>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">Tidak ada panduan khusus</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->is_active)
                                    <x-badge variant="success" size="sm">Aktif</x-badge>
                                @else
                                    <x-badge variant="neutral" size="sm">Nonaktif</x-badge>
                                @endif
                            </td>
                            <td class="py-3.5 pl-4 pr-5 sm:pr-6 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Tombol Edit -->
                                    <button type="button" 
                                            onclick="editChecklist({{ $item->toJson() }})" 
                                            class="p-1.5 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 border border-gray-200 min-h-[36px] min-w-[36px] inline-flex items-center justify-center transition-colors cursor-pointer" 
                                            title="Edit Kriteria">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>

                                    <!-- Tombol Toggle Status -->
                                    <form method="POST" action="{{ route('pengaturan.checklist.toggle-status', $item->id) }}"
                                          data-confirm
                                          data-confirm-title="{{ $item->is_active ? 'Nonaktifkan Kriteria' : 'Aktifkan Kembali Kriteria' }}"
                                          data-confirm-message="Apakah Anda yakin ingin {{ $item->is_active ? 'menonaktifkan' : 'mengaktifkan kembali' }} kriteria pemeriksaan '{{ $item->nama }}'?"
                                          data-confirm-type="{{ $item->is_active ? 'warning' : 'success' }}"
                                          data-confirm-text="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                          class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg {{ $item->is_active ? 'text-amber-600 hover:bg-amber-50 border-amber-200' : 'text-emerald-600 hover:bg-emerald-50 border-emerald-200' }} border min-h-[36px] min-w-[36px] inline-flex items-center justify-center transition-colors cursor-pointer" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            @if($item->is_active)
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Tombol Hapus (Hanya Non-Default) -->
                                    @if(!$item->is_default)
                                        <form method="POST" action="{{ route('pengaturan.checklist.destroy', $item->id) }}"
                                              data-confirm
                                              data-confirm-title="Hapus Kriteria Pemeriksaan"
                                              data-confirm-message="Apakah Anda yakin ingin menghapus kriteria '{{ $item->nama }}'? Jika kriteria memiliki riwayat pemeriksaan masa lalu, statusnya akan dialihkan ke nonaktif."
                                              data-confirm-type="danger"
                                              data-confirm-text="Hapus Parameter"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 border-rose-200 border min-h-[36px] min-w-[36px] inline-flex items-center justify-center transition-colors cursor-pointer" title="Hapus Kriteria">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">
                                Belum ada data parameter checklist pemeriksaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL TAMBAH PARAMETER CHECKLIST -->
    <div id="modal-tambah-checklist" class="hidden fixed inset-0 z-50 overflow-y-auto overscroll-contain p-4 flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="modal-tambah-title">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeModal('modal-tambah-checklist')" aria-hidden="true"></div>
        <div class="relative bg-white rounded-2xl border border-gray-200 shadow-2xl w-full max-w-lg p-5 sm:p-6 overflow-hidden transform transition-all duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-[#111827] text-white flex items-center justify-center text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </span>
                    <h3 id="modal-tambah-title" class="text-base font-bold text-gray-900">Tambah Kriteria Pemeriksaan</h3>
                </div>
                <button type="button" onclick="closeModal('modal-tambah-checklist')" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none" aria-label="Tutup formulir">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('pengaturan.checklist.store') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="tambah-nama" class="block font-semibold text-gray-700 text-xs mb-1">
                        Nama Parameter / Kriteria <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="tambah-nama" name="nama" required autocomplete="off" placeholder="Contoh: Kebersihan Kotak APAR" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                    <p class="text-[11px] text-gray-500 mt-1">Nama kriteria harus jelas dan dapat diverifikasi secara fisik oleh petugas di lapangan.</p>
                </div>

                <div>
                    <label for="tambah-deskripsi" class="block font-semibold text-gray-700 text-xs mb-1">
                        Panduan Catatan / Contoh Temuan <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea id="tambah-deskripsi" name="deskripsi_bantuan" rows="2" placeholder="Contoh: Kotak APAR bersih, bebas debu, kaca utuh, dan kunci darurat tersedia..." class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900"></textarea>
                    <p class="text-[11px] text-gray-500 mt-1">Akan ditampilkan sebagai placeholder atau petunjuk pengisian catatan di formulir inspeksi.</p>
                </div>

                <div>
                    <label for="tambah-urutan" class="block font-semibold text-gray-700 text-xs mb-1">
                        Nomor Urut Tampilan <span class="text-gray-400 font-normal">(1 - 99)</span>
                    </label>
                    <input type="number" id="tambah-urutan" name="urutan" min="1" max="99" value="{{ $stats['total'] + 1 }}" class="w-28 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-mono text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-tambah-checklist')" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 min-h-[40px] cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-[#111827] text-white text-xs font-semibold hover:bg-gray-800 transition shadow-xs min-h-[40px] cursor-pointer">
                        Simpan Kriteria
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PARAMETER CHECKLIST -->
    <div id="modal-edit-checklist" class="hidden fixed inset-0 z-50 overflow-y-auto overscroll-contain p-4 flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="modal-edit-title">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeModal('modal-edit-checklist')" aria-hidden="true"></div>
        <div class="relative bg-white rounded-2xl border border-gray-200 shadow-2xl w-full max-w-lg p-5 sm:p-6 overflow-hidden transform transition-all duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-[#111827] text-white flex items-center justify-center text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </span>
                    <h3 id="modal-edit-title" class="text-base font-bold text-gray-900">Edit Kriteria Pemeriksaan</h3>
                </div>
                <button type="button" onclick="closeModal('modal-edit-checklist')" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none" aria-label="Tutup formulir">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="form-edit-checklist" method="POST" action="" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit-nama" class="block font-semibold text-gray-700 text-xs mb-1">
                        Nama Parameter / Kriteria <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="edit-nama" name="nama" required autocomplete="off" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                </div>

                <div>
                    <label for="edit-deskripsi" class="block font-semibold text-gray-700 text-xs mb-1">
                        Panduan Catatan / Contoh Temuan <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea id="edit-deskripsi" name="deskripsi_bantuan" rows="2" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900"></textarea>
                </div>

                <div>
                    <label for="edit-urutan" class="block font-semibold text-gray-700 text-xs mb-1">
                        Nomor Urut Tampilan <span class="text-gray-400 font-normal">(1 - 99)</span>
                    </label>
                    <input type="number" id="edit-urutan" name="urutan" min="1" max="99" class="w-28 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-mono text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-edit-checklist')" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 min-h-[40px] cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-[#111827] text-white text-xs font-semibold hover:bg-gray-800 transition shadow-xs min-h-[40px] cursor-pointer">
                        Perbarui Parameter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Manajemen Modal -->
    <script>
        let previousActiveElement = null;

        function openModal(id) {
            const el = document.getElementById(id);
            if (el) {
                previousActiveElement = document.activeElement;
                el.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                setTimeout(() => {
                    const focusable = el.querySelector('input:not([type=hidden]):not([disabled]), textarea:not([disabled]), button:not([disabled])');
                    if (focusable) focusable.focus();
                }, 50);
            }
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                if (previousActiveElement && typeof previousActiveElement.focus === 'function') {
                    previousActiveElement.focus();
                }
            }
        }

        function editChecklist(item) {
            const form = document.getElementById('form-edit-checklist');
            form.action = `/pengaturan/checklist/${item.id}`;
            document.getElementById('edit-nama').value = item.nama || '';
            document.getElementById('edit-deskripsi').value = item.deskripsi_bantuan || '';
            document.getElementById('edit-urutan').value = item.urutan || 1;
            openModal('modal-edit-checklist');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('modal-tambah-checklist');
                closeModal('modal-edit-checklist');
            }
        });
    </script>
</x-layouts.app>

