<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\OrangTua;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        $data = [
            'totalGuru' => Guru::count(),
            'totalWaliKelas' => Guru::where('is_wali_kelas', true)->count(),
            'totalGuruMapel' => Guru::where('is_guru_mapel', true)->count(),
            'totalSiswa' => \App\Models\Siswa::count(),
            'totalKelas' => Kelas::count(),
        ];
        $title = 'Dashboard Admin';

        return view('admin.dashboard', $data, compact('title'));
    }

    // ========== GURU MANAGEMENT ==========

    /**
     * Display listing of guru
     */
    public function manageGuru()
    {
        $gurus = Guru::with(['user', 'kelasWali', 'mapels'])
            ->latest()
            ->paginate(10);
        $title = 'Daftar Guru';

        return view('admin.guru.index', compact('gurus', 'title'));
    }

    /**
     * Show form for creating new guru
     */
    public function createGuru()
    {
        $kelasTersedia = Kelas::whereNull('wali_guru_id')->get();
        $mapels = Mapel::all();
        $title = 'Tambah Guru Baru';

        return view('admin.guru.create', compact('kelasTersedia', 'mapels', 'title'));
    }

    /**
     * Store a newly created guru
     */
    public function storeGuru(Request $request)
    {
        $validated = $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip' => 'required|string|unique:gurus,nip|max:50',
            'is_wali_kelas' => 'boolean',
            'is_guru_mapel' => 'boolean',
            'kelas_id' => 'nullable|exists:kelas,id',
            'mapel_ids' => 'nullable|array',
            'mapel_ids.*' => 'exists:mapels,id',
        ]);

        DB::beginTransaction();
        try {
            // Buat guru tanpa user (user_id nullable)
            $guru = Guru::create([
                'user_id' => null, // Akun bisa dibuat nanti
                'nama_guru' => $validated['nama_guru'],
                'nip' => $validated['nip'],
                'is_wali_kelas' => $request->has('is_wali_kelas'),
                'is_guru_mapel' => $request->has('is_guru_mapel'),
            ]);

            // Assign sebagai wali kelas
            if ($request->has('is_wali_kelas') && $request->kelas_id) {
                Kelas::where('id', $request->kelas_id)->update([
                    'wali_guru_id' => $guru->id
                ]);
            }

            // Assign mata pelajaran
            if ($request->has('is_guru_mapel') && $request->mapel_ids) {
                $guru->mapels()->attach($request->mapel_ids);
            }

            DB::commit();

            return redirect()
                ->route('admin.guru.index')
                ->with('success', 'Data guru berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan guru: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing guru
     */
    public function editGuru($id)
    {
        $guru = Guru::with(['user', 'kelasWali', 'mapels'])->findOrFail($id);
        $kelasTersedia = Kelas::whereNull('wali_guru_id')
            ->orWhere('wali_guru_id', $guru->id)
            ->get();
        $mapels = Mapel::all();
        $title = 'Edit Guru';

        return view('admin.guru.edit', compact('guru', 'kelasTersedia', 'mapels', 'title'));
    }

    /**
     * Update guru
     */
    public function updateGuru(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $validated = $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip' => [
                'required',
                'string',
                'max:50',
                Rule::unique('gurus', 'nip')->ignore($id)
            ],
            'is_wali_kelas' => 'boolean',
            'is_guru_mapel' => 'boolean',
            'kelas_id' => 'nullable|exists:kelas,id',
            'mapel_ids' => 'nullable|array',
            'mapel_ids.*' => 'exists:mapels,id',
        ]);

        DB::beginTransaction();
        try {
            // Update guru (tidak update user)
            $guru->update([
                'nama_guru' => $validated['nama_guru'],
                'nip' => $validated['nip'],
                'is_wali_kelas' => $request->has('is_wali_kelas'),
                'is_guru_mapel' => $request->has('is_guru_mapel'),
            ]);

            // Update wali kelas
            // Hapus assignment lama
            Kelas::where('wali_guru_id', $guru->id)->update(['wali_guru_id' => null]);

            // Assign yang baru
            if ($request->has('is_wali_kelas') && $request->kelas_id) {
                Kelas::where('id', $request->kelas_id)->update([
                    'wali_guru_id' => $guru->id
                ]);
            }

            // Update mata pelajaran
            if ($request->has('is_guru_mapel')) {
                $guru->mapels()->sync($request->mapel_ids ?? []);
            } else {
                $guru->mapels()->detach();
            }

            DB::commit();

            return redirect()
                ->route('admin.guru.index')
                ->with('success', 'Data guru berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate guru: ' . $e->getMessage());
        }
    }

    /**
     * Show detail guru
     */
    public function showGuru($id)
    {
        $guru = Guru::with(['user', 'kelasWali.siswas', 'mapels', 'jadwals.kelas'])
            ->findOrFail($id);
        $title = 'Detail Guru';

        return view('admin.guru.show', compact('guru', 'title'));
    }

    /**
     * Delete guru
     */
    public function destroyGuru($id)
    {
        DB::beginTransaction();
        try {
            $guru = Guru::findOrFail($id);

            // Lepas assignment wali kelas
            if ($guru->kelasWali) {
                $guru->kelasWali->update(['wali_guru_id' => null]);
            }

            // Lepas mata pelajaran
            $guru->mapels()->detach();

            // Hapus guru (user tidak dihapus jika ada)
            $guru->delete();

            DB::commit();

            return redirect()
                ->route('admin.guru.index')
                ->with('success', 'Guru berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus guru: ' . $e->getMessage());
        }
    }

    // ========== ORANG TUA MANAGEMENT ==========
    public function manageOrangtua()
    {
        $orangtuas = OrangTua::with(['user', 'siswas'])
            ->latest()
            ->paginate(10);
        $title = 'Daftar Orang Tua';
        return view('admin.orangtua.index', compact('orangtuas', 'title'));
    }
    /**
     * Show form for creating new orang tua
     */
    public function createOrangtua()
    {
        $title = 'Tambah Orang Tua Baru';

        return view('admin.orangtua.create', compact('title'));
    }

    /**
     * Store a newly created orang tua
     */
    public function storeOrangtua(Request $request)
    {
        $validated = $request->validate([
            'nama_orang_tua' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nomor_tlp' => 'required|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            OrangTua::create([
                'user_id' => null, // Akun bisa dibuat nanti
                'nama_orang_tua' => $validated['nama_orang_tua'],
                'alamat' => $validated['alamat'],
                'nomor_tlp' => $validated['nomor_tlp'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.orangtua.index')
                ->with('success', 'Data orang tua berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan orang tua: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing orang tua
     */
    public function editOrangtua($id)
    {
        $orangtua = OrangTua::with(['user', 'siswas'])->findOrFail($id);
        $title = 'Edit Orang Tua';

        return view('admin.orangtua.edit', compact('orangtua', 'title'));
    }

    /**
     * Update orang tua
     */
    public function updateOrangtua(Request $request, $id)
    {
        $orangtua = OrangTua::findOrFail($id);

        $validated = $request->validate([
            'nama_orang_tua' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nomor_tlp' => 'required|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            $orangtua->update([
                'nama_orang_tua' => $validated['nama_orang_tua'],
                'alamat' => $validated['alamat'],
                'nomor_tlp' => $validated['nomor_tlp'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.orangtua.index')
                ->with('success', 'Data orang tua berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate orang tua: ' . $e->getMessage());
        }
    }

    /**
     * Show detail orang tua
     */
    public function showOrangtua($id)
    {
        $orangtua = OrangTua::with(['user', 'siswas.kelas'])->findOrFail($id);
        $title = 'Detail Orang Tua';

        return view('admin.orangtua.show', compact('orangtua', 'title'));
    }

    /**
     * Delete orang tua
     */
    public function destroyOrangtua($id)
    {
        DB::beginTransaction();
        try {
            $orangtua = OrangTua::findOrFail($id);

            // Cek apakah masih memiliki siswa
            if ($orangtua->siswas()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Tidak dapat menghapus orang tua yang masih memiliki siswa terdaftar');
            }

            $orangtua->delete();

            DB::commit();

            return redirect()
                ->route('admin.orangtua.index')
                ->with('success', 'Orang tua berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus orang tua: ' . $e->getMessage());
        }
    }

    // ========== KELAS MANAGEMENT ==========

    /**
     * Display listing of kelas
     */
    public function manageKelas()
    {
        $kelas = Kelas::with(['waliKelas', 'siswas'])
            ->latest()
            ->paginate(10);
        $title = 'Daftar Kelas';

        return view('admin.kelas.index', compact('kelas', 'title'));
    }

    /**
     * Show form for creating new kelas
     */
    public function createKelas()
    {
        // Ambil guru yang is_wali_kelas = true dan belum menjadi wali kelas
        $guruTersedia = Guru::where('is_wali_kelas', true)
            ->whereDoesntHave('kelasWali')
            ->get();
        $title = 'Tambah Kelas Baru';

        return view('admin.kelas.create', compact('guruTersedia', 'title'));
    }

    /**
     * Store a newly created kelas
     */
    public function storeKelas(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kelas,nama',
            'wali_guru_id' => 'nullable|exists:gurus,id',
        ]);

        DB::beginTransaction();
        try {
            // Validasi jika wali guru dipilih
            if ($request->wali_guru_id) {
                $guru = Guru::find($request->wali_guru_id);
                if (!$guru->is_wali_kelas) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Guru yang dipilih bukan wali kelas');
                }

                if ($guru->kelasWali) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Guru sudah menjadi wali kelas di kelas lain');
                }
            }

            // ✅ FIX: Create kelas
            $kelas = Kelas::create([
                'nama' => $validated['nama'],
                'wali_guru_id' => $request->wali_guru_id,
            ]);

            // ✅ TAMBAHKAN INI: Trigger observer manually jika perlu
            // Atau biarkan observer handle via event
            $kelas->refresh(); // Refresh untuk memastikan relasi ter-load

            DB::commit();

            return redirect()
                ->route('admin.kelas.index')
                ->with('success', 'Data kelas berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kelas: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing kelas
     */
    public function editKelas($id)
    {
        $kelas = Kelas::with(['waliKelas', 'siswas'])->findOrFail($id);

        // Ambil guru yang is_wali_kelas = true dan belum menjadi wali kelas
        // atau guru yang saat ini menjadi wali kelas di kelas ini
        $guruTersedia = Guru::where('is_wali_kelas', true)
            ->where(function ($query) use ($kelas) {
                $query->whereDoesntHave('kelasWali')
                    ->orWhere('id', $kelas->wali_guru_id);
            })
            ->get();

        $title = 'Edit Kelas';

        return view('admin.kelas.edit', compact('kelas', 'guruTersedia', 'title'));
    }

    /**
     * Update kelas
     */
    public function updateKelas(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kelas', 'nama')->ignore($id)
            ],
            'wali_guru_id' => 'nullable|exists:gurus,id',
        ]);

        DB::beginTransaction();
        try {
            // Validasi jika wali guru dipilih
            if ($request->wali_guru_id) {
                $guru = Guru::find($request->wali_guru_id);
                if (!$guru->is_wali_kelas) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Guru yang dipilih bukan wali kelas');
                }

                if ($guru->kelasWali && $guru->kelasWali->id != $kelas->id) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Guru sudah menjadi wali kelas di kelas lain');
                }
            }

            // ✅ FIX: Update pakai eloquent (biar observer jalan)
            $kelas->update([
                'nama' => $validated['nama'],
                'wali_guru_id' => $request->wali_guru_id,
            ]);

            // Observer akan otomatis handle sync kelas_wali_id

            DB::commit();

            return redirect()
                ->route('admin.kelas.index')
                ->with('success', 'Data kelas berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate kelas: ' . $e->getMessage());
        }
    }

    /**
     * Show detail kelas
     */
    public function showKelas($id)
    {
        $kelas = Kelas::with(['waliKelas', 'siswas.orangTua', 'jadwals.mapel.guruMapels.guru'])
            ->findOrFail($id);
        $title = 'Detail Kelas';

        return view('admin.kelas.show', compact('kelas', 'title'));
    }

    /**
     * Delete kelas
     */
    public function destroyKelas($id)
    {
        DB::beginTransaction();
        try {
            $kelas = Kelas::findOrFail($id);

            // Cek apakah masih memiliki siswa
            if ($kelas->siswas()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Tidak dapat menghapus kelas yang masih memiliki siswa');
            }

            $kelas->delete();

            DB::commit();

            return redirect()
                ->route('admin.kelas.index')
                ->with('success', 'Kelas berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus kelas: ' . $e->getMessage());
        }
    }
    // ========== SISWA MANAGEMENT ==========

    /**
     * Display listing of siswa
     */
    public function manageSiswa()
    {
        $siswas = Siswa::with(['kelas', 'orangTua'])
            ->latest()
            ->paginate(10);
        $title = 'Daftar Siswa';

        return view('admin.siswa.index', compact('siswas', 'title'));
    }

    /**
     * Show form for creating new siswa
     */
    public function createSiswa()
    {
        $kelas = Kelas::all();
        $orangtuas = OrangTua::all();
        $title = 'Tambah Siswa Baru';

        return view('admin.siswa.create', compact('kelas', 'orangtuas', 'title'));
    }

    /**
     * Store a newly created siswa
     */
    public function storeSiswa(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|unique:siswas,nis|max:50',
            'kelas_id' => 'nullable|exists:kelas,id',
            'orang_tua_id' => 'required|exists:orang_tuas,id',
        ]);

        DB::beginTransaction();
        try {
            Siswa::create([
                'nama' => $validated['nama'],
                'nis' => $validated['nis'],
                'kelas_id' => $request->kelas_id,
                'orang_tua_id' => $validated['orang_tua_id'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.siswa.index')
                ->with('success', 'Data siswa berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan siswa: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing siswa
     */
    public function editSiswa($id)
    {
        $siswa = Siswa::with(['kelas', 'orangTua'])->findOrFail($id);
        $kelas = Kelas::all();
        $orangtuas = OrangTua::all();
        $title = 'Edit Siswa';

        return view('admin.siswa.edit', compact('siswa', 'kelas', 'orangtuas', 'title'));
    }

    /**
     * Update siswa
     */
    public function updateSiswa(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => [
                'required',
                'string',
                'max:50',
                Rule::unique('siswas', 'nis')->ignore($id)
            ],
            'kelas_id' => 'nullable|exists:kelas,id',
            'orang_tua_id' => 'required|exists:orang_tuas,id',
        ]);

        DB::beginTransaction();
        try {
            $siswa->update([
                'nama' => $validated['nama'],
                'nis' => $validated['nis'],
                'kelas_id' => $request->kelas_id,
                'orang_tua_id' => $validated['orang_tua_id'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.siswa.index')
                ->with('success', 'Data siswa berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate siswa: ' . $e->getMessage());
        }
    }

    /**
     * Show detail siswa
     */
    public function showSiswa($id)
    {
        $siswa = Siswa::with(['kelas.waliKelas', 'orangTua'])
            ->findOrFail($id);
        $title = 'Detail Siswa';

        return view('admin.siswa.show', compact('siswa', 'title'));
    }

    /**
     * Delete siswa
     */
    public function destroySiswa($id)
    {
        DB::beginTransaction();
        try {
            $siswa = Siswa::findOrFail($id);
            $siswa->delete();

            DB::commit();

            return redirect()
                ->route('admin.siswa.index')
                ->with('success', 'Siswa berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus siswa: ' . $e->getMessage());
        }
    }

    // ========== OTHER MANAGEMENT (placeholder) ==========

    // ========== JADWAL MANAGEMENT ==========

    /**
     * Display listing of jadwal
     */
    public function manageJadwal()
    {
        $jadwals = Jadwal::with(['mapel', 'kelas', 'guru'])
            ->orderBy('hari')
            ->orderBy('waktu_mulai')
            ->paginate(15);
        $title = 'Daftar Jadwal';

        return view('admin.jadwal.index', compact('jadwals', 'title'));
    }

    /**
     * Show form for creating new jadwal
     */
    public function createJadwal()
    {

        $mapels = Mapel::with('gurus')->get();
        $kelas = Kelas::all();
        $gurus = Guru::where('is_guru_mapel', true)->get();
        $title = 'Tambah Jadwal Baru';

        return view('admin.jadwal.create', compact('mapels', 'kelas', 'gurus', 'title'));
    }

    /**
     * Store a newly created jadwal
     */
    public function storeJadwal(Request $request)
    {
        $validated = $request->validate([
            'mapel_id' => 'required|exists:mapels,id',
            'kelas_id' => 'required|exists:kelas,id',
            'guru_id' => 'required|exists:gurus,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
        ]);

        DB::beginTransaction();
        try {
            // Validasi guru mengajar mapel yang dipilih
            $guru = Guru::find($request->guru_id);
            if (!$guru->mapels->contains($request->mapel_id)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Guru yang dipilih tidak mengajar mata pelajaran ini');
            }

            // Cek bentrok jadwal untuk kelas
            $bentrokKelas = Jadwal::where('kelas_id', $request->kelas_id)
                ->where('hari', $request->hari)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                        ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                                ->where('waktu_selesai', '>=', $request->waktu_selesai);
                        });
                })
                ->exists();

            if ($bentrokKelas) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Jadwal bentrok dengan jadwal kelas lain di waktu yang sama');
            }

            // Cek bentrok jadwal untuk guru
            $bentrokGuru = Jadwal::where('guru_id', $request->guru_id)
                ->where('hari', $request->hari)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                        ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                                ->where('waktu_selesai', '>=', $request->waktu_selesai);
                        });
                })
                ->exists();

            if ($bentrokGuru) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Guru sudah memiliki jadwal mengajar di waktu yang sama');
            }

            Jadwal::create([
                'mapel_id' => $validated['mapel_id'],
                'kelas_id' => $validated['kelas_id'],
                'guru_id' => $validated['guru_id'],
                'hari' => $validated['hari'],
                'waktu_mulai' => $validated['waktu_mulai'],
                'waktu_selesai' => $validated['waktu_selesai'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.jadwal.index')
                ->with('success', 'Jadwal berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing jadwal
     */
    public function editJadwal($id)
    {
        $jadwal = Jadwal::with(['mapel', 'kelas', 'guru'])->findOrFail($id);
        $mapels = Mapel::all();
        $kelas = Kelas::all();
        $gurus = Guru::where('is_guru_mapel', true)->get();
        $title = 'Edit Jadwal';

        return view('admin.jadwal.edit', compact('jadwal', 'mapels', 'kelas', 'gurus', 'title'));
    }

    /**
     * Update jadwal
     */
    public function updateJadwal(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        $validated = $request->validate([
            'mapel_id' => 'required|exists:mapels,id',
            'kelas_id' => 'required|exists:kelas,id',
            'guru_id' => 'required|exists:gurus,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
        ]);

        DB::beginTransaction();
        try {
            // Validasi guru mengajar mapel yang dipilih
            $guru = Guru::find($request->guru_id);
            if (!$guru->mapels->contains($request->mapel_id)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Guru yang dipilih tidak mengajar mata pelajaran ini');
            }

            // Cek bentrok jadwal untuk kelas (kecuali jadwal ini sendiri)
            $bentrokKelas = Jadwal::where('kelas_id', $request->kelas_id)
                ->where('hari', $request->hari)
                ->where('id', '!=', $id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                        ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                                ->where('waktu_selesai', '>=', $request->waktu_selesai);
                        });
                })
                ->exists();

            if ($bentrokKelas) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Jadwal bentrok dengan jadwal kelas lain di waktu yang sama');
            }

            // Cek bentrok jadwal untuk guru (kecuali jadwal ini sendiri)
            $bentrokGuru = Jadwal::where('guru_id', $request->guru_id)
                ->where('hari', $request->hari)
                ->where('id', '!=', $id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                        ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                                ->where('waktu_selesai', '>=', $request->waktu_selesai);
                        });
                })
                ->exists();

            if ($bentrokGuru) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Guru sudah memiliki jadwal mengajar di waktu yang sama');
            }

            $jadwal->update([
                'mapel_id' => $validated['mapel_id'],
                'kelas_id' => $validated['kelas_id'],
                'guru_id' => $validated['guru_id'],
                'hari' => $validated['hari'],
                'waktu_mulai' => $validated['waktu_mulai'],
                'waktu_selesai' => $validated['waktu_selesai'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.jadwal.index')
                ->with('success', 'Jadwal berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Show detail jadwal
     */
    public function showJadwal($id)
    {
        $jadwal = Jadwal::with(['mapel', 'kelas.waliKelas', 'guru'])->findOrFail($id);
        $title = 'Detail Jadwal';

        return view('admin.jadwal.show', compact('jadwal', 'title'));
    }

    /**
     * Delete jadwal
     */
    public function destroyJadwal($id)
    {
        DB::beginTransaction();
        try {
            $jadwal = Jadwal::findOrFail($id);
            $jadwal->delete();

            DB::commit();

            return redirect()
                ->route('admin.jadwal.index')
                ->with('success', 'Jadwal berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }
    // ========== MAPEL MANAGEMENT ==========

    /**
     * Display listing of mapel
     */
    public function manageMapel()
    {
        $mapels = Mapel::withCount(['gurus', 'jadwals'])
            ->latest()
            ->paginate(10);
        $title = 'Daftar Mata Pelajaran';

        return view('admin.mapel.index', compact('mapels', 'title'));
    }

    /**
     * Show form for creating new mapel
     */
    public function createMapel()
    {
        $gurus = Guru::where('is_guru_mapel', true)->get();
        $title = 'Tambah Mata Pelajaran Baru';

        return view('admin.mapel.create', compact('gurus', 'title'));
    }

    /**
     * Store a newly created mapel
     */
    public function storeMapel(Request $request)
    {
        $validated = $request->validate([
            'nama_matapelajaran' => 'required|string|max:255|unique:mapels,nama_matapelajaran',
            'kode_mapel' => 'nullable|string|max:10|unique:mapels,kode_mapel',
            'deskripsi' => 'nullable|string',
            'guru_ids' => 'nullable|array',
            'guru_ids.*' => 'exists:gurus,id',
        ]);

        DB::beginTransaction();
        try {
            // Validasi guru yang dipilih adalah guru mapel
            if ($request->guru_ids) {
                $invalidGurus = Guru::whereIn('id', $request->guru_ids)
                    ->where('is_guru_mapel', false)
                    ->exists();

                if ($invalidGurus) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Salah satu guru yang dipilih bukan guru mata pelajaran');
                }
            }

            $mapel = Mapel::create([
                'nama_matapelajaran' => $validated['nama_matapelajaran'],
                'kode_mapel' => $validated['kode_mapel'],
                'deskripsi' => $validated['deskripsi'],
            ]);

            // Assign guru ke mapel (otomatis buat GuruMapel)
            if ($request->guru_ids) {
                $mapel->gurus()->attach($request->guru_ids);
            }

            DB::commit();

            return redirect()
                ->route('admin.mapel.index')
                ->with('success', 'Mata pelajaran berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan mata pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing mapel
     */
    public function editMapel($id)
    {
        $mapel = Mapel::with(['gurus'])->findOrFail($id);
        $gurus = Guru::where('is_guru_mapel', true)->get();
        $title = 'Edit Mata Pelajaran';

        return view('admin.mapel.edit', compact('mapel', 'gurus', 'title'));
    }

    /**
     * Update mapel
     */
    public function updateMapel(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $validated = $request->validate([
            'nama_matapelajaran' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mapels', 'nama_matapelajaran')->ignore($id)
            ],
            'kode_mapel' => [
                'nullable',
                'string',
                'max:10',
                Rule::unique('mapels', 'kode_mapel')->ignore($id)
            ],
            'deskripsi' => 'nullable|string',
            'guru_ids' => 'nullable|array',
            'guru_ids.*' => 'exists:gurus,id',
        ]);

        DB::beginTransaction();
        try {
            // Validasi guru yang dipilih adalah guru mapel
            if ($request->guru_ids) {
                $invalidGurus = Guru::whereIn('id', $request->guru_ids)
                    ->where('is_guru_mapel', false)
                    ->exists();

                if ($invalidGurus) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Salah satu guru yang dipilih bukan guru mata pelajaran');
                }
            }

            $mapel->update([
                'nama_matapelajaran' => $validated['nama_matapelajaran'],
                'kode_mapel' => $validated['kode_mapel'],
                'deskripsi' => $validated['deskripsi'],
            ]);

            // Sync guru (otomatis tambah/hapus di GuruMapel)
            $mapel->gurus()->sync($request->guru_ids ?? []);

            DB::commit();

            return redirect()
                ->route('admin.mapel.index')
                ->with('success', 'Mata pelajaran berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate mata pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Show detail mapel
     */
    public function showMapel($id)
    {
        $mapel = Mapel::with(['gurus', 'jadwals.kelas', 'jadwals.guru'])
            ->findOrFail($id);
        $title = 'Detail Mata Pelajaran';

        return view('admin.mapel.show', compact('mapel', 'title'));
    }

    /**
     * Delete mapel
     */
    public function destroyMapel($id)
    {
        DB::beginTransaction();
        try {
            $mapel = Mapel::findOrFail($id);

            // Cek apakah masih digunakan di jadwal
            if ($mapel->jadwals()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Tidak dapat menghapus mata pelajaran yang masih digunakan di jadwal');
            }

            // Detach semua guru (hapus GuruMapel)
            $mapel->gurus()->detach();

            $mapel->delete();

            DB::commit();

            return redirect()
                ->route('admin.mapel.index')
                ->with('success', 'Mata pelajaran berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus mata pelajaran: ' . $e->getMessage());
        }
    }
}
