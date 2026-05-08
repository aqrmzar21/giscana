<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Rute Evakuasi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            background: #fff;
        }

        /* ── HEADER ─────────────────────────────── */
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
            letter-spacing: 0.5px;
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
        .badge-kecamatan {
            display: inline-block;
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
            border-radius: 20px;
            padding: 2px 12px;
            font-size: 10px;
            font-weight: 600;
            margin-top: 6px;
        }

        /* ── SUMMARY BOXES ───────────────────────── */
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

        /* ── TABLE ───────────────────────────────── */
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
            letter-spacing: 0.3px;
        }
        table.data tbody tr:nth-child(even) { background: #f5f7ff; }
        table.data tbody tr:nth-child(odd)  { background: #ffffff; }
        table.data tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        /* ── BADGES ──────────────────────────────── */
        .badge {
            display: inline-block;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: 600;
        }
        .badge-active   { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #f3f4f6; color: #374151; }
        .badge-access   { background: #dbeafe; color: #1e40af; }
        .badge-primary  { background: #eef2ff; color: #3730a3; }
        .badge-secondary{ background: #fef9c3; color: #854d0e; }
        .badge-emergency{ background: #fee2e2; color: #991b1b; }

        /* ── FOOTER ──────────────────────────────── */
        .footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            border-top: 1px solid #e5e7eb;
            padding: 6px 0;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            background: #fff;
        }

        /* ── EMPTY STATE ─────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 40px 0;
            color: #9ca3af;
            font-size: 12px;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>LAPORAN RUTE EVAKUASI</h1>
        <h2>Sistem Informasi Geospasial Bencana (GISCANA)</h2>
        <div class="meta">
            Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }} WIB
        </div>
        <div class="badge-kecamatan">
            Kecamatan: {{ $districtName }}
        </div>
    </div>

    {{-- SUMMARY --}}
    @php
        $totalAll      = $routes->count();
        $totalAktif    = $routes->where('is_active', true)->count();
        $totalPrimary  = $routes->where('route_type', 'primary')->count();
        $totalDarurat  = $routes->where('route_type', 'emergency')->count();
    @endphp
    <table class="summary">
        <tr>
            <td style="width:25%; padding:0 5px 0 0;">
                <div class="summary-box" style="background:#eef2ff; border:1px solid #c7d2fe;">
                    <div class="num" style="color:#4338ca;">{{ $totalAll }}</div>
                    <div class="lbl">Total Rute</div>
                </div>
            </td>
            <td style="width:25%; padding:0 5px;">
                <div class="summary-box" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <div class="num" style="color:#166534;">{{ $totalAktif }}</div>
                    <div class="lbl">Rute Aktif</div>
                </div>
            </td>
            <td style="width:25%; padding:0 5px;">
                <div class="summary-box" style="background:#eff6ff; border:1px solid #bfdbfe;">
                    <div class="num" style="color:#1e40af;">{{ $totalPrimary }}</div>
                    <div class="lbl">Rute Utama</div>
                </div>
            </td>
            <td style="width:25%; padding:0 0 0 5px;">
                <div class="summary-box" style="background:#fff1f2; border:1px solid #fecdd3;">
                    <div class="num" style="color:#991b1b;">{{ $totalDarurat }}</div>
                    <div class="lbl">Rute Darurat</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- TABLE --}}
    @if($routes->isEmpty())
        <div class="empty-state">Tidak ada data rute evakuasi untuk ditampilkan.</div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th style="width:4%;">No</th>
                    <th style="width:22%;">Nama Rute</th>
                    <th style="width:20%;">Fasilitas Tujuan</th>
                    <th style="width:16%;">Kecamatan</th>
                    <th style="width:12%;">Tipe Rute</th>
                    <th style="width:26%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($routes as $i => $route)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $route->name ?? '-' }}</td>
                    <td>{{ $route->nama_fasilitas ?? $route->evacuationFacility?->name ?? '-' }}</td>
                    <td>{{ $route->evacuationFacility?->district_name ?? '-' }}</td>
                    <td>
                        @if($route->route_type === 'primary')
                            <span class="badge badge-primary">Utama</span>
                        @elseif($route->route_type === 'secondary')
                            <span class="badge badge-secondary">Sekunder</span>
                        @else
                            <span class="badge badge-emergency">Darurat</span>
                        @endif
                    </td>
                    <td>
                        @if($route->is_active)
                            <span class="badge badge-active">Aktif</span>
                        @else
                            <span class="badge badge-inactive">Tidak Aktif</span>
                        @endif
                        @if($route->is_accessible)
                            <span class="badge badge-access">Aksesibel</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        GISCANA &mdash; Sistem Informasi Geospasial Bencana &nbsp;|&nbsp; Halaman <span style="font-weight:600;">{{ 1 }}</span>
    </div>

</body>
</html>
