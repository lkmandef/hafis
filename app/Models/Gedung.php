<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Gedung extends Model
{
    use HasFactory;

    protected $table = 'gedungs';

    protected $fillable = [
        'kode',
        'nama',
        'keterangan',
        'status',
    ];

    /**
     * Relasi: Gedung memiliki banyak Lantai
     */
    public function lantais(): HasMany
    {
        return $this->hasMany(Lantai::class, 'gedung_id')->orderBy('nomor_lantai', 'asc');
    }

    /**
     * Relasi: Gedung memiliki banyak Ruangan melalui Lantai
     */
    public function ruangans(): HasManyThrough
    {
        return $this->hasManyThrough(Ruangan::class, Lantai::class, 'gedung_id', 'lantai_id');
    }

    /**
     * Relasi: Gedung memiliki banyak Lokasi Penempatan K3RS
     */
    public function lokasis(): HasMany
    {
        return $this->hasMany(Lokasi::class, 'gedung_id');
    }

    /**
     * Jumlah unit APAR di gedung ini (otoritatif via master lokasi K3RS / ruangan)
     */
    public function getAparsCountAttribute(): int
    {
        return Apar::diGedung($this->id)->count();
    }
}
