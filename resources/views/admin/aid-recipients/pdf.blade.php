<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Penerima Bantuan</title>
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
        .badge-type { background: #dbeafe; color: #1e40af; }

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
        <h1>LAPORAN PENERIMA BANTUAN</h1>
        <h2>Sistem Informasi Geospasial Bencana (GISCANA)</h2>
        <div class="meta">
            Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }} WIB
            @if(request('start_date') || request('end_date') || request('search'))
                <br>
                Filter: 
                @if(request('search')) Pencarian "{{ request('search') }}" @endif
                @if(request('start_date') && request('end_date'))
                    Tanggal {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                @elseif(request('start_date'))
                    Mulai Tanggal {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}
                @elseif(request('end_date'))
                    Hingga Tanggal {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                @endif
            @endif
        </div>
    </div>

    {{-- SUMMARY --}}
    @php
        $totalPenerima = $aidRecipients->count();
        $totalBantuan  = $aidRecipients->sum('amount');
        $totalJenis    = $aidRecipients->pluck('aid_type')->unique()->count();
    @endphp
    <table class="summary">
        <tr>
            <td style="width:33%; padding:0 5px 0 0;">
                <div class="summary-box" style="background:#eef2ff; border:1px solid #c7d2fe;">
                    <div class="num" style="color:#4338ca;">{{ number_format($totalPenerima, 0, ',', '.') }}</div>
                    <div class="lbl">Total Penerima</div>
                </div>
            </td>
            <td style="width:33%; padding:0 5px;">
                <div class="summary-box" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <div class="num" style="color:#166534;">{{ number_format($totalBantuan, 0, ',', '.') }}</div>
                    <div class="lbl">Total Penyaluran (Barang/Unit)</div>
                </div>
            </td>
            <td style="width:34%; padding:0 0 0 5px;">
                <div class="summary-box" style="background:#eff6ff; border:1px solid #bfdbfe;">
                    <div class="num" style="color:#1e40af;">{{ $totalJenis }}</div>
                    <div class="lbl">Jenis Bantuan</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- TABLE --}}
    @if($aidRecipients->isEmpty())
        <div class="empty-state">Tidak ada data penerima bantuan untuk ditampilkan.</div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:12%;">Tanggal</th>
                    <th style="width:25%;">Nama Penerima</th>
                    <th style="width:18%;">Jenis Bantuan</th>
                    <th style="width:15%;">Jumlah</th>
                    <th style="width:25%;">Desa/Kelurahan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($aidRecipients as $i => $recipient)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $recipient->date->format('d/m/Y') }}</td>
                    <td>{{ $recipient->recipient_name }}</td>
                    <td>
                        <span class="badge badge-type">{{ $recipient->aid_type }}</span>
                    </td>
                    <td>{{ number_format($recipient->amount, 0, ',', '.') }}</td>
                    <td>{{ $recipient->village->yard ?? $recipient->village->full_name ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        GISCANA &mdash; Sistem Informasi Geospasial Bencana &nbsp;|&nbsp; Halaman <span style="font-weight:600;">1</span>
    </div>

</body>
</html>
