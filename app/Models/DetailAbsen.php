<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailAbsen extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Get absen induk
     */
    public function absen()
    {
        return $this->belongsTo(Absen::class, 'absen_id');
    }

    /**
     * Get siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Check if siswa hadir
     */
    public function isHadir()
    {
        return $this->status === 'Hadir';
    }

    /**
     * Check if siswa sakit
     */
    public function isSakit()
    {
        return $this->status === 'Sakit';
    }

    /**
     * Check if siswa izin
     */
    public function isIzin()
    {
        return $this->status === 'Izin';
    }

    /**
     * Check if siswa alpa
     */
    public function isAlpa()
    {
        return $this->status === 'Alpa';
    }

    /**
     * Get status badge class (untuk tampilan)
     */
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'Hadir' => 'success',
            'Sakit' => 'warning',
            'Izin' => 'info',
            'Alpa' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan siswa
     */
    public function scopeSiswa($query, $siswaId)
    {
        return $query->where('siswa_id', $siswaId);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
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

    /**
     * Boot method untuk update presentase kehadiran otomatis
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($detailAbsen) {
            $detailAbsen->absen->updatePresentaseKehadiran();
        });

        static::deleted(function ($detailAbsen) {
            $detailAbsen->absen->updatePresentaseKehadiran();
        });
    }
}
