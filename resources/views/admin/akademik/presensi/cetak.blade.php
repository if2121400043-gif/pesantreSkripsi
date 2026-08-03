<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi Santri Admin - PP Nurul Furqon</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.4;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px;
        }
        /* Kop Surat Official */
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-surat .nama-pesantren {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-surat .sub-nama {
            font-size: 12pt;
            font-weight: bold;
        }
        .kop-surat .alamat {
            font-size: 9pt;
            font-style: italic;
            margin-top: 3px;
        }
        .judul-dokumen {
            text-align: center;
            margin-bottom: 20px;
        }
        .judul-dokumen h2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            text-decoration: underline;
        }
        .judul-dokumen p {
            font-size: 10pt;
            margin: 3px 0 0 0;
        }
        /* Section Block */
        .jadwal-block {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .jadwal-title {
            font-size: 11pt;
            font-weight: bold;
            background: #f1f5f9;
            padding: 6px 10px;
            border: 1px solid #000;
            border-bottom: none;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            text-align: left;
        }
        table.data-table th {
            background-color: #e2e8f0;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9pt;
        }
        table.data-table td.text-center {
            text-align: center;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            font-size: 10.5pt;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
            width: 50%;
        }
        .signature-space {
            height: 65px;
        }
        .no-print {
            background: #f8fafc;
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-print {
            background: #047857;
            color: #fff;
            border: none;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        @media print {
            .no-print { display: none !important; }
            .container { max-width: 100% !important; padding: 0 !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            🖨️ Cetak Sekarang / Simpan PDF
        </button>
        <button class="btn-print" style="background-color: #475569;" onclick="window.close()">
            ✖️ Tutup Halaman
        </button>
    </div>

    <div class="container">
        
        {{-- Kop Surat --}}
        <div class="kop-surat">
            <div class="nama-pesantren">Pondok Pesantren Nurul Furqon</div>
            <div class="sub-nama">Yayasan Nurul Furqon Wakatobi</div>
            <div class="alamat">Jln. KH. Zaini Mun'im, Desa Timu, Kec. Tomia Timur, Kab. Wakatobi, Sulawesi Tenggara</div>
        </div>

        {{-- Judul --}}
        <div class="judul-dokumen">
            <h2>LAPORAN REKAPITULASI PRESENSI SANTRI (ADMINISTRATOR)</h2>
            <p>Periode: <strong>{{ $periodeLabel }}</strong></p>
        </div>

        @forelse($jadwals as $jadwal)
            @php
                $pesertaList = $jadwal->rombel->riwayatPeserta->map->pesertaDidik;
            @endphp
            <div class="jadwal-block">
                <div class="jadwal-title">
                    📖 {{ $jadwal->mataPelajaran->nama }} | 🏫 {{ str_starts_with(strtolower($jadwal->rombel->nama ?? ''), 'kelas') ? $jadwal->rombel->nama : 'Kelas ' . ($jadwal->rombel->nama ?? '-') }} | 👨‍🏫 Guru: {{ $jadwal->guru->orang->nama_lengkap ?? '-' }} | ⏰ {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WITA
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">No</th>
                            <th style="width: 80px;">NISN</th>
                            <th>Nama Santri</th>
                            <th style="width: 35px;">H</th>
                            <th style="width: 35px;">S</th>
                            <th style="width: 35px;">I</th>
                            <th style="width: 35px;">A</th>
                            <th style="width: 65px;">% Hadir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesertaList as $index => $peserta)
                            @php
                                $hCount = 0; $sCount = 0; $iCount = 0; $aCount = 0;
                                foreach ($dateList as $d) {
                                    $p = $presensiData[$peserta->id][$d][0] ?? null;
                                    if ($p) {
                                        $st = strtoupper($p->status);
                                        if ($st === 'HADIR') $hCount++;
                                        elseif ($st === 'SAKIT') $sCount++;
                                        elseif ($st === 'IZIN') $iCount++;
                                        elseif ($st === 'ALPA' || $st === 'ALPHA') $aCount++;
                                    }
                                }
                                $totalPertemuan = count($dateList);
                                $persen = $totalPertemuan > 0 ? round(($hCount / $totalPertemuan) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $peserta->nisn ?? '-' }}</td>
                                <td><strong>{{ $peserta->orang->nama ?? '-' }}</strong></td>
                                <td class="text-center">{{ $hCount }}</td>
                                <td class="text-center">{{ $sCount }}</td>
                                <td class="text-center">{{ $iCount }}</td>
                                <td class="text-center">{{ $aCount }}</td>
                                <td class="text-center"><strong>{{ $persen }}%</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada santri aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @empty
            <p style="text-align: center;">Tidak ada data presensi yang sesuai dengan filter.</p>
        @endforelse

        {{-- Signatures --}}
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Kepala Sekolah / Pengasuh</strong>
                    <div class="signature-space"></div>
                    ( .................................................... )
                </td>
                <td>
                    Tomia Timur, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}<br>
                    <strong>Administrator Pesantren</strong>
                    <div class="signature-space"></div>
                    <strong><u>{{ auth()->user()->orang->nama_lengkap ?? auth()->user()->name }}</u></strong>
                </td>
            </tr>
        </table>

    </div>

</body>
</html>
