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
        'dikirim_at' => 'datetime',
        'diselesaikan_at' => 'datetime',
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

    /**
     * Relasi ke Mapel
     */
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    /**
     * Relasi ke Guru yang menyelesaikan (Wali Kelas)
     */
    public function diselesaikanOleh()
    {
        return $this->belongsTo(Guru::class, 'diselesaikan_oleh');
    }

    // ========== STATUS SCOPES ==========

    /**
     * Scope untuk status draft
     */
    public function scopeDraft($query)
    {
        return $query->where('status_rekapan', 'draft');
    }

    /**
     * Scope untuk status dikirim
     */
    public function scopeDikirim($query)
    {
        return $query->where('status_rekapan', 'dikirim');
    }

    /**
     * Scope untuk status selesai
     */
    public function scopeSelesai($query)
    {
        return $query->where('status_rekapan', 'selesai');
    }

    /**
     * Scope untuk absen hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', today());
    }

    /**
     * Scope untuk absen yang menunggu persetujuan wali kelas
     */
    public function scopeMenungguWaliKelas($query, $kelasId)
    {
        return $query->where('kelas_id', $kelasId)
            ->where('status_rekapan', 'dikirim');
    }

    // ========== WORKFLOW METHODS ==========

    /**
     * Check apakah ini absen dari wali kelas
     */
    public function isAbsenWaliKelas($guru): bool
    {
        if (!$guru->is_wali_kelas) {
            return false;
        }

        // Cek apakah guru ini adalah wali kelas dari kelas ini
        return $this->kelas && $this->kelas->wali_guru_id === $guru->id;
    }

    /**
     * Check apakah bisa diedit
     */
    public function canEdit(): bool
    {
        return $this->status_rekapan !== 'selesai';
    }

    /**
     * Check apakah bisa dikirim ke wali kelas
     */
    public function canKirim(): bool
    {
        return $this->status_rekapan === 'draft';
    }

    /**
     * Check apakah bisa diselesaikan
     */
    public function canSelesai(): bool
    {
        return $this->status_rekapan === 'dikirim';
    }

    /**
     * Kirim ke wali kelas
     */
    public function kirimKeWaliKelas(): void
    {
        if (!$this->canKirim()) {
            throw new \Exception('Absen tidak dapat dikirim');
        }

        $this->status_rekapan = 'dikirim';
        $this->dikirim_at = now();
        $this->save();
    }

    /**
     * Selesaikan absen (oleh wali kelas)
     */
    public function selesaikan($guru): void
    {
        // Validasi hanya wali kelas yang bisa menyelesaikan
        if (!$this->isAbsenWaliKelas($guru)) {
            throw new \Exception('Hanya wali kelas yang dapat menyelesaikan absen');
        }

        $this->status_rekapan = 'selesai';
        $this->diselesaikan_at = now();
        $this->diselesaikan_oleh = $guru->id;
        $this->save();

        // Trigger generate rekapan untuk semua siswa di kelas ini
        $this->generateRekapanForKelas();
    }

    /**
     * Langsung selesaikan (untuk wali kelas yang absen di kelasnya sendiri)
     */
    public function selesaikanLangsung($guru): void
    {
        if (!$this->isAbsenWaliKelas($guru)) {
            throw new \Exception('Hanya wali kelas yang dapat menyelesaikan absen');
        }

        $this->status_rekapan = 'selesai';
        $this->dikirim_at = now(); // Set juga dikirim_at
        $this->diselesaikan_at = now();
        $this->diselesaikan_oleh = $guru->id;
        $this->save();

        // Trigger generate rekapan untuk semua siswa di kelas ini
        $this->generateRekapanForKelas();
    }

    /**
     * Generate atau update rekapan untuk semua siswa di kelas
     */
    private function generateRekapanForKelas(): void
    {
        $siswaList = $this->kelas->siswas;

        foreach ($siswaList as $siswa) {
            // Cari atau buat rekapan untuk siswa ini
            $rekapan = Rekapan::firstOrNew([
                'siswa_id' => $siswa->id,
                'tanggal' => $this->tanggal,
            ]);

            // Generate kehadiran
            $rekapan->generateKehadiran();

            // Generate perilaku jika belum ada
            if (empty($rekapan->perilaku)) {
                $rekapan->generatePerilaku();
            }

            $rekapan->save();
        }
    }

    // ========== UI HELPERS ==========

    /**
     * Get status badge color untuk UI
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status_rekapan) {
            'selesai' => 'success',
            'dikirim' => 'info',
            'draft' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get status text untuk UI
     */
    public function getStatusTextAttribute(): string
    {
        return match ($this->status_rekapan) {
            'selesai' => 'Selesai',
            'dikirim' => 'Menunggu Wali Kelas',
            'draft' => 'Draft',
            default => 'Unknown'
        };
    }
    /**
     * Get button action untuk UI
     */
    public function getButtonAction($guru): array
    {
        // STATUS: DRAFT
        if ($this->status_rekapan === 'draft') {
            // Jika wali kelas absen di kelasnya sendiri
            if ($this->isAbsenWaliKelas($guru)) {
                return [
                    'text' => 'Selesai',
                    'color' => 'success',
                    'action' => 'selesaikan_langsung',
                    'disabled' => false
                ];
            }

            // Guru mapel - kirim ke wali kelas
            return [
                'text' => 'Kirim ke Wali Kelas',
                'color' => 'primary',
                'action' => 'kirim',
                'disabled' => false
            ];
        }

        // STATUS: DIKIRIM (Menunggu Wali Kelas)
        if ($this->status_rekapan === 'dikirim') {
            // Cek apakah user adalah wali kelas dari kelas ini
            if ($this->isAbsenWaliKelas($guru)) {
                return [
                    'text' => 'Selesaikan',
                    'color' => 'success',
                    'action' => 'selesaikan',  // ✅ ACTION BARU!
                    'disabled' => false
                ];
            }

            // Bukan wali kelas - disabled
            return [
                'text' => 'Menunggu Wali Kelas',
                'color' => 'secondary',
                'action' => 'disabled',
                'disabled' => true
            ];
        }

        // STATUS: SELESAI
        if ($this->status_rekapan === 'selesai') {
            return [
                'text' => 'Sudah Selesai',
                'color' => 'success',
                'action' => 'disabled',
                'disabled' => true
            ];
        }

        // Default - Unknown status
        return [
            'text' => 'Unknown',
            'color' => 'secondary',
            'action' => 'disabled',
            'disabled' => true
        ];
    }
}
