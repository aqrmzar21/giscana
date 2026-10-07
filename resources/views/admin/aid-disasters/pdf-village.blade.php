<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Rincian Penyaluran Logistik per Desa</title>
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
            border-bottom: 3px solid #059669;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header h1 {
            font-size: 15px;
            font-weight: 800;
            color: #064e3b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 12px;
            font-weight: 600;
            color: #059669;
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
            font-size: 18px;
            font-weight: 800;
        }
        .summary-box .lbl {
            font-size: 9px;
            color: #4b5563;
            margin-top: 3px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .district-header {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-left: 5px solid #059669;
            padding: 8px 12px;
            margin-top: 15px;
            margin-bottom: 8px;
            border-radius: 4px;
        }
        .district-header h3 {
            font-size: 12px;
            font-weight: bold;
            color: #064e3b;
        }
        .district-header .subtitle {
            font-size: 9px;
            color: #047857;
            margin-top: 2px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-bottom: 18px;
        }
        table.data thead tr {
            background: #059669;
            color: #ffffff;
        }
        table.data thead th {
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        table.data tbody tr:nth-child(even) { background: #f8fafc; }
        table.data tbody tr:nth-child(odd)  { background: #ffffff; }
        table.data tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .item-tag {
            display: inline-block;
            background: #e0e7ff;
            color: #3730a3;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8.5px;
            font-weight: 600;
            margin-right: 3px;
            margin-bottom: 2px;
        }
        .empty-row {
            padding: 12px;
            text-align: center;
            color: #9ca3af;
            font-style: italic;
        }
        .signature-section {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signature-box {
            text-align: center;
            width: 40%;
            font-size: 10px;
        }
        .signature-space {
            height: 55px;
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

    @php
        $singleDisaster = ($disasterId && $aidDisasters->count() === 1) ? $aidDisasters->first() : null;
        $titleScope = $singleDisaster ? 'KECAMATAN ' . strtoupper($singleDisaster->district_name) : 'SEMUA KECAMATAN';
        
        // Hitung total penerima & desa terjangkau
        $totalDesaCount = 0;
        foreach($aidDisasters as $aid) {
            $vList = $villageBreakdown[$aid->id]['villages'] ?? [];
            $totalDesaCount += count($vList);
        }
    @endphp

    <div class="header">
        <h1>LAPORAN RINCIAN PENYALURAN LOGISTIK BANTUAN BENCANA PER DESA</h1>
        <h2>BADAN PENANGGULANGAN BENCANA DAERAH (BPBD) KABUPATEN BONE BOLANGO</h2>
        <div class="meta">
            Cakupan Wilayah: <strong>{{ $titleScope }}</strong> &bull; Filter Tahun: <strong>{{ $selectedYear !== 'all' ? $selectedYear : 'Semua Tahun' }}</strong> &bull; Dicetak: {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }} WITA
        </div>
    </div>

    <table class="summary">
        <tr>
            <td style="width:25%; padding:0 5px 0 0;">
                <div class="summary-box" style="background:#ecfdf5; border:1px solid #a7f3d0;">
                    <div class="num" style="color:#047857;">{{ number_format($aidDisasters->count()) }}</div>
                    <div class="lbl">Kecamatan</div>
                </div>
            </td>
            <td style="width:25%; padding:0 5px;">
                <div class="summary-box" style="background:#eef2ff; border:1px solid #c7d2fe;">
                    <div class="num" style="color:#3730a3;">{{ number_format($totalDesaCount) }}</div>
                    <div class="lbl">Desa / Kelurahan</div>
                </div>
            </td>
            <td style="width:25%; padding:0 5px;">
                <div class="summary-box" style="background:#eff6ff; border:1px solid #bfdbfe;">
                    <div class="num" style="color:#1d4ed8;">{{ number_format($totalRecipientsCount ?? 0) }}</div>
                    <div class="lbl">Total KK Penerima</div>
                </div>
            </td>
            <td style="width:25%; padding:0 0 0 5px;">
                <div class="summary-box" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <div class="num" style="color:#166534;">{{ number_format($totalDistributedSum ?? 0) }}</div>
                    <div class="lbl">Logistik Tersalur (Unit)</div>
                </div>
            </td>
        </tr>
    </table>

    @foreach($aidDisasters as $aid)
        @php
            $villages = $villageBreakdown[$aid->id]['villages'] ?? [];
            $vCount = count($villages);
            $yearUnits = $villageBreakdown[$aid->id]['year_received'] ?? 0;
            $yearRecipients = $villageBreakdown[$aid->id]['year_recipients'] ?? 0;
        @endphp

        <div class="district-header">
            <h3>Kecamatan {{ $aid->district_name }}</h3>
            <div class="subtitle">
                Total Desa Terdata: <strong>{{ $vCount }} Desa</strong> &bull; Total Logistik Tersalur: <strong>{{ number_format($yearUnits) }} Unit</strong> &bull; Total Penerima: <strong>{{ number_format($yearRecipients) }} KK</strong>
            </div>
        </div>

        @if($vCount === 0)
            <div class="empty-row">
                Tidak ada data transaksi penyaluran logistik desa terdaftar di Kecamatan {{ $aid->district_name }} {{ $selectedYear !== 'all' ? 'pada tahun ' . $selectedYear : '' }}.
            </div>
        @else
            <table class="data">
                <thead>
                    <tr>
                        <th style="width:6%; text-align:center;">No</th>
                        <th style="width:28%;">Nama Desa / Kelurahan</th>
                        <th style="width:18%; text-align:center;">Jumlah Penerima (KK)</th>
                        <th style="width:20%; text-align:center;">Total Barang Tersalur</th>
                        <th style="width:28%;">Kategori & Jenis Barang Bantuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNo = 1; @endphp
                    @foreach($villages as $vName => $records)
                        @php
                            $recipients = $records->pluck('beneficiary_id')->unique()->count();
                            $qty = $records->sum('quantity_received');
                            $itemNames = $records->pluck('aidInventory.item_name')->filter()->unique();
                        @endphp
                        <tr>
                            <td style="text-align:center;">{{ $rowNo++ }}</td>
                            <td><strong>{{ $vName }}</strong></td>
                            <td style="text-align:center; font-weight:bold;">{{ number_format($recipients) }} KK</td>
                            <td style="text-align:center; color:#047857; font-weight:bold;">{{ number_format($qty) }} Unit</td>
                            <td>
                                @foreach($itemNames as $itemName)
                                    <span class="item-tag">{{ $itemName }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

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
        GISCANA &mdash; Sistem Informasi Geospasial Bencana BPBD Kabupaten Bone Bolango &nbsp;|&nbsp; Dokumen Laporan Rincian Penerima Desa
    </div>

</body>
</html>
