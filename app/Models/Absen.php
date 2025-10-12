<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absen extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
        'presentase_kehadiran' => 'decimal:2',
    ];

    /**
     * Get kelas untuk absen ini
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Get detail absen (per siswa)
     */
    public function detailAbsens()
    {
        return $this->hasMany(DetailAbsen::class, 'absen_id');
    }

    /**
     * Hitung presentase kehadiran
     */
    public function hitungPresentaseKehadiran()
    {
        $totalSiswa = $this->detailAbsens()->count();

        if ($totalSiswa === 0) {
            return 0;
        }

        $siswaHadir = $this->detailAbsens()->where('status', 'Hadir')->count();

        return round(($siswaHadir / $totalSiswa) * 100, 2);
    }

    /**
     * Update presentase kehadiran
     */
    public function updatePresentaseKehadiran()
    {
        $this->presentase_kehadiran = $this->hitungPresentaseKehadiran();
        $this->save();
    }

    /**
     * Get jumlah siswa hadir
     */
    public function getJumlahHadirAttribute()
    {
        return $this->detailAbsens()->where('status', 'Hadir')->count();
    }

    /**
     * Get jumlah siswa sakit
     */
    public function getJumlahSakitAttribute()
    {
        return $this->detailAbsens()->where('status', 'Sakit')->count();
    }

    /**
     * Get jumlah siswa izin
     */
    public function getJumlahIzinAttribute()
    {
        return $this->detailAbsens()->where('status', 'Izin')->count();
    }

    /**
     * Get jumlah siswa alpa
     */
    public function getJumlahAlpaAttribute()
    {
        return $this->detailAbsens()->where('status', 'Alpa')->count();
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    /**
     * Scope untuk filter berdasarkan kelas
     */
    public function scopeKelas($query, $kelasId)
    {
        return $query->where('kelas_id', $kelasId);
    }

    /**
     * Scope untuk filter berdasarkan mata pelajaran
     */
    public function scopeMapel($query, $mapel)
    {
        return $query->where('mata_pelajaran', $mapel);
    }

    /**
     * Scope untuk filter berdasarkan bulan
     */
    public function scopeBulan($query, $bulan, $tahun = null)
    {
        $tahun = $tahun ?? now()->year;
        return $query->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);
    }
}
