<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaGuruController extends Controller
{
    /**
     * Display daftar siswa (untuk Wali Kelas & Guru Mapel)
     */
    public function index(Request $request)
    {
        $guru = Auth::user()->guru;
        $title = 'Daftar Siswa';

        // Filter kelas untuk guru mapel
        $kelasId = $request->get('kelas_id');
        $search = $request->get('search');

        // ✅ DUAL ROLE: Wali Kelas + Guru Mapel
        if ($guru->isWaliKelas() && $guru->isGuruMapel()) {
            // Default ke kelas wali jika belum pilih kelas lain
            if (!$kelasId) {
                $kelasId = $guru->kelasWali->id;
            }

            // Get semua kelas yang bisa diakses
            $kelasWaliId = $guru->kelasWali->id;
            $kelasMapelIds = $guru->jadwals()->pluck('kelas_id')->unique();
            $allKelasIds = $kelasMapelIds->push($kelasWaliId)->unique();

            // Validasi: Kelas harus dalam list yang boleh diakses
            if (!$allKelasIds->contains($kelasId)) {
                return redirect()->route('guru.siswa.index')
                    ->with('error', 'Anda tidak memiliki akses ke kelas ini');
            }

            $kelas = Kelas::findOrFail($kelasId);
            $title = 'Daftar Siswa Kelas ' . $kelas->nama;

            // Query siswa
            $query = $kelas->siswas()->with(['orangTua', 'pelanggarans']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            }

            $siswas = $query->orderBy('nama')->paginate(15);

            // Statistik kelas
            $stats = [
                'total_siswa' => $kelas->jumlah_siswa,
                'siswa_lengkap' => $kelas->siswas()->whereNotNull('orang_tua_id')->count(),
                'total_pelanggaran' => $kelas->jumlah_pelanggaran,
                'pelanggaran_bulan_ini' => $kelas->getPelanggaranBulanIni()->count(),
            ];

            // List kelas untuk dropdown
            $kelasList = Kelas::whereIn('id', $allKelasIds)
                ->orderBy('nama')
                ->get();

            return view('guru.siswa.index', compact(
                'siswas',
                'kelas',
                'guru',
                'title',
                'search',
                'stats',
                'kelasList',
                'kelasId'
            ));
        } elseif ($guru->isWaliKelas()) {
            // ✅ HANYA WALI KELAS: Langsung tampilkan kelas wali
            $kelas = $guru->kelasWali;

            if (!$kelas) {
                return redirect()->route('guru.dashboard')
                    ->with('error', 'Anda belum ditugaskan sebagai wali kelas');
            }

            $title = 'Daftar Siswa Kelas ' . $kelas->nama;

            // Query siswa
            $query = $kelas->siswas()->with(['orangTua', 'pelanggarans']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            }

            $siswas = $query->orderBy('nama')->paginate(15);

            // Statistik kelas
            $stats = [
                'total_siswa' => $kelas->jumlah_siswa,
                'siswa_lengkap' => $kelas->siswas()->whereNotNull('orang_tua_id')->count(),
                'total_pelanggaran' => $kelas->jumlah_pelanggaran,
                'pelanggaran_bulan_ini' => $kelas->getPelanggaranBulanIni()->count(),
            ];

            // Default empty untuk compatibility
            $kelasList = collect();
            $kelasId = null;

            return view('guru.siswa.index', compact(
                'siswas',
                'kelas',
                'guru',
                'title',
                'search',
                'stats',
                'kelasList',
                'kelasId'
            ));
        } elseif ($guru->isGuruMapel()) {
            // ✅ HANYA GURU MAPEL: Harus pilih kelas dulu
            $kelasList = Kelas::whereIn('id', $guru->jadwals()->pluck('kelas_id')->unique())
                ->orderBy('nama')
                ->get();

            // Jika belum pilih kelas, tampilkan pilihan kelas
            if (!$kelasId) {
                return view('guru.siswa.select-kelas', compact(
                    'guru',
                    'kelasList',
                    'title'
                ));
            }

            // Validasi: Guru mapel harus mengajar di kelas ini
            if (!$guru->jadwals()->where('kelas_id', $kelasId)->exists()) {
                return redirect()->route('guru.siswa.index')
                    ->with('error', 'Anda tidak mengajar di kelas ini');
            }

            $kelas = Kelas::findOrFail($kelasId);
            $title = 'Daftar Siswa Kelas ' . $kelas->nama;

            // Query siswa
            $query = $kelas->siswas()->with(['orangTua', 'pelanggarans']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            }

            $siswas = $query->orderBy('nama')->paginate(15);

            // Statistik kelas
            $stats = [
                'total_siswa' => $kelas->jumlah_siswa,
                'siswa_lengkap' => $kelas->siswas()->whereNotNull('orang_tua_id')->count(),
                'total_pelanggaran' => $kelas->jumlah_pelanggaran,
                'pelanggaran_bulan_ini' => $kelas->getPelanggaranBulanIni()->count(),
            ];

            return view('guru.siswa.index', compact(
                'siswas',
                'kelas',
                'guru',
                'title',
                'search',
                'stats',
                'kelasList',
                'kelasId'
            ));
        } else {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }
    }

    /**
     * Show detail siswa (Read Only)
     */
    public function show($id)
    {
        $guru = Auth::user()->guru;

        // Validasi: Siswa harus dari kelas yang diwali/diajar
        $siswa = Siswa::with(['kelas', 'orangTua', 'pelanggarans' => function ($query) {
            $query->orderBy('tanggal', 'desc');
        }])->findOrFail($id);

        // Validasi akses berdasarkan role
        if ($guru->isWaliKelas() && $siswa->kelas_id === $guru->kelasWali->id) {
            // Wali kelas bisa akses siswa di kelasnya
            $kelas = $guru->kelasWali;
        } elseif ($guru->isGuruMapel() && $guru->jadwals()->where('kelas_id', $siswa->kelas_id)->exists()) {
            // Guru mapel bisa akses siswa di kelas yang diajar
            $kelas = $siswa->kelas;
        } else {
            abort(403, 'Anda tidak memiliki akses ke siswa ini');
        }

        $title = 'Detail Siswa - ' . $siswa->nama;

        // Statistik siswa
        $stats = [
            'total_pelanggaran' => $siswa->jumlah_pelanggaran,
            'pelanggaran_ringan' => $siswa->pelanggaran_ringan,
            'pelanggaran_sedang' => $siswa->pelanggaran_sedang,
            'pelanggaran_berat' => $siswa->pelanggaran_berat,
            'pelanggaran_bulan_ini' => $siswa->getPelanggaranBulanIni()->count(),
        ];

        return view('guru.siswa.show', compact(
            'siswa',
            'kelas',
            'guru',
            'title',
            'stats'
        ));
    }
}
