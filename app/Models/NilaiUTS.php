<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NilaiUTS extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'nilai_uts';

    protected $casts = [
        'nilai_uts' => 'decimal:2',
    ];

    /**
     * Get siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Get kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Get mata pelajaran
     */
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    /**
     * Get formatted nilai
     */
    public function getFormattedNilaiAttribute()
    {
        return number_format($this->nilai_uts, 2);
    }

    /**
     * Check if nilai sudah mencapai KKM (asumsi KKM = 75)
     */
    public function isTuntas($kkm = 75)
    {
        return $this->nilai_uts >= $kkm;
    }

    /**
     * Get status kelulusan
     */
    public function getStatusAttribute()
    {
        return $this->isTuntas() ? 'Tuntas' : 'Tidak Tuntas';
    }

    /**
     * Scope untuk filter berdasarkan siswa
     */
    public function scopeSiswa($query, $siswaId)
    {
        return $query->where('siswa_id', $siswaId);
    }

    /**
     * Scope untuk filter berdasarkan kelas
     */
    public function scopeKelas($query, $kelasId)
    {
        return $query->where('kelas_id', $kelasId);
    }

    /**
     * Scope untuk filter berdasarkan mapel
     */
    public function scopeMapel($query, $mapelId)
    {
        return $query->where('mapel_id', $mapelId);
    }

    /**
     * Scope untuk nilai di atas KKM
     */
    public function scopeTuntas($query, $kkm = 75)
    {
        return $query->where('nilai_uts', '>=', $kkm);
    }

    /**
     * Scope untuk nilai di bawah KKM
     */
    public function scopeTidakTuntas($query, $kkm = 75)
    {
        return $query->where('nilai_uts', '<', $kkm);
    }
}
