<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Warga Penerima Bantuan</title>
    <style>
        table { border-collapse: collapse; width: 100%; font-family: sans-serif; font-size: 11px; }
        th { background-color: #4338ca; color: #ffffff; font-weight: bold; border: 1px solid #312e81; padding: 8px; text-align: left; }
        td { border: 1px solid #d1d5db; padding: 6px 8px; vertical-align: top; }
        .title { font-size: 16px; font-weight: bold; text-align: center; color: #1e1b4b; height: 35px; }
        .subtitle { font-size: 11px; text-align: center; color: #4338ca; height: 25px; }
        .status-received { color: #15803d; font-weight: bold; }
        .status-pending { color: #c2410c; }
        .number { mso-number-format: "\@"; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="7" class="title" style="text-align: center; font-size: 16px; font-weight: bold;">LAPORAN DATA WARGA PENERIMA BANTUAN</td>
        </tr>
        <tr>
            <td colspan="7" class="subtitle" style="text-align: center; font-size: 11px; color: #4338ca;">Sistem Informasi Geospasial Bencana (GISCANA) &bull; Tanggal Export: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY HH:mm') }} WIB</td>
        </tr>
        <tr><td colspan="7"></td></tr>
        <thead>
            <tr>
                <th style="width: 50px; text-align: center; background-color: #4338ca; color: #ffffff; font-weight: bold;">No</th>
                <th style="width: 220px; background-color: #4338ca; color: #ffffff; font-weight: bold;">Nama Kepala Keluarga (KK)</th>
                <th style="width: 170px; background-color: #4338ca; color: #ffffff; font-weight: bold;">NIK / No Identitas</th>
                <th style="width: 150px; background-color: #4338ca; color: #ffffff; font-weight: bold;">Kecamatan</th>
                <th style="width: 160px; background-color: #4338ca; color: #ffffff; font-weight: bold;">Desa</th>
                <th style="width: 250px; background-color: #4338ca; color: #ffffff; font-weight: bold;">Alamat Detail</th>
                <th style="width: 140px; background-color: #4338ca; color: #ffffff; font-weight: bold;">Status Penyaluran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($beneficiaries as $i => $b)
            <tr>
                <td style="text-align: center;">{{ $i + 1 }}</td>
                <td><strong>{{ $b->recipient_name }}</strong></td>
                <td class="number">'{{ $b->identity_card_number ?? '-' }}</td>
                <td>{{ $b->district?->name ?? '-' }}</td>
                <td>{{ $b->village?->full_name ?? $b->village?->yard ?? $b->village?->name ?? '-' }}</td>
                <td>{{ $b->address_detail ?? '-' }}</td>
                <td>
                    @if($b->aid_status === 'received')
                        <span class="status-received">Sudah Menerima</span>
                    @else
                        <span class="status-pending">Belum Menerima</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
