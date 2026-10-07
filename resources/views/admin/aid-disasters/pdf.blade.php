<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Bantuan Bencana Kecamatan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #4338ca;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: 700;
            color: #1e1b4b;
        }
        .header h2 {
            font-size: 13px;
            font-weight: 600;
            color: #4338ca;
            margin-top: 2px;
        }
        .header .meta {
            font-size: 10px;
            color: #6b7280;
            margin-top: 4px;
        }
        table.summary {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
        }
        .summary-box {
            border-radius: 6px;
            padding: 8px 12px;
            text-align: center;
        }
        .summary-box .num {
            font-size: 20px;
            font-weight: 700;
        }
        .summary-box .lbl {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table.data thead tr {
            background: #4338ca;
            color: #fff;
        }
        table.data thead th {
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
        }
        table.data tbody tr:nth-child(even) { background: #f5f7ff; }
        table.data tbody tr:nth-child(odd)  { background: #ffffff; }
        table.data tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        .footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            border-top: 1px solid #e5e7eb;
            padding: 6px 0;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN REKAPITULASI BANTUAN BENCANA KECAMATAN</h1>
        <h2>Sistem Informasi Geospasial Bencana (GISCANA)</h2>
        <div class="meta">
            Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }} WIB
        </div>
    </div>

    @php
        $totalTarget = $aidDisasters->sum('total_recipients');
        $totalTersalur = $aidDisasters->sum('total_received');
        $totalKecamatan = $aidDisasters->count();
        $overallPct = $totalTarget > 0 ? round(($totalTersalur / $totalTarget) * 100, 1) : 0;
    @endphp

    <table class="summary">
        <tr>
            <td style="width:25%; padding:0 5px 0 0;">
                <div class="summary-box" style="background:#eef2ff; border:1px solid #c7d2fe;">
                    <div class="num" style="color:#4338ca;">{{ number_format($totalKecamatan) }}</div>
                    <div class="lbl">Total Kecamatan</div>
                </div>
            </td>
            <td style="width:25%; padding:0 5px;">
                <div class="summary-box" style="background:#eff6ff; border:1px solid #bfdbfe;">
                    <div class="num" style="color:#1d4ed8;">{{ number_format($totalTarget) }}</div>
                    <div class="lbl">Total Target KK</div>
                </div>
            </td>
            <td style="width:25%; padding:0 5px;">
                <div class="summary-box" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <div class="num" style="color:#166534;">{{ number_format($totalTersalur) }}</div>
                    <div class="lbl">Bantuan Tersalurkan</div>
                </div>
            </td>
            <td style="width:25%; padding:0 0 0 5px;">
                <div class="summary-box" style="background:#fff7ed; border:1px solid #fed7aa;">
                    <div class="num" style="color:#c2410c;">{{ $overallPct }}%</div>
                    <div class="lbl">Capaian Rata-Rata</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width:8%;">No</th>
                <th style="width:32%;">Kecamatan</th>
                <th style="width:20%;">Target Penerima (KK)</th>
                <th style="width:20%;">Bantuan Tersalurkan</th>
                <th style="width:20%;">Persentase Capaian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aidDisasters as $i => $aid)
            @php
                $pct = $aid->total_recipients > 0 ? round(($aid->distributed_aid / $aid->total_recipients) * 100, 1) : 0;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $aid->district_name }}</strong></td>
                <td>{{ number_format($aid->total_recipients) }} KK</td>
                <td>{{ number_format($aid->distributed_aid) }} Unit</td>
                <td><strong>{{ $pct }}%</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        GISCANA &mdash; Sistem Informasi Geospasial Bencana &nbsp;|&nbsp; Dokumen Laporan Resmi
    </div>

</body>
</html>
