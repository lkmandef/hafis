<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Relasi: Lantai memiliki banyak Ruangan / Area
     */
    public function ruangans(): HasMany
    {
        return $this->hasMany(Ruangan::class, 'lantai_id')->orderBy('nama', 'asc');
    }
}
