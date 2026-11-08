<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public const TIPE_ROOM = 'ROOM';

    public const TIPE_CORRIDOR = 'CORRIDOR';

    public const TIPE_LOBBY = 'LOBBY';

    public const TIPE_OPEN_AREA = 'OPEN_AREA';

    public const TIPE_SERVICE_AREA = 'SERVICE_AREA';

    public const TIPE_WAREHOUSE = 'WAREHOUSE';

    public const TIPE_ELEVATOR = 'ELEVATOR';

    public const TIPE_OTHER = 'OTHER';

    public const TIPE_LABELS = [
        self::TIPE_ROOM => 'Ruangan Tertutup',
        self::TIPE_CORRIDOR => 'Koridor / Selasar',
        self::TIPE_LOBBY => 'Lobi / Ruang Tunggu',
        self::TIPE_OPEN_AREA => 'Area Terbuka / Luar',
        self::TIPE_SERVICE_AREA => 'Zona Layanan / Utilitas',
        self::TIPE_WAREHOUSE => 'Gudang / Penyimpanan',
        self::TIPE_ELEVATOR => 'Lift / Fasilitas Vertikal',
        self::TIPE_OTHER => 'Fasilitas Lainnya',
    ];

    protected $fillable = [
        'nama',
        'kode',
        'kategori',
        'status',
        'keterangan',
        'gedung_id',
        'lantai_id',
        'ruangan_id',
        'tipe_lokasi',
    ];

    /**
     * Relasi: Titik Lokasi K3RS terhubung dengan Master Ruangan Fisik
     */
    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    /**
     * Relasi: Lokasi K3RS berada di satu Gedung fisik
     */
    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    /**
     * Relasi: Lokasi K3RS berada di satu Lantai gedung (opsional)
     */
    public function lantai(): BelongsTo
    {
        return $this->belongsTo(Lantai::class, 'lantai_id');
    }

    /**
     * Accessor: Label Bahasa Indonesia untuk tipe lokasi fisik
     */
    public function getTipeLokasiLabelAttribute(): string
    {
        return self::TIPE_LABELS[$this->tipe_lokasi] ?? $this->tipe_lokasi ?? 'Zona Layanan / Utilitas';
    }

    /**
     * Accessor: Teks lokasi lengkap dengan hierarki gedung dan lantai
     */
    public function getLokasiLengkapAttribute(): string
    {
        $parts = [];
        if ($this->gedung) {
            $parts[] = $this->gedung->nama;
        }
        if ($this->lantai && $this->lantai->nama !== 'Non-Lantai') {
            $parts[] = $this->lantai->nama;
        }
        $parts[] = $this->nama;

        return implode(', ', $parts);
    }

    /**
     * Relasi: Satu Lokasi K3RS memiliki banyak unit APAR
     */
    public function apars(): HasMany
    {
        return $this->hasMany(Apar::class, 'lokasi_id');
    }

    /**
     * Accessor: Rincian jenis media dan kapasitas APAR di lokasi ini
     * Format: "3 Powder 6kg, 1 CO2 3kg"
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
}
