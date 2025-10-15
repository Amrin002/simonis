<?php

namespace App\Http\Controllers;

use App\Models\NilaiTugas;
use App\Models\NilaiUTS;
use App\Models\NilaiUAS;
use App\Models\NilaiAkhir;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
{
    /**
     * Display daftar nilai
     */
    public function index(Request $request)
    {
        $guru = Auth::user()->guru;
        $title = 'Daftar Nilai';

        // Filter parameters
        $kelasId = $request->get('kelas_id');
        $mapelId = $request->get('mapel_id');
        $search = $request->get('search');

        // Get kelas dan mapel list untuk filter
        $kelasList = $this->getKelasListForGuru($guru);
        $mapelList = $guru->isGuruMapel() ? $guru->mapels : collect();

        // Query nilai akhir dengan filter
        $query = NilaiAkhir::with(['siswa', 'kelas', 'mapel']);

        // Filter berdasarkan role
        if ($guru->isWaliKelas()) {
            $query->where('kelas_id', $guru->kelasWali->id);
        } elseif ($guru->isGuruMapel()) {
            $mapelIds = $guru->mapels->pluck('id');
            $query->whereIn('mapel_id', $mapelIds);
        }

        // Apply filters
        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        if ($mapelId) {
            $query->where('mapel_id', $mapelId);
        }

        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $nilaiList = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('guru.nilai.index', compact(
            'nilaiList',
            'guru',
            'title',
            'kelasList',
            'mapelList',
            'kelasId',
            'mapelId',
            'search'
        ));
    }

    /**
     * Pilih kelas dan mapel (untuk guru mapel)
     */
    public function selectKelasMapel()
    {
        $guru = Auth::user()->guru;

        if (!$guru->isGuruMapel()) {
            return redirect()->route('guru.nilai.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        $jadwals = $guru->jadwals()
            ->with(['kelas', 'mapel'])
            ->get()
            ->groupBy('kelas_id');

        $title = 'Pilih Kelas & Mata Pelajaran';

        return view('guru.nilai.select-kelas-mapel', compact('guru', 'jadwals', 'title'));
    }

    /**
     * Show form create nilai tugas
     */
    public function createTugas(Request $request)
    {
        $guru = Auth::user()->guru;

        if (!$guru->isGuruMapel()) {
            abort(403, 'Hanya Guru Mata Pelajaran yang dapat menginput nilai');
        }

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
            return redirect()->route('guru.nilai.select-kelas-mapel')
                ->with('error', 'Anda tidak mengajar di kelas dan mata pelajaran ini');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $mapel = Mapel::findOrFail($mapelId);
        $siswas = $kelas->siswas()->orderBy('nama')->get();

        // Get nilai tugas yang sudah ada untuk kelas dan mapel ini
        $existingNilai = NilaiTugas::where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->get()
            ->keyBy('siswa_id');

        $title = 'Input Nilai Tugas - ' . $mapel->nama_matapelajaran . ' (' . $kelas->nama . ')';

        return view('guru.nilai.create-tugas', compact(
            'guru',
            'kelas',
            'mapel',
            'siswas',
            'existingNilai',
            'title'
        ));
    }

    /**
     * Store nilai tugas
     */
    public function storeTugas(Request $request)
    {
        $guru = Auth::user()->guru;

        if (!$guru->isGuruMapel()) {
            abort(403, 'Hanya Guru Mata Pelajaran yang dapat menginput nilai');
        }

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'nilai_tugas' => 'required|array',
            'nilai_tugas.*' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|array',
            'keterangan.*' => 'nullable|string|max:500',
        ]);

        // Validasi jadwal
        $jadwal = Jadwal::where('guru_id', $guru->id)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->first();

        if (!$jadwal) {
            return back()->with('error', 'Anda tidak mengajar di kelas dan mata pelajaran ini');
        }

        DB::beginTransaction();
        try {
            foreach ($request->nilai_tugas as $siswaId => $nilai) {
                // Update or create nilai tugas
                NilaiTugas::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'kelas_id' => $request->kelas_id,
                        'mapel_id' => $request->mapel_id,
                    ],
                    [
                        'nilai_tugas' => $nilai,
                        'keterangan' => $request->keterangan[$siswaId] ?? null,
                    ]
                );

                // Auto update nilai akhir jika semua nilai sudah ada
                $this->autoUpdateNilaiAkhir($siswaId, $request->kelas_id, $request->mapel_id);
            }

            DB::commit();

            return redirect()->route('guru.nilai.index')
                ->with('success', 'Nilai Tugas berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    /**
     * Show form create nilai UTS
     */
    public function createUTS(Request $request)
    {
        $guru = Auth::user()->guru;

        if (!$guru->isGuruMapel()) {
            abort(403, 'Hanya Guru Mata Pelajaran yang dapat menginput nilai');
        }

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
        ]);

        $kelasId = $request->kelas_id;
        $mapelId = $request->mapel_id;

        // Validasi jadwal
        $jadwal = Jadwal::where('guru_id', $guru->id)
            ->where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->first();

        if (!$jadwal) {
            return redirect()->route('guru.nilai.select-kelas-mapel')
                ->with('error', 'Anda tidak mengajar di kelas dan mata pelajaran ini');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $mapel = Mapel::findOrFail($mapelId);
        $siswas = $kelas->siswas()->orderBy('nama')->get();

        // Get nilai UTS yang sudah ada
        $existingNilai = NilaiUTS::where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->get()
            ->keyBy('siswa_id');

        $title = 'Input Nilai UTS - ' . $mapel->nama_matapelajaran . ' (' . $kelas->nama . ')';

        return view('guru.nilai.create-uts', compact(
            'guru',
            'kelas',
            'mapel',
            'siswas',
            'existingNilai',
            'title'
        ));
    }

    /**
     * Store nilai UTS
     */
    public function storeUTS(Request $request)
    {
        $guru = Auth::user()->guru;

        if (!$guru->isGuruMapel()) {
            abort(403, 'Hanya Guru Mata Pelajaran yang dapat menginput nilai');
        }

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'nilai_uts' => 'required|array',
            'nilai_uts.*' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|array',
            'keterangan.*' => 'nullable|string|max:500',
        ]);

        // Validasi jadwal
        $jadwal = Jadwal::where('guru_id', $guru->id)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->first();

        if (!$jadwal) {
            return back()->with('error', 'Anda tidak mengajar di kelas dan mata pelajaran ini');
        }

        DB::beginTransaction();
        try {
            foreach ($request->nilai_uts as $siswaId => $nilai) {
                NilaiUTS::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'kelas_id' => $request->kelas_id,
                        'mapel_id' => $request->mapel_id,
                    ],
                    [
                        'nilai_uts' => $nilai,
                        'keterangan' => $request->keterangan[$siswaId] ?? null,
                    ]
                );

                // Auto update nilai akhir
                $this->autoUpdateNilaiAkhir($siswaId, $request->kelas_id, $request->mapel_id);
            }

            DB::commit();

            return redirect()->route('guru.nilai.index')
                ->with('success', 'Nilai UTS berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    /**
     * Show form create nilai UAS
     */
    public function createUAS(Request $request)
    {
        $guru = Auth::user()->guru;

        if (!$guru->isGuruMapel()) {
            abort(403, 'Hanya Guru Mata Pelajaran yang dapat menginput nilai');
        }

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
        ]);

        $kelasId = $request->kelas_id;
        $mapelId = $request->mapel_id;

        // Validasi jadwal
        $jadwal = Jadwal::where('guru_id', $guru->id)
            ->where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->first();

        if (!$jadwal) {
            return redirect()->route('guru.nilai.select-kelas-mapel')
                ->with('error', 'Anda tidak mengajar di kelas dan mata pelajaran ini');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $mapel = Mapel::findOrFail($mapelId);
        $siswas = $kelas->siswas()->orderBy('nama')->get();

        // Get nilai UAS yang sudah ada
        $existingNilai = NilaiUAS::where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->get()
            ->keyBy('siswa_id');

        $title = 'Input Nilai UAS - ' . $mapel->nama_matapelajaran . ' (' . $kelas->nama . ')';

        return view('guru.nilai.create-uas', compact(
            'guru',
            'kelas',
            'mapel',
            'siswas',
            'existingNilai',
            'title'
        ));
    }

    /**
     * Store nilai UAS
     */
    public function storeUAS(Request $request)
    {
        $guru = Auth::user()->guru;

        if (!$guru->isGuruMapel()) {
            abort(403, 'Hanya Guru Mata Pelajaran yang dapat menginput nilai');
        }

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'nilai_uas' => 'required|array',
            'nilai_uas.*' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|array',
            'keterangan.*' => 'nullable|string|max:500',
        ]);

        // Validasi jadwal
        $jadwal = Jadwal::where('guru_id', $guru->id)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->first();

        if (!$jadwal) {
            return back()->with('error', 'Anda tidak mengajar di kelas dan mata pelajaran ini');
        }

        DB::beginTransaction();
        try {
            foreach ($request->nilai_uas as $siswaId => $nilai) {
                NilaiUAS::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'kelas_id' => $request->kelas_id,
                        'mapel_id' => $request->mapel_id,
                    ],
                    [
                        'nilai_uas' => $nilai,
                        'keterangan' => $request->keterangan[$siswaId] ?? null,
                    ]
                );

                // Auto update nilai akhir
                $this->autoUpdateNilaiAkhir($siswaId, $request->kelas_id, $request->mapel_id);
            }

            DB::commit();

            return redirect()->route('guru.nilai.index')
                ->with('success', 'Nilai UAS berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    /**
     * Show detail nilai siswa untuk 1 mapel
     */
    public function show($siswaId, $mapelId)
    {
        $guru = Auth::user()->guru;
        $siswa = Siswa::findOrFail($siswaId);
        $mapel = Mapel::findOrFail($mapelId);

        // Validasi akses
        if (!$this->canAccessNilai($guru, $siswa->kelas_id, $mapelId)) {
            abort(403, 'Anda tidak memiliki akses ke nilai ini');
        }

        $nilaiTugas = NilaiTugas::where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->first();

        $nilaiUts = NilaiUTS::where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->first();

        $nilaiUas = NilaiUAS::where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->first();

        $nilaiAkhir = NilaiAkhir::where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->first();

        $title = 'Detail Nilai - ' . $siswa->nama;

        return view('guru.nilai.show', compact(
            'siswa',
            'mapel',
            'nilaiTugas',
            'nilaiUts',
            'nilaiUas',
            'nilaiAkhir',
            'guru',
            'title'
        ));
    }

    /**
     * Show nilai akhir semua siswa
     */
    public function showNilaiAkhir(Request $request)
    {
        $guru = Auth::user()->guru;
        $title = 'Nilai Akhir Siswa';

        $kelasId = $request->get('kelas_id');
        $mapelId = $request->get('mapel_id');

        $kelasList = $this->getKelasListForGuru($guru);
        $mapelList = $guru->isGuruMapel() ? $guru->mapels : collect();

        $query = NilaiAkhir::with(['siswa', 'kelas', 'mapel']);

        // Filter berdasarkan role
        if ($guru->isWaliKelas()) {
            $query->where('kelas_id', $guru->kelasWali->id);
        } elseif ($guru->isGuruMapel()) {
            $mapelIds = $guru->mapels->pluck('id');
            $query->whereIn('mapel_id', $mapelIds);
        }

        // Apply filters
        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        if ($mapelId) {
            $query->where('mapel_id', $mapelId);
        }

        $nilaiAkhirList = $query->orderBy('nilai_akhir', 'desc')->paginate(20);

        // Statistik
        $stats = [
            'total' => $query->count(),
            'tuntas' => $query->where('nilai_akhir', '>=', 75)->count(),
            'tidak_tuntas' => $query->where('nilai_akhir', '<', 75)->count(),
            'rata_rata' => round($query->avg('nilai_akhir'), 2),
        ];

        return view('guru.nilai.nilai-akhir', compact(
            'nilaiAkhirList',
            'guru',
            'title',
            'kelasList',
            'mapelList',
            'kelasId',
            'mapelId',
            'stats'
        ));
    }

    /**
     * Edit nilai (tugas/uts/uas)
     */
    public function edit($id, $jenis)
    {
        $guru = Auth::user()->guru;

        if (!$guru->isGuruMapel()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        $nilai = $this->getNilaiByJenis($jenis, $id);

        if (!$nilai) {
            abort(404, 'Nilai tidak ditemukan');
        }

        // Validasi akses
        if (!$this->canAccessNilai($guru, $nilai->kelas_id, $nilai->mapel_id)) {
            abort(403, 'Anda tidak memiliki akses untuk edit nilai ini');
        }

        $title = 'Edit Nilai ' . strtoupper($jenis);

        return view('guru.nilai.edit', compact('nilai', 'jenis', 'guru', 'title'));
    }
    /**
     * Print nilai akhir
     */
    public function print(Request $request)
    {
        $guru = Auth::user()->guru;
        $title = 'Print Nilai Akhir Siswa';

        $kelasId = $request->get('kelas_id');
        $mapelId = $request->get('mapel_id');

        $kelasList = $this->getKelasListForGuru($guru);
        $mapelList = $guru->isGuruMapel() ? $guru->mapels : collect();

        // Query nilai akhir
        $query = NilaiAkhir::with(['siswa', 'kelas', 'mapel']);

        // Filter berdasarkan role
        if ($guru->isWaliKelas()) {
            $query->where('kelas_id', $guru->kelasWali->id);
        } elseif ($guru->isGuruMapel()) {
            $mapelIds = $guru->mapels->pluck('id');
            $query->whereIn('mapel_id', $mapelIds);
        }

        // Apply filters
        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        if ($mapelId) {
            $query->where('mapel_id', $mapelId);
        }

        $nilaiList = $query->orderBy('nilai_akhir', 'desc')->get();

        // Statistik
        $stats = [
            'total' => $nilaiList->count(),
            'tuntas' => $nilaiList->where('nilai_akhir', '>=', 75)->count(),
            'tidak_tuntas' => $nilaiList->where('nilai_akhir', '<', 75)->count(),
            'rata_rata' => $nilaiList->count() > 0 ? round($nilaiList->avg('nilai_akhir'), 2) : 0,
            'tertinggi' => $nilaiList->count() > 0 ? $nilaiList->max('nilai_akhir') : 0,
            'terendah' => $nilaiList->count() > 0 ? $nilaiList->min('nilai_akhir') : 0,
        ];

        // Info filter
        $filterInfo = [
            'kelas' => $kelasId ? Kelas::find($kelasId)->nama : 'Semua Kelas',
            'mapel' => $mapelId ? Mapel::find($mapelId)->nama_matapelajaran : 'Semua Mata Pelajaran',
        ];

        return view('guru.nilai.print', compact(
            'nilaiList',
            'guru',
            'title',
            'stats',
            'filterInfo'
        ));
    }
    /**
     * Update nilai (tugas/uts/uas)
     */
    public function update(Request $request, $id, $jenis)
    {
        $guru = Auth::user()->guru;

        if (!$guru->isGuruMapel()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        $nilai = $this->getNilaiByJenis($jenis, $id);

        if (!$nilai) {
            abort(404, 'Nilai tidak ditemukan');
        }

        // Validasi akses
        if (!$this->canAccessNilai($guru, $nilai->kelas_id, $nilai->mapel_id)) {
            abort(403, 'Anda tidak memiliki akses untuk update nilai ini');
        }

        $fieldName = 'nilai_' . $jenis;

        $request->validate([
            $fieldName => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $nilai->update([
                $fieldName => $request->$fieldName,
                'keterangan' => $request->keterangan,
            ]);

            // Auto update nilai akhir
            $this->autoUpdateNilaiAkhir($nilai->siswa_id, $nilai->kelas_id, $nilai->mapel_id);

            DB::commit();

            return redirect()->route('guru.nilai.show', [$nilai->siswa_id, $nilai->mapel_id])
                ->with('success', 'Nilai berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update nilai: ' . $e->getMessage());
        }
    }

    /**
     * Delete nilai (tugas/uts/uas)
     */
    public function destroy($id, $jenis)
    {
        $guru = Auth::user()->guru;

        if (!$guru->isGuruMapel()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        $nilai = $this->getNilaiByJenis($jenis, $id);

        if (!$nilai) {
            abort(404, 'Nilai tidak ditemukan');
        }

        // Validasi akses
        if (!$this->canAccessNilai($guru, $nilai->kelas_id, $nilai->mapel_id)) {
            abort(403, 'Anda tidak memiliki akses untuk hapus nilai ini');
        }

        try {
            $siswaId = $nilai->siswa_id;
            $kelasId = $nilai->kelas_id;
            $mapelId = $nilai->mapel_id;

            $nilai->delete();

            // Update nilai akhir setelah hapus
            $this->autoUpdateNilaiAkhir($siswaId, $kelasId, $mapelId);

            return redirect()->route('guru.nilai.index')
                ->with('success', 'Nilai berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus nilai: ' . $e->getMessage());
        }
    }

    // ========== HELPER METHODS ==========

    /**
     * Auto update atau create nilai akhir jika semua nilai sudah lengkap
     */
    private function autoUpdateNilaiAkhir($siswaId, $kelasId, $mapelId)
    {
        $nilaiTugas = NilaiTugas::where('siswa_id', $siswaId)
            ->where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->first();

        $nilaiUts = NilaiUTS::where('siswa_id', $siswaId)
            ->where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->first();

        $nilaiUas = NilaiUAS::where('siswa_id', $siswaId)
            ->where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->first();

        // Jika semua nilai sudah ada, update nilai akhir
        if ($nilaiTugas && $nilaiUts && $nilaiUas) {
            $nilaiAkhir = NilaiAkhir::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'kelas_id' => $kelasId,
                    'mapel_id' => $mapelId,
                ],
                [
                    'nilai_tugas' => $nilaiTugas->nilai_tugas,
                    'nilai_uts' => $nilaiUts->nilai_uts,
                    'nilai_uas' => $nilaiUas->nilai_uas,
                ]
            );

            // Method ini akan auto-calculate nilai_akhir dan predikat (ada di model boot)
            $nilaiAkhir->save();
        }
    }

    /**
     * Check if guru can access nilai
     */
    private function canAccessNilai($guru, $kelasId, $mapelId)
    {
        // Wali kelas bisa akses semua nilai di kelasnya
        if ($guru->isWaliKelas() && $kelasId == $guru->kelasWali->id) {
            return true;
        }

        // Guru mapel hanya bisa akses nilai mapel yang dia ajar
        if ($guru->isGuruMapel()) {
            $jadwal = Jadwal::where('guru_id', $guru->id)
                ->where('kelas_id', $kelasId)
                ->where('mapel_id', $mapelId)
                ->exists();

            return $jadwal;
        }

        return false;
    }

    /**
     * Get kelas list for guru
     */
    private function getKelasListForGuru($guru)
    {
        if ($guru->isWaliKelas()) {
            return Kelas::where('id', $guru->kelasWali->id)->get();
        } elseif ($guru->isGuruMapel()) {
            $kelasIds = $guru->jadwals()->pluck('kelas_id')->unique();
            return Kelas::whereIn('id', $kelasIds)->orderBy('nama')->get();
        }

        return collect();
    }

    /**
     * Get nilai by jenis (tugas/uts/uas)
     */
    private function getNilaiByJenis($jenis, $id)
    {
        switch ($jenis) {
            case 'tugas':
                return NilaiTugas::find($id);
            case 'uts':
                return NilaiUTS::find($id);
            case 'uas':
                return NilaiUAS::find($id);
            default:
                return null;
        }
    }
}
