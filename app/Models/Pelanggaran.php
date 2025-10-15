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
        'diselesaikan_at' => 'datetime',
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

    // ========== STATUS SCOPES ==========

    /**
     * Scope untuk status draft
     */
    public function scopeDraft($query)
    {
        return $query->where('status_rekapan', 'draft');
    }

    /**
     * Scope untuk status selesai
     */
    public function scopeSelesai($query)
    {
        return $query->where('status_rekapan', 'selesai');
    }

    /**
     * Scope untuk pelanggaran hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', today());
    }

    // ========== WORKFLOW METHODS ==========

    /**
     * Check apakah bisa diedit
     */
    public function canEdit(): bool
    {
        return $this->status_rekapan !== 'selesai';
    }

    /**
     * Check apakah bisa diselesaikan
     */
    public function canSelesai(): bool
    {
        return $this->status_rekapan === 'draft';
    }

    /**
     * Validasi: Max 1 pelanggaran selesai per siswa per hari
     */
    public static function canAddForToday($siswaId, $tanggal = null): bool
    {
        $tanggal = $tanggal ?: today();

        $existing = self::where('siswa_id', $siswaId)
            ->whereDate('tanggal', $tanggal)
            ->where('status_rekapan', 'selesai')
            ->exists();

        return !$existing;
    }

    /**
     * Selesaikan pelanggaran
     */
    public function selesaikan(): void
    {
        if (!$this->canSelesai()) {
            throw new \Exception('Pelanggaran tidak dapat diselesaikan');
        }

        // Validasi max 1 pelanggaran per hari
        if (!self::canAddForToday($this->siswa_id, $this->tanggal)) {
            throw new \Exception('Siswa ini sudah memiliki pelanggaran yang diselesaikan untuk hari ini');
        }

        $this->status_rekapan = 'selesai';
        $this->diselesaikan_at = now();
        $this->save();

        // Trigger generate/update rekapan untuk siswa ini
        $this->generateRekapanForSiswa();
    }

    /**
     * Generate atau update rekapan untuk siswa ini
     */
    private function generateRekapanForSiswa(): void
    {
        // Cari atau buat rekapan untuk siswa ini
        $rekapan = Rekapan::firstOrNew([
            'siswa_id' => $this->siswa_id,
            'tanggal' => $this->tanggal,
        ]);

        // Generate perilaku
        $rekapan->generatePerilaku();

        // Generate kehadiran jika belum ada
        if (empty($rekapan->kehadiran)) {
            $rekapan->generateKehadiran();
        }

        $rekapan->save();
    }

    // ========== UI HELPERS ==========

    /**
     * Get status badge color untuk UI (untuk status_rekapan)
     */
    public function getStatusRekapanBadgeColorAttribute(): string
    {
        return match ($this->status_rekapan) {
            'selesai' => 'success',
            'draft' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get status rekapan text untuk UI
     */
    public function getStatusRekapanTextAttribute(): string
    {
        return match ($this->status_rekapan) {
            'selesai' => 'Selesai',
            'draft' => 'Draft',
            default => 'Unknown'
        };
    }

    /**
     * Get kategori icon untuk UI
     */
    public function getKategoriIconAttribute(): string
    {
        return match ($this->kategori) {
            'Berat' => 'fa-exclamation-triangle',
            'Sedang' => 'fa-exclamation-circle',
            'Ringan' => 'fa-info-circle',
            default => 'fa-circle'
        };
    }
}
