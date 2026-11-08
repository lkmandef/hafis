<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lantai extends Model
{
    use HasFactory;

    protected $table = 'lantais';

    protected $fillable = [
        'gedung_id',
        'nomor_lantai',
        'nama',
        'keterangan',
    ];

    /**
     * Relasi: Lantai dimiliki oleh satu Gedung
     */
    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    /**
     * Relasi: Lantai memiliki satu gambar Denah
     */
    public function denah(): HasOne
    {
        return $this->hasOne(Denah::class, 'lantai_id');
    }

    /**
     * Helper: Mengecek apakah lantai sudah memiliki denah
     */
    public function getHasDenahAttribute(): bool
    {
        return $this->denah !== null;
    }

    /**
     * Relasi: Lantai memiliki banyak Ruangan / Area
     */
    public function ruangans(): HasMany
    {
        return $this->hasMany(Ruangan::class, 'lantai_id')->orderBy('nama', 'asc');
    }

    /**
     * Relasi: Lantai memiliki banyak Lokasi Penempatan K3RS
     */
    public function lokasis(): HasMany
    {
        return $this->hasMany(Lokasi::class, 'lantai_id')->orderBy('nama', 'asc');
    }

    /**
     * Relasi: Lantai memiliki banyak APAR melalui Ruangan
     */
    public function apars(): HasManyThrough
    {
        return $this->hasManyThrough(Apar::class, Ruangan::class, 'lantai_id', 'ruangan_id');
    }

    /**
     * Helper: Total unit APAR pada lantai ini
     */
    public function getAparsCountAttribute(): int
    {
        if ($this->relationLoaded('lokasis')) {
            return (int) $this->lokasis->sum(fn ($lok) => $lok->relationLoaded('apars') ? $lok->apars->count() : $lok->apars()->count());
        }

        return (int) Apar::whereIn('lokasi_id', $this->lokasis()->select('id'))->count();
    }
}
