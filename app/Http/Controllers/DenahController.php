<?php

namespace App\Http\Controllers;

use App\Models\Denah;
use App\Models\Lantai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DenahController extends Controller
{
    /**
     * Upload atau perbarui gambar denah untuk lantai tertentu
     */
    public function store(Request $request, Lantai $lantai)
    {
        $fileKey = $request->hasFile('file_denah') ? 'file_denah' : 'gambar';

        $validated = $request->validate([
            $fileKey => ['required', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'], // Max 5MB
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            "{$fileKey}.required" => 'Pilih file gambar denah terlebih dahulu.',
            "{$fileKey}.image" => 'File harus berupa gambar.',
            "{$fileKey}.mimes" => 'Format gambar yang diperbolehkan: JPG, JPEG, PNG, WebP, SVG.',
            "{$fileKey}.max" => 'Ukuran file denah maksimal 5 MB.',
        ]);

        $file = $request->file($fileKey);

        // Hapus file lama dari storage jika sebelumnya sudah ada
        if ($lantai->denah && Storage::disk('public')->exists($lantai->denah->file_path)) {
            Storage::disk('public')->delete($lantai->denah->file_path);
        }

        // Simpan file baru di storage public/denah dengan nama aman
        $path = $file->store('denah', 'public');

        $lantai->denah()->updateOrCreate(
            ['lantai_id' => $lantai->id],
            [
                'file_path' => $path,
                'nama_file_asli' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'keterangan' => $validated['keterangan'] ?? null,
            ]
        );

        return redirect()->back()->with('success', "Denah untuk {$lantai->nama} ({$lantai->gedung->nama}) berhasil diunggah.");
    }

    /**
     * Hapus gambar denah dari lantai tertentu
     */
    public function destroy(Lantai $lantai)
    {
        if ($lantai->denah) {
            if (Storage::disk('public')->exists($lantai->denah->file_path)) {
                Storage::disk('public')->delete($lantai->denah->file_path);
            }
            $lantai->denah()->delete();
        }

        return redirect()->back()->with('success', "Denah {$lantai->nama} berhasil dihapus.");
    }
}
