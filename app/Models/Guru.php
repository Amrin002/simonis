<?php

namespace App\Models;

use Carbon\Carbon;
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
     * Get pelanggaran yang ditangani wali kelas
     */
    public function pelanggarans()
    {
        return $this->hasMany(Pelanggaran::class, 'wali_kelas_id');
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
        if ($this->isWaliKelas() && $this->isGuruMapel()) {
            return 'Wali Kelas & Guru Mata Pelajaran';
        } elseif ($this->isWaliKelas()) {
            return 'Wali Kelas';
        } elseif ($this->isGuruMapel()) {
            return 'Guru Mata Pelajaran';
        }
        return 'Guru';
    }

    /**
     * Get nama kelas wali
     */
    public function getNamaKelasWaliAttribute(): string
    {
        return $this->kelasWali ? $this->kelasWali->nama : '-';
    }

    /**
     * Get sambutan untuk dashboard
     */
    public function getSambutanDashboardAttribute(): string
    {
        if ($this->isWaliKelas() && $this->isGuruMapel()) {
            return "Selamat datang {$this->nama_guru}, Wali Kelas {$this->namaKelasWali}";
        } elseif ($this->isWaliKelas()) {
            return "Selamat datang Wali Kelas {$this->namaKelasWali}";
        } else {
            return "Selamat datang {$this->nama_guru}";
        }
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
        Carbon::setLocale('id');
        $hariIni = Carbon::now()->isoFormat('dddd');

        return $this->jadwals()
            ->with(['mapel', 'kelas'])
            ->where('hari', $hariIni)
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

    // ========== PELANGGARAN HELPERS ==========

    /**
     * Get jumlah pelanggaran yang ditangani
     */
    public function getJumlahPelanggaranAttribute(): int
    {
        return $this->pelanggarans()->count();
    }

    /**
     * Get pelanggaran berdasarkan kategori
     */
    public function getPelanggaranByKategori(string $kategori): int
    {
        return $this->pelanggarans()->where('kategori', $kategori)->count();
    }

    /**
     * Get pelanggaran bulan ini
     */
    public function getPelanggaranBulanIni()
    {
        return $this->pelanggarans()
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    /**
     * Get total pelanggaran di kelas wali
     */
    public function getTotalPelanggaranKelasWali(): int
    {
        if (!$this->isWaliKelas() || !$this->kelasWali) {
            return 0;
        }

        return Pelanggaran::where('kelas_id', $this->kelasWali->id)->count();
    }
    /**
     * Get pelanggaran yang ditangani wali kelas
     */
    // public function pelanggarans()
    // {
    //     return $this->hasMany(Pelanggaran::class, 'wali_kelas_id');
    // }
}
