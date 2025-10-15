<?php

namespace App\Observers;

use App\Models\Kelas;
use App\Models\Guru;

class KelasObserver
{
    /**
     * Handle kelas "created" event
     */
    public function created(Kelas $kelas)
    {
        // ✅ Jalankan sync saat create
        $this->syncWaliKelas($kelas);
    }

    /**
     * Handle kelas "updated" event
     */
    public function updated(Kelas $kelas)
    {
        // ✅ Jalankan sync saat update
        if ($kelas->isDirty('wali_guru_id')) {
            $this->syncWaliKelas($kelas);
        }
    }

    /**
     * Handle kelas "deleted" event
     */
    public function deleted(Kelas $kelas)
    {
        if ($kelas->wali_guru_id) {
            Guru::where('id', $kelas->wali_guru_id)->update([
                'kelas_wali_id' => null,
            ]);
        }
    }

    /**
     * Sync wali kelas relation
     */
    private function syncWaliKelas(Kelas $kelas)
    {
        // Hapus relasi lama jika ada perubahan
        if ($kelas->getOriginal('wali_guru_id')) {
            Guru::where('id', $kelas->getOriginal('wali_guru_id'))
                ->update(['kelas_wali_id' => null]);
        }

        // Set relasi baru
        if ($kelas->wali_guru_id) {
            Guru::where('id', $kelas->wali_guru_id)->update([
                'is_wali_kelas' => true,
                'kelas_wali_id' => $kelas->id,
            ]);
        }
    }
}
