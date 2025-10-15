<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            font-size: 12px;
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
            color: #333;
        }

        .header h2 {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }

        .header p {
            color: #888;
            font-size: 11px;
        }

        .info-section {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        .info-box {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }

        .info-box strong {
            color: #333;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-box {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            border: 1px solid #e2e8f0;
        }

        .stat-box .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-box .value {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background: #667eea;
            color: white;
        }

        table th {
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        table th.center {
            text-align: center;
        }

        table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        table td {
            padding: 10px 8px;
            font-size: 11px;
        }

        table td.center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-align: center;
        }

        .badge-success {
            background: #48bb78;
            color: white;
        }

        .badge-danger {
            background: #f56565;
            color: white;
        }

        .badge-primary {
            background: #4299e1;
            color: white;
        }

        .badge-warning {
            background: #ed8936;
            color: white;
        }

        .badge-dark {
            background: #2d3748;
            color: white;
        }

        .badge-info {
            background: #38b2ac;
            color: white;
        }

        .predikat-large {
            font-size: 16px;
            font-weight: bold;
            padding: 6px 12px;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
            page-break-inside: avoid;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background: #5568d3;
        }

        .print-info {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            color: #856404;
        }
    </style>
</head>

<body>
    {{-- Print Button --}}
    <button onclick="window.print()" class="print-button no-print">
        🖨️ Print / Save PDF
    </button>

    {{-- Print Info --}}
    <div class="print-info no-print">
        📄 Klik tombol "Print / Save PDF" atau tekan <strong>Ctrl+P</strong> untuk mencetak atau menyimpan sebagai PDF
    </div>

    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN NILAI AKHIR SISWA</h1>
        <h2>{{ config('app.name', 'Sistem Informasi Sekolah') }}</h2>
        <p>Dicetak pada: {{ now()->isoFormat('dddd, D MMMM YYYY - HH:mm') }} WIB</p>
    </div>

    {{-- Info Section --}}
    <div class="info-section">
        <div class="info-box">
            <strong>Guru:</strong> {{ $guru->nama }}<br>
            @if($guru->isWaliKelas())
                <strong>Status:</strong> Wali Kelas {{ $guru->kelasWali->nama }}
            @else
                <strong>Status:</strong> Guru Mata Pelajaran
            @endif
        </div>
        <div class="info-box">
            <strong>Kelas:</strong> {{ $filterInfo['kelas'] }}<br>
            <strong>Mata Pelajaran:</strong> {{ $filterInfo['mapel'] }}
        </div>
    </div>

    {{-- Statistics --}}
    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">Total Siswa</div>
            <div class="value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Tuntas</div>
            <div class="value" style="color: #48bb78;">{{ $stats['tuntas'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Tidak Tuntas</div>
            <div class="value" style="color: #f56565;">{{ $stats['tidak_tuntas'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Rata-rata</div>
            <div class="value" style="color: #4299e1;">{{ number_format($stats['rata_rata'], 2) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Tertinggi</div>
            <div class="value" style="color: #48bb78;">{{ number_format($stats['tertinggi'], 2) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Terendah</div>
            <div class="value" style="color: #f56565;">{{ number_format($stats['terendah'], 2) }}</div>
        </div>
    </div>

    {{-- Table --}}
    @if($nilaiList->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">NIS</th>
                    <th style="width: 20%;">Nama Siswa</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 16%;">Mata Pelajaran</th>
                    <th class="center" style="width: 8%;">Tugas</th>
                    <th class="center" style="width: 8%;">UTS</th>
                    <th class="center" style="width: 8%;">UAS</th>
                    <th class="center" style="width: 8%;">Akhir</th>
                    <th class="center" style="width: 8%;">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nilaiList as $index => $nilai)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $nilai->siswa->nis }}</strong></td>
                        <td>{{ $nilai->siswa->nama }}</td>
                        <td>{{ $nilai->kelas->nama }}</td>
                        <td>{{ $nilai->mapel->nama_matapelajaran }}</td>
                        <td class="center">{{ number_format($nilai->nilai_tugas, 0) }}</td>
                        <td class="center">{{ number_format($nilai->nilai_uts, 0) }}</td>
                        <td class="center">{{ number_format($nilai->nilai_uas, 0) }}</td>
                        <td class="center"><strong>{{ number_format($nilai->nilai_akhir, 2) }}</strong></td>
                        <td class="center">
                            <span class="badge badge-{{ $nilai->predikat_color }} predikat-large">
                                {{ $nilai->predikat }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Footer / Signature --}}
        <div class="footer">
            <div class="signature-box">
                <p>Mengetahui,</p>
                <p><strong>{{ $guru->isWaliKelas() ? 'Wali Kelas' : 'Guru Mata Pelajaran' }}</strong></p>
                <div class="signature-line">
                    <strong>{{ $guru->nama }}</strong><br>
                    NIP: {{ $guru->nip }}
                </div>
            </div>
        </div>
    @else
        <div class="empty-state">
            <h3>📊 Tidak Ada Data Nilai</h3>
            <p>Belum ada data nilai akhir yang dapat dicetak.</p>
        </div>
    @endif
</body>

</html>
