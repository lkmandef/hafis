<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'apars';

    public const STATUS_BAIK = 'baik';
    public const STATUS_PERHATIAN = 'perhatian';
    public const STATUS_RUSAK = 'rusak';
    public const STATUS_TIDAK_AKTIF = 'tidak_aktif';

    public const STATUS_LABELS = [
        self::STATUS_BAIK => 'Baik',
        self::STATUS_PERHATIAN => 'Perlu Perhatian',
        self::STATUS_RUSAK => 'Rusak / Perlu Tindakan',
        self::STATUS_TIDAK_AKTIF => 'Tidak Aktif',
    ];

    public const JENIS_MEDIA_OPTIONS = [
        'Dry Chemical Powder',
        'CO2 (Carbon Dioxide)',
        'Foam (AFFF)',
        'Clean Agent',
        'Wet Chemical',
    ];

    protected $fillable = [
        'kode',
        'nomor_register',
        'qr_code',
        'ruangan_id',
        'posisi_detail',
        'jenis_media',
        'kapasitas',
        'merek',
        'tipe_model',
        'status',
        'tanggal_pemasangan',
        'tanggal_kadaluarsa',
        'tanggal_pemeriksaan_terakhir',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pemasangan' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'tanggal_pemeriksaan_terakhir' => 'date',
    ];

    /**
     * Relasi: APAR berada di satu Ruangan / Area
     */
    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    /**
     * Accessor: Lantai pemilik ruangan tempat APAR berada
     */
    public function getLantaiAttribute(): ?Lantai
    {
        return $this->ruangan?->lantai;
    }

    /**
     * Accessor: Gedung pemilik lantai tempat APAR berada
     */
    public function getGedungAttribute(): ?Gedung
    {
        return $this->ruangan?->lantai?->gedung;
    }

    /**
     * Accessor: Teks lokasi berjenjang lengkap
     */
    public function getLokasiLengkapAttribute(): string
    {
        $gedungNama = $this->gedung?->nama ?? '-';
        $lantaiNama = $this->lantai?->nama ?? '-';
        $ruanganNama = $this->ruangan?->nama ?? '-';

        return "{$gedungNama}, {$lantaiNama}, {$ruanganNama}";
    }

    /**
     * Accessor: Label human-readable status APAR
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Accessor: Varian badge UI (success, warning, danger, neutral)
     */
    public function getStatusBadgeVariantAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_BAIK => 'success',
            self::STATUS_PERHATIAN => 'warning',
            self::STATUS_RUSAK => 'danger',
            default => 'neutral',
        };
    }
}
