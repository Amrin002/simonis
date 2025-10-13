<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PelanggaranController extends Controller
{
    /**
     * Display daftar pelanggaran
     */
    public function index(Request $request)
    {
        $guru = Auth::user()->guru;

        // Validasi: Hanya wali kelas
        if (!$guru->isWaliKelas()) {
            abort(403, 'Hanya Wali Kelas yang dapat mengakses halaman pelanggaran');
        }

        $kelas = $guru->kelasWali;
        $title = 'Daftar Pelanggaran Kelas ' . $kelas->nama;

        // Filter parameters
        $search = $request->get('search');
        $kategori = $request->get('kategori');
        $tanggal = $request->get('tanggal');
        $bulan = $request->get('bulan');

        // Query pelanggaran
        $query = Pelanggaran::with(['siswa', 'waliKelas'])
            ->where('kelas_id', $kelas->id);

        // Apply filters
        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        if ($bulan) {
            $query->whereYear('tanggal', date('Y', strtotime($bulan)))
                ->whereMonth('tanggal', date('m', strtotime($bulan)));
        }

        $pelanggarans = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Statistik
        $stats = [
            'total' => $kelas->jumlah_pelanggaran,
            'ringan' => $kelas->pelanggaran_ringan,
            'sedang' => $kelas->pelanggaran_sedang,
            'berat' => $kelas->pelanggaran_berat,
            'bulan_ini' => $kelas->getPelanggaranBulanIni()->count(),
        ];

        return view('guru.pelanggaran.index', compact(
            'pelanggarans',
            'kelas',
            'guru',
            'title',
            'stats',
            'search',
            'kategori',
            'tanggal',
            'bulan'
        ));
    }

    /**
     * Show form create pelanggaran
     */
    public function create(Request $request)
    {
        $guru = Auth::user()->guru;

        // Validasi: Hanya wali kelas
        if (!$guru->isWaliKelas()) {
            abort(403, 'Hanya Wali Kelas yang dapat menambah pelanggaran');
        }

        $kelas = $guru->kelasWali;
        $title = 'Tambah Pelanggaran';

        // Get siswa yang dipilih (jika ada)
        $selectedSiswaId = $request->get('siswa_id');
        $selectedSiswa = null;

        if ($selectedSiswaId) {
            $selectedSiswa = Siswa::where('kelas_id', $kelas->id)
                ->findOrFail($selectedSiswaId);
        }

        // Get semua siswa di kelas
        $siswas = $kelas->siswas()->orderBy('nama')->get();

        // Daftar jenis pelanggaran umum (bisa dikustomisasi)
        $jenisPelanggaranOptions = [
            'Terlambat',
            'Tidak Mengerjakan PR',
            'Tidak Masuk Tanpa Keterangan',
            'Tidak Memakai Seragam',
            'Berkelahi',
            'Merokok',
            'Membawa HP',
            'Tidur di Kelas',
            'Mencontek',
            'Tidak Sopan',
            'Merusak Fasilitas',
            'Lainnya',
        ];

        return view('guru.pelanggaran.create', compact(
            'kelas',
            'guru',
            'title',
            'siswas',
            'selectedSiswa',
            'jenisPelanggaranOptions'
        ));
    }

    /**
     * Store pelanggaran
     */
    public function store(Request $request)
    {
        $guru = Auth::user()->guru;

        // Validasi: Hanya wali kelas
        if (!$guru->isWaliKelas()) {
            abort(403, 'Hanya Wali Kelas yang dapat menambah pelanggaran');
        }

        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'jenis_pelanggaran' => 'required|string|max:255',
            'kategori' => 'required|in:Ringan,Sedang,Berat',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $kelas = $guru->kelasWali;

        // Validasi: Siswa harus dari kelas yang diwali
        $siswa = Siswa::findOrFail($request->siswa_id);
        if ($siswa->kelas_id !== $kelas->id) {
            return back()->with('error', 'Siswa tidak terdaftar di kelas Anda!');
        }

        DB::beginTransaction();
        try {
            Pelanggaran::create([
                'siswa_id' => $request->siswa_id,
                'jenis_pelanggaran' => $request->jenis_pelanggaran,
                'kategori' => $request->kategori,
                'tanggal' => $request->tanggal,
                'wali_kelas_id' => $guru->id,
                'kelas_id' => $kelas->id,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            return redirect()->route('guru.pelanggaran.index')
                ->with('success', 'Pelanggaran berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menambahkan pelanggaran: ' . $e->getMessage());
        }
    }

    /**
     * Show detail pelanggaran
     */
    public function show($id)
    {
        $guru = Auth::user()->guru;

        // Validasi: Hanya wali kelas
        if (!$guru->isWaliKelas()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        $pelanggaran = Pelanggaran::with(['siswa', 'waliKelas', 'kelas'])
            ->findOrFail($id);

        // Validasi: Pelanggaran harus dari kelas yang diwali
        if ($pelanggaran->kelas_id !== $guru->kelasWali->id) {
            abort(403, 'Anda tidak memiliki akses ke pelanggaran ini');
        }

        $title = 'Detail Pelanggaran';

        return view('guru.pelanggaran.show', compact(
            'pelanggaran',
            'guru',
            'title'
        ));
    }

    /**
     * Show form edit pelanggaran
     */
    public function edit($id)
    {
        $guru = Auth::user()->guru;

        // Validasi: Hanya wali kelas
        if (!$guru->isWaliKelas()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        $pelanggaran = Pelanggaran::with(['siswa', 'kelas'])
            ->findOrFail($id);

        // Validasi: Pelanggaran harus dari kelas yang diwali
        if ($pelanggaran->kelas_id !== $guru->kelasWali->id) {
            abort(403, 'Anda tidak memiliki akses untuk edit pelanggaran ini');
        }

        $kelas = $guru->kelasWali;
        $title = 'Edit Pelanggaran';

        // Get semua siswa di kelas
        $siswas = $kelas->siswas()->orderBy('nama')->get();

        // Daftar jenis pelanggaran umum
        $jenisPelanggaranOptions = [
            'Terlambat',
            'Tidak Mengerjakan PR',
            'Tidak Masuk Tanpa Keterangan',
            'Tidak Memakai Seragam',
            'Berkelahi',
            'Merokok',
            'Membawa HP',
            'Tidur di Kelas',
            'Mencontek',
            'Tidak Sopan',
            'Merusak Fasilitas',
            'Lainnya',
        ];

        return view('guru.pelanggaran.edit', compact(
            'pelanggaran',
            'kelas',
            'guru',
            'title',
            'siswas',
            'jenisPelanggaranOptions'
        ));
    }

    /**
     * Update pelanggaran
     */
    public function update(Request $request, $id)
    {
        $guru = Auth::user()->guru;

        // Validasi: Hanya wali kelas
        if (!$guru->isWaliKelas()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        $pelanggaran = Pelanggaran::findOrFail($id);

        // Validasi: Pelanggaran harus dari kelas yang diwali
        if ($pelanggaran->kelas_id !== $guru->kelasWali->id) {
            abort(403, 'Anda tidak memiliki akses untuk update pelanggaran ini');
        }

        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'jenis_pelanggaran' => 'required|string|max:255',
            'kategori' => 'required|in:Ringan,Sedang,Berat',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $kelas = $guru->kelasWali;

        // Validasi: Siswa harus dari kelas yang diwali
        $siswa = Siswa::findOrFail($request->siswa_id);
        if ($siswa->kelas_id !== $kelas->id) {
            return back()->with('error', 'Siswa tidak terdaftar di kelas Anda!');
        }

        DB::beginTransaction();
        try {
            $pelanggaran->update([
                'siswa_id' => $request->siswa_id,
                'jenis_pelanggaran' => $request->jenis_pelanggaran,
                'kategori' => $request->kategori,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            return redirect()->route('guru.pelanggaran.show', $pelanggaran->id)
                ->with('success', 'Pelanggaran berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update pelanggaran: ' . $e->getMessage());
        }
    }

    /**
     * Delete pelanggaran
     */
    public function destroy($id)
    {
        $guru = Auth::user()->guru;

        // Validasi: Hanya wali kelas
        if (!$guru->isWaliKelas()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        $pelanggaran = Pelanggaran::findOrFail($id);

        // Validasi: Pelanggaran harus dari kelas yang diwali
        if ($pelanggaran->kelas_id !== $guru->kelasWali->id) {
            abort(403, 'Anda tidak memiliki akses untuk hapus pelanggaran ini');
        }

        try {
            $pelanggaran->delete();

            return redirect()->route('guru.pelanggaran.index')
                ->with('success', 'Pelanggaran berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus pelanggaran: ' . $e->getMessage());
        }
    }
}
