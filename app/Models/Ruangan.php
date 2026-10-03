<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangans';

    protected $fillable = [
        'lantai_id',
        'kode',
        'nama',
        'kategori',
        'keterangan',
    ];

    /**
     * Relasi: Ruangan dimiliki oleh satu Lantai
     */
    public function lantai(): BelongsTo
    {
        return $this->belongsTo(Lantai::class, 'lantai_id');
    }

    /**
     * Relasi: Ruangan memiliki banyak APAR
     */
    public function apars(): HasMany
    {
        return $this->hasMany(Apar::class, 'ruangan_id');
    }

    /**
     * Accessor: Gedung pemilik ruangan melalui lantai
     */
    public function getGedungAttribute(): ?Gedung
    {
        return $this->lantai ? $this->lantai->gedung : null;
    }
}
