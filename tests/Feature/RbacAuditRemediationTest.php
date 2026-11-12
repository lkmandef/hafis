<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use App\Models\TindakLanjut;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAuditRemediationTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleApar(): Apar
    {
        $gedung = Gedung::create(['kode' => 'GDG-RBAC', 'nama' => 'Gedung RBAC', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang RBAC', 'kode' => 'R-RBAC']);
        $lokasi = Lokasi::create(['nama' => 'Lokasi RBAC', 'status' => Lokasi::STATUS_AKTIF]);

        return Apar::create([
            'kode' => 'APAR-RBAC-'.rand(100, 999),
            'qr_code' => 'HAFIS-QR-RBAC-'.uniqid(),
            'ruangan_id' => $ruangan->id,
            'lokasi_id' => $lokasi->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
        ]);
    }

    private function createSampleJadwal(): Jadwal
    {
        return Jadwal::create([
            'nama' => 'Jadwal Inspeksi Bulanan',
            'tanggal_mulai' => now()->startOfMonth()->format('Y-m-d'),
            'tanggal_selesai' => now()->endOfMonth()->format('Y-m-d'),
            'tipe_target' => 'semua',
            'status' => 'berjalan',
            'deskripsi' => 'Pengujian jadwal RBAC',
        ]);
    }

    private function createSampleTindakLanjut(): TindakLanjut
    {
        $apar = $this->createSampleApar();
        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'pemeriksa_nama' => 'Petugas Lapangan',
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'selesai',
            'periode_tahun' => now()->year,
            'periode_bulan' => now()->month,
        ]);

        return TindakLanjut::create([
            'apar_id' => $apar->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'deskripsi_temuan' => 'Tekanan jarum di bawah zona hijau',
            'jenis_tindakan' => TindakLanjut::JENIS_PERBAIKAN,
            'uraian_tindakan' => 'Pengisian ulang tekanan N2',
            'status' => TindakLanjut::STATUS_BELUM_DITINDAKLANJUTI,
            'prioritas' => TindakLanjut::PRIORITAS_TINGGI,
            'petugas_pelaksana' => 'Teknisi K3RS',
            'tanggal_tindakan' => now()->toDateString(),
        ]);
    }

    // =========================================================================
    // FOCUS 1: FINDING-01 — JADWAL STATUS CANCELLATION RESTRICTION
    // =========================================================================

    /**
     * Petugas TIDAK DAPAT mengubah status jadwal menjadi 'dibatalkan' via form edit / request langsung
     */
    public function test_petugas_cannot_update_jadwal_status_to_dibatalkan_via_edit_request(): void
    {
        $this->actingAsPetugas();
        $jadwal = $this->createSampleJadwal();

        $response = $this->put(route('jadwal.update', $jadwal->id), [
            'nama' => 'Jadwal Telah Diedit Petugas',
            'tanggal_mulai' => $jadwal->tanggal_mulai->format('Y-m-d'),
            'tanggal_selesai' => $jadwal->tanggal_selesai->format('Y-m-d'),
            'status' => 'dibatalkan',
            'deskripsi' => 'Mencoba batalkan dari akun petugas',
        ]);

        $response->assertStatus(403);
        $this->assertNotEquals('dibatalkan', $jadwal->fresh()->status);
    }

    /**
     * Admin (Kepala K3RS) DAPAT mengubah status jadwal menjadi 'dibatalkan' via form edit
     */
    public function test_admin_can_update_jadwal_status_to_dibatalkan_via_edit_request(): void
    {
        $this->actingAsAdmin();
        $jadwal = $this->createSampleJadwal();

        $response = $this->put(route('jadwal.update', $jadwal->id), [
            'nama' => $jadwal->nama,
            'tanggal_mulai' => $jadwal->tanggal_mulai->format('Y-m-d'),
            'tanggal_selesai' => $jadwal->tanggal_selesai->format('Y-m-d'),
            'status' => 'dibatalkan',
            'deskripsi' => 'Pembatalan resmi oleh Admin K3RS',
        ]);

        $response->assertRedirect(route('jadwal.show', $jadwal->id));
        $this->assertEquals('dibatalkan', $jadwal->fresh()->status);
    }

    /**
     * Petugas TIDAK DAPAT mengakses endpoint pembatalan langsung jadwal (POST /jadwal/{id}/batalkan)
     */
    public function test_petugas_cannot_access_jadwal_batalkan_endpoint(): void
    {
        $this->actingAsPetugas();
        $jadwal = $this->createSampleJadwal();

        $response = $this->post(route('jadwal.batalkan', $jadwal->id));

        $response->assertStatus(403);
        $this->assertNotEquals('dibatalkan', $jadwal->fresh()->status);
    }

    /**
     * Admin DAPAT membatalkan jadwal melalui endpoint resmi pembatalan
     */
    public function test_admin_can_cancel_jadwal_via_batalkan_endpoint(): void
    {
        $this->actingAsAdmin();
        $jadwal = $this->createSampleJadwal();

        $response = $this->post(route('jadwal.batalkan', $jadwal->id));

        $response->assertRedirect();
        $this->assertEquals('dibatalkan', $jadwal->fresh()->status);
    }

    // =========================================================================
    // FOCUS 2: FINDING-03 — TINDAK LANJUT CANCELLATION & AUDIT TRAIL PRESERVATION
    // =========================================================================

    /**
     * Petugas TIDAK DAPAT membatalkan tiket tindak lanjut (403 Forbidden)
     */
    public function test_petugas_cannot_cancel_tindak_lanjut(): void
    {
        $this->actingAsPetugas();
        $tl = $this->createSampleTindakLanjut();

        $response = $this->post(route('tindak-lanjut.batalkan', $tl->id), [
            'alasan_pembatalan' => 'Alasan pembatalan oleh petugas',
        ]);

        $response->assertStatus(403);
        $this->assertNotEquals(TindakLanjut::STATUS_DIBATALKAN, $tl->fresh()->status);
    }

    /**
     * Petugas TIDAK DAPAT menghapus tiket tindak lanjut melalui endpoint destroy (403 Forbidden)
     */
    public function test_petugas_cannot_destroy_tindak_lanjut(): void
    {
        $this->actingAsPetugas();
        $tl = $this->createSampleTindakLanjut();

        $response = $this->delete(route('tindak-lanjut.destroy', $tl->id), [
            'alasan_pembatalan' => 'Alasan pembatalan oleh petugas',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('tindak_lanjuts', ['id' => $tl->id]);
    }

    /**
     * Admin WAJIB menyertakan alasan pembatalan saat membatalkan tiket tindak lanjut
     */
    public function test_admin_must_provide_alasan_when_cancelling_tindak_lanjut(): void
    {
        $this->actingAsAdmin();
        $tl = $this->createSampleTindakLanjut();

        // Kirim tanpa alasan_pembatalan
        $response = $this->post(route('tindak-lanjut.batalkan', $tl->id), [
            'alasan_pembatalan' => '',
        ]);

        $response->assertSessionHasErrors('alasan_pembatalan');
        $this->assertNotEquals(TindakLanjut::STATUS_DIBATALKAN, $tl->fresh()->status);

        // Kirim alasan terlalu pendek (< 5 karakter)
        $responseShort = $this->post(route('tindak-lanjut.batalkan', $tl->id), [
            'alasan_pembatalan' => 'Abc',
        ]);

        $responseShort->assertSessionHasErrors('alasan_pembatalan');
        $this->assertNotEquals(TindakLanjut::STATUS_DIBATALKAN, $tl->fresh()->status);
    }

    /**
     * Admin berhasil membatalkan tiket tindak lanjut: status berubah ke dibatalkan, alasan & jejak audit tersimpan, dan record TIDAK dihapus dari database
     */
    public function test_admin_can_cancel_tindak_lanjut_with_audit_trail_preservation(): void
    {
        $admin = $this->actingAsAdmin();
        $tl = $this->createSampleTindakLanjut();

        $alasan = 'Unit APAR telah diganti dengan tabung baru sehingga tiket perbaikan ini dibatalkan secara sah.';

        $response = $this->post(route('tindak-lanjut.batalkan', $tl->id), [
            'alasan_pembatalan' => $alasan,
        ]);

        $response->assertRedirect(route('tindak-lanjut.show', $tl->id));
        $response->assertSessionHas('success');

        // Pastikan record tetap ada di database (TIDAK di-hard-delete)
        $this->assertDatabaseHas('tindak_lanjuts', [
            'id' => $tl->id,
            'status' => TindakLanjut::STATUS_DIBATALKAN,
            'alasan_pembatalan' => $alasan,
            'dibatalkan_oleh_id' => $admin->id,
        ]);

        $freshTl = $tl->fresh();
        $this->assertEquals(TindakLanjut::STATUS_DIBATALKAN, $freshTl->status);
        $this->assertNotNull($freshTl->dibatalkan_at);
        $this->assertEquals($admin->id, $freshTl->dibatalkan_oleh_id);
        $this->assertStringContainsString($alasan, $freshTl->catatan);

        // Pastikan relasi APAR dan Pemeriksaan tetap utuh
        $this->assertNotNull($freshTl->apar);
        $this->assertNotNull($freshTl->pemeriksaan);
    }

    /**
     * Endpoint destroy tidak melakukan hard delete, melainkan mengalihkan ke mekanisme pembatalan dengan alasan wajib
     */
    public function test_destroy_endpoint_delegates_to_cancellation_and_does_not_hard_delete(): void
    {
        $admin = $this->actingAsAdmin();
        $tl = $this->createSampleTindakLanjut();

        $alasan = 'Pembatalan melalui endpoint destroy oleh Admin';

        $response = $this->delete(route('tindak-lanjut.destroy', $tl->id), [
            'alasan_pembatalan' => $alasan,
        ]);

        $response->assertRedirect(route('tindak-lanjut.show', $tl->id));

        // Verifikasi bahwa tiket TIDAK hilang dari database
        $this->assertDatabaseHas('tindak_lanjuts', [
            'id' => $tl->id,
            'status' => TindakLanjut::STATUS_DIBATALKAN,
            'alasan_pembatalan' => $alasan,
        ]);
    }

    /**
     * Tiket tindak lanjut yang telah dibatalkan TIDAK DAPAT diubah status pengerjaannya melalui aksi cepat updateStatus
     */
    public function test_cancelled_tindak_lanjut_cannot_be_transitioned_via_update_status(): void
    {
        $admin = $this->actingAsAdmin();
        $tl = $this->createSampleTindakLanjut();

        // Batalkan tiket terlebih dahulu
        $this->post(route('tindak-lanjut.batalkan', $tl->id), [
            'alasan_pembatalan' => 'Pembatalan validasi awal',
        ]);
        $this->assertEquals(TindakLanjut::STATUS_DIBATALKAN, $tl->fresh()->status);

        // Coba lakukan transisi status cepat ke 'dalam_proses'
        $response = $this->patch(route('tindak-lanjut.status', $tl->id), [
            'status' => 'dalam_proses',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(TindakLanjut::STATUS_DIBATALKAN, $tl->fresh()->status);
    }

    /**
     * Petugas TIDAK DAPAT mengubah status tiket menjadi 'dibatalkan' via form edit umum
     */
    public function test_petugas_cannot_update_tindak_lanjut_status_to_dibatalkan_via_edit_request(): void
    {
        $this->actingAsPetugas();
        $tl = $this->createSampleTindakLanjut();

        $response = $this->put(route('tindak-lanjut.update', $tl->id), [
            'jenis_tindakan' => $tl->jenis_tindakan,
            'uraian_tindakan' => $tl->uraian_tindakan,
            'status' => 'dibatalkan',
            'prioritas' => $tl->prioritas,
            'petugas_pelaksana' => $tl->petugas_pelaksana,
            'tanggal_tindakan' => $tl->tanggal_tindakan->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
        $this->assertNotEquals(TindakLanjut::STATUS_DIBATALKAN, $tl->fresh()->status);
    }
}
