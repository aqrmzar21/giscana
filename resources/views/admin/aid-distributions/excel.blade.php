<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Catatan Distribusi Bantuan</title>
    <style>
        table { border-collapse: collapse; width: 100%; font-family: sans-serif; font-size: 11px; }
        th { background-color: #4338ca; color: #ffffff; font-weight: bold; border: 1px solid #312e81; padding: 8px; text-align: left; }
        td { border: 1px solid #d1d5db; padding: 6px 8px; vertical-align: top; }
        .title { font-size: 16px; font-weight: bold; text-align: center; color: #1e1b4b; height: 35px; }
        .subtitle { font-size: 11px; text-align: center; color: #4338ca; height: 25px; }
        .number { mso-number-format: "\@"; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="10" class="title" style="text-align: center; font-size: 16px; font-weight: bold;">LAPORAN CATATAN DISTRIBUSI BANTUAN LOGISTIK</td>
        </tr>
        <tr>
            <td colspan="10" class="subtitle" style="text-align: center; font-size: 11px; color: #4338ca;">Sistem Informasi Geospasial Bencana (GISCANA) &bull; Tanggal Export: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY HH:mm') }} WIB</td>
        </tr>
        <tr><td colspan="10"></td></tr>
        <thead>
            <tr>
                <th style="width: 50px; text-align: center; background-color: #4338ca; color: #ffffff; font-weight: bold;">No</th>
                <th style="width: 110px; background-color: #4338ca; color: #ffffff; font-weight: bold;">Tanggal</th>
                <th style="width: 220px; background-color: #4338ca; color: #ffffff; font-weight: bold;">Nama Penerima (KK)</th>
                <th style="width: 170px; background-color: #4338ca; color: #ffffff; font-weight: bold;">NIK / No Identitas</th>
                <th style="width: 150px; background-color: #4338ca; color: #ffffff; font-weight: bold;">Kecamatan</th>
                <th style="width: 160px; background-color: #4338ca; color: #ffffff; font-weight: bold;">Desa</th>
                <th style="width: 180px; background-color: #4338ca; color: #ffffff; font-weight: bold;">Barang Logistik</th>
                <th style="width: 130px; background-color: #4338ca; color: #ffffff; font-weight: bold;">Kategori</th>
                <th style="width: 110px; text-align: right; background-color: #4338ca; color: #ffffff; font-weight: bold;">Jumlah (Unit)</th>
                <th style="width: 160px; background-color: #4338ca; color: #ffffff; font-weight: bold;">Petugas / Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distributions as $i => $dist)
            <tr>
                <td style="text-align: center;">{{ $i + 1 }}</td>
                <td>{{ $dist->distribution_date ? \Carbon\Carbon::parse($dist->distribution_date)->format('d/m/Y') : '-' }}</td>
                <td><strong>{{ $dist->beneficiary?->recipient_name ?? '-' }}</strong></td>
                <td class="number">'{{ $dist->beneficiary?->identity_card_number ?? '-' }}</td>
                <td>{{ $dist->beneficiary?->district?->name ?? $dist->aidDisaster?->district_name ?? '-' }}</td>
                <td>{{ $dist->beneficiary?->village?->yard ?? $dist->village?->yard ?? $dist->village?->name ?? '-' }}</td>
                <td>{{ $dist->aidInventory?->item_name ?? '-' }}</td>
                <td>{{ $dist->aidInventory?->category ?? '-' }}</td>
                <td style="text-align: right;"><strong>{{ number_format($dist->quantity_received) }}</strong></td>
                <td>{{ $dist->user?->name ?? 'System' }} {{ $dist->description ? "({$dist->description})" : '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
