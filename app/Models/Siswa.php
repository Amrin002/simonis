<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get kelas siswa
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Get orang tua siswa
     */
    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class, 'orang_tua_id');
    }
    /**
     * Get pelanggaran siswa
     */
    public function pelanggarans()
    {
        return $this->hasMany(Pelanggaran::class, 'siswa_id');
    }

    // ========== ACCESSORS ==========

    /**
     * Get nama lengkap dengan NIS
     */
    public function getNamaLengkapAttribute(): string
    {
        return "{$this->nama} ({$this->nis})";
    }

    /**
     * Get nama kelas atau status
     */
    public function getNamaKelasAttribute(): string
    {
        return $this->kelas ? $this->kelas->nama : 'Belum Ada Kelas';
    }

    /**
     * Get nama orang tua atau status
     */
    public function getNamaOrangTuaAttribute(): string
    {
        return $this->orangTua ? $this->orangTua->nama_orang_tua : 'Tidak Ada';
    }

    /**
     * Check if siswa has kelas
     */
    public function hasKelas(): bool
    {
        return !is_null($this->kelas_id) && $this->kelas !== null;
    }

    /**
     * Check if siswa has orang tua
     */
    public function hasOrangTua(): bool
    {
        return !is_null($this->orang_tua_id) && $this->orangTua !== null;
    }

    /**
     * Get status lengkap
     */
    public function getStatusLengkapAttribute(): string
    {
        if ($this->hasKelas() && $this->hasOrangTua()) {
            return 'Lengkap';
        }
        return 'Belum Lengkap';
    }

    /**
     * Get nama wali kelas
     */
    public function getNamaWaliKelasAttribute(): ?string
    {
        if ($this->kelas && $this->kelas->waliKelas) {
            return $this->kelas->waliKelas->nama_guru;
        }
        return null;
    }

    /**
     * Get siblings (saudara kandung)
     */
    public function getSiblingsAttribute()
    {
        if (!$this->orangTua) {
            return collect([]);
        }

        return $this->orangTua->siswas->where('id', '!=', $this->id);
    }

    /**
     * Check if has siblings
     */
    public function hasSiblings(): bool
    {
        return $this->siblings->count() > 0;
    }

    // ========== PELANGGARAN HELPERS ==========

    /**
     * Get jumlah pelanggaran
     */
    public function getJumlahPelanggaranAttribute(): int
    {
        return $this->pelanggarans()->count();
    }

    /**
     * Get jumlah pelanggaran berdasarkan kategori
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
     * Check if siswa has pelanggaran
     */
    public function hasPelanggaran(): bool
    {
        return $this->pelanggarans()->exists();
    }

    /**
     * Get rekapan siswa (one-to-one)
     */
    public function rekapan()
    {
        return $this->hasOne(Rekapan::class, 'siswa_id');
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
     * Get detail absen siswa
     */
    // public function detailAbsens()
    // {
    //     return $this->hasMany(DetailAbsen::class, 'siswa_id');
    // }

    // /**
    //  * Get nilai tugas siswa
    //  */
    // public function nilaiTugas()
    // {
    //     return $this->hasMany(NilaiTugas::class, 'siswa_id');
    // }

    // /**
    //  * Get nilai UTS siswa
    //  */
    // public function nilaiUts()
    // {
    //     return $this->hasMany(NilaiUTS::class, 'siswa_id');
    // }

    // /**
    //  * Get nilai UAS siswa
    //  */
    // public function nilaiUas()
    // {
    //     return $this->hasMany(NilaiUAS::class, 'siswa_id');
    // }

    // /**
    //  * Get nilai akhir siswa
    //  */
    // public function nilaiAkhirs()
    // {
    //     return $this->hasMany(NilaiAkhir::class, 'siswa_id');
    // }

    // /**
    //  * Get pelanggaran siswa
    //  */
    // public function pelanggarans()
    // {
    //     return $this->hasMany(Pelanggaran::class, 'siswa_id');
    // }

    // /**
    //  * Get rekapan siswa
    //  */
    // public function rekapan()
    // {
    //     return $this->hasOne(Rekapan::class, 'siswa_id');
    // }
}
