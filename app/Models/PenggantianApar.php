<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenggantianApar extends Model
{
    use HasFactory;

    protected $table = 'penggantian_apars';

    public const ALASAN_RUSAK = 'rusak';

    public const ALASAN_TIDAK_LAYAK = 'tidak_layak';

    public const ALASAN_KADALUARSA = 'kadaluarsa';

    public const ALASAN_HASIL_PEMERIKSAAN = 'hasil_pemeriksaan';

    public const ALASAN_PENGGANTIAN_UNIT = 'penggantian_unit';

    public const ALASAN_LAINNYA = 'lainnya';

    public const ALASAN_LABELS = [
        self::ALASAN_RUSAK => 'Unit Rusak / Tidak Berfungsi',
        self::ALASAN_TIDAK_LAYAK => 'Fisik Tidak Layak Digunakan',
        self::ALASAN_KADALUARSA => 'Tabung / Media Melewati Masa Pakai',
        self::ALASAN_HASIL_PEMERIKSAAN => 'Rekomendasi Hasil Inspeksi Fisik',
        self::ALASAN_PENGGANTIAN_UNIT => 'Peremajaan Unit APAR',
        self::ALASAN_LAINNYA => 'Alasan Lainnya',
    ];

    protected $fillable = [
        'apar_lama_id',
        'apar_baru_id',
        'tanggal_penggantian',
        'alasan',
        'alasan_keterangan',
        'catatan',
        'petugas_nama',
        'user_id',
    ];

    protected $casts = [
        'tanggal_penggantian' => 'date',
    ];

    /**
     * Relasi ke unit APAR lama yang digantikan
     */
    public function aparLama(): BelongsTo
    {
        return $this->belongsTo(Apar::class, 'apar_lama_id');
    }

    /**
     * Relasi ke unit APAR baru yang menggantikan
     */
    public function aparBaru(): BelongsTo
    {
        return $this->belongsTo(Apar::class, 'apar_baru_id');
    }

    /**
     * Relasi ke user / petugas yang melakukan pencatatan penggantian
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor label alasan penggantian
     */
    public function getAlasanLabelAttribute(): string
    {
        $label = self::ALASAN_LABELS[$this->alasan] ?? ucfirst(str_replace('_', ' ', $this->alasan));

        if ($this->alasan === self::ALASAN_LAINNYA && ! empty($this->alasan_keterangan)) {
            return "{$label} ({$this->alasan_keterangan})";
        }

        return $label;
    }
}
