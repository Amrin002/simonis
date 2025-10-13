<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class JadwalGuruController extends Controller
{
    /**
     * Display jadwal for logged in guru
     */
    public function index(Request $request)
    {
        $title = 'Jadwal Mengajar';
        $guru = Auth::user()->guru;


        // Get filter parameters
        $hari = $request->get('hari');
        $kelas = $request->get('kelas');

        // Query jadwal
        $query = Jadwal::with(['mapel', 'kelas', 'guru'])
            ->where('guru_id', $guru->id);

        // Apply filters
        if ($hari) {
            $query->hari($hari);
        }

        if ($kelas) {
            $query->kelas($kelas);
        }

        // Get jadwal grouped by hari
        $jadwalGrouped = $query->orderBy('hari')
            ->orderBy('waktu_mulai')
            ->get()
            ->groupBy('hari');

        // Get all jadwal for statistics
        $allJadwal = Jadwal::where('guru_id', $guru->id)->get();

        // Statistics
        $stats = [
            'total_jadwal' => $allJadwal->count(),
            'total_kelas' => $allJadwal->pluck('kelas_id')->unique()->count(),
            'total_mapel' => $allJadwal->pluck('mapel_id')->unique()->count(),
            'jadwal_hari_ini' => $allJadwal->filter(fn($j) => $j->isToday())->count(),
            'jadwal_aktif' => $allJadwal->filter(fn($j) => $j->isActive())->count(),
        ];

        // Get unique kelas for filter
        $kelasList = Jadwal::where('guru_id', $guru->id)
            ->with('kelas')
            ->get()
            ->pluck('kelas')
            ->unique('id')
            ->sortBy('nama');

        return view('guru.jadwal.index', compact('jadwalGrouped', 'stats', 'kelasList', 'hari', 'kelas', 'title'));
    }

    /**
     * Display jadwal for today
     */
    public function today()
    {
        $guru = Auth::user()->guru;


        // Get hari ini
        $hariIni = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $hari = $hariIni[now()->format('l')];

        // Get jadwal hari ini
        $jadwalToday = Jadwal::with(['mapel', 'kelas', 'guru'])
            ->where('guru_id', $guru->id)
            ->hari($hari)
            ->orderBy('waktu_mulai')
            ->get();

        // Separate by status
        $jadwalAktif = $jadwalToday->filter(fn($j) => $j->isActive());
        $jadwalUpcoming = $jadwalToday->filter(fn($j) => $j->status === 'upcoming');
        $jadwalFinished = $jadwalToday->filter(fn($j) => $j->status === 'finished');

        return view('guru.jadwal.today', compact(
            'jadwalToday',
            'jadwalAktif',
            'jadwalUpcoming',
            'jadwalFinished',
            'hari'
        ));
    }

    /**
     * Display jadwal by specific day
     */
    public function byDay($hari)
    {
        $guru = Auth::user()->guru;


        // Validate hari
        $validHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        if (!in_array($hari, $validHari)) {
            abort(404);
        }

        // Get jadwal for specific day
        $jadwal = Jadwal::with(['mapel', 'kelas', 'guru'])
            ->where('guru_id', $guru->id)
            ->hari($hari)
            ->orderBy('waktu_mulai')
            ->get();

        return view('guru.jadwal.by-day', compact('jadwal', 'hari'));
    }

    /**
     * Display jadwal by specific kelas
     */
    public function byKelas($kelasId)
    {
        $guru = Auth::user()->guru;


        // Get jadwal for specific kelas
        $jadwal = Jadwal::with(['mapel', 'kelas', 'guru'])
            ->where('guru_id', $guru->id)
            ->where('kelas_id', $kelasId)
            ->orderBy('hari')
            ->orderBy('waktu_mulai')
            ->get()
            ->groupBy('hari');

        // Get kelas info
        $kelas = $jadwal->first()->first()->kelas ?? null;

        if (!$kelas) {
            abort(404, 'Jadwal tidak ditemukan');
        }

        return view('guru.jadwal.by-kelas', compact('jadwal', 'kelas'));
    }

    /**
     * Show detail jadwal
     */
    public function show($id)
    {
        $guru = Auth::user()->guru;


        $jadwal = Jadwal::with(['mapel', 'kelas.siswas', 'guru'])
            ->where('guru_id', $guru->id)
            ->findOrFail($id);

        return view('guru.jadwal.show', compact('jadwal'));
    }

    /**
     * Get jadwal in calendar format (JSON)
     */
    public function calendar(Request $request)
    {
        $guru = Auth::user()->guru;


        $jadwal = Jadwal::with(['mapel', 'kelas'])
            ->where('guru_id', $guru->id)
            ->get();

        // Convert to calendar events format
        $events = $jadwal->map(function ($item) {
            // Map hari to day of week (0 = Sunday, 1 = Monday, etc.)
            $hariMap = [
                'Minggu' => 0,
                'Senin' => 1,
                'Selasa' => 2,
                'Rabu' => 3,
                'Kamis' => 4,
                'Jumat' => 5,
                'Sabtu' => 6,
            ];

            return [
                'id' => $item->id,
                'title' => $item->mapel->nama_matapelajaran . ' - ' . $item->kelas->nama,
                'daysOfWeek' => [$hariMap[$item->hari]],
                'startTime' => date('H:i', strtotime($item->waktu_mulai)),
                'endTime' => date('H:i', strtotime($item->waktu_selesai)),
                'color' => $this->getColorByDay($item->hari),
                'extendedProps' => [
                    'mapel' => $item->mapel->nama_matapelajaran,
                    'kelas' => $item->kelas->nama,
                    'hari' => $item->hari,
                    'waktu' => $item->waktu,
                    'status' => $item->status,
                    'status_label' => $item->status_label,
                ]
            ];
        });

        return response()->json($events);
    }

    // /**
    //  * Export jadwal to PDF
    //  */
    // public function exportPdf()
    // {
    //     $guru = Auth::user()->guru;


    //     $jadwal = Jadwal::with(['mapel', 'kelas', 'guru'])
    //         ->where('guru_id', $guru->id)
    //         ->orderBy('hari')
    //         ->orderBy('waktu_mulai')
    //         ->get()
    //         ->groupBy('hari');

    //     $pdf = \PDF::loadView('guru.jadwal.pdf', compact('jadwal', 'guru'));

    //     return $pdf->download('jadwal-mengajar-' . $guru->nama . '.pdf');
    // }

    /**
     * Print jadwal
     */
    public function print()
    {
        $guru = Auth::user()->guru;


        $jadwal = Jadwal::with(['mapel', 'kelas', 'guru'])
            ->where('guru_id', $guru->id)
            ->orderBy('hari')
            ->orderBy('waktu_mulai')
            ->get()
            ->groupBy('hari');

        return view('guru.jadwal.print', compact('jadwal', 'guru'));
    }

    /**
     * Get weekly schedule summary
     */
    public function weeklySummary()
    {
        $guru = Auth::user()->guru;


        $jadwal = Jadwal::with(['mapel', 'kelas'])
            ->where('guru_id', $guru->id)
            ->orderBy('hari')
            ->orderBy('waktu_mulai')
            ->get()
            ->groupBy('hari');

        // Calculate weekly statistics
        $summary = [
            'total_jam_mengajar' => 0,
            'hari' => []
        ];

        foreach ($jadwal as $hari => $items) {
            $totalMenit = $items->sum(fn($j) => $j->durasi);
            $summary['total_jam_mengajar'] += $totalMenit;
            $summary['hari'][$hari] = [
                'jumlah_jadwal' => $items->count(),
                'total_menit' => $totalMenit,
                'total_jam' => floor($totalMenit / 60),
                'sisa_menit' => $totalMenit % 60,
            ];
        }

        // Convert total minutes to hours
        $summary['total_jam'] = floor($summary['total_jam_mengajar'] / 60);
        $summary['sisa_menit'] = $summary['total_jam_mengajar'] % 60;

        return view('guru.jadwal.weekly-summary', compact('jadwal', 'summary'));
    }

    /**
     * Helper: Get color based on day
     */
    private function getColorByDay($hari)
    {
        $colors = [
            'Senin' => '#0d6efd',
            'Selasa' => '#198754',
            'Rabu' => '#0dcaf0',
            'Kamis' => '#ffc107',
            'Jumat' => '#dc3545',
            'Sabtu' => '#6c757d',
        ];

        return $colors[$hari] ?? '#6c757d';
    }
}
