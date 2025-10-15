<?php

namespace App\Http\Controllers;

use App\Models\Rekapan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class OrangTuaController extends Controller
{
    /**
     * Dashboard Orang Tua (Homepage)
     * Menampilkan overview semua anak + rekapan hari ini
     */
    public function dashboard()
    {
        $orangTua = Auth::user()->orangTua;

        // Validasi: User harus punya data orang tua
        if (!$orangTua) {
            abort(403, 'Data orang tua tidak ditemukan');
        }

        // Ambil semua anak dari orang tua ini
        $siswas = $orangTua->siswas()->with(['kelas.waliKelas'])->get();

        // Ambil rekapan hari ini untuk semua anak
        $rekapanHariIni = [];
        $notifikasi = [];

        foreach ($siswas as $siswa) {
            $rekapan = Rekapan::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', today())
                ->first();

            // Jika belum ada rekapan hari ini, generate
            if (!$rekapan) {
                $rekapan = new Rekapan([
                    'siswa_id' => $siswa->id,
                    'tanggal' => today(),
                    'kehadiran' => 'Belum ada data kehadiran untuk hari ini',
                    'perilaku' => 'Status: Baik\nCatatan: Tidak ada pelanggaran hari ini',
                ]);
            }

            $rekapanHariIni[$siswa->id] = $rekapan;

            // Cek notifikasi penting
            if (
                $rekapan->kehadiran &&
                (str_contains($rekapan->kehadiran, 'Sakit') ||
                    str_contains($rekapan->kehadiran, 'Alpa') ||
                    str_contains($rekapan->kehadiran, 'Izin'))
            ) {

                $status = 'Sakit';
                if (str_contains($rekapan->kehadiran, 'Alpa')) $status = 'Alpa';
                if (str_contains($rekapan->kehadiran, 'Izin')) $status = 'Izin';

                $notifikasi[] = [
                    'type' => 'kehadiran',
                    'siswa' => $siswa->nama,
                    'message' => "{$siswa->nama} tidak hadir hari ini ({$status})",
                    'badge' => $status === 'Alpa' ? 'danger' : 'warning'
                ];
            }

            // Cek pelanggaran
            if (
                $rekapan->perilaku &&
                !str_contains($rekapan->perilaku, 'Baik') &&
                !str_contains($rekapan->perilaku, 'Tidak ada pelanggaran')
            ) {

                $notifikasi[] = [
                    'type' => 'pelanggaran',
                    'siswa' => $siswa->nama,
                    'message' => "{$siswa->nama} memiliki catatan pelanggaran hari ini",
                    'badge' => 'danger'
                ];
            }
        }

        $title = 'Dashboard Orang Tua';

        return view('orang-tua.dashboard', compact(
            'orangTua',
            'siswas',
            'rekapanHariIni',
            'notifikasi',
            'title'
        ));
    }

    /**
     * Detail Anak
     * Menampilkan info lengkap anak + rekapan hari ini + statistik
     */
    public function detailAnak($id)
    {
        $orangTua = Auth::user()->orangTua;

        // Validasi: User harus punya data orang tua
        if (!$orangTua) {
            abort(403, 'Data orang tua tidak ditemukan');
        }

        // Ambil data siswa
        $siswa = Siswa::with(['kelas.waliKelas', 'orangTua'])
            ->findOrFail($id);

        // Validasi: Siswa harus anak dari orang tua yang login
        if ($siswa->orang_tua_id !== $orangTua->id) {
            abort(403, 'Anda tidak memiliki akses ke data siswa ini');
        }

        // Rekapan hari ini
        $rekapanHariIni = Rekapan::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', today())
            ->first();

        // Jika belum ada, buat dummy
        if (!$rekapanHariIni) {
            $rekapanHariIni = new Rekapan([
                'siswa_id' => $siswa->id,
                'tanggal' => today(),
                'kehadiran' => 'Belum ada data kehadiran untuk hari ini',
                'perilaku' => 'Status: Baik\nCatatan: Tidak ada pelanggaran hari ini',
            ]);
        }

        // Statistik bulan ini
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $rekapanBulanIni = Rekapan::where('siswa_id', $siswa->id)
            ->whereYear('tanggal', $tahunIni)
            ->whereMonth('tanggal', $bulanIni)
            ->get();

        $totalHari = $rekapanBulanIni->count();

        // Hitung kehadiran
        $hadir = $rekapanBulanIni->filter(function ($r) {
            return $r->kehadiran && str_contains($r->kehadiran, 'Hadir');
        })->count();

        $sakit = $rekapanBulanIni->filter(function ($r) {
            return $r->kehadiran && str_contains($r->kehadiran, 'Sakit');
        })->count();

        $izin = $rekapanBulanIni->filter(function ($r) {
            return $r->kehadiran && str_contains($r->kehadiran, 'Izin');
        })->count();

        $alpa = $rekapanBulanIni->filter(function ($r) {
            return $r->kehadiran && str_contains($r->kehadiran, 'Alpa');
        })->count();

        // Hitung pelanggaran
        $pelanggaran = $rekapanBulanIni->filter(function ($r) {
            return $r->perilaku &&
                !str_contains($r->perilaku, 'Baik') &&
                !str_contains($r->perilaku, 'Tidak ada pelanggaran');
        })->count();

        $persentaseKehadiran = $totalHari > 0 ? round(($hadir / $totalHari) * 100, 1) : 0;

        $statistik = [
            'total_hari' => $totalHari,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
            'pelanggaran' => $pelanggaran,
            'persentase' => $persentaseKehadiran
        ];

        // Riwayat 7 hari terakhir
        $riwayat7Hari = Rekapan::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', '<=', today())
            ->orderBy('tanggal', 'desc')
            ->limit(7)
            ->get();

        $title = 'Detail - ' . $siswa->nama;

        return view('orang-tua.detail-anak', compact(
            'siswa',
            'rekapanHariIni',
            'statistik',
            'riwayat7Hari',
            'title'
        ));
    }

    /**
     * Riwayat Lengkap
     * Menampilkan semua riwayat rekapan anak dengan pagination
     */
    public function riwayatAnak(Request $request, $id)
    {
        $orangTua = Auth::user()->orangTua;

        // Validasi: User harus punya data orang tua
        if (!$orangTua) {
            abort(403, 'Data orang tua tidak ditemukan');
        }

        // Ambil data siswa
        $siswa = Siswa::with(['kelas.waliKelas', 'orangTua'])
            ->findOrFail($id);

        // Validasi: Siswa harus anak dari orang tua yang login
        if ($siswa->orang_tua_id !== $orangTua->id) {
            abort(403, 'Anda tidak memiliki akses ke data siswa ini');
        }

        // Filter berdasarkan bulan (optional)
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun', now()->year);

        $query = Rekapan::where('siswa_id', $siswa->id);

        if ($bulan) {
            $query->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan);
        }

        // Ambil rekapan dengan pagination
        $rekapans = $query->orderBy('tanggal', 'desc')
            ->paginate(20);

        // List bulan untuk filter
        $bulanOptions = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $title = 'Riwayat Rekapan - ' . $siswa->nama;

        return view('orang-tua.riwayat-anak', compact(
            'siswa',
            'rekapans',
            'bulan',
            'tahun',
            'bulanOptions',
            'title'
        ));
    }

    /**
     * Detail Rekapan Spesifik
     * Menampilkan detail rekapan untuk tanggal tertentu
     */
    public function detailRekapan($id)
    {
        $orangTua = Auth::user()->orangTua;

        // Validasi: User harus punya data orang tua
        if (!$orangTua) {
            abort(403, 'Data orang tua tidak ditemukan');
        }

        // Ambil rekapan
        $rekapan = Rekapan::with(['siswa.kelas.waliKelas', 'siswa.orangTua'])
            ->findOrFail($id);

        // Validasi: Siswa harus anak dari orang tua yang login
        if ($rekapan->siswa->orang_tua_id !== $orangTua->id) {
            abort(403, 'Anda tidak memiliki akses ke rekapan ini');
        }

        $title = 'Detail Rekapan - ' . $rekapan->siswa->nama;

        return view('orang-tua.detail-rekapan', compact(
            'rekapan',
            'title'
        ));
    }

    /**
     * Profil Orang Tua
     * Menampilkan dan edit data orang tua
     */
    public function profil()
    {
        $orangTua = Auth::user()->orangTua;

        // Validasi: User harus punya data orang tua
        if (!$orangTua) {
            abort(403, 'Data orang tua tidak ditemukan');
        }

        // Ambil semua anak
        $siswas = $orangTua->siswas()->with('kelas')->get();

        $title = 'Profil Saya';

        return view('orang-tua.profil', compact(
            'orangTua',
            'siswas',
            'title'
        ));
    }

    /**
     * Update Profil Orang Tua
     */
    public function updateProfil(Request $request)
    {
        $orangTua = Auth::user()->orangTua;

        if (!$orangTua) {
            abort(403, 'Data orang tua tidak ditemukan');
        }

        $validated = $request->validate([
            'nama_orang_tua' => 'required|string|max:255',
            'nomor_tlp' => 'required|string|max:20',
            'alamat' => 'required|string',
        ]);

        $orangTua->update($validated);

        return redirect()
            ->route('orangtua.profil')
            ->with('success', 'Profil berhasil diperbarui');
    }
    /**
     * Update Password Orang Tua
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Validasi
        $validated = $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:8|confirmed',
        ], [
            'password_lama.required' => 'Password lama harus diisi',
            'password_baru.required' => 'Password baru harus diisi',
            'password_baru.min' => 'Password baru minimal 8 karakter',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Cek password lama
        if (!Hash::check($validated['password_lama'], $user->password)) {
            return redirect()
                ->route('orangtua.profil')
                ->with('error', 'Password lama tidak sesuai!');
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password_baru'])
        ]);

        return redirect()
            ->route('orangtua.profil')
            ->with('success', 'Password berhasil diubah!');
    }
}
