<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PemeriksaanDetail extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_details';

    public const HASIL_BAIK = 'baik';

    public const HASIL_TIDAK_BAIK = 'tidak_baik';

    public const HASIL_TIDAK_DIPERIKSA = 'tidak_diperiksa';

    public const HASIL_LABELS = [
        self::HASIL_BAIK => 'Baik',
        self::HASIL_TIDAK_BAIK => 'Tidak Baik',
        self::HASIL_TIDAK_DIPERIKSA => 'Tidak Diperiksa',
    ];

    protected $fillable = [
        'pemeriksaan_id',
        'item_nama',
        'hasil',
        'catatan',
        'urutan',
    ];

    public function pemeriksaan(): BelongsTo
    {
        return $this->belongsTo(Pemeriksaan::class, 'pemeriksaan_id');
    }

    public function getHasilLabelAttribute(): string
    {
        return self::HASIL_LABELS[$this->hasil] ?? ucfirst(str_replace('_', ' ', $this->hasil));
    }

    public function getHasilBadgeVariantAttribute(): string
    {
        return match ($this->hasil) {
            self::HASIL_BAIK => 'success',
            self::HASIL_TIDAK_BAIK => 'danger',
            default => 'neutral',
        };
    }

    /**
     * Relasi ke tindak lanjut / pemeliharaan yang dibuat khusus untuk temuan item ini
     */
    public function tindakLanjuts(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'pemeriksaan_detail_id');
    }

    /**
     * Accessor: Ambil tindak lanjut terbaru untuk item checklist ini
     */
    public function getTindakLanjutAttribute(): ?TindakLanjut
    {
        return $this->tindakLanjuts()->latest()->first();
    }
}
