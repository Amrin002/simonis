<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NilaiAkhir extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'nilai_tugas' => 'decimal:2',
        'nilai_uts' => 'decimal:2',
        'nilai_uas' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
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
     * Hitung nilai akhir berdasarkan bobot
     * Rumus: (Tugas * 30%) + (UTS * 35%) + (UAS * 35%)
     */
    public function hitungNilaiAkhir()
    {
        $bobotTugas = 0.30;
        $bobotUts = 0.35;
        $bobotUas = 0.35;

        return round(
            ($this->nilai_tugas * $bobotTugas) +
                ($this->nilai_uts * $bobotUts) +
                ($this->nilai_uas * $bobotUas),
            2
        );
    }

    /**
     * Tentukan predikat berdasarkan nilai akhir
     */
    public function tentukanPredikat()
    {
        $nilai = $this->nilai_akhir;

        if ($nilai >= 90) {
            return 'A';
        } elseif ($nilai >= 80) {
            return 'B';
        } elseif ($nilai >= 70) {
            return 'C';
        } elseif ($nilai >= 60) {
            return 'D';
        } else {
            return 'E';
        }
    }

    /**
     * Update nilai akhir dan predikat
     */
    public function updateNilaiAkhir()
    {
        $this->nilai_akhir = $this->hitungNilaiAkhir();
        $this->predikat = $this->tentukanPredikat();
        $this->save();
    }

    /**
     * Get formatted nilai akhir
     */
    public function getFormattedNilaiAkhirAttribute()
    {
        return number_format($this->nilai_akhir, 2);
    }

    /**
     * Check if nilai sudah mencapai KKM
     */
    public function isTuntas($kkm = 75)
    {
        return $this->nilai_akhir >= $kkm;
    }

    /**
     * Get status kelulusan
     */
    public function getStatusAttribute()
    {
        return $this->isTuntas() ? 'Tuntas' : 'Tidak Tuntas';
    }

    /**
     * Get predikat description
     */
    public function getPredikatDescriptionAttribute()
    {
        return match ($this->predikat) {
            'A' => 'Sangat Baik',
            'B' => 'Baik',
            'C' => 'Cukup',
            'D' => 'Kurang',
            'E' => 'Sangat Kurang',
            default => '-',
        };
    }

    /**
     * Get predikat color (untuk badge/UI)
     */
    public function getPredikatColorAttribute()
    {
        return match ($this->predikat) {
            'A' => 'success',
            'B' => 'primary',
            'C' => 'warning',
            'D' => 'danger',
            'E' => 'dark',
            default => 'secondary',
        };
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
     * Scope untuk nilai tuntas
     */
    public function scopeTuntas($query, $kkm = 75)
    {
        return $query->where('nilai_akhir', '>=', $kkm);
    }

    /**
     * Scope untuk nilai tidak tuntas
     */
    public function scopeTidakTuntas($query, $kkm = 75)
    {
        return $query->where('nilai_akhir', '<', $kkm);
    }

    /**
     * Scope untuk filter berdasarkan predikat
     */
    public function scopePredikat($query, $predikat)
    {
        return $query->where('predikat', $predikat);
    }

    /**
     * Boot method untuk auto-calculate nilai akhir dan predikat
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($nilaiAkhir) {
            // Auto calculate nilai akhir jika nilai tugas, uts, uas sudah diisi
            if ($nilaiAkhir->nilai_tugas && $nilaiAkhir->nilai_uts && $nilaiAkhir->nilai_uas) {
                $nilaiAkhir->nilai_akhir = $nilaiAkhir->hitungNilaiAkhir();
                $nilaiAkhir->predikat = $nilaiAkhir->tentukanPredikat();
            }
        });
    }
}
