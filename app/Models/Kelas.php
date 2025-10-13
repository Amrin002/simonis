<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get wali kelas
     */
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_guru_id');
    }

    /**
     * Get all siswa di kelas ini
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    // /**
    //  * Get jadwal kelas
    //  */
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'kelas_id');
    }
    /**
     * Get pelanggaran di kelas ini
     */
    public function pelanggarans()
    {
        return $this->hasMany(Pelanggaran::class, 'kelas_id');
    }

    // ========== ACCESSORS ==========

    /**
     * Get nama kelas dengan jumlah siswa
     */
    public function getNamaLengkapAttribute(): string
    {
        $jumlahSiswa = $this->siswas()->count();
        return "{$this->nama} ({$jumlahSiswa} siswa)";
    }

    /**
     * Get jumlah siswa
     */
    public function getJumlahSiswaAttribute(): int
    {
        return $this->siswas()->count();
    }

    /**
     * Get nama wali kelas atau status
     */
    public function getNamaWaliKelasAttribute(): string
    {
        return $this->waliKelas ? $this->waliKelas->nama_guru : 'Belum Ada Wali Kelas';
    }

    /**
     * Check if kelas has wali kelas
     */
    public function hasWaliKelas(): bool
    {
        return !is_null($this->wali_guru_id) && $this->waliKelas !== null;
    }

    /**
     * Check if kelas has siswa
     */
    public function hasSiswa(): bool
    {
        return $this->siswas()->count() > 0;
    }

    /**
     * Get status lengkap
     */
    public function getStatusLengkapAttribute(): string
    {
        if ($this->hasWaliKelas() && $this->hasSiswa()) {
            return 'Lengkap';
        } elseif ($this->hasWaliKelas() || $this->hasSiswa()) {
            return 'Belum Lengkap';
        }
        return 'Baru Dibuat';
    }

    /**
     * Get jumlah mata pelajaran
     */
    public function getJumlahMapelAttribute(): int
    {
        return $this->jadwals()->distinct('mapel_id')->count('mapel_id');
    }

    /**
     * Get jumlah jadwal
     */
    public function getJumlahJadwalAttribute(): int
    {
        return $this->jadwals()->count();
    }

    // ========== PELANGGARAN HELPERS ==========

    /**
     * Get jumlah pelanggaran di kelas ini
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
     * Get pelanggaran ringan
     */
    public function getPelanggaranRinganAttribute(): int
    {
        return $this->getPelanggaranByKategori('Ringan');
    }

    /**
     * Get pelanggaran sedang
     */
    public function getPelanggaranSedangAttribute(): int
    {
        return $this->getPelanggaranByKategori('Sedang');
    }

    /**
     * Get pelanggaran berat
     */
    public function getPelanggaranBeratAttribute(): int
    {
        return $this->getPelanggaranByKategori('Berat');
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
     * Get siswa dengan pelanggaran terbanyak
     */
    public function getSiswaWithMostPelanggaran($limit = 5)
    {
        return $this->siswas()
            ->withCount('pelanggarans')
            ->orderBy('pelanggarans_count', 'desc')
            ->limit($limit)
            ->get();
    }

    // /**
    //  * Get absen kelas
    //  */
    // public function absens()
    // {
    //     return $this->hasMany(Absen::class, 'kelas_id');
    // }

    // /**
    //  * Get nilai tugas kelas
    //  */
    // public function nilaiTugas()
    // {
    //     return $this->hasMany(NilaiTugas::class, 'kelas_id');
    // }

    // /**
    //  * Get nilai UTS kelas
    //  */
    // public function nilaiUts()
    // {
    //     return $this->hasMany(NilaiUTS::class, 'kelas_id');
    // }

    // /**
    //  * Get nilai UAS kelas
    //  */
    // public function nilaiUas()
    // {
    //     return $this->hasMany(NilaiUAS::class, 'kelas_id');
    // }

    // /**
    //  * Get nilai akhir kelas
    //  */
    // public function nilaiAkhirs()
    // {
    //     return $this->hasMany(NilaiAkhir::class, 'kelas_id');
    // }

    // /**
    //  * Get pelanggaran di kelas ini
    //  */
    // public function pelanggarans()
    // {
    //     return $this->hasMany(Pelanggaran::class, 'kelas_id');
    // }
}
