<x-layouts.app title="Edit Pengguna - HAFIS">
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Breadcrumb / Back button -->
        <div>
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-900 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Daftar Pengguna</span>
            </a>
        </div>

        <!-- Section 1: Profil & Identitas -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900">Edit Profil Pengguna</h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Perbarui nama, username, email, dan peran akun {{ $user->name }}.
                    </p>
                </div>
                <div>
                    @if($user->isActive())
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Akun Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Akun Nonaktif
                        </span>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('users.update', $user->id) }}" class="p-5 sm:p-6 space-y-4">
                @csrf
                @method('PUT')

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Nama Lengkap Petugas <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $user->name) }}"
                        required
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
                            value="{{ old('username', $user->username) }}"
                            required
                            class="w-full px-3 py-2 text-xs text-slate-900 bg-white border @error('username') border-rose-300 ring-1 ring-rose-200 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px]" />
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
                            value="{{ old('email', $user->email) }}"
                            class="w-full px-3 py-2 text-xs text-slate-900 bg-white border @error('email') border-rose-300 ring-1 ring-rose-200 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px]" />
                        @error('email')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Peran / Role -->
                <div>
                    <label for="role" class="block text-xs font-semibold text-slate-700 mb-1">
                        Peran Pengguna <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="role"
                        id="role"
                        required
                        class="w-full px-3 py-2 text-xs text-slate-900 bg-white border @error('role') border-rose-300 ring-1 ring-rose-200 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px]">
                        <option value="petugas" {{ old('role', $user->role) === 'petugas' ? 'selected' : '' }}>Petugas K3RS (Operasional Lapangan & Inspeksi)</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Kepala K3RS (Admin: Akses Penuh & Manajemen Pengguna)</option>
                    </select>
                    @error('role')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 shadow-xs transition-colors min-h-[44px] flex items-center justify-center cursor-pointer">
                        Perbarui Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 2: Reset Kata Sandi -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Reset Kata Sandi Akun</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Tetapkan kata sandi baru untuk akun {{ $user->name }}.
                </p>
            </div>

            <form method="POST" action="{{ route('users.reset-password', $user->id) }}" class="p-5 sm:p-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="reset_password" class="block text-xs font-semibold text-slate-700 mb-1">
                            Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="password"
                            name="password"
                            id="reset_password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            class="w-full px-3 py-2 text-xs text-slate-900 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px]" />
                    </div>

                    <div>
                        <label for="reset_password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                            Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="reset_password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi kata sandi baru"
                            class="w-full px-3 py-2 text-xs text-slate-900 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px]" />
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-amber-600 text-white text-xs font-semibold hover:bg-amber-700 shadow-xs transition-colors min-h-[44px] flex items-center justify-center cursor-pointer">
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
