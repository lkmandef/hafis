<?php

namespace App\Http\Controllers;

use App\Models\MasterChecklist;
use App\Models\PemeriksaanDetail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChecklistMasterController extends Controller
{
    /**
     * Tampilkan daftar master parameter checklist pemeriksaan
     */
    public function index(Request $request)
    {
        $checklists = MasterChecklist::orderBy('urutan', 'asc')->orderBy('id', 'asc')->get();

        $stats = [
            'total' => $checklists->count(),
            'aktif' => $checklists->where('is_active', true)->count(),
            'nonaktif' => $checklists->where('is_active', false)->count(),
        ];

        return view('pengaturan.checklist.index', compact('checklists', 'stats'));
    }

    /**
     * Simpan parameter checklist baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:master_checklists,nama'],
            'deskripsi_bantuan' => ['nullable', 'string', 'max:255'],
            'urutan' => ['nullable', 'integer', 'min:1', 'max:99'],
        ], [
            'nama.required' => 'Nama parameter kriteria pemeriksaan wajib diisi.',
            'nama.unique' => 'Nama parameter tersebut sudah ada di daftar checklist.',
        ]);

        if (empty($validated['urutan'])) {
            $maxUrutan = (int) MasterChecklist::max('urutan');
            $validated['urutan'] = $maxUrutan + 1;
        }

        $validated['is_active'] = true;
        $validated['is_default'] = false;

        MasterChecklist::create($validated);

        return redirect()->route('pengaturan.checklist.index')
            ->with('success', "Parameter checklist '{$validated['nama']}' berhasil ditambahkan ke formulir pemeriksaan.");
    }

    /**
     * Perbarui data parameter checklist
     */
    public function update(Request $request, MasterChecklist $checklist)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('master_checklists', 'nama')->ignore($checklist->id)],
            'deskripsi_bantuan' => ['nullable', 'string', 'max:255'],
            'urutan' => ['nullable', 'integer', 'min:1', 'max:99'],
        ], [
            'nama.required' => 'Nama parameter kriteria pemeriksaan wajib diisi.',
            'nama.unique' => 'Nama parameter tersebut sudah digunakan oleh kriteria lain.',
        ]);

        if (empty($validated['urutan'])) {
            $validated['urutan'] = $checklist->urutan;
        }

        $checklist->update($validated);

        return redirect()->route('pengaturan.checklist.index')
            ->with('success', "Parameter checklist '{$checklist->nama}' berhasil diperbarui.");
    }

    /**
     * Aktifkan / Nonaktifkan parameter checklist
     */
    public function toggleStatus(MasterChecklist $checklist)
    {
        // Cegah menonaktifkan seluruh item (harus ada minimal 1 item aktif)
        if ($checklist->is_active) {
            $activeCount = MasterChecklist::where('is_active', true)->count();
            if ($activeCount <= 1) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menonaktifkan seluruh parameter. Harus ada minimal 1 kriteria pemeriksaan yang aktif.');
            }
        }

        $checklist->is_active = ! $checklist->is_active;
        $checklist->save();

        $statusStr = $checklist->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Parameter checklist '{$checklist->nama}' berhasil {$statusStr}.");
    }

    /**
     * Hapus parameter checklist (hanya untuk non-default dan belum memiliki riwayat pemeriksaan)
     */
    public function destroy(MasterChecklist $checklist)
    {
        if ($checklist->is_default) {
            return redirect()->back()
                ->with('error', 'Parameter bawaan standar K3RS tidak dapat dihapus permanen. Gunakan opsi nonaktifkan jika tidak diperlukan.');
        }

        // Cek apakah pernah digunakan dalam pemeriksaan historis
        $isUsedInHistory = PemeriksaanDetail::where('item_nama', $checklist->nama)->exists();
        if ($isUsedInHistory) {
            $checklist->is_active = false;
            $checklist->save();

            return redirect()->back()
                ->with('warning', "Parameter '{$checklist->nama}' memiliki rekaman riwayat pemeriksaan di masa lalu. Demi integritas audit, statusnya otomatis dinonaktifkan alih-alih dihapus permanen.");
        }

        $nama = $checklist->nama;
        $checklist->delete();

        return redirect()->route('pengaturan.checklist.index')
            ->with('success', "Parameter checklist '{$nama}' berhasil dihapus.");
    }
}
