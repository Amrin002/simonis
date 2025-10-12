<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jadwal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'waktu_mulai' => 'datetime:H:i',
        'waktu_selesai' => 'datetime:H:i',
    ];

    /**
     * Get kelas untuk jadwal ini
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Get guru yang mengajar
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }


    /**
     * Get mata pelajaran
     */
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }
    /**
     * Scope untuk filter berdasarkan hari
     */
    public function scopeHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    /**
     * Scope untuk filter berdasarkan kelas
     */
    public function scopeKelas($query, $kelasId)
    {
        return $query->where('kelas_id', $kelasId);
    }

    /**
     * Scope untuk filter berdasarkan guru
     */
    public function scopeGuru($query, $guruId)
    {
        return $query->where('guru_id', $guruId);
    }



    // ========== ACCESSORS ==========

    /**
     * Get formatted waktu
     */
    public function getWaktuAttribute()
    {
        return date('H:i', strtotime($this->waktu_mulai)) . ' - ' . date('H:i', strtotime($this->waktu_selesai));
    }

    /**
     * Get formatted waktu mulai
     */
    public function getWaktuMulaiFormatAttribute()
    {
        return date('H:i', strtotime($this->waktu_mulai));
    }

    /**
     * Get formatted waktu selesai
     */
    public function getWaktuSelesaiFormatAttribute()
    {
        return date('H:i', strtotime($this->waktu_selesai));
    }

    /**
     * Get durasi dalam menit
     */
    public function getDurasiAttribute()
    {
        $mulai = Carbon::parse($this->waktu_mulai);
        $selesai = Carbon::parse($this->waktu_selesai);
        return $mulai->diffInMinutes($selesai);
    }

    /**
     * Get durasi formatted (jam:menit)
     */
    public function getDurasiFormatAttribute()
    {
        $durasi = $this->durasi;
        $jam = floor($durasi / 60);
        $menit = $durasi % 60;

        if ($jam > 0) {
            return $jam . ' jam ' . ($menit > 0 ? $menit . ' menit' : '');
        }
        return $menit . ' menit';
    }

    /**
     * Get nama lengkap jadwal
     */
    public function getNamaLengkapAttribute(): string
    {
        return "{$this->mapel->nama_matapelajaran} - {$this->kelas->nama} ({$this->hari}, {$this->waktu})";
    }

    /**
     * Get label hari dengan badge color
     */
    public function getHariBadgeColorAttribute(): string
    {
        $colors = [
            'Senin' => 'primary',
            'Selasa' => 'success',
            'Rabu' => 'info',
            'Kamis' => 'warning',
            'Jumat' => 'danger',
            'Sabtu' => 'secondary',
        ];

        return $colors[$this->hari] ?? 'secondary';
    }

    // ========== METHODS ==========

    /**
     * Check if jadwal is for today
     */
    public function isToday(): bool
    {
        $hariIni = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        return $this->hari === $hariIni[now()->format('l')];
    }

    /**
     * Check if jadwal is currently active (sedang berlangsung)
     */
    public function isActive(): bool
    {
        if (!$this->isToday()) {
            return false;
        }

        $now = now()->format('H:i');
        $mulai = date('H:i', strtotime($this->waktu_mulai));
        $selesai = date('H:i', strtotime($this->waktu_selesai));

        return $now >= $mulai && $now <= $selesai;
    }

    /**
     * Get status jadwal (upcoming, active, finished)
     */
    public function getStatusAttribute(): string
    {
        if (!$this->isToday()) {
            return 'scheduled';
        }

        if ($this->isActive()) {
            return 'active';
        }

        $now = now()->format('H:i');
        $selesai = date('H:i', strtotime($this->waktu_selesai));

        if ($now > $selesai) {
            return 'finished';
        }

        return 'upcoming';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'scheduled' => 'Terjadwal',
            'active' => 'Sedang Berlangsung',
            'finished' => 'Selesai',
            'upcoming' => 'Akan Datang',
        ];

        return $labels[$this->status] ?? 'Terjadwal';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        $colors = [
            'scheduled' => 'secondary',
            'active' => 'success',
            'finished' => 'danger',
            'upcoming' => 'info',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Check if there's conflict with another jadwal
     */
    public function hasConflict($kelasId = null, $guruId = null, $excludeId = null): bool
    {
        $query = static::where('hari', $this->hari)
            ->where(function ($q) {
                $q->whereBetween('waktu_mulai', [$this->waktu_mulai, $this->waktu_selesai])
                    ->orWhereBetween('waktu_selesai', [$this->waktu_mulai, $this->waktu_selesai])
                    ->orWhere(function ($q2) {
                        $q2->where('waktu_mulai', '<=', $this->waktu_mulai)
                            ->where('waktu_selesai', '>=', $this->waktu_selesai);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        if ($guruId) {
            $query->where('guru_id', $guruId);
        }

        return $query->exists();
    }
}
