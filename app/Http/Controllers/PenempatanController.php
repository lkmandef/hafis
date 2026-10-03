<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use Illuminate\Http\Request;

class PenempatanController extends Controller
{
    /**
     * Tampilkan denah lantai dan titik penempatan APAR
     * Alur: Pilih Gedung -> Pilih Lantai -> Tampilkan Denah -> Titik APAR
     */
    public function index(Request $request)
    {
        // 1. Ambil seluruh data gedung dan lantai untuk navigasi
        $gedungs = Gedung::with([
            'lantais' => fn ($q) => $q->orderBy('nomor_lantai', 'asc'),
            'lantais.denah',
        ])->orderBy('nama', 'asc')->get();

        if ($gedungs->isEmpty()) {
            return view('penempatan.index', [
                'gedungs' => collect(),
                'selectedGedung' => null,
                'selectedLantai' => null,
                'placedApars' => collect(),
                'unplacedApars' => collect(),
                'highlightApar' => null,
            ]);
        }

        // 2. Tentukan Gedung terpilih
        $selectedGedung = null;
        if ($request->filled('gedung_id')) {
            $selectedGedung = $gedungs->firstWhere('id', (int) $request->gedung_id);
        }

        // Jika ada parameter lantai_id terlebih dahulu
        if (! $selectedGedung && $request->filled('lantai_id')) {
            $targetLantai = Lantai::find($request->lantai_id);
            if ($targetLantai) {
                $selectedGedung = $gedungs->firstWhere('id', $targetLantai->gedung_id);
            }
        }

        // Jika ada highlight kode APAR, cari gedung dan lantai dari APAR tersebut
        $highlightCode = $request->input('highlight') ?? $request->input('apar_id');
        $highlightApar = null;
        if ($highlightCode) {
            $highlightApar = is_numeric($highlightCode)
                ? Apar::with(['ruangan.lantai.gedung'])->find($highlightCode)
                : Apar::with(['ruangan.lantai.gedung'])->where('kode', $highlightCode)->first();

            if ($highlightApar && $highlightApar->ruangan && $highlightApar->ruangan->lantai) {
                $selectedGedung = $gedungs->firstWhere('id', $highlightApar->ruangan->lantai->gedung_id) ?? $selectedGedung;
            }
        }

        // Default ke gedung pertama jika belum terpilih
        if (! $selectedGedung) {
            $selectedGedung = $gedungs->first();
        }

        // 3. Tentukan Lantai terpilih dalam gedung
        $selectedLantai = null;
        if ($selectedGedung && $selectedGedung->lantais->isNotEmpty()) {
            if ($request->filled('lantai_id')) {
                $selectedLantai = $selectedGedung->lantais->firstWhere('id', (int) $request->lantai_id);
            } elseif ($request->filled('lantai')) {
                // Kompatibilitas query param ?lantai=1
                $nomorLantai = (int) $request->lantai;
                $selectedLantai = $selectedGedung->lantais->firstWhere('nomor_lantai', $nomorLantai);
            } elseif ($highlightApar && $highlightApar->ruangan && $highlightApar->ruangan->lantai) {
                $selectedLantai = $selectedGedung->lantais->firstWhere('id', $highlightApar->ruangan->lantai_id);
            }

            // Default ke lantai pertama gedung
            if (! $selectedLantai) {
                $selectedLantai = $selectedGedung->lantais->first();
            }
        }

        // 4. Muat daftar APAR pada lantai terpilih melalui ruangan
        $placedApars = collect();
        $unplacedApars = collect();

        if ($selectedLantai) {
            $selectedLantai->load(['denah', 'ruangans.apars' => fn ($q) => $q->orderBy('kode', 'asc')]);

            $allApars = $selectedLantai->ruangans->flatMap->apars;

            $placedApars = $allApars->filter(fn ($a) => $a->has_titik)->values();
            $unplacedApars = $allApars->filter(fn ($a) => ! $a->has_titik)->values();
        }

        return view('penempatan.index', compact(
            'gedungs',
            'selectedGedung',
            'selectedLantai',
            'placedApars',
            'unplacedApars',
            'highlightApar'
        ));
    }

    /**
     * Simpan atau ubah koordinat titik APAR pada denah lantai (persentase X dan Y: 0.00 - 100.00)
     */
    public function simpanTitik(Request $request, Apar $apar)
    {
        $validated = $request->validate([
            'koordinat_x' => ['required', 'numeric', 'min:0', 'max:100'],
            'koordinat_y' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'koordinat_x.required' => 'Koordinat horizontal (X) wajib ditentukan.',
            'koordinat_x.min' => 'Koordinat X minimal 0%.',
            'koordinat_x.max' => 'Koordinat X maksimal 100%.',
            'koordinat_y.required' => 'Koordinat vertikal (Y) wajib ditentukan.',
            'koordinat_y.min' => 'Koordinat Y minimal 0%.',
            'koordinat_y.max' => 'Koordinat Y maksimal 100%.',
        ]);

        $apar->update([
            'koordinat_x' => round((float) $validated['koordinat_x'], 2),
            'koordinat_y' => round((float) $validated['koordinat_y'], 2),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Posisi titik APAR {$apar->kode} berhasil disimpan pada denah.",
                'apar' => [
                    'id' => $apar->id,
                    'kode' => $apar->kode,
                    'status' => $apar->status,
                    'status_label' => $apar->status_label,
                    'status_badge_variant' => $apar->status_badge_variant,
                    'koordinat_x' => $apar->koordinat_x,
                    'koordinat_y' => $apar->koordinat_y,
                    'ruangan' => $apar->ruangan?->nama ?? '-',
                    'lokasi_lengkap' => $apar->lokasi_lengkap,
                    'jenis_media' => $apar->jenis_media,
                    'kapasitas' => $apar->kapasitas,
                ],
            ]);
        }

        return redirect()->back()->with('success', "Posisi titik APAR {$apar->kode} berhasil diperbarui pada denah.");
    }

    /**
     * Hapus titik koordinat APAR dari denah (status menjadi 'Posisi denah belum ditentukan')
     */
    public function hapusTitik(Request $request, Apar $apar)
    {
        $kode = $apar->kode;
        $apar->update([
            'koordinat_x' => null,
            'koordinat_y' => null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Titik koordinat APAR {$kode} telah dihapus dari denah.",
            ]);
        }

        return redirect()->back()->with('success', "Titik koordinat APAR {$kode} berhasil dihapus dari denah.");
    }
}
