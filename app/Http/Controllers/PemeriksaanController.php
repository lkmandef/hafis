<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PemeriksaanController extends Controller
{
    /**
     * Halaman persiapan inspeksi APAR (Tahap 5: Mulai Pemeriksaan)
     * Menghubungkan target APAR yang dipilih/discan dengan Jadwal Inspeksi aktif.
     */
    public function mulai(Request $request)
    {
        $aparId = $request->input('apar_id');
        $jadwalId = $request->input('jadwal_id');

        // Jika apar_id tidak disediakan, pandu petugas ke scanner atau jadwal
        if (empty($aparId)) {
            return redirect()->route('scan.index')
                ->with('info', 'Silakan scan QR code atau masukkan kode APAR terlebih dahulu untuk memulai pemeriksaan.');
        }

        $apar = Apar::with(['ruangan.lantai.gedung'])->find($aparId);
        if (!$apar) {
            return redirect()->route('scan.index')
                ->with('error', 'Data APAR yang dipilih tidak ditemukan.');
        }

        // Validasi: APAR tidak aktif tidak boleh diperiksa
        if ($apar->status === Apar::STATUS_TIDAK_AKTIF) {
            return redirect()->route('apar.show', $apar->id)
                ->with('warning', 'Unit APAR ini berstatus Tidak Aktif dan tidak dapat dilakukan pemeriksaan.');
        }

        // Tentukan jadwal inspeksi terkait
        $jadwal = null;
        if (!empty($jadwalId)) {
            $jadwal = Jadwal::find($jadwalId);
        }

        // Fallback: jika tidak dikirim dari detail jadwal, periksa apakah APAR memiliki jadwal aktif
        if (!$jadwal) {
            $jadwal = $apar->active_jadwal;
        }

        $waktuSekarang = Carbon::now();
        $petugas = auth()->user()?->name ?? 'Petugas K3RS';

        return view('pemeriksaan.mulai', compact('apar', 'jadwal', 'waktuSekarang', 'petugas'));
    }

    /**
     * Kompatibilitas route GET /pemeriksaan
     */
    public function index(Request $request)
    {
        if ($request->has('apar_id')) {
            return $this->mulai($request);
        }

        return redirect()->route('jadwal.index');
    }
}
