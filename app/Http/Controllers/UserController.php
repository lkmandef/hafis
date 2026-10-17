<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh akun pengguna sistem HAFIS
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');
        $roleFilter = $request->input('role');
        $statusFilter = $request->input('status');

        $query = User::query();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($roleFilter) && in_array($roleFilter, User::ROLES, true)) {
            $query->where('role', $roleFilter);
        }

        if ($statusFilter === 'aktif') {
            $query->where('is_active', true);
        } elseif ($statusFilter === 'nonaktif') {
            $query->where('is_active', false);
        }

        $users = $query->orderBy('role', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', User::ROLE_ADMIN)->count(),
            'petugas' => User::where('role', User::ROLE_PETUGAS)->count(),
            'aktif' => User::where('is_active', true)->count(),
            'nonaktif' => User::where('is_active', false)->count(),
        ];

        return view('users.index', compact('users', 'stats', 'search', 'roleFilter', 'statusFilter'));
    }

    /**
     * Tampilkan formulir pembuatan akun pengguna baru
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Simpan akun pengguna baru ke database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(User::ROLES)],
        ], [
            'name.required' => 'Nama lengkap petugas / pejabat K3RS wajib diisi.',
            'username.required' => 'Username login wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh memuat huruf, angka, strip (-), dan garis bawah (_).',
            'username.unique' => 'Username tersebut sudah terdaftar di sistem.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email tersebut sudah terdaftar di sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal harus terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'role.required' => 'Peran pengguna wajib ditentukan.',
            'role.in' => 'Peran pengguna harus berupa Admin (Kepala K3RS) atau Petugas K3RS.',
        ]);

        $validated['username'] = strtolower(trim($validated['username']));
        $validated['is_active'] = true;

        $user = User::create($validated);

        return redirect()->route('users.index')
            ->with('success', "Akun pengguna {$user->name} ({$user->username}) berhasil dibuat.");
    }

    /**
     * Tampilkan formulir pengeditan profil akun pengguna
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Perbarui data profil akun pengguna
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(User::ROLES)],
        ], [
            'name.required' => 'Nama lengkap petugas / pejabat K3RS wajib diisi.',
            'username.required' => 'Username login wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh memuat huruf, angka, strip (-), dan garis bawah (_).',
            'username.unique' => 'Username tersebut sudah digunakan oleh akun lain.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email tersebut sudah digunakan oleh akun lain.',
            'role.required' => 'Peran pengguna wajib ditentukan.',
            'role.in' => 'Peran pengguna harus berupa Admin (Kepala K3RS) atau Petugas K3RS.',
        ]);

        // Pencegahan penurunan peran diri sendiri
        if ($user->id === auth()->id() && $validated['role'] !== User::ROLE_ADMIN) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'Anda tidak dapat menurunkan peran akun Anda sendiri dari Kepala K3RS.']);
        }

        // Pencegahan penurunan peran admin terakhir
        if ($user->isAdmin() && $validated['role'] !== User::ROLE_ADMIN) {
            $activeAdminCount = User::where('role', User::ROLE_ADMIN)->where('is_active', true)->count();
            if ($activeAdminCount <= 1) {
                return back()
                    ->withInput()
                    ->withErrors(['role' => 'Tidak dapat mengubah peran karena ini adalah akun admin terakhir yang aktif di sistem.']);
            }
        }

        $validated['username'] = strtolower(trim($validated['username']));
        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', "Data akun pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Beralih status keaktifan akun (aktif / nonaktif)
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Pencegahan penonaktifan diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang Anda gunakan saat ini.');
        }

        // Pencegahan penonaktifan admin terakhir
        if ($user->isAdmin() && $user->isActive()) {
            $activeAdminCount = User::where('role', User::ROLE_ADMIN)->where('is_active', true)->count();
            if ($activeAdminCount <= 1) {
                return back()->with('error', 'Tidak dapat menonaktifkan akun admin terakhir yang aktif di sistem.');
            }
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        return redirect()->route('users.index')
            ->with('success', "Akun pengguna {$user->name} ({$user->username}) berhasil {$statusText}.");
    }

    /**
     * Reset kata sandi akun pengguna oleh Admin
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal harus terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->password = $validated['password'];
        $user->save();

        return redirect()->route('users.index')
            ->with('success', "Kata sandi untuk akun {$user->name} ({$user->username}) berhasil diubah.");
    }
}
