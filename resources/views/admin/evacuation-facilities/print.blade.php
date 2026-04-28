<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Fasilitas Evakuasi</title>
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
        .header .logo-area {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 6px;
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
        .summary-row {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }
        .summary-box {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            text-align: center;
        }
        .summary-box .num {
            font-size: 20px;
            font-weight: 700;
            color: #4338ca;
        }
        .summary-box .lbl {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── TABLE ───────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        thead tr {
            background: #4338ca;
            color: #fff;
        }
        thead th {
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        tbody tr:nth-child(even) { background: #f5f7ff; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody tr:hover            { background: #eef2ff; }
        tbody td {
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

        /* ── FOOTER ──────────────────────────────── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #e5e7eb;
            padding: 6px 0;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            background: #fff;
        }
        .page-num:after { content: counter(page); }

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
        <h1>LAPORAN FASILITAS EVAKUASI</h1>
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
        $totalAll    = $facilities->count();
        $totalAktif  = $facilities->where('is_active', true)->count();
        $totalAkses  = $facilities->where('is_accessible', true)->count();
    @endphp
    <table style="width:100%; margin-bottom:16px; border-collapse:collapse;">
        <tr>
            <td style="width:33%; padding:0 5px 0 0;">
                <div style="background:#eef2ff; border:1px solid #c7d2fe; border-radius:6px; padding:8px 12px; text-align:center;">
                    <div style="font-size:20px; font-weight:700; color:#4338ca;">{{ $totalAll }}</div>
                    <div style="font-size:9px; color:#6b7280; margin-top:2px;">Total Fasilitas</div>
                </div>
            </td>
            <td style="width:33%; padding:0 5px;">
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:8px 12px; text-align:center;">
                    <div style="font-size:20px; font-weight:700; color:#166534;">{{ $totalAktif }}</div>
                    <div style="font-size:9px; color:#6b7280; margin-top:2px;">Fasilitas Aktif</div>
                </div>
            </td>
            <td style="width:33%; padding:0 0 0 5px;">
                <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; padding:8px 12px; text-align:center;">
                    <div style="font-size:20px; font-weight:700; color:#1e40af;">{{ $totalAkses }}</div>
                    <div style="font-size:9px; color:#6b7280; margin-top:2px;">Aksesibel Disabilitas</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- TABLE --}}
    @if($facilities->isEmpty())
        <div class="empty-state">Tidak ada data fasilitas evakuasi untuk ditampilkan.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:4%;">No</th>
                    <th style="width:22%;">Nama Fasilitas</th>
                    <th style="width:14%;">Kecamatan</th>
                    <th style="width:25%;">Alamat</th>
                    <th style="width:10%;">Kapasitas</th>
                    <th style="width:12%;">Kontak</th>
                    <th style="width:13%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facilities as $i => $facility)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $facility->name ?? '-' }}</td>
                    <td>{{ $facility->district_name ?? $facility->aidDisaster?->district_name ?? '-' }}</td>
                    <td>{{ $facility->address ?? '-' }}</td>
                    <td>{{ $facility->capacity ? number_format($facility->capacity) . ' org' : '-' }}</td>
                    <td>
                        @if($facility->contact_person)
                            {{ $facility->contact_person }}
                            @if($facility->contact_phone)
                                <br><span style="color:#6b7280;">{{ $facility->contact_phone }}</span>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($facility->is_active)
                            <span class="badge badge-active">Aktif</span>
                        @else
                            <span class="badge badge-inactive">Tidak Aktif</span>
                        @endif
                        @if($facility->is_accessible)
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
        GISCANA &mdash; Sistem Informasi Geospasial Bencana &nbsp;|&nbsp;
        Halaman <span class="page-num"></span>
    </div>

</body>
</html>
