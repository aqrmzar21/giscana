<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Rekapitulasi Bantuan Bencana per Kecamatan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            background: #fff;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #3730a3;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: 800;
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 12px;
            font-weight: 600;
            color: #4338ca;
            margin-top: 4px;
        }
        .header .meta {
            font-size: 9px;
            color: #6b7280;
            margin-top: 5px;
        }
        table.summary {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .summary-box {
            border-radius: 8px;
            padding: 10px 12px;
            text-align: center;
        }
        .summary-box .num {
            font-size: 20px;
            font-weight: 800;
        }
        .summary-box .lbl {
            font-size: 9px;
            color: #4b5563;
            margin-top: 3px;
            text-transform: uppercase;
            font-weight: bold;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 25px;
        }
        table.data thead tr {
            background: #3730a3;
            color: #ffffff;
        }
        table.data thead th {
            padding: 9px 10px;
            text-align: left;
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table.data tbody tr:nth-child(even) { background: #f8fafc; }
        table.data tbody tr:nth-child(odd)  { background: #ffffff; }
        table.data tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-info    { background: #e0e7ff; color: #3730a3; }
        .badge-warning { background: #fef3c7; color: #92400e; }

        .signature-section {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-box {
            text-align: center;
            width: 40%;
            font-size: 10px;
        }
        .signature-space {
            height: 60px;
        }

        .footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            border-top: 1px solid #e5e7eb;
            padding: 8px 0;
            text-align: center;
            font-size: 8.5px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN REKAPITULASI PENYALURAN BANTUAN BENCANA PER KECAMATAN</h1>
        <h2>BADAN PENANGGULANGAN BENCANA DAERAH (BPBD) KABUPATEN BONE BOLANGO</h2>
        <div class="meta">
            Periode Data: <strong>Tahun {{ $selectedYear !== 'all' ? $selectedYear : 'Semua Tahun Terdata' }}</strong> &bull; Dicetak: {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }} WITA
        </div>
    </div>

    @php
        $totalTarget = $aidDisasters->sum('total_recipients');
        
        // Mengambil total KK unik yang sudah menerima dari $villageBreakdown['year_recipients'] atau $aid->total_received
        $totalSudahMenerima = $aidDisasters->sum(function($aid) use ($villageBreakdown) {
            return isset($villageBreakdown[$aid->id]) 
                ? $villageBreakdown[$aid->id]['year_recipients'] 
                : $aid->total_received;
        });

        $totalKecamatan = $aidDisasters->count();
        $overallPct = $totalTarget > 0 ? min(100, round(($totalSudahMenerima / $totalTarget) * 100, 1)) : 0;
    @endphp

    <table class="summary">
        <tr>
            <td style="width:25%; padding:0 5px 0 0;">
                <div class="summary-box" style="background:#eef2ff; border:1px solid #c7d2fe;">
                    <div class="num" style="color:#3730a3;">{{ number_format($totalKecamatan) }}</div>
                    <div class="lbl">Total Kecamatan</div>
                </div>
            </td>
            <td style="width:25%; padding:0 5px;">
                <div class="summary-box" style="background:#eff6ff; border:1px solid #bfdbfe;">
                    <div class="num" style="color:#1d4ed8;">{{ number_format($totalTarget) }}</div>
                    <div class="lbl">Target Penerima (KK)</div>
                </div>
            </td>
            <td style="width:25%; padding:0 5px;">
                <div class="summary-box" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <div class="num" style="color:#166534;">{{ number_format($totalSudahMenerima) }}</div>
                    <div class="lbl">Sudah Menerima (KK)</div>
                </div>
            </td>
            <td style="width:25%; padding:0 0 0 5px;">
                <div class="summary-box" style="background:#fff7ed; border:1px solid #fed7aa;">
                    <div class="num" style="color:#c2410c;">{{ $overallPct }}%</div>
                    <div class="lbl">Tingkat Capaian</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width:6%; text-align:center;">No</th>
                <th style="width:34%;">Kecamatan</th>
                <th style="width:20%; text-align:center;">Target Penerima (KK)</th>
                <th style="width:20%; text-align:center;">Sudah Menerima (KK)</th>
                <th style="width:20%; text-align:center;">Capaian (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aidDisasters as $i => $aid)
            @php
                // Mengambil key 'year_recipients' (KK unik) bawaan controller
                $distVal = isset($villageBreakdown[$aid->id]) 
                    ? $villageBreakdown[$aid->id]['year_recipients'] 
                    : $aid->total_received;
                    
                $pct = $aid->total_recipients > 0 ? min(100, round(($distVal / $aid->total_recipients) * 100, 1)) : 0;
            @endphp
            <tr>
                <td style="text-align:center;">{{ $i + 1 }}</td>
                <td><strong>Kecamatan {{ $aid->district_name }}</strong></td>
                <td style="text-align:center;">{{ number_format($aid->total_recipients) }} KK</td>
                <td style="text-align:center; color:#166534; font-weight:bold;">{{ number_format($distVal) }} KK</td>
                <td style="text-align:center;">
                    @if($pct >= 80)
                        <span class="badge badge-success">{{ $pct }}% (Sangat Baik)</span>
                    @elseif($pct >= 40)
                        <span class="badge badge-info">{{ $pct }}% (Sedang)</span>
                    @else
                        <span class="badge badge-warning">{{ $pct }}% (Perlu Perhatian)</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature-section">
        <tr>
            <td class="signature-box" style="float:left;">
                <p>Mengetahui,</p>
                <p><strong>Kepala Pelaksana BPBD</strong></p>
                <div class="signature-space"></div>
                <p><strong><u>H. Nama Kepala BPBD, M.Si</u></strong></p>
                <p>NIP. 19780101 200501 1 002</p>
            </td>
            <td style="width:20%;"></td>
            <td class="signature-box" style="float:right;">
                <p>Bone Bolango, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</p>
                <p><strong>Petugas / Operator Data Bencana</strong></p>
                <div class="signature-space"></div>
                <p><strong><u>{{ auth()->user()->name }}</u></strong></p>
                <p>Petugas Posko Logistik BPBD</p>
            </td>
        </tr>
    </table>

    <div class="footer">
        GISCANA &mdash; Sistem Informasi Geospasial Bencana BPBD Kabupaten Bone Bolango &nbsp;|&nbsp; Dokumen Rekapitulasi Resmi Pimpinan
    </div>

</body>
</html>
