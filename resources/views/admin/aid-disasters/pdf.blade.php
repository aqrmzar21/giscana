<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Statistik Penyaluran Bantuan Bencana</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            background: #fff;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #4338ca;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 15px;
            font-weight: 700;
            color: #1e1b4b;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 12px;
            font-weight: 600;
            color: #4338ca;
            margin-top: 3px;
        }
        .header .meta {
            font-size: 9px;
            color: #6b7280;
            margin-top: 4px;
        }
        table.summary {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .summary-box {
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
        }
        .summary-box .num {
            font-size: 18px;
            font-weight: 700;
        }
        .summary-box .lbl {
            font-size: 9px;
            color: #4b5563;
            margin-top: 2px;
            text-transform: uppercase;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-bottom: 15px;
        }
        table.data thead tr {
            background: #4338ca;
            color: #fff;
        }
        table.data thead th {
            padding: 7px 8px;
            text-align: left;
            font-weight: 600;
        }
        table.data tbody tr:nth-child(even) { background: #f8fafc; }
        table.data tbody tr:nth-child(odd)  { background: #ffffff; }
        table.data tbody td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e1b4b;
            margin-top: 15px;
            margin-bottom: 8px;
            border-left: 4px solid #4338ca;
            padding-left: 8px;
        }
        .footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            border-top: 1px solid #e5e7eb;
            padding: 6px 0;
            text-align: center;
            font-size: 8.5px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN STATISTIK PENYALURAN BANTUAN BENCANA</h1>
        <h2>Kabupaten Bone Bolango &mdash; Periode Tahun {{ $selectedYear !== 'all' ? $selectedYear : 'Semua Tahun' }}</h2>
        <div class="meta">
            Badan Penanggulangan Bencana Daerah (BPBD) &bull; Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }} WIB
        </div>
    </div>

    @php
        $totalTarget = $aidDisasters->sum('total_recipients');
        $totalTersalur = $totalDistributedSum ?? $aidDisasters->sum('distributed_aid');
        $totalKecamatan = $aidDisasters->count();
        $overallPct = $totalTarget > 0 ? round(($totalTersalur / $totalTarget) * 100, 1) : 0;
    @endphp

    <table class="summary">
        <tr>
            <td style="width:25%; padding:0 4px 0 0;">
                <div class="summary-box" style="background:#eef2ff; border:1px solid #c7d2fe;">
                    <div class="num" style="color:#4338ca;">{{ number_format($totalKecamatan) }}</div>
                    <div class="lbl">Total Kecamatan</div>
                </div>
            </td>
            <td style="width:25%; padding:0 4px;">
                <div class="summary-box" style="background:#eff6ff; border:1px solid #bfdbfe;">
                    <div class="num" style="color:#1d4ed8;">{{ number_format($totalTarget) }}</div>
                    <div class="lbl">Total Target KK</div>
                </div>
            </td>
            <td style="width:25%; padding:0 4px;">
                <div class="summary-box" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <div class="num" style="color:#166534;">{{ number_format($totalTersalur) }}</div>
                    <div class="lbl">Logistik Tersalur ({{ $selectedYear !== 'all' ? $selectedYear : 'Total' }})</div>
                </div>
            </td>
            <td style="width:25%; padding:0 0 0 4px;">
                <div class="summary-box" style="background:#fff7ed; border:1px solid #fed7aa;">
                    <div class="num" style="color:#c2410c;">{{ $overallPct }}%</div>
                    <div class="lbl">Tingkat Pemenuhan</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">1. REKAPITULASI PENYALURAN PER KECAMATAN</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width:6%; text-align:center;">No</th>
                <th style="width:34%;">Kecamatan</th>
                <th style="width:20%; text-align:center;">Target Penerima (KK)</th>
                <th style="width:20%; text-align:center;">Tersalurkan (Unit)</th>
                <th style="width:20%; text-align:center;">Capaian (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aidDisasters as $i => $aid)
            @php
                $distVal = isset($villageBreakdown[$aid->id]) ? $villageBreakdown[$aid->id]['year_received'] : $aid->total_received;
                $pct = $aid->total_recipients > 0 ? round(($distVal / $aid->total_recipients) * 100, 1) : 0;
            @endphp
            <tr>
                <td style="text-align:center;">{{ $i + 1 }}</td>
                <td><strong>Kecamatan {{ $aid->district_name }}</strong></td>
                <td style="text-align:center;">{{ number_format($aid->total_recipients) }} KK</td>
                <td style="text-align:center; color:#166534; font-weight:bold;">{{ number_format($distVal) }} Unit</td>
                <td style="text-align:center;"><strong>{{ $pct }}%</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">2. RINCIAN DISTRIBUSI LOGISTIK PER DESA & KATEGORI</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width:5%; text-align:center;">No</th>
                <th style="width:25%;">Kecamatan & Desa</th>
                <th style="width:18%; text-align:center;">Penerima (KK)</th>
                <th style="width:20%; text-align:center;">Total Tersalur</th>
                <th style="width:32%;">Kategori Barang Bantuan</th>
            </tr>
        </thead>
        <tbody>
            @php $rowNo = 1; @endphp
            @foreach($aidDisasters as $aid)
                @php
                    $villages = $villageBreakdown[$aid->id]['villages'] ?? [];
                @endphp
                @foreach($villages as $vName => $records)
                <tr>
                    <td style="text-align:center;">{{ $rowNo++ }}</td>
                    <td><strong>Kec. {{ $aid->district_name }}</strong> &mdash; {{ $vName }}</td>
                    <td style="text-align:center;">{{ number_format($records->pluck('beneficiary_id')->unique()->count()) }} KK</td>
                    <td style="text-align:center; color:#166534; font-weight:bold;">{{ number_format($records->sum('quantity_received')) }} Unit</td>
                    <td>
                        {{ implode(', ', $records->pluck('aidInventory.item_name')->filter()->unique()->toArray()) }}
                    </td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        GISCANA &mdash; Sistem Informasi Geospasial Bencana BPBD Kabupaten Bone Bolango &nbsp;|&nbsp; Dokumen Resmi Pimpinan
    </div>

</body>
</html>
