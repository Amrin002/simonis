<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrangTua extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the user that owns the orang tua.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all siswa (anak) dari orang tua ini
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'orang_tua_id');
    }
    // ========== ACCESSORS ==========

    /**
     * Get nama lengkap dengan nomor telepon
     */
    public function getNamaLengkapAttribute(): string
    {
        return "{$this->nama_orang_tua} ({$this->nomor_tlp})";
    }

    /**
     * Get jumlah anak/siswa
     */
    public function getJumlahAnakAttribute(): int
    {
        return $this->siswas()->count();
    }

    /**
     * Get daftar nama anak
     */
    public function getDaftarNamaAnakAttribute(): string
    {
        return $this->siswas->pluck('nama')->join(', ') ?: 'Belum ada anak terdaftar';
    }

    /**
     * Format nomor telepon
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->nomor_tlp;

        // Format: 0812-3456-789
        if (strlen($phone) >= 10) {
            return substr($phone, 0, 4) . '-' . substr($phone, 4, 4) . '-' . substr($phone, 8);
        }

        return $phone;
    }
}
