<?php

namespace App\Http\Controllers;

use App\Models\Rekapan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapanController extends Controller
{
    /**
     * Halaman kirim rekapan (untuk wali kelas)
     */
    public function halamanKirim(Request $request)
    {
        $guru = Auth::user()->guru;

        // Validasi: Hanya wali kelas
        if (!$guru->is_wali_kelas || !$guru->kelasWali) {
            abort(403, 'Hanya wali kelas yang dapat mengakses halaman ini');
        }

        // Get tanggal dari request, default hari ini
        $tanggal = $request->get('tanggal', today()->format('Y-m-d'));

        // Get filter status
        $statusFilter = $request->get('status', 'all');

        // Ambil semua siswa di kelas
        $siswas = $guru->kelasWali->siswas;

        // Ambil atau buat rekapan untuk masing-masing siswa
        $rekapans = collect();
        foreach ($siswas as $siswa) {
            $rekapan = Rekapan::firstOrNew([
                'siswa_id' => $siswa->id,
                'tanggal' => $tanggal,
            ]);

            // Jika rekapan baru, generate data
            if (!$rekapan->exists) {
                $rekapan->generateAll();
                $rekapan->save();
            }

            // Generate WA link jika belum ada
            if (empty($rekapan->wa_link) && $rekapan->validateNoHp()) {
                $rekapan->generateWaLink();
                $rekapan->save();
            }

            $rekapans->push($rekapan);
        }

        // Filter berdasarkan status jika bukan 'all'
        if ($statusFilter !== 'all') {
            $rekapans = $rekapans->where('status_kirim', $statusFilter);
        }

        $title = 'Kirim Rekapan Harian';

        return view('wali-kelas.kirim-rekapan', compact('rekapans', 'tanggal', 'statusFilter', 'title'));
    }

    /**
     * Mark rekapan sebagai dikirim (AJAX)
     */
    public function markDikirim($id)
    {
        try {
            $rekapan = Rekapan::findOrFail($id);
            $guru = Auth::user()->guru;

            // Validasi: Hanya wali kelas dari kelas siswa ini
            if (!$guru->is_wali_kelas || $rekapan->siswa->kelas_id !== $guru->kelasWali->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Mark sebagai dikirim
            $rekapan->markAsDikirim();

            return response()->json([
                'success' => true,
                'message' => 'Rekapan ditandai sebagai dikirim',
                'dikirim_at' => $rekapan->dikirim_at->diffForHumans()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate ulang semua rekapan untuk hari ini
     */
    public function regenerateHariIni()
    {
        try {
            $guru = Auth::user()->guru;

            // Validasi: Hanya wali kelas
            if (!$guru->is_wali_kelas || !$guru->kelasWali) {
                return redirect()
                    ->back()
                    ->with('error', 'Hanya wali kelas yang dapat melakukan ini');
            }

            $siswas = $guru->kelasWali->siswas;
            $tanggal = today();
            $count = 0;

            foreach ($siswas as $siswa) {
                $rekapan = Rekapan::firstOrNew([
                    'siswa_id' => $siswa->id,
                    'tanggal' => $tanggal,
                ]);

                $rekapan->generateAll();

                if ($rekapan->validateNoHp()) {
                    $rekapan->generateWaLink();
                }

                $rekapan->save();
                $count++;
            }

            return redirect()
                ->back()
                ->with('success', "Berhasil generate ulang {$count} rekapan untuk hari ini");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal generate rekapan: ' . $e->getMessage());
        }
    }

    /**
     * Lihat detail rekapan
     */
    public function show($id)
    {
        $rekapan = Rekapan::with(['siswa.kelas', 'siswa.orangTua'])
            ->findOrFail($id);

        $guru = Auth::user()->guru;

        // Validasi akses
        if ($guru->is_wali_kelas && $rekapan->siswa->kelas_id !== $guru->kelasWali->id) {
            abort(403, 'Anda tidak memiliki akses ke rekapan ini');
        }

        $title = 'Detail Rekapan - ' . $rekapan->siswa->nama;

        return view('wali-kelas.detail-rekapan', compact('rekapan', 'title'));
    }

    /**
     * Riwayat rekapan per siswa
     */
    public function riwayatSiswa($siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);
        $guru = Auth::user()->guru;

        // Validasi akses
        if ($guru->is_wali_kelas && $siswa->kelas_id !== $guru->kelasWali->id) {
            abort(403, 'Anda tidak memiliki akses ke data siswa ini');
        }

        $rekapans = Rekapan::where('siswa_id', $siswaId)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        $title = 'Riwayat Rekapan - ' . $siswa->nama;

        return view('wali-kelas.riwayat-rekapan', compact('siswa', 'rekapans', 'title'));
    }

    /**
     * Dashboard rekapan (statistik)
     */
    public function dashboard(Request $request)
    {
        $guru = Auth::user()->guru;

        // Validasi: Hanya wali kelas
        if (!$guru->is_wali_kelas || !$guru->kelasWali) {
            abort(403, 'Hanya wali kelas yang dapat mengakses halaman ini');
        }

        // ✅ TAMBAH: Get tanggal dari request
        $tanggal = $request->get('tanggal')
            ? \Carbon\Carbon::parse($request->get('tanggal'))
            : today();

        // Statistik hari ini
        $totalSiswa = $guru->kelasWali->siswas()->count();

        $rekapanHariIni = Rekapan::byTanggal($tanggal)
            ->whereHas('siswa', function ($q) use ($guru) {
                $q->where('kelas_id', $guru->kelasWali->id);
            })
            ->get();

        $belumDikirim = $rekapanHariIni->where('status_kirim', 'belum_dikirim')->count();
        $sudahDikirim = $rekapanHariIni->where('status_kirim', 'dikirim')->count();
        $gagal = $rekapanHariIni->where('status_kirim', 'gagal')->count();

        $title = 'Dashboard Rekapan';

        return view('wali-kelas.dashboard-rekapan', compact(
            'totalSiswa',
            'belumDikirim',
            'sudahDikirim',
            'gagal',
            'tanggal',
            'title'
        ));
    }
}
