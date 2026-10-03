<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjut extends Model
{
    use HasFactory;

    protected $table = 'tindak_lanjuts';

    public const STATUS_BELUM_DITINDAKLANJUTI = 'belum_ditindaklanjuti';

    public const STATUS_DALAM_PROSES = 'dalam_proses';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_LABELS = [
        self::STATUS_BELUM_DITINDAKLANJUTI => 'Belum Ditindaklanjuti',
        self::STATUS_DALAM_PROSES => 'Dalam Proses',
        self::STATUS_SELESAI => 'Selesai',
    ];

    public const STATUS_BADGE_VARIANTS = [
        self::STATUS_BELUM_DITINDAKLANJUTI => 'warning',
        self::STATUS_DALAM_PROSES => 'neutral',
        self::STATUS_SELESAI => 'success',
    ];

    public const JENIS_PERBAIKAN = 'perbaikan';

    public const JENIS_PENGISIAN_ULANG = 'pengisian_ulang';

    public const JENIS_PEMBERSIHAN = 'pembersihan';

    public const JENIS_PENGGANTIAN_KOMPONEN = 'penggantian_komponen';

    public const JENIS_PEMERIKSAAN_ULANG = 'pemeriksaan_ulang';

    public const JENIS_LAINNYA = 'lainnya';

    public const JENIS_LABELS = [
        self::JENIS_PERBAIKAN => 'Perbaikan',
        self::JENIS_PENGISIAN_ULANG => 'Pengisian Ulang',
        self::JENIS_PEMBERSIHAN => 'Pembersihan',
        self::JENIS_PENGGANTIAN_KOMPONEN => 'Penggantian Komponen',
        self::JENIS_PEMERIKSAAN_ULANG => 'Pemeriksaan Ulang',
        self::JENIS_LAINNYA => 'Tindakan Lainnya',
    ];

    public const PRIORITAS_RENDAH = 'rendah';

    public const PRIORITAS_SEDANG = 'sedang';

    public const PRIORITAS_TINGGI = 'tinggi';

    public const PRIORITAS_LABELS = [
        self::PRIORITAS_RENDAH => 'Rendah',
        self::PRIORITAS_SEDANG => 'Sedang',
        self::PRIORITAS_TINGGI => 'Tinggi',
    ];

    protected $fillable = [
        'apar_id',
        'pemeriksaan_id',
        'pemeriksaan_detail_id',
        'user_id',
        'deskripsi_temuan',
        'jenis_tindakan',
        'uraian_tindakan',
        'status',
        'prioritas',
        'petugas_pelaksana',
        'tanggal_tindakan',
        'tanggal_selesai',
        'catatan',
        'foto_path',
    ];

    protected $casts = [
        'tanggal_tindakan' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function apar(): BelongsTo
    {
        return $this->belongsTo(Apar::class, 'apar_id');
    }

    public function pemeriksaan(): BelongsTo
    {
        return $this->belongsTo(Pemeriksaan::class, 'pemeriksaan_id');
    }

    public function pemeriksaanDetail(): BelongsTo
    {
        return $this->belongsTo(PemeriksaanDetail::class, 'pemeriksaan_detail_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getStatusBadgeVariantAttribute(): string
    {
        return self::STATUS_BADGE_VARIANTS[$this->status] ?? 'neutral';
    }

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS_LABELS[$this->jenis_tindakan] ?? ucfirst(str_replace('_', ' ', $this->jenis_tindakan));
    }

    public function getPrioritasLabelAttribute(): string
    {
        return self::PRIORITAS_LABELS[$this->prioritas] ?? ucfirst($this->prioritas);
    }

    public function getTemuanTeksAttribute(): string
    {
        if (! empty($this->deskripsi_temuan)) {
            return $this->deskripsi_temuan;
        }

        if ($this->pemeriksaanDetail) {
            $text = $this->pemeriksaanDetail->item_nama;
            if ($this->pemeriksaanDetail->catatan) {
                $text .= ': '.$this->pemeriksaanDetail->catatan;
            }

            return $text;
        }

        return 'Temuan pemeriksaan fisik unit APAR';
    }
}
