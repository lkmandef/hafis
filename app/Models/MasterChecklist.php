<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class MasterChecklist extends Model
{
    use HasFactory;

    protected $table = 'master_checklists';

    protected $fillable = [
        'nama',
        'deskripsi_bantuan',
        'urutan',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Scope item aktif
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pengurutan kanonikal
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('urutan', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Dapatkan daftar item checklist aktif untuk formulir pemeriksaan (1-based index)
     *
     * @return array<int, string>
     */
    public static function getActiveItems(): array
    {
        try {
            if (Schema::hasTable('master_checklists')) {
                $dbItems = static::active()->ordered()->get();

                if ($dbItems->isNotEmpty()) {
                    $result = [];
                    foreach ($dbItems as $idx => $item) {
                        $result[$idx + 1] = $item->nama;
                    }

                    return $result;
                }
            }
        } catch (\Throwable $e) {
            // Fallback aman jika tabel belum termigrasi
        }

        return Pemeriksaan::K3RS_CHECKLIST_ITEMS;
    }

    /**
     * Dapatkan daftar nama parameter kanonikal aktif untuk validasi controller
     *
     * @return array<int, string>
     */
    public static function getActiveCanonicalNames(): array
    {
        try {
            if (Schema::hasTable('master_checklists')) {
                $names = static::active()->ordered()->pluck('nama')->toArray();

                if (! empty($names)) {
                    return array_values(array_unique($names));
                }
            }
        } catch (\Throwable $e) {
            // Fallback aman jika tabel belum termigrasi
        }

        return Pemeriksaan::CANONICAL_CHECKLIST_ITEMS;
    }

    /**
     * Dapatkan peta deskripsi bantuan placeholder untuk form pemeriksaan
     *
     * @return array<string, string>
     */
    public static function getHelperMap(): array
    {
        try {
            if (Schema::hasTable('master_checklists')) {
                return static::active()->pluck('deskripsi_bantuan', 'nama')->filter()->toArray();
            }
        } catch (\Throwable $e) {
            // Fallback aman jika tabel belum termigrasi
        }

        return [
            'Tekanan' => 'Jarum manometer berada tepat di dalam zona hijau (normal)...',
            'Pin/Segel' => 'Pin pengaman terpasang kokoh dan segel pengaman (safety seal) masih utuh...',
            'Handle' => 'Tuas penekan (handle lever) dan pegangan bersih, tidak macet...',
            'Kondisi Fisik' => 'Tabung bebas korosi, karat, penyok, serta stiker instruksi terbaca jelas...',
            'Nozel' => 'Corong semprot (nozzle) dan selang lentur, tidak retak, dan bebas sumbatan...',
        ];
    }
}
