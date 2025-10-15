<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Rekapan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'rekapans';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'siswa_id',
        'tanggal',
        'kehadiran',
        'perilaku',
        'status_kirim',
        'dikirim_at',
        'catatan_pengiriman',
        'wa_link',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tanggal' => 'date',
        'dikirim_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Relasi ke Siswa (belongsTo)
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope untuk rekapan hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', today());
    }

    /**
     * Scope untuk rekapan yang belum dikirim
     */
    public function scopeBelumDikirim($query)
    {
        return $query->where('status_kirim', 'belum_dikirim');
    }

    /**
     * Scope untuk rekapan yang sudah dikirim
     */
    public function scopeSudahDikirim($query)
    {
        return $query->where('status_kirim', 'dikirim');
    }

    /**
     * Scope dengan data siswa dan orang tua
     */
    public function scopeWithDetails($query)
    {
        return $query->with([
            'siswa.kelas.waliKelas',
            'siswa.orangTua'
        ]);
    }

    /**
     * Scope berdasarkan tanggal
     */
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    /**
     * Scope berdasarkan siswa
     */
    public function scopeBySiswa($query, $siswaId)
    {
        return $query->where('siswa_id', $siswaId);
    }

    // ========== ACCESSORS ==========

    /**
     * Get formatted kehadiran
     */
    public function getFormattedKehadiranAttribute(): array
    {
        if (empty($this->kehadiran)) {
            return [];
        }

        $lines = explode("\n", $this->kehadiran);
        $data = [];

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                $data[trim($key)] = trim($value);
            }
        }

        return $data;
    }

    /**
     * Get formatted perilaku
     */
    public function getFormattedPerilakuAttribute(): array
    {
        if (empty($this->perilaku)) {
            return [];
        }

        $lines = explode("\n", $this->perilaku);
        $data = [];

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                $data[trim($key)] = trim($value);
            }
        }

        return $data;
    }

    /**
     * Get status badge color untuk UI
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status_kirim) {
            'dikirim' => 'success',
            'gagal' => 'danger',
            'belum_dikirim' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get status text untuk UI
     */
    public function getStatusTextAttribute(): string
    {
        return match ($this->status_kirim) {
            'dikirim' => 'Sudah Dikirim',
            'gagal' => 'Gagal Dikirim',
            'belum_dikirim' => 'Belum Dikirim',
            default => 'Unknown'
        };
    }

    // ========== GENERATE METHODS ==========

    /**
     * Generate kehadiran dari absen yang sudah selesai
     */
    public function generateKehadiran(): void
    {
        if (!$this->siswa) {
            return;
        }

        // Cari absen yang sudah selesai untuk tanggal ini
        $absen = Absen::where('tanggal', $this->tanggal)
            ->where('kelas_id', $this->siswa->kelas_id)
            ->where('status_rekapan', 'selesai')
            ->first();

        if (!$absen) {
            $this->kehadiran = "Belum ada data kehadiran untuk hari ini";
            return;
        }

        // Cari detail absen siswa ini
        $detailAbsen = DetailAbsen::where('absen_id', $absen->id)
            ->where('siswa_id', $this->siswa->id)
            ->first();

        if (!$detailAbsen) {
            $this->kehadiran = "Data kehadiran tidak ditemukan";
            return;
        }

        $kehadiran = [];
        $kehadiran[] = "Status: {$detailAbsen->status}";
        $kehadiran[] = "Tanggal: " . $this->tanggal->format('d-m-Y');

        if ($detailAbsen->keterangan) {
            $kehadiran[] = "Keterangan: {$detailAbsen->keterangan}";
        }

        $this->kehadiran = implode("\n", $kehadiran);
    }

    /**
     * Generate perilaku dari pelanggaran yang sudah selesai
     */
    public function generatePerilaku(): void
    {
        if (!$this->siswa) {
            return;
        }

        // Cari pelanggaran yang sudah selesai untuk tanggal ini
        $pelanggaran = Pelanggaran::where('siswa_id', $this->siswa->id)
            ->whereDate('tanggal', $this->tanggal)
            ->where('status_rekapan', 'selesai')
            ->first();

        if (!$pelanggaran) {
            $perilaku = [];
            $perilaku[] = "Status: Baik";
            $perilaku[] = "Catatan: Tidak ada pelanggaran hari ini";

            $this->perilaku = implode("\n", $perilaku);
            return;
        }

        $perilaku = [];
        $perilaku[] = "Jenis Pelanggaran: {$pelanggaran->jenis_pelanggaran}";
        $perilaku[] = "Kategori: {$pelanggaran->kategori}";
        $perilaku[] = "Tanggal: " . $pelanggaran->tanggal->format('d-m-Y');

        if ($pelanggaran->keterangan) {
            $perilaku[] = "Keterangan: {$pelanggaran->keterangan}";
        }

        $this->perilaku = implode("\n", $perilaku);
    }

    /**
     * Generate semua data rekapan untuk hari ini
     */
    public function generateAll(): void
    {
        $this->generateKehadiran();
        $this->generatePerilaku();
    }

    // ========== WHATSAPP METHODS ==========

    /**
     * Generate WhatsApp link untuk orang tua
     */
    public function generateWaLink(): string
    {
        if (!$this->siswa || !$this->siswa->orangTua) {
            return '';
        }

        $orangTua = $this->siswa->orangTua;

        // Clean nomor HP (hapus karakter non-numerik)
        $noHp = preg_replace('/[^0-9]/', '', $orangTua->nomor_tlp);

        // Tambah kode negara jika belum ada (Indonesia: 62)
        if (substr($noHp, 0, 1) === '0') {
            $noHp = '62' . substr($noHp, 1);
        } elseif (substr($noHp, 0, 2) !== '62') {
            $noHp = '62' . $noHp;
        }

        // Generate pesan
        $message = $this->generateWaMessage();

        // Encode pesan untuk URL
        $encodedMessage = urlencode($message);

        // Generate link wa.me
        $waLink = "https://wa.me/{$noHp}?text={$encodedMessage}";

        // Simpan ke database
        $this->wa_link = $waLink;

        return $waLink;
    }

    /**
     * Generate pesan WhatsApp
     */
    public function generateWaMessage(): string
    {
        $siswa = $this->siswa;
        $tanggal = $this->tanggal->locale('id')->isoFormat('D MMMM YYYY');

        $message = "*REKAPAN HARIAN SISWA*\n\n";
        $message .= "Kepada Yth. Orang Tua/Wali dari:\n";
        $message .= "Nama: *{$siswa->nama}*\n";
        $message .= "NIS: {$siswa->nis}\n";
        $message .= "Kelas: " . ($siswa->kelas ? $siswa->kelas->nama : '-') . "\n";
        $message .= "Tanggal: *{$tanggal}*\n\n";

        $message .= "--- KEHADIRAN ---\n";
        $message .= $this->kehadiran ?: "Belum ada data";
        $message .= "\n\n";

        $message .= "--- PERILAKU ---\n";
        $message .= $this->perilaku ?: "Tidak ada catatan";
        $message .= "\n\n";

        $message .= "--- Pesan Otomatis dari Sistem Monitoring Siswa ---\n";
        $message .= "Wali Kelas: " . ($siswa->kelas && $siswa->kelas->waliKelas ? $siswa->kelas->waliKelas->nama_guru : '-');

        return $message;
    }

    /**
     * Validasi nomor HP sebelum generate link
     */
    public function validateNoHp(): bool
    {
        if (!$this->siswa || !$this->siswa->orangTua) {
            Log::warning("Siswa {$this->siswa->nama} tidak punya data orang tua");
            return false;
        }

        $noHp = $this->siswa->orangTua->nomor_tlp;

        // ✅ TAMBAH LOG UNTUK DEBUG
        Log::info("Validasi HP - Siswa: {$this->siswa->nama}, Nomor: {$noHp}");

        $cleaned = preg_replace('/[^0-9]/', '', $noHp);

        // ✅ TAMBAH LOG JUMLAH DIGIT
        Log::info("Nomor setelah cleaning: {$cleaned}, Jumlah digit: " . strlen($cleaned));

        if (strlen($cleaned) < 10 || strlen($cleaned) > 15) {
            $this->markAsGagal('Nomor HP tidak valid: ' . $noHp . ' (Digit: ' . strlen($cleaned) . ')');
            Log::warning("Nomor tidak valid - kurang/lebih dari 10-15 digit");
            return false;
        }

        return true;
    }

    // ========== STATUS METHODS ==========

    /**
     * Tandai sebagai sudah dikirim
     */
    public function markAsDikirim(): void
    {
        $this->status_kirim = 'dikirim';
        $this->dikirim_at = now();
        $this->save();
    }

    /**
     * Tandai sebagai gagal dikirim
     */
    public function markAsGagal(string $catatan): void
    {
        $this->status_kirim = 'gagal';
        $this->catatan_pengiriman = $catatan;
        $this->save();
    }

    /**
     * Check apakah sudah dikirim
     */
    public function isDikirim(): bool
    {
        return $this->status_kirim === 'dikirim';
    }

    /**
     * Check apakah belum dikirim
     */
    public function isBelumDikirim(): bool
    {
        return $this->status_kirim === 'belum_dikirim';
    }

    /**
     * Check apakah gagal dikirim
     */
    public function isGagal(): bool
    {
        return $this->status_kirim === 'gagal';
    }
}
