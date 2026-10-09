<?php

namespace App\Models;

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
     * Item checklist fisik 5 parameter utama K3RS (Arsitektur Baru)
     */
    public const K3RS_CHECKLIST_ITEMS = [
        1 => 'Tekanan',
        2 => 'Pin/Segel',
        3 => 'Handle',
        4 => 'Kondisi Fisik',
        5 => 'Nozel',
    ];

    /**
     * Item checklist fisik yang dapat dikonfigurasi (configurable / legacy)
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
        'periode_tahun',
        'periode_bulan',
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
        'periode_tahun' => 'integer',
        'periode_bulan' => 'integer',
    ];

    /**
     * Validasi domain dan perlindungan invariant bulanan
     */
    protected static function booted(): void
    {
        static::saving(function (Pemeriksaan $pemeriksaan) {
            if ($pemeriksaan->periode_bulan !== null) {
                if ($pemeriksaan->periode_bulan < 1 || $pemeriksaan->periode_bulan > 12) {
                    throw new \InvalidArgumentException('Periode bulan harus bernilai antara 1 sampai 12.');
                }
            }

            if ($pemeriksaan->periode_tahun !== null) {
                if ($pemeriksaan->periode_tahun < 1000 || $pemeriksaan->periode_tahun > 9999) {
                    throw new \InvalidArgumentException('Periode tahun harus berupa 4 digit tahun yang valid.');
                }
            }

            // Invariant target: Satu APAR hanya boleh memiliki satu hasil pemeriksaan per bulan kalender
            if ($pemeriksaan->apar_id && $pemeriksaan->periode_tahun !== null && $pemeriksaan->periode_bulan !== null) {
                $duplicateExists = static::where('apar_id', $pemeriksaan->apar_id)
                    ->where('periode_tahun', $pemeriksaan->periode_tahun)
                    ->where('periode_bulan', $pemeriksaan->periode_bulan)
                    ->where('id', '!=', $pemeriksaan->id ?? 0)
                    ->exists();

                if ($duplicateExists) {
                    throw new \DomainException("Unit APAR ID {$pemeriksaan->apar_id} sudah memiliki hasil pemeriksaan untuk periode {$pemeriksaan->periode_tahun}-{$pemeriksaan->periode_bulan}.");
                }
            }
        });
    }

    /**
     * Helper: Cari record pemeriksaan berdasarkan unit APAR dan periode bulan/tahun
     */
    public static function findByAparPeriod(int|Apar $apar, int $tahun, int $bulan): ?self
    {
        $aparId = $apar instanceof Apar ? $apar->id : $apar;

        return static::where('apar_id', $aparId)
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->first();
    }

    /**
     * Scope: Filter berdasarkan periode tahun dan bulan
     */
    public function scopeForPeriod($query, int $tahun, int $bulan)
    {
        return $query->where('periode_tahun', $tahun)->where('periode_bulan', $bulan);
    }

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

    /**
     * Relasi ke tindak lanjut / pemeliharaan yang bersumber dari pemeriksaan ini
     */
    public function tindakLanjuts(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'pemeriksaan_id')->orderBy('created_at', 'desc');
    }
}
