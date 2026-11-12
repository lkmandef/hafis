<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Services\SmartInspectionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    /**
     * Tampilkan halaman Scan QR APAR dengan kamera & formulir input kode manual
     */
    public function index(Request $request)
    {
        $hasPeriod = $request->filled('bulan') || $request->filled('periode_bulan');
        $bulan = $hasPeriod ? (int) $request->input('bulan', $request->input('periode_bulan')) : null;
        $tahun = $hasPeriod ? (int) $request->input('tahun', $request->input('periode_tahun', Carbon::now()->year)) : null;

        if ($bulan !== null && ($bulan < 1 || $bulan > 12)) {
            $bulan = (int) Carbon::now()->month;
        }

        if ($tahun !== null && ($tahun < 2000 || $tahun > 2100)) {
            $tahun = (int) Carbon::now()->year;
        }

        $jadwalId = $request->input('jadwal_id');

        return view('scan.index', compact('bulan', 'tahun', 'jadwalId'));
    }

    /**
     * Cari dan identifikasi APAR berdasarkan payload scan QR atau input kode manual
     */
    public function cari(Request $request)
    {
        $raw = trim($request->input('kode', ''));
        $jadwalId = $request->input('jadwal_id');
        $hasPeriod = $request->filled('bulan') || $request->filled('periode_bulan');
        $bulan = $hasPeriod ? (int) $request->input('bulan', $request->input('periode_bulan')) : null;
        $tahun = $hasPeriod ? (int) $request->input('tahun', $request->input('periode_tahun', Carbon::now()->year)) : null;

        if ($bulan !== null && ($bulan < 1 || $bulan > 12)) {
            $bulan = (int) Carbon::now()->month;
        }

        if ($tahun !== null && ($tahun < 2000 || $tahun > 2100)) {
            $tahun = (int) Carbon::now()->year;
        }

        $fallbackParams = [];
        if (! empty($jadwalId)) {
            $fallbackParams['jadwal_id'] = $jadwalId;
        }
        if ($hasPeriod) {
            $fallbackParams['bulan'] = $bulan;
            $fallbackParams['tahun'] = $tahun;
        }

        if (empty($raw)) {
            return redirect()->route('scan.index', $fallbackParams)
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
                if (empty($jadwalId) && ! empty($queryParams['jadwal_id'])) {
                    $jadwalId = $queryParams['jadwal_id'];
                }
                if (! empty($queryParams['bulan'])) {
                    $bulan = (int) $queryParams['bulan'];
                }
                if (! empty($queryParams['tahun'])) {
                    $tahun = (int) $queryParams['tahun'];
                }
            } elseif (isset($parsed['path'])) {
                // Misal /apar/12 -> extract ID
                if (preg_match('#/apar/(\d+)#', $parsed['path'], $matches)) {
                    $byId = Apar::with(['lokasi.gedung', 'lokasi.lantai', 'ruangan.lantai.gedung', 'aparPengganti'])->find($matches[1]);
                    if ($byId) {
                        return $this->handleAparFound($byId, $jadwalId, $bulan, $tahun);
                    }
                }
            }
        }

        // Pencarian APAR secara fleksibel (qr_code, kode APAR, atau nomor register)
        $apar = Apar::with(['lokasi.gedung', 'lokasi.lantai', 'ruangan.lantai.gedung', 'aparPengganti'])
            ->where('qr_code', $cleanInput)
            ->orWhere('kode', $cleanInput)
            ->orWhere('nomor_register', $cleanInput)
            ->orWhereRaw('LOWER(kode) = ?', [strtolower($cleanInput)])
            ->orWhereRaw('LOWER(qr_code) = ?', [strtolower($cleanInput)])
            ->first();

        // 1. Jika QR / Kode TIDAK DITEMUKAN
        if (! $apar) {
            return redirect()->route('scan.index', $fallbackParams)
                ->withInput()
                ->with('error', "APAR tidak ditemukan untuk kode: '{$raw}'. Pastikan label QR atau kode yang dimasukkan valid.");
        }

        // 2. Jika APAR DITEMUKAN
        return $this->handleAparFound($apar, $jadwalId, $bulan, $tahun);
    }

    /**
     * Response penanganan APAR yang ditemukan
     */
    private function handleAparFound(Apar $apar, $jadwalId = null, $bulan = null, $tahun = null)
    {
        $redirectParams = ['id' => $apar->id];
        if (! empty($jadwalId)) {
            $redirectParams['jadwal_id'] = $jadwalId;
        }

        // 1. Jika APAR sudah digantikan oleh unit baru (Tahap 8)
        if ($apar->is_replaced) {
            $aparPengganti = $apar->aparPengganti;
            $kodePengganti = $aparPengganti ? $aparPengganti->kode : 'unit baru';
            $tglPenggantian = $apar->tanggal_nonaktif ? $apar->tanggal_nonaktif->format('d/m/Y') : '-';

            return redirect()->route('apar.show', $redirectParams)
                ->with('warning', "Unit APAR {$apar->kode} sudah tidak aktif karena telah digantikan oleh {$kodePengganti} pada {$tglPenggantian} dan tidak dapat diinspeksi.");
        }

        // 2. Jika APAR berstatus Tidak Aktif biasa (Section 5 / Legacy Inactive)
        if ($apar->status === Apar::STATUS_TIDAK_AKTIF) {
            return redirect()->route('apar.show', $redirectParams)
                ->with('warning', "Unit APAR {$apar->kode} berstatus Tidak Aktif dan tidak memenuhi syarat untuk inspeksi operasional bulanan.");
        }

        // 3. Jika APAR berada di lokasi stok sediaan (GUDANG K3RS)
        if ($apar->lokasi && $apar->lokasi->status === Lokasi::STATUS_STOK) {
            return redirect()->route('apar.show', $redirectParams)
                ->with('warning', "Unit APAR {$apar->kode} berada di lokasi stok sediaan ({$apar->lokasi->nama}) dan bukan target inspeksi operasional bulanan.");
        }

        // 4. Jika ada jadwal_id eksplisit tanpa periode bulanan: pertahankan alur jadwal legacy
        if (! empty($jadwalId) && empty($bulan) && empty($tahun)) {
            return redirect()->route('apar.show', $redirectParams)
                ->with('success', "Unit APAR {$apar->kode} berhasil teridentifikasi dari pemindaian.");
        }

        // 5. Alur Inspeksi Bulanan (Eksplisit atau Standalone Default untuk APAR Terpasang Operasional)
        $isOperationalInstalled = ! empty($apar->lokasi_id) && $apar->isEligibleForInspection();
        $hasPeriod = ! empty($bulan) && ! empty($tahun);

        if ($hasPeriod || $isOperationalInstalled) {
            $effectiveBulan = $bulan ?? (int) Carbon::now()->month;
            $effectiveTahun = $tahun ?? (int) Carbon::now()->year;
            $namaBulan = Pemeriksaan::NAMA_BULAN[$effectiveBulan] ?? (string) $effectiveBulan;

            // Cek apakah sudah ada catatan pemeriksaan untuk periode bulan/tahun ini
            $existingPemeriksaan = Pemeriksaan::findByAparPeriod($apar->id, $effectiveTahun, $effectiveBulan);
            if ($existingPemeriksaan) {
                return redirect()->route('pemeriksaan.show', $existingPemeriksaan->id)
                    ->with('info', "Unit APAR {$apar->kode} sudah memiliki catatan hasil pemeriksaan untuk periode {$namaBulan} {$effectiveTahun}.");
            }

            // Alur Smart Inspection untuk APAR Operasional Terpasang
            $smartService = app(SmartInspectionService::class);
            $eval = $smartService->evaluateAparContext($apar, Carbon::createFromDate($effectiveTahun, $effectiveBulan, 1));

            // Jika belum diperiksa pada periode ini, buka formulir pemeriksaan baru
            $createParams = [
                'apar_id' => $apar->id,
                'periode_bulan' => $effectiveBulan,
                'periode_tahun' => $effectiveTahun,
            ];
            if (! empty($jadwalId)) {
                $createParams['jadwal_id'] = $jadwalId;
            }

            $lokasiNama = $apar->lokasi?->nama ? " ({$apar->lokasi->nama})" : '';

            $flashMessage = ($eval['mode'] === 'multi_period')
                ? "Unit APAR {$apar->kode}{$lokasiNama} teridentifikasi. Checklist pemeriksaan ini akan menyelesaikan kepatuhan periode {$eval['backlog_period']['nama_bulan']} (terlambat) dan {$eval['current_period']['nama_bulan']} (berjalan) sekaligus."
                : "Unit APAR {$apar->kode}{$lokasiNama} teridentifikasi. Silakan isi checklist pemeriksaan untuk periode {$namaBulan} {$effectiveTahun}.";

            return redirect()->route('pemeriksaan.create', $createParams)
                ->with('success', $flashMessage);
        }

        // 6. Default/General Scan Lookup (tanpa parameter periode dan APAR non-operasional/unplaced)
        return redirect()->route('apar.show', $redirectParams)
            ->with('success', "Unit APAR {$apar->kode} berhasil teridentifikasi dari pemindaian.");
    }
}
