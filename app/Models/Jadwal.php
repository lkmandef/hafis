<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwals';

    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'tipe_target',
        'gedung_id',
        'status',
        'deskripsi',
        'dibuat_oleh',
        'user_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Relasi: Jadwal memiliki banyak target APAR asli (tabel pivot jadwal_apars)
     */
    public function apars(): BelongsToMany
    {
        return $this->belongsToMany(Apar::class, 'jadwal_apars')
            ->withPivot('status_pemeriksaan', 'catatan')
            ->withTimestamps();
    }

    /**
     * Relasi opsional: Gedung induk jika target difilter per gedung
     */
    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    /**
     * Relasi opsional: User pembuat jadwal jika login
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi: Semua pemeriksaan APAR yang dilakukan dalam jadwal ini
     */
    public function pemeriksaans(): HasMany
    {
        return $this->hasMany(Pemeriksaan::class, 'jadwal_id');
    }

    /**
     * Accessor: Label status yang rapi dan mudah dipahami
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'belum_dimulai' => 'Belum Dimulai',
            'berjalan' => 'Berjalan',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Accessor: Varian badge visual konsisten sistem HAFIS
     */
    public function getStatusBadgeVariantAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'default',
            'belum_dimulai' => 'info',
            'berjalan' => 'warning',
            'selesai' => 'success',
            'dibatalkan' => 'danger',
            default => 'default',
        };
    }

    /**
     * Accessor: Label cakupan target jadwal
     */
    public function getTargetLabelAttribute(): string
    {
        return match ($this->tipe_target) {
            'gedung' => $this->gedung ? "Gedung {$this->gedung->nama}" : 'Gedung Tertentu',
            'custom' => 'APAR Tertentu',
            default => 'Semua APAR Aktif',
        };
    }

    /**
     * Helper: Menghitung total target APAR
     */
    public function getTotalTargetAttribute(): int
    {
        return $this->apars->count();
    }

    /**
     * Helper: Menghitung jumlah APAR yang telah diperiksa
     */
    public function getTotalDiperiksaAttribute(): int
    {
        return $this->apars->where('pivot.status_pemeriksaan', 'sudah_diperiksa')->count();
    }

    /**
     * Helper: Teks progress pemeriksaan
     */
    public function getProgressLabelAttribute(): string
    {
        $diperiksa = $this->total_diperiksa;
        $total = $this->total_target;

        if ($diperiksa === 0) {
            return "0 / {$total} Unit";
        }

        return "{$diperiksa} / {$total} Unit";
    }

    /**
     * Otomatisasi status berbasis tanggal kalender jika belum selesai atau dibatalkan
     */
    public function sinkronkanStatus(): void
    {
        if (in_array($this->status, ['draft', 'dibatalkan', 'selesai'])) {
            return;
        }

        $today = Carbon::today();

        if ($today->lt($this->tanggal_mulai)) {
            $newStatus = 'belum_dimulai';
        } else {
            $newStatus = 'berjalan';
        }

        if ($this->status !== $newStatus) {
            $this->update(['status' => $newStatus]);
        }
    }
}
