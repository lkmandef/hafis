<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemeriksaan extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaans';

    public const HASIL_BAIK = 'baik';
    public const HASIL_PERLU_TINDAKAN = 'perlu_tindakan';

    public const HASIL_LABELS = [
        self::HASIL_BAIK => 'Baik',
        self::HASIL_PERLU_TINDAKAN => 'Perlu Tindakan',
    ];

    /**
     * Item checklist fisik yang dapat dikonfigurasi (configurable)
     */
    public const DEFAULT_CHECKLIST_ITEMS = [
        1 => 'Posisi APAR sesuai lokasi dan ketinggian pemasangan',
        2 => 'Akses menuju APAR bebas hambatan dan mudah dijangkau',
        3 => 'Kondisi fisik tabung baik (bebas korosi, karat, dan penyok)',
        4 => 'Segel pengaman (safety seal) masih terpasang utuh',
        5 => 'Pin pengaman (locking pin) terpasang pada posisinya',
        6 => 'Indikator tekanan / manometer berada dalam zona hijau',
        7 => 'Selang (hose) dan nozzle dalam kondisi lentur tanpa retak',
        8 => 'Label instruksi penggunaan dan identitas masih terbaca jelas',
        9 => 'Tidak ditemukan kebocoran media pemadam',
        10 => 'Masa berlaku tabung / media masih sesuai (belum kedaluwarsa)',
    ];

    protected $fillable = [
        'apar_id',
        'jadwal_id',
        'user_id',
        'pemeriksa_nama',
        'tanggal_pemeriksaan',
        'hasil_keseluruhan',
        'status',
        'catatan',
        'foto_path',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'datetime',
    ];

    /**
     * Relasi ke unit APAR yang diperiksa
     */
    public function apar(): BelongsTo
    {
        return $this->belongsTo(Apar::class, 'apar_id');
    }

    /**
     * Relasi ke Jadwal Inspeksi terkait (opsional jika ad-hoc)
     */
    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    /**
     * Relasi ke User/Petugas pemeriksa
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke rincian item checklist
     */
    public function details(): HasMany
    {
        return $this->hasMany(PemeriksaanDetail::class, 'pemeriksaan_id')->orderBy('urutan');
    }

    /**
     * Label hasil keseluruhan
     */
    public function getHasilLabelAttribute(): string
    {
        return self::HASIL_LABELS[$this->hasil_keseluruhan] ?? 'Belum Ditentukan';
    }

    /**
     * Varian badge untuk tampilan UI
     */
    public function getHasilBadgeVariantAttribute(): string
    {
        return match ($this->hasil_keseluruhan) {
            self::HASIL_BAIK => 'success',
            self::HASIL_PERLU_TINDAKAN => 'danger',
            default => 'neutral',
        };
    }
}
