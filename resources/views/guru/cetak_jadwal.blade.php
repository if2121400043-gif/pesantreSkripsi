<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Jadwal Mengajar — {{ $pegawai->orang->nama_lengkap ?? 'Guru' }}</title>
    <style>
        /* Reset & Base */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
            line-height: 1.5;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 20mm 20mm 20mm;
            position: relative;
        }

        /* Kop Surat */
        .kop-surat {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 3px double #000;
            margin-bottom: 20px;
        }
        .kop-surat .nama-pesantren {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .kop-surat .sub-nama {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 2px;
        }
        .kop-surat .alamat {
            font-size: 9pt;
            color: #333;
            margin-top: 4px;
        }

        /* Judul Dokumen */
        .judul-dokumen {
            text-align: center;
            margin: 25px 0 15px 0;
        }
        .judul-dokumen h2 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-decoration: underline;
        }

        /* Info Guru */
        .info-guru {
            margin-bottom: 15px;
        }
        .info-guru table {
            border: none;
            border-collapse: collapse;
        }
        .info-guru td {
            padding: 2px 0;
            font-size: 11pt;
            border: none;
        }
        .info-guru td.label {
            width: 160px;
            font-weight: bold;
        }
        .info-guru td.separator {
            width: 15px;
            text-align: center;
        }

        /* Tabel Jadwal */
        .tabel-jadwal {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 10.5pt;
        }
        .tabel-jadwal th,
        .tabel-jadwal td {
            border: 1px solid #000;
            padding: 6px 10px;
            text-align: left;
            vertical-align: middle;
        }
        .tabel-jadwal thead th {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 10pt;
            letter-spacing: 0.5px;
        }
        .tabel-jadwal td.no {
            text-align: center;
            width: 40px;
            font-weight: bold;
        }
        .tabel-jadwal td.hari {
            font-weight: bold;
            text-align: center;
            background-color: #f5f5f5;
            vertical-align: middle;
        }
        .tabel-jadwal td.jam {
            text-align: center;
            white-space: nowrap;
            font-weight: bold;
        }

        /* Footer */
        .footer-cetak {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .footer-cetak .tanggal {
            font-size: 10pt;
            color: #333;
        }
        .footer-cetak .ttd {
            text-align: center;
            width: 200px;
        }
        .footer-cetak .ttd .jabatan {
            font-size: 10pt;
            font-weight: bold;
        }
        .footer-cetak .ttd .garis {
            margin-top: 70px;
            border-bottom: 1px solid #000;
            width: 100%;
        }
        .footer-cetak .ttd .nama-ttd {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 4px;
        }

        /* Tombol Cetak (tidak tampil saat print) */
        .btn-cetak-container {
            text-align: center;
            margin: 20px 0;
        }
        .btn-cetak {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 32px;
            background: #1e1b4b;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: background 0.2s;
        }
        .btn-cetak:hover { background: #312e81; }
        .btn-kembali {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-kembali:hover { background: #e2e8f0; }

        /* Print Styles */
        @media print {
            body { background: none; }
            .page {
                margin: 0;
                padding: 10mm 15mm 15mm 15mm;
                width: 100%;
            }
            .btn-cetak-container { display: none !important; }
            .tabel-jadwal th { background-color: #e8e8e8 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .tabel-jadwal td.hari { background-color: #f5f5f5 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    {{-- Tombol Cetak & Kembali (hilang saat print) --}}
    <div class="btn-cetak-container">
        <a href="{{ route('guru.jadwal-mengajar') }}" class="btn-kembali">
            ← Kembali
        </a>
        <button class="btn-cetak" onclick="window.print()">
            🖨️ Cetak Sekarang
        </button>
    </div>

    <div class="page">
        {{-- Kop Surat --}}
        <div class="kop-surat">
            <div class="nama-pesantren">Pondok Pesantren Nurul Furqon</div>
            <div class="sub-nama">Yayasan Nurul Furqon</div>
            <div class="alamat">Jln. KH. Zaini Mun'im, Desa Timu, Kec. Tomia Timur, Kab. Wakatobi, Sulawesi Tenggara</div>
        </div>

        {{-- Judul Dokumen --}}
        <div class="judul-dokumen">
            <h2>Jadwal Mengajar</h2>
        </div>

        {{-- Info Guru --}}
        <div class="info-guru">
            <table>
                <tr>
                    <td class="label">Nama Guru / Pengajar</td>
                    <td class="separator">:</td>
                    <td><strong>{{ $pegawai->orang->nama_lengkap ?? auth()->user()->name }}</strong></td>
                </tr>
                <tr>
                    <td class="label">NIUP</td>
                    <td class="separator">:</td>
                    <td>{{ $pegawai->orang->niup ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Tahun Pelajaran</td>
                    <td class="separator">:</td>
                    <td>{{ \App\Models\TahunPelajaran::where('is_active', true)->first()->nama ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Tabel Jadwal Mengajar --}}
        <table class="tabel-jadwal">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 90px;">Hari</th>
                    <th style="width: 130px;">Jam Pelajaran</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 120px;">Kelas</th>
                </tr>
            </thead>
            <tbody>
                @php $nomor = 1; @endphp
                @foreach($hariOrder as $hari)
                    @if(isset($semuaJadwal[$hari]) && $semuaJadwal[$hari]->count() > 0)
                        @foreach($semuaJadwal[$hari] as $index => $jadwal)
                            <tr>
                                <td class="no">{{ $nomor++ }}</td>
                                @if($index === 0)
                                    <td class="hari" rowspan="{{ $semuaJadwal[$hari]->count() }}">{{ $hari }}</td>
                                @endif
                                <td class="jam">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                                <td>{{ $jadwal->mataPelajaran->nama ?? '-' }}</td>
                                <td>{{ $jadwal->rombel->nama ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>

        {{-- Footer: Tanggal & Tanda Tangan --}}
        <div class="footer-cetak">
            <div class="tanggal">
                Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
            <div class="ttd">
                <div class="jabatan">Mengetahui,</div>
                <div style="font-size: 10pt;">Pimpinan Pondok Pesantren</div>
                <div class="garis"></div>
                <div class="nama-ttd">( ...................................... )</div>
            </div>
        </div>

    </div>

</body>
</html>
