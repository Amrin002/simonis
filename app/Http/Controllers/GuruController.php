<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;

class GuruController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $guru = $user->guru;

        // Validasi: pastikan guru ada
        if (!$guru) {
            return redirect()->route('login')
                ->with('error', 'Data guru tidak ditemukan. Hubungi administrator.');
        }

        $title = 'Dashboard Guru';

        // Set lokalisasi ke Indonesia untuk nama hari
        Carbon::setLocale('id');
        $hariIni = Carbon::now()->isoFormat('dddd');

        // Data umum untuk semua tipe guru
        $data = [
            'title' => $title,
            'guru' => $guru,
            'hariIni' => $hariIni,
            'tanggalHariIni' => Carbon::now()->isoFormat('D MMMM YYYY'),
        ];

        // Data khusus Guru Mapel
        if ($guru->isGuruMapel()) {
            $data['jumlahMapel'] = $guru->mapels()->count();
            $data['jumlahKelasMapel'] = $guru->jadwals()->distinct('kelas_id')->count('kelas_id');
            $data['jadwalHariIni'] = $guru->getJadwalHariIni();
            $data['jadwalMingguIni'] = $this->getJadwalMingguIni($guru);
        }

        // Data khusus Wali Kelas
        if ($guru->isWaliKelas()) {
            $kelasWali = $guru->kelasWali;
            $data['kelasWali'] = $kelasWali;
            $data['jumlahSiswaKelas'] = $kelasWali ? $kelasWali->siswas()->count() : 0;
            $data['siswaTerbaru'] = $kelasWali ? $kelasWali->siswas()->latest()->take(5)->get() : collect();
        }

        // Statistik gabungan
        $data['totalJadwalMingguIni'] = $guru->jadwals()->count();
        $data['jadwalHariIniCount'] = $guru->getJadwalHariIni()->count();

        return view('guru.dashboard', $data);
    }

    /**
     * Get jadwal mengajar untuk minggu ini
     */
    private function getJadwalMingguIni($guru)
    {
        $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return $guru->jadwals()
            ->with(['mapel', 'kelas'])
            ->whereIn('hari', $daftarHari)
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('waktu_mulai')
            ->get()
            ->groupBy('hari');
    }

    /**
     * Lihat detail kelas wali
     */
    public function detailKelasWali()
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru->isWaliKelas()) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda bukan wali kelas');
        }

        $kelasWali = $guru->kelasWali;
        $siswas = $kelasWali->siswas()->orderBy('nama')->get();

        return view('guru.kelas-wali', [
            'title' => 'Kelas Wali - ' . $kelasWali->nama,
            'kelas' => $kelasWali,
            'siswas' => $siswas,
            'guru' => $guru
        ]);
    }

    /**
     * Lihat jadwal mengajar
     */
    public function jadwalMengajar()
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru->isGuruMapel()) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda bukan guru mata pelajaran');
        }

        $jadwals = $guru->jadwals()
            ->with(['mapel', 'kelas'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('waktu_mulai')
            ->get()
            ->groupBy('hari');

        return view('guru.jadwal-mengajar', [
            'title' => 'Jadwal Mengajar',
            'jadwals' => $jadwals,
            'guru' => $guru,
            'mapels' => $guru->mapels
        ]);
    }

    /**
     * Lihat daftar siswa untuk guru mapel
     */
    public function daftarSiswa()
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru->isGuruMapel()) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda bukan guru mata pelajaran');
        }

        // Ambil semua kelas yang diajar
        $kelasIds = $guru->jadwals()->distinct('kelas_id')->pluck('kelas_id');
        $kelasList = Kelas::whereIn('id', $kelasIds)
            ->with(['siswas' => function ($query) {
                $query->orderBy('nama');
            }])
            ->get();

        return view('guru.daftar-siswa', [
            'title' => 'Daftar Siswa',
            'kelasList' => $kelasList,
            'guru' => $guru
        ]);
    }

    /**
     * Update profile guru
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $guru = $user->guru;

        $request->validate([
            'nama_guru' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_telepon' => 'nullable|string|max:15',
        ]);

        // Update user
        $user->update([
            'name' => $request->nama_guru,
            'email' => $request->email,
        ]);

        // Update guru (sesuaikan dengan kolom di tabel gurus)
        $guru->update([
            'nama_guru' => $request->nama_guru,
            // 'no_telepon' => $request->no_telepon, // Uncomment jika ada kolom ini
        ]);

        return redirect()->back()->with('success', 'Profile berhasil diupdate');
    }
}
