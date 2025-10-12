<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guru extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_wali_kelas' => 'boolean',
        'is_guru_mapel' => 'boolean',
    ];

    /**
     * Get the user that owns the guru.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get kelas yang diampu sebagai wali kelas
     */
    public function kelasWali()
    {
        return $this->hasOne(Kelas::class, 'wali_guru_id');
    }

    /**
     * Get mata pelajaran yang diampu guru
     */
    public function guruMapels()
    {
        return $this->hasMany(GuruMapel::class, 'guru_id');
    }

    /**
     * Get jadwal mengajar guru
     */
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'guru_id');
    }
    /**
     * Get mata pelajaran yang diampu (many to many)
     */
    public function mapels()
    {
        return $this->belongsToMany(Mapel::class, 'guru_mapels', 'guru_id', 'mapel_id')
            ->withTimestamps();
    }

    /**
     * Check if guru is wali kelas
     */
    public function isWaliKelas(): bool
    {
        return $this->is_wali_kelas;
    }

    /**
     * Check if guru is guru mapel
     */
    public function isGuruMapel(): bool
    {
        return $this->is_guru_mapel;
    }

    /**
     * Get siswa di kelas yang diampu (untuk wali kelas)
     */
    public function getSiswaKelasWali()
    {
        if (!$this->kelasWali) {
            return collect([]);
        }
        return $this->kelasWali->siswas;
    }
    // ========== ACCESSORS ==========

    /**
     * Get nama guru dengan NIP
     */
    public function getNamaLengkapAttribute(): string
    {
        return "{$this->nama} ({$this->nip})"; // ✅ UBAH dari nama_guru ke nama
    }

    /**
     * Get role label
     */
    public function getRoleLabelAttribute(): string
    {
        if ($this->is_wali_kelas && $this->is_guru_mapel) {
            return 'Wali Kelas & Guru Mapel';
        } elseif ($this->is_wali_kelas) {
            return 'Wali Kelas';
        } elseif ($this->is_guru_mapel) {
            return 'Guru Mapel';
        }
        return 'Guru';
    }

    /**
     * Get nama kelas yang diampu (untuk wali kelas)
     */
    public function getNamaKelasWaliAttribute(): ?string
    {
        return $this->kelasWali?->nama;
    }

    /**
     * Get daftar nama mapel yang diajar
     */
    public function getDaftarMapelAttribute(): string
    {
        return $this->mapels->pluck('nama_matapelajaran')->join(', ') ?: '-';
    }

    /**
     * Get jadwal hari ini
     */
    public function getJadwalHariIni()
    {
        $hari = now()->locale('id')->dayName;
        return $this->jadwals()
            ->where('hari', $hari)
            ->with(['mapel', 'kelas'])
            ->orderBy('waktu_mulai')
            ->get();
    }

    /**
     * Get jumlah siswa di kelas wali
     */
    public function getJumlahSiswaKelasWali(): int
    {
        return $this->kelasWali?->siswas()->count() ?? 0;
    }
    /**
     * Get pelanggaran yang ditangani wali kelas
     */
    // public function pelanggarans()
    // {
    //     return $this->hasMany(Pelanggaran::class, 'wali_kelas_id');
    // }
}
