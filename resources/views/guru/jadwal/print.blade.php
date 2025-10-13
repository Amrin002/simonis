<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Mengajar - {{ $guru->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .header h2 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #34495e;
        }

        .header p {
            font-size: 12px;
            color: #7f8c8d;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .info-item {
            flex: 1;
        }

        .info-item strong {
            display: block;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .schedule-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .day-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 15px;
            border-radius: 8px 8px 0 0;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .schedule-table thead {
            background: #f8f9fa;
        }

        .schedule-table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #dee2e6;
            font-size: 11px;
            text-transform: uppercase;
        }

        .schedule-table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: top;
        }

        .schedule-table tbody tr:hover {
            background: #f8f9fa;
        }

        .schedule-table tbody tr:last-child td {
            border-bottom: none;
        }

        .time-cell {
            font-weight: bold;
            color: #667eea;
            white-space: nowrap;
        }

        .mapel-cell {
            font-weight: 600;
            color: #2c3e50;
        }

        .kelas-badge {
            display: inline-block;
            padding: 4px 8px;
            background: #17a2b8;
            color: white;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 500;
        }

        .duration-badge {
            display: inline-block;
            padding: 4px 8px;
            background: #ffc107;
            color: #333;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 500;
        }

        .no-schedule {
            text-align: center;
            padding: 30px;
            color: #95a5a6;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            text-align: center;
            min-width: 200px;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        .summary-box {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }

        .summary-box h3 {
            color: #28a745;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }

        .summary-item:last-child {
            border-bottom: none;
            font-weight: bold;
            padding-top: 10px;
        }

        @media print {
            body {
                padding: 10px;
            }

            .schedule-section {
                page-break-inside: avoid;
            }

            .no-print {
                display: none;
            }

            @page {
                margin: 1cm;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .print-button:hover {
            background: #5568d3;
        }

        .close-button {
            position: fixed;
            top: 20px;
            right: 150px;
            padding: 12px 24px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .close-button:hover {
            background: #5a6268;
        }
    </style>
</head>

<body>
    {{-- Print & Close Buttons --}}
    <button class="print-button no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Print Jadwal
    </button>
    <button class="close-button no-print" onclick="window.close()">
        <i class="fas fa-times"></i> Tutup
    </button>

    {{-- Header --}}
    <div class="header">
        <h1>JADWAL MENGAJAR</h1>
        <h2>GURU MATA PELAJARAN</h2>
        <p>Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</p>
    </div>

    {{-- Info Section --}}
    <div class="info-section">
        <div class="info-item">
            <strong>Nama Guru:</strong>
            {{ $guru->nama }}
        </div>
        <div class="info-item">
            <strong>NIP:</strong>
            {{ $guru->nip }}
        </div>
        <div class="info-item">
            <strong>Dicetak:</strong>
            {{ now()->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIT
        </div>
    </div>

    {{-- Summary Box --}}
    @php
        $totalJadwal = 0;
        $totalMenit = 0;
        foreach ($jadwal as $items) {
            $totalJadwal += $items->count();
            $totalMenit += $items->sum(fn($j) => $j->durasi);
        }
        $totalJam = floor($totalMenit / 60);
        $sisaMenit = $totalMenit % 60;
    @endphp

    <div class="summary-box">
        <h3>📊 RINGKASAN JADWAL</h3>
        <div class="summary-item">
            <span>Total Jadwal Mengajar:</span>
            <span>{{ $totalJadwal }} Jadwal</span>
        </div>
        <div class="summary-item">
            <span>Jumlah Hari Mengajar:</span>
            <span>{{ $jadwal->count() }} Hari</span>
        </div>
        <div class="summary-item">
            <span>Total Jam Mengajar per Minggu:</span>
            <span>{{ $totalJam }} Jam {{ $sisaMenit }} Menit</span>
        </div>
    </div>

    {{-- Schedule by Day --}}
    @if($jadwal->count() > 0)
        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
            @if(isset($jadwal[$hari]) && $jadwal[$hari]->count() > 0)
                <div class="schedule-section">
                    <div class="day-header">
                        <span>📅 {{ strtoupper($hari) }}</span>
                        <span>{{ $jadwal[$hari]->count() }} Jadwal</span>
                    </div>
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Waktu</th>
                                <th width="30%">Mata Pelajaran</th>
                                <th width="20%">Kelas</th>
                                <th width="15%">Durasi</th>
                                <th width="15%">Kode Mapel</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwal[$hari] as $index => $item)
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td class="time-cell">
                                        {{ $item->waktu_mulai_format }}<br>
                                        <small style="color: #6c757d;">{{ $item->waktu_selesai_format }}</small>
                                    </td>
                                    <td class="mapel-cell">
                                        {{ $item->mapel->nama_matapelajaran }}
                                    </td>
                                    <td>
                                        <span class="kelas-badge">
                                            {{ $item->kelas->nama }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="duration-badge">
                                            {{ $item->durasi_format }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $item->mapel->kode }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach
    @else
        <div class="no-schedule">
            <p>Tidak ada jadwal mengajar yang tersedia.</p>
        </div>
    @endif

    {{-- Footer with Signature --}}
    <div class="footer">
        <div class="signature">
            <p>Mengetahui,</p>
            <p><strong>Kepala Sekolah</strong></p>
            <div class="signature-line">
                <strong>(...........................)</strong>
            </div>
        </div>
        <div class="signature">
            <p>{{ now()->isoFormat('D MMMM YYYY') }}</p>
            <p><strong>Guru Mata Pelajaran</strong></p>
            <div class="signature-line">
                <strong>{{ $guru->nama }}</strong>
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() {
        //     window.print();
        // }

        // Close window after print
        window.onafterprint = function () {
            // window.close();
        }
    </script>
</body>

</html>
