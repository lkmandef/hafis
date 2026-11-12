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

    public const TIPE_TEPAT_WAKTU = 'tepat_waktu';

    public const TIPE_TERLAMBAT = 'terlambat';

    public const TIPE_KOREKSI = 'koreksi';

    public const TIPE_LABELS = [
        self::TIPE_TEPAT_WAKTU => 'Tepat Waktu',
        self::TIPE_TERLAMBAT => 'Selesai Terlambat',
        self::TIPE_KOREKSI => 'Koreksi',
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
     * Nama kanonikal 5 parameter checklist bulanan K3RS
     */
    public const CANONICAL_CHECKLIST_ITEMS = [
        'Tekanan',
        'Pin/Segel',
        'Handle',
        'Kondisi Fisik',
        'Nozel',
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
        'pemeriksaan_induk_id',
        'periode_tahun',
        'periode_bulan',
        'user_id',
        'pemeriksa_nama',
        'tanggal_pemeriksaan',
        'hasil_keseluruhan',
        'tipe_penyelesaian',
        'status',
        'catatan',
        'foto_path',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'datetime',
        'periode_tahun' => 'integer',
        'periode_bulan' => 'integer',
    ];

    public const NAMA_BULAN = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
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
                if ($pemeriksaan->periode_tahun < 2000 || $pemeriksaan->periode_tahun > 2100) {
                    throw new \InvalidArgumentException('Periode tahun harus bernilai antara 2000 sampai 2100.');
                }
            }

            // Cegah pemeriksaan baru pada APAR yang tidak memenuhi syarat operasional (Section 1)
            if ($pemeriksaan->apar_id && ! $pemeriksaan->exists) {
                $apar = $pemeriksaan->apar ?? Apar::find($pemeriksaan->apar_id);
                if ($apar && ! $apar->isEligibleForInspection()) {
                    throw new \DomainException("Unit APAR ID {$apar->id} ({$apar->kode}) tidak memenuhi syarat untuk pemeriksaan operasional.");
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
     * Helper: Cek apakah ini pemeriksaan bulanan K3RS
     */
    public function isMonthlyInspection(): bool
    {
        return $this->periode_tahun !== null && $this->periode_bulan !== null;
    }

    /**
     * Helper: Cek apakah ini pemeriksaan legacy
     */
    public function isLegacyInspection(): bool
    {
        return ! $this->isMonthlyInspection();
    }

    /**
     * Label periode bulan dan tahun (misal: "Oktober 2026")
     */
    public function getPeriodeLabelAttribute(): ?string
    {
        if (! $this->isMonthlyInspection()) {
            return null;
        }

        $namaBulan = self::NAMA_BULAN[$this->periode_bulan] ?? "Bulan {$this->periode_bulan}";

        return "{$namaBulan} {$this->periode_tahun}";
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
     * Scope: Hanya ambil aktivitas pemeriksaan fisik riil (mengecualikan linked record resolusi)
     */
    public function scopeAktivitasFisik($query)
    {
        return $query->whereNull('pemeriksaan_induk_id');
    }

    /**
     * Scope: Hanya ambil pemeriksaan berstatus terlambat
     */
    public function scopeTerlambat($query)
    {
        return $query->where('tipe_penyelesaian', self::TIPE_TERLAMBAT);
    }

    /**
     * Scope: Hanya ambil pemeriksaan berstatus tepat waktu
     */
    public function scopeTepatWaktu($query)
    {
        return $query->where('tipe_penyelesaian', self::TIPE_TEPAT_WAKTU);
    }

    /**
     * Relasi ke record fisik induk jika baris ini merupakan linked multi-period resolution
     */
    public function pemeriksaanInduk(): BelongsTo
    {
        return $this->belongsTo(self::class, 'pemeriksaan_induk_id');
    }

    /**
     * Relasi ke seluruh baris resolusi anak yang tersambung ke record fisik ini
     */
    public function resolusiAnak(): HasMany
    {
        return $this->hasMany(self::class, 'pemeriksaan_induk_id');
    }

    /**
     * Helper: Cek apakah record ini merupakan linked record resolusi periode lain
     */
    public function isLinkedRecord(): bool
    {
        return $this->pemeriksaan_induk_id !== null;
    }

    /**
     * Accessor: Label tipe penyelesaian kepatuhan periode
     */
    public function getTipePenyelesaianLabelAttribute(): string
    {
        return self::TIPE_LABELS[$this->tipe_penyelesaian] ?? ucfirst(str_replace('_', ' ', $this->tipe_penyelesaian ?? 'Tepat Waktu'));
    }

    /**
     * Accessor: Varian badge UI untuk tipe penyelesaian
     */
    public function getTipeBadgeVariantAttribute(): string
    {
        return match ($this->tipe_penyelesaian) {
            self::TIPE_TEPAT_WAKTU => 'success',
            self::TIPE_TERLAMBAT => 'warning',
            self::TIPE_KOREKSI => 'info',
            default => 'neutral',
        };
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
