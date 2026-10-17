<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_PETUGAS = 'petugas';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_PETUGAS,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Cek apakah pengguna memiliki peran Admin / Kepala K3RS
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Cek apakah pengguna memiliki peran Petugas K3RS
     */
    public function isPetugas(): bool
    {
        return $this->role === self::ROLE_PETUGAS;
    }

    /**
     * Cek apakah akun berstatus aktif
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Label representasi peran pengguna
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Kepala K3RS (Admin)',
            self::ROLE_PETUGAS => 'Petugas K3RS',
            default => ucfirst((string) $this->role),
        };
    }

    /**
     * Relasi riwayat pemeriksaan fisik yang dilakukan pengguna
     */
    public function pemeriksaans(): HasMany
    {
        return $this->hasMany(Pemeriksaan::class, 'user_id');
    }

    /**
     * Relasi jadwal inspeksi yang dibuat oleh pengguna
     */
    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'user_id');
    }

    /**
     * Relasi tindak lanjut pemeliharaan yang ditangani oleh pengguna
     */
    public function tindakLanjuts(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'user_id');
    }

    /**
     * Relasi penggantian unit APAR yang dicatat oleh pengguna
     */
    public function penggantianApars(): HasMany
    {
        return $this->hasMany(PenggantianApar::class, 'user_id');
    }
}
