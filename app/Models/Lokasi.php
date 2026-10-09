<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasis';

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_KOSONG = 'kosong';

    public const STATUS_STOK = 'stok';

    public const STATUS_LABELS = [
        self::STATUS_AKTIF => 'Aktif',
        self::STATUS_KOSONG => 'Kosong (0 APAR)',
        self::STATUS_STOK => 'Stok Persediaan',
    ];

    protected $fillable = [
        'nama',
        'kode',
        'kategori',
        'status',
        'keterangan',
    ];

    /**
     * Relasi: Satu Lokasi K3RS memiliki banyak unit APAR
     */
    public function apars(): HasMany
    {
        return $this->hasMany(Apar::class, 'lokasi_id');
    }
}
