<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Siswa;

class GuruController extends Controller
{
    public function dashboard()
    {
        $guru = Auth::user()->guru;

        // Data umum untuk semua guru
        $data = [
            'guru' => $guru,
            'isWaliKelas' => $guru->isWaliKelas(),
            'isGuruMapel' => $guru->isGuruMapel(),
        ];

        // Data khusus wali kelas
        if ($guru->isWaliKelas()) {
            $data['kelasWali'] = $guru->kelasWali;
            $data['jumlahSiswa'] = $guru->kelasWali ? $guru->kelasWali->siswas->count() : 0;
            $data['siswaList'] = $guru->getSiswaKelasWali();
        }

        // Data khusus guru mapel
        if ($guru->isGuruMapel()) {
            $data['mapelDiajar'] = $guru->mapels;
            $data['jadwalMengajar'] = $guru->jadwals()
                ->with(['mapel', 'kelas'])
                ->orderBy('hari')
                ->orderBy('waktu_mulai')
                ->get();
            $data['jumlahMapel'] = $guru->mapels->count();
        }

        return view('guru.dashboard', $data);
    }

    // Untuk Wali Kelas - Kelola Siswa di Kelasnya
    public function kelolaKelas()
    {
        $guru = Auth::user()->guru;

        if (!$guru->isWaliKelas()) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda bukan wali kelas');
        }

        $kelas = $guru->kelasWali()->with('siswas')->first();

        return view('guru.kelas.index', compact('kelas'));
    }

    // Untuk Guru Mapel - Kelola Jadwal & Nilai
    public function jadwalMengajar()
    {
        $guru = Auth::user()->guru;

        if (!$guru->isGuruMapel()) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda bukan guru mapel');
        }

        $jadwals = $guru->jadwals()
            ->with(['mapel', 'kelas'])
            ->orderBy('hari')
            ->orderBy('waktu_mulai')
            ->get();

        return view('guru.jadwal.index', compact('jadwals'));
    }

    // Untuk Guru Mapel - Input Absen
    public function absensi()
    {
        $guru = Auth::user()->guru;

        if (!$guru->isGuruMapel()) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda bukan guru mapel');
        }

        $jadwalHariIni = $guru->jadwals()
            ->where('hari', now()->locale('id')->dayName)
            ->with(['mapel', 'kelas.siswas'])
            ->get();

        return view('guru.absensi.index', compact('jadwalHariIni'));
    }
}
