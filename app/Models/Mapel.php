<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mapel extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // ========== RELATIONS ==========

    /**
     * Get guru yang mengampu mapel ini
     */
    public function gurus()
    {
        return $this->belongsToMany(Guru::class, 'guru_mapels', 'mapel_id', 'guru_id')
            ->withTimestamps();
    }

    /**
     * Get guru mapel (pivot table)
     */
    public function guruMapels()
    {
        return $this->hasMany(GuruMapel::class, 'mapel_id');
    }

    /**
     * Get jadwal untuk mapel ini
     */
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'mapel_id');
    }

    /**
     * Get absen untuk mapel ini
     */
    public function absens()
    {
        return $this->hasMany(Absen::class, 'mapel_id');
    }

    // ========== ACCESSORS ==========

    /**
     * Get nama lengkap dengan kode
     */
    public function getNamaLengkapAttribute(): string
    {
        return $this->kode_mapel
            ? "{$this->nama_matapelajaran} ({$this->kode_mapel})"
            : $this->nama_matapelajaran;
    }

    /**
     * Get jumlah guru pengampu
     */
    public function getJumlahGuruAttribute(): int
    {
        return $this->gurus()->count();
    }

    /**
     * Get daftar nama guru
     */
    public function getDaftarNamaGuruAttribute(): string
    {
        return $this->gurus->pluck('nama_guru')->join(', ') ?: '-';
    }

    /**
     * Get jumlah jadwal
     */
    public function getJumlahJadwalAttribute(): int
    {
        return $this->jadwals()->count();
    }

    /**
     * Get jumlah kelas yang diajar
     */
    public function getJumlahKelasAttribute(): int
    {
        return $this->jadwals()->distinct('kelas_id')->count('kelas_id');
    }

    /**
     * Check if mapel has guru
     */
    public function hasGuru(): bool
    {
        return $this->gurus()->count() > 0;
    }

    /**
     * Check if mapel has jadwal
     */
    public function hasJadwal(): bool
    {
        return $this->jadwals()->count() > 0;
    }

    /**
     * Get status penggunaan
     */
    public function getStatusPenggunaanAttribute(): string
    {
        if ($this->hasJadwal()) {
            return 'Aktif Digunakan';
        } elseif ($this->hasGuru()) {
            return 'Ada Guru';
        }
        return 'Belum Digunakan';
    }

    /**
     * Get badge color for status
     */
    public function getStatusBadgeColorAttribute(): string
    {
        if ($this->hasJadwal()) {
            return 'success';
        } elseif ($this->hasGuru()) {
            return 'info';
        }
        return 'secondary';
    }
}
