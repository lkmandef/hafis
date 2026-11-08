<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
     * Relasi: Master Ruangan memiliki titik penempatan Lokasi K3RS
     */
    public function lokasi(): HasOne
    {
        return $this->hasOne(Lokasi::class, 'ruangan_id');
    }

    /**
     * Relasi: Ruangan memiliki banyak APAR
     */
    public function apars(): HasMany
    {
        return $this->hasMany(Apar::class, 'ruangan_id');
    }

    /**
     * Accessor: Rincian jenis media dan kapasitas APAR di ruangan ini
     */
    public function getRincianMediaAttribute(): string
    {
        if ($this->apars->isEmpty()) {
            return '-';
        }

        $summary = [];
        foreach ($this->apars as $a) {
            $media = match ($a->jenis_media) {
                'Dry Chemical Powder' => 'Powder',
                'CO2 (Carbon Dioxide)' => 'CO2',
                default => $a->jenis_media ?? 'Powder',
            };
            $kapasitas = str_replace(' ', '', (string) $a->kapasitas);
            $key = $media.($kapasitas !== '' ? " {$kapasitas}" : '');
            $summary[$key] = ($summary[$key] ?? 0) + 1;
        }

        $parts = [];
        foreach ($summary as $key => $count) {
            $parts[] = "{$count} {$key}";
        }

        return implode(', ', $parts);
    }

    /**
     * Accessor: Gedung pemilik ruangan melalui lantai
     */
    public function getGedungAttribute(): ?Gedung
    {
        return $this->lantai ? $this->lantai->gedung : null;
    }
}
