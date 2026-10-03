<?php

namespace App\Models;

use App\Services\QrCodeService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'koordinat_x',
        'koordinat_y',
        'jenis_media',
        'kapasitas',
        'merek',
        'tipe_model',
        'status',
        'tanggal_pemasangan',
        'tanggal_kadaluarsa',
        'tanggal_pemeriksaan_terakhir',
        'keterangan',
        'replaced_by_apar_id',
        'replaces_apar_id',
        'alasan_nonaktif',
        'tanggal_nonaktif',
    ];

    protected $casts = [
        'tanggal_pemasangan' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'tanggal_pemeriksaan_terakhir' => 'date',
        'tanggal_nonaktif' => 'date',
        'koordinat_x' => 'float',
        'koordinat_y' => 'float',
    ];

    /**
     * Helper: Cek apakah APAR sudah memiliki titik koordinat pada denah
     */
    public function getHasTitikAttribute(): bool
    {
        return ! is_null($this->koordinat_x) && ! is_null($this->koordinat_y);
    }

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
     * Helper: Cek apakah unit APAR ini sudah pernah digantikan oleh unit lain
     */
    public function getIsReplacedAttribute(): bool
    {
        return ! is_null($this->replaced_by_apar_id) || $this->alasan_nonaktif === 'sudah_diganti';
    }

    /**
     * Helper: Cek apakah unit APAR ini merupakan unit pengganti dari unit sebelumnya
     */
    public function getIsReplacementAttribute(): bool
    {
        return ! is_null($this->replaces_apar_id);
    }

    /**
     * Helper: Cek apakah unit APAR aktif dan memenuhi syarat untuk dapat digantikan
     */
    public function getCanBeReplacedAttribute(): bool
    {
        return $this->status !== self::STATUS_TIDAK_AKTIF && ! $this->is_replaced;
    }

    /**
     * Accessor: Label human-readable status APAR
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->is_replaced) {
            return 'Sudah Diganti';
        }

        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Accessor: Varian badge UI (success, warning, danger, neutral)
     */
    public function getStatusBadgeVariantAttribute(): string
    {
        if ($this->is_replaced) {
            return 'neutral';
        }

        return match ($this->status) {
            self::STATUS_BAIK => 'success',
            self::STATUS_PERHATIAN => 'warning',
            self::STATUS_RUSAK => 'danger',
            default => 'neutral',
        };
    }

    /**
     * Relasi: Data penggantian ketika APAR ini digantikan oleh APAR baru
     */
    public function penggantianKeluar(): HasOne
    {
        return $this->hasOne(PenggantianApar::class, 'apar_lama_id');
    }

    /**
     * Relasi: Data penggantian ketika APAR ini menggantikan APAR lama
     */
    public function penggantianMasuk(): HasOne
    {
        return $this->hasOne(PenggantianApar::class, 'apar_baru_id');
    }

    /**
     * Relasi: Unit APAR baru yang menggantikan unit ini
     */
    public function aparPengganti(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replaced_by_apar_id');
    }

    /**
     * Relasi: Unit APAR lama yang digantikan oleh unit ini
     */
    public function aparDigantikan(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replaces_apar_id');
    }

    /**
     * Relasi: Jadwal inspeksi yang mencakup APAR ini
     */
    public function jadwals(): BelongsToMany
    {
        return $this->belongsToMany(Jadwal::class, 'jadwal_apars')
            ->withPivot('status_pemeriksaan', 'catatan')
            ->withTimestamps();
    }

    /**
     * Accessor: Cari jadwal inspeksi aktif (status berjalan) untuk APAR ini
     */
    public function getActiveJadwalAttribute(): ?Jadwal
    {
        return $this->jadwals()->where('jadwals.status', 'berjalan')->first();
    }

    /**
     * Relasi: Log riwayat pemeriksaan fisik untuk APAR ini
     */
    public function pemeriksaans(): HasMany
    {
        return $this->hasMany(Pemeriksaan::class, 'apar_id')->orderBy('tanggal_pemeriksaan', 'desc');
    }

    /**
     * Relasi: Riwayat tindak lanjut dan pemeliharaan untuk APAR ini
     */
    public function tindakLanjuts(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'apar_id')->orderBy('tanggal_tindakan', 'desc');
    }

    /**
     * Accessor: Generate representasi SVG QR Code untuk APAR ini
     */
    public function getQrSvgAttribute(): string
    {
        return QrCodeService::generateSvg($this->qr_code ?: $this->kode);
    }
}
