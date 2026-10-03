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
     * Relasi: Lantai memiliki banyak APAR melalui Ruangan
     */
    public function apars(): HasManyThrough
    {
        return $this->hasManyThrough(Apar::class, Ruangan::class, 'lantai_id', 'ruangan_id');
    }
}
