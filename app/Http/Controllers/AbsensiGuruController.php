<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\DetailAbsen;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiGuruController extends Controller
{
    /**
     * Display absensi index (untuk semua guru)
     */
    public function index(Request $request)
    {
        $guru = Auth::user()->guru;
        $title = 'Daftar Absensi';

        // Filter parameters
        $tanggal = $request->get('tanggal');
        $kelas = $request->get('kelas');
        $mapel = $request->get('mapel');

        // Base query
        $query = Absen::with(['kelas', 'detailAbsens']);

        // Filter berdasarkan role
        if ($guru->isWaliKelas()) {
            // Wali kelas lihat semua absensi kelasnya
            $query->where('kelas_id', $guru->kelas_wali_id);
        } elseif ($guru->isGuruMapel()) {
            // ✅ PERBAIKAN: Guru mapel HANYA lihat absensi mapel yang diajar
            $kelasIds = $guru->jadwals()->pluck('kelas_id')->unique();
            $query->whereIn('kelas_id', $kelasIds);

            // ✅ HAPUS orWhereNull - guru mapel tidak boleh lihat absensi harian
            $mapelNames = $guru->mapels()->pluck('nama_matapelajaran');
            $query->whereIn('mata_pelajaran', $mapelNames);
        }

        // Apply filters
        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        if ($kelas) {
            $query->where('kelas_id', $kelas);
        }

        if ($mapel) {
            $query->where('mata_pelajaran', $mapel);
        }

        $absensis = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get data untuk filter
        $kelasList = $this->getKelasListForGuru($guru);
        $mapelList = $guru->isGuruMapel() ? $guru->mapels : collect();

        return view('guru.absensi.index', compact(
            'absensis',
            'guru',
            'title',
            'kelasList',
            'mapelList',
            'tanggal',
            'kelas',
            'mapel'
        ));
    }

    /**
     * Show form untuk create absensi (Wali Kelas)
     */
    public function createWaliKelas()
    {
        $guru = Auth::user()->guru;
        $kelas = $guru->kelasWali()->with('waliKelas')->first();
        $siswas = $kelas->siswas()->orderBy('nama')->get();
        $title = 'Input Absensi Kelas ' . $kelas->nama;

        // Check apakah sudah ada absensi hari ini
        $today = now()->format('Y-m-d');
        $absenHariIni = Absen::where('kelas_id', $kelas->id)
            ->where('tanggal', $today)
            ->whereNull('mata_pelajaran')
            ->first();

        return view('guru.absensi.create-wali-kelas', compact(
            'guru',
            'kelas',
            'siswas',
            'title',
            'absenHariIni'
        ));
    }

    /**
     * Store absensi (Wali Kelas)
     */
    public function storeWaliKelas(Request $request)
    {
        $guru = Auth::user()->guru;

        $request->validate([
            'tanggal' => 'required|date',
            'status' => 'required|array',
            'status.*' => 'required|in:Hadir,Sakit,Izin,Alpa',
            'keterangan' => 'nullable|array',
            'keterangan.*' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $kelas = $guru->kelasWali;

            // Cek duplikasi
            $existing = Absen::where('kelas_id', $kelas->id)
                ->where('tanggal', $request->tanggal)
                ->whereNull('mata_pelajaran')
                ->first();

            if ($existing) {
                return back()->with('error', 'Absensi untuk tanggal ini sudah ada!');
            }

            // Create absen tanpa mata pelajaran
            $absen = Absen::create([
                'tanggal' => $request->tanggal,
                'kelas_id' => $kelas->id,
                'mata_pelajaran' => null,
                'presentase_kehadiran' => 0,
            ]);

            // Create detail absen untuk setiap siswa
            foreach ($request->status as $siswaId => $status) {
                DetailAbsen::create([
                    'absen_id' => $absen->id,
                    'siswa_id' => $siswaId,
                    'tanggal' => $request->tanggal,
                    'status' => $status,
                    'keterangan' => $request->keterangan[$siswaId] ?? null,
                ]);
            }

            // Update presentase kehadiran
            $absen->updatePresentaseKehadiran();

            DB::commit();

            return redirect()->route('guru.absensi.index')
                ->with('success', 'Absensi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    /**
     * Pilih kelas dan mapel untuk absensi (Guru Mapel)
     */
    public function selectKelasMapel()
    {
        $guru = Auth::user()->guru;

        $jadwals = $guru->jadwals()
            ->with(['kelas', 'mapel'])
            ->get()
            ->groupBy('kelas_id');

        $title = 'Pilih Kelas & Mata Pelajaran';

        return view('guru.absensi.select-kelas-mapel', compact('guru', 'jadwals', 'title'));
    }

    /**
     * Show form untuk create absensi (Guru Mapel)
     */
    public function createGuruMapel(Request $request)
    {
        $guru = Auth::user()->guru;

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
        ]);

        $kelasId = $request->kelas_id;
        $mapelId = $request->mapel_id;

        // Validasi: Pastikan guru mengajar di kelas dan mapel ini
        $jadwal = Jadwal::where('guru_id', $guru->id)
            ->where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->first();

        if (!$jadwal) {
            return redirect()->route('guru.absensi.select-kelas-mapel')
                ->with('error', 'Anda tidak mengajar di kelas dan mata pelajaran ini');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $mapel = $guru->mapels()->findOrFail($mapelId);
        $siswas = $kelas->siswas()->orderBy('nama')->get();
        $title = 'Input Absensi ' . $mapel->nama_matapelajaran . ' - Kelas ' . $kelas->nama;

        // Check apakah sudah ada absensi hari ini untuk mapel ini
        $today = now()->format('Y-m-d');
        $absenHariIni = Absen::where('kelas_id', $kelas->id)
            ->where('tanggal', $today)
            ->where('mata_pelajaran', $mapel->nama_matapelajaran)
            ->first();

        return view('guru.absensi.create-guru-mapel', compact(
            'guru',
            'kelas',
            'mapel',
            'siswas',
            'title',
            'absenHariIni'
        ));
    }

    /**
     * Store absensi (Guru Mapel)
     */
    public function storeGuruMapel(Request $request)
    {
        $guru = Auth::user()->guru;

        $request->validate([
            'tanggal' => 'required|date',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'status' => 'required|array',
            'status.*' => 'required|in:Hadir,Sakit,Izin,Alpa',
            'keterangan' => 'nullable|array',
            'keterangan.*' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Validasi jadwal
            $jadwal = Jadwal::where('guru_id', $guru->id)
                ->where('kelas_id', $request->kelas_id)
                ->where('mapel_id', $request->mapel_id)
                ->first();

            if (!$jadwal) {
                return back()->with('error', 'Anda tidak mengajar di kelas dan mata pelajaran ini');
            }

            $mapel = $guru->mapels()->findOrFail($request->mapel_id);

            // Cek duplikasi
            $existing = Absen::where('kelas_id', $request->kelas_id)
                ->where('tanggal', $request->tanggal)
                ->where('mata_pelajaran', $mapel->nama_matapelajaran)
                ->first();

            if ($existing) {
                return back()->with('error', 'Absensi untuk tanggal dan mata pelajaran ini sudah ada!');
            }

            // Create absen
            $absen = Absen::create([
                'tanggal' => $request->tanggal,
                'kelas_id' => $request->kelas_id,
                'mata_pelajaran' => $mapel->nama_matapelajaran,
                'presentase_kehadiran' => 0,
            ]);

            // Create detail absen untuk setiap siswa
            foreach ($request->status as $siswaId => $status) {
                DetailAbsen::create([
                    'absen_id' => $absen->id,
                    'siswa_id' => $siswaId,
                    'tanggal' => $request->tanggal,
                    'status' => $status,
                    'keterangan' => $request->keterangan[$siswaId] ?? null,
                ]);
            }

            // Update presentase kehadiran
            $absen->updatePresentaseKehadiran();

            DB::commit();

            return redirect()->route('guru.absensi.index')
                ->with('success', 'Absensi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    /**
     * Show detail absensi
     */
    public function show($id)
    {
        $guru = Auth::user()->guru;
        $absen = Absen::with(['kelas', 'detailAbsens.siswa'])->findOrFail($id);

        // Validasi akses
        if (!$this->canAccessAbsen($guru, $absen)) {
            abort(403, 'Anda tidak memiliki akses ke absensi ini');
        }

        $title = 'Detail Absensi';

        return view('guru.absensi.show', compact('absen', 'guru', 'title'));
    }

    /**
     * Show form edit absensi
     */
    public function edit($id)
    {
        $guru = Auth::user()->guru;
        $absen = Absen::with(['kelas', 'detailAbsens.siswa'])->findOrFail($id);

        // Validasi akses
        if (!$this->canAccessAbsen($guru, $absen)) {
            abort(403, 'Anda tidak memiliki akses untuk edit absensi ini');
        }

        $title = 'Edit Absensi';
        $siswas = $absen->kelas->siswas()->orderBy('nama')->get();

        return view('guru.absensi.edit', compact('absen', 'guru', 'title', 'siswas'));
    }

    /**
     * Update absensi
     */
    public function update(Request $request, $id)
    {
        $guru = Auth::user()->guru;
        $absen = Absen::findOrFail($id);

        // Validasi akses
        if (!$this->canAccessAbsen($guru, $absen)) {
            abort(403, 'Anda tidak memiliki akses untuk update absensi ini');
        }

        $request->validate([
            'status' => 'required|array',
            'status.*' => 'required|in:Hadir,Sakit,Izin,Alpa',
            'keterangan' => 'nullable|array',
            'keterangan.*' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Update detail absen
            foreach ($request->status as $detailId => $status) {
                $detail = DetailAbsen::findOrFail($detailId);
                $detail->update([
                    'status' => $status,
                    'keterangan' => $request->keterangan[$detailId] ?? null,
                ]);
            }

            // Update presentase kehadiran
            $absen->updatePresentaseKehadiran();

            DB::commit();

            return redirect()->route('guru.absensi.show', $absen->id)
                ->with('success', 'Absensi berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update absensi: ' . $e->getMessage());
        }
    }

    /**
     * Delete absensi
     */
    public function destroy($id)
    {
        $guru = Auth::user()->guru;
        $absen = Absen::findOrFail($id);

        // Validasi akses
        if (!$this->canAccessAbsen($guru, $absen)) {
            abort(403, 'Anda tidak memiliki akses untuk hapus absensi ini');
        }

        try {
            $absen->delete();
            return redirect()->route('guru.absensi.index')
                ->with('success', 'Absensi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus absensi: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Check if guru can access absen
     * ✅ PERBAIKAN: Tambah validasi mata_pelajaran untuk guru mapel
     */
    private function canAccessAbsen($guru, $absen)
    {
        // Wali kelas bisa akses semua absensi kelasnya
        if ($guru->isWaliKelas() && $absen->kelas_id == $guru->kelas_wali_id) {
            return true;
        }

        // ✅ Guru mapel hanya bisa akses absensi mapel yang dia ajar
        if ($guru->isGuruMapel()) {
            $kelasIds = $guru->jadwals()->pluck('kelas_id')->unique();
            $isKelasValid = $kelasIds->contains($absen->kelas_id);

            // Cek apakah mata pelajaran sesuai
            $mapelNames = $guru->mapels()->pluck('nama_matapelajaran');
            $isMapelValid = $mapelNames->contains($absen->mata_pelajaran);

            return $isKelasValid && $isMapelValid;
        }

        return false;
    }

    /**
     * Helper: Get kelas list for guru
     */
    private function getKelasListForGuru($guru)
    {
        if ($guru->isWaliKelas()) {
            return Kelas::where('id', $guru->kelas_wali_id)->get();
        } elseif ($guru->isGuruMapel()) {
            $kelasIds = $guru->jadwals()->pluck('kelas_id')->unique();
            return Kelas::whereIn('id', $kelasIds)->orderBy('nama')->get();
        }

        return collect();
    }
}
