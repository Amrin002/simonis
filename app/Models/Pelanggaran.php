<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get siswa yang melakukan pelanggaran
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Get wali kelas yang mencatat pelanggaran
     */
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    /**
     * Get kelas tempat pelanggaran terjadi
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // ========== ACCESSORS ==========

    /**
     * Get nama siswa dengan NIS
     */
    public function getNamaSiswaLengkapAttribute(): string
    {
        return $this->siswa ? $this->siswa->nama_lengkap : '-';
    }

    /**
     * Get nama wali kelas
     */
    public function getNamaWaliKelasAttribute(): string
    {
        return $this->waliKelas ? $this->waliKelas->nama_guru : '-';
    }

    /**
     * Get nama kelas
     */
    public function getNamaKelasAttribute(): string
    {
        return $this->kelas ? $this->kelas->nama : '-';
    }

    /**
     * Get badge color berdasarkan kategori
     */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->kategori) {
            'Ringan' => 'success',
            'Sedang' => 'warning',
            'Berat' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get tanggal dalam format Indonesia
     */
    public function getTanggalFormatAttribute(): string
    {
        return $this->tanggal->locale('id')->isoFormat('D MMMM YYYY');
    }

    // ========== SCOPES ==========

    /**
     * Scope untuk filter berdasarkan siswa
     */
    public function scopeBySiswa($query, $siswaId)
    {
        return $query->where('siswa_id', $siswaId);
    }

    /**
     * Scope untuk filter berdasarkan kelas
     */
    public function scopeByKelas($query, $kelasId)
    {
        return $query->where('kelas_id', $kelasId);
    }

    /**
     * Scope untuk filter berdasarkan kategori
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    /**
     * Scope untuk filter berdasarkan bulan
     */
    public function scopeByBulan($query, $bulan, $tahun)
    {
        return $query->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);
    }

    /**
     * Scope untuk mendapatkan pelanggaran terbaru
     */
    public function scopeTerbaru($query)
    {
        return $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');
    }
}
