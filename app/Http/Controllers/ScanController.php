<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    /**
     * Tampilkan halaman Scan QR APAR dengan kamera & formulir input kode manual
     */
    public function index()
    {
        return view('scan.index');
    }

    /**
     * Cari dan identifikasi APAR berdasarkan payload scan QR atau input kode manual
     */
    public function cari(Request $request)
    {
        $raw = trim($request->input('kode', ''));

        if (empty($raw)) {
            return redirect()->route('scan.index')
                ->with('error', 'Masukkan kode APAR atau lakukan pemindaian QR terlebih dahulu.');
        }

        // Tangani jika payload berupa full URL (misal scan kamera membaca URL aplikasi)
        $cleanInput = $raw;
        if (filter_var($raw, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($raw);
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $queryParams);
                if (! empty($queryParams['kode'])) {
                    $cleanInput = $queryParams['kode'];
                }
            } elseif (isset($parsed['path'])) {
                // Misal /apar/12 -> extract ID
                if (preg_match('#/apar/(\d+)#', $parsed['path'], $matches)) {
                    $byId = Apar::find($matches[1]);
                    if ($byId) {
                        return $this->handleAparFound($byId);
                    }
                }
            }
        }

        // Pencarian APAR secara fleksibel (qr_code, kode APAR, atau nomor register)
        $apar = Apar::with(['ruangan.lantai.gedung'])
            ->where('qr_code', $cleanInput)
            ->orWhere('kode', $cleanInput)
            ->orWhere('nomor_register', $cleanInput)
            ->orWhereRaw('LOWER(kode) = ?', [strtolower($cleanInput)])
            ->orWhereRaw('LOWER(qr_code) = ?', [strtolower($cleanInput)])
            ->first();

        // 1. Jika QR / Kode TIDAK DITEMUKAN (Section 5)
        if (! $apar) {
            return redirect()->route('scan.index')
                ->withInput()
                ->with('error', "APAR tidak ditemukan untuk kode: '{$raw}'. Pastikan label QR atau kode yang dimasukkan valid.");
        }

        // 2. Jika APAR DITEMUKAN
        return $this->handleAparFound($apar);
    }

    /**
     * Response penanganan APAR yang ditemukan
     */
    private function handleAparFound(Apar $apar)
    {
        // Jika APAR berstatus Tidak Aktif (Section 5)
        if ($apar->status === Apar::STATUS_TIDAK_AKTIF) {
            return redirect()->route('apar.show', $apar->id)
                ->with('warning', "Unit APAR {$apar->kode} berhasil ditemukan namun berstatus Tidak Aktif dan tidak dapat dijadikan target pemeriksaan.");
        }

        return redirect()->route('apar.show', $apar->id)
            ->with('success', "Unit APAR {$apar->kode} berhasil teridentifikasi dari pemindaian.");
    }
}
