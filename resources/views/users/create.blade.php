<x-layouts.app title="Tambah Pengguna Baru - HAFIS">
    <div class="max-w-2xl mx-auto">
        <!-- Breadcrumb / Back button -->
        <div class="mb-4">
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-900 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Daftar Pengguna</span>
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100">
                <h1 class="text-base sm:text-lg font-bold text-slate-900">Tambah Akun Petugas Baru</h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Daftarkan akun operasional baru untuk staf atau pejabat K3RS.
                </p>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="p-5 sm:p-6 space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Nama Lengkap Petugas <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        placeholder="Contoh: Budi Santoso, S.Kep"
                        class="w-full px-3 py-2 text-xs text-slate-900 bg-white border @error('name') border-rose-300 ring-1 ring-rose-200 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px]" />
                    @error('name')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="username" class="block text-xs font-semibold text-slate-700 mb-1">
                            Username Login <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="username"
                            id="username"
                            value="{{ old('username') }}"
                            required
                            placeholder="Contoh: k3_budi"
                            class="w-full px-3 py-2 text-xs text-slate-900 bg-white border @error('username') border-rose-300 ring-1 ring-rose-200 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px]" />
                        <p class="text-[10px] text-slate-400 mt-1">Huruf kecil, angka, dan underscore (_).</p>
                        @error('username')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                            Alamat Email <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            placeholder="budi@rsansarisaleh.id"
                            class="w-full px-3 py-2 text-xs text-slate-900 bg-white border @error('email') border-rose-300 ring-1 ring-rose-200 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px]" />
                        @error('email')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Peran / Role -->
                <div>
                    <label for="role" class="block text-xs font-semibold text-slate-700 mb-1">
                        Peran & Hak Akses <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="role"
                        id="role"
                        required
                        class="w-full px-3 py-2 text-xs text-slate-900 bg-white border @error('role') border-rose-300 ring-1 ring-rose-200 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px]">
                        <option value="petugas" {{ old('role', 'petugas') === 'petugas' ? 'selected' : '' }}>Petugas K3RS (Operasional Lapangan & Inspeksi)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Kepala K3RS (Admin: Akses Penuh & Manajemen Pengguna)</option>
                    </select>
                    @error('role')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password & Konfirmasi Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                            Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            class="w-full px-3 py-2 text-xs text-slate-900 bg-white border @error('password') border-rose-300 ring-1 ring-rose-200 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px]" />
                        @error('password')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                            Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi kata sandi"
                            class="w-full px-3 py-2 text-xs text-slate-900 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px]" />
                    </div>
                </div>

                <!-- Aksi Form -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}" class="px-4 py-2.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors min-h-[44px] flex items-center justify-center">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 shadow-xs transition-colors min-h-[44px] flex items-center justify-center cursor-pointer">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
