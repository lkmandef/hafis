<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Denah extends Model
{
    use HasFactory;

    protected $table = 'denahs';

    protected $fillable = [
        'lantai_id',
        'file_path',
        'nama_file_asli',
        'mime_type',
        'file_size',
        'keterangan',
    ];

    /**
     * Relasi: Denah dimiliki oleh satu Lantai
     */
    public function lantai(): BelongsTo
    {
        return $this->belongsTo(Lantai::class, 'lantai_id');
    }

    /**
     * Accessor URL file denah untuk ditampilkan di browser
     */
    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        if (str_starts_with($this->file_path, 'images/')) {
            return asset($this->file_path);
        }

        return Storage::disk('public')->url($this->file_path);
    }
}
