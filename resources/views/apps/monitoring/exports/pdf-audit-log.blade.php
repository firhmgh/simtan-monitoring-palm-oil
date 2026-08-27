<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Audit Trail SIMTAN</title>
    <style>
        /* ================================================
         * SIMTAN PDF AUDIT TRAIL TEMPLATE
         * Orientasi: Landscape A4 | Standar Dokumen Resmi
         * Branding: Hijau PTPN IV (#00a76f)
         * ================================================ */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8pt;
            color: #1a202c;
            background: #ffffff;
        }

        /* ------ KOP DOKUMEN (Header Resmi) ------ */
        .kop {
            display: table;
            width: 100%;
            border-bottom: 3px solid #00a76f;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .kop-logo { display: table-cell; width: 80px; vertical-align: middle; }
        .kop-logo img { width: 65px; height: auto; }
        .kop-text { display: table-cell; vertical-align: middle; padding-left: 12px; }
        .kop-text .instansi {
            font-size: 9pt;
            font-weight: bold;
            color: #00a76f;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .kop-text .judul-doc {
            font-size: 14pt;
            font-weight: bold;
            color: #1a202c;
            letter-spacing: 1px;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .kop-text .sub-judul {
            font-size: 7.5pt;
            color: #4a5568;
            margin-top: 2px;
        }
        .kop-meta {
            display: table-cell;
            width: 220px;
            vertical-align: bottom;
            text-align: right;
        }
        .kop-meta table { width: 100%; font-size: 7pt; color: #4a5568; }
        .kop-meta td { padding: 1px 0; }
        .kop-meta td:first-child { font-weight: bold; color: #00a76f; padding-right: 6px; }

        /* ------ PANEL RINGKASAN STATISTIK ------ */
        .summary-bar {
            display: table;
            width: 100%;
            margin-bottom: 14px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }
        .summary-cell {
            display: table-cell;
            text-align: center;
            padding: 8px 10px;
            border-radius: 6px;
            vertical-align: middle;
        }
        /* Warna utama diganti ke Hijau PTPN (#00a76f) untuk konsistensi branding */
        .summary-cell.blue { background: #e6f7f1; border: 1px solid #6dd4b0; }
        .summary-cell.green { background: #f0fff4; border: 1px solid #9ae6b4; }
        .summary-cell.red { background: #fff5f5; border: 1px solid #fed7d7; }
        .summary-cell.gray { background: #f7fafc; border: 1px solid #e2e8f0; }
        .summary-label {
            font-size: 6.5pt;
            color: #718096;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-value {
            font-size: 18pt;
            font-weight: bold;
            line-height: 1.1;
            margin-top: 2px;
        }
        .summary-value.blue { color: #00a76f; }
        .summary-value.green { color: #276749; }
        .summary-value.red { color: #c53030; }
        .summary-value.gray { color: #2d3748; }

        /* ------ TABEL DATA UTAMA ------ */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        /* Header tabel menggunakan warna Hijau PTPN sebagai identitas visual utama */
        table.main-table thead tr {
            background: #00a76f;
            color: #ffffff;
        }
        table.main-table thead th {
            padding: 7px 6px;
            text-align: left;
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            border: 1px solid #00a76f;
        }
        table.main-table thead th.center { text-align: center; }
        table.main-table tbody tr:nth-child(even) { background: #f7fafc; }
        table.main-table tbody tr:nth-child(odd) { background: #ffffff; }
        table.main-table tbody td {
            padding: 5px 6px;
            font-size: 7.5pt;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
            color: #2d3748;
        }
        table.main-table tbody td.center { text-align: center; }
        table.main-table tbody td.no { text-align: center; color: #718096; font-weight: bold; width: 28px; }

        /* Status Badge */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 6.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .badge-success { background: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
        .badge-danger { background: #fed7d7; color: #742a2a; border: 1px solid #fc8181; }

        /* File name cell */
        /* Nama file menggunakan monospace agar lebih mudah dibaca pada audit log */
        .file-name { font-family: 'DejaVu Sans Mono', monospace; font-size: 7pt; color: #2d3748; }
        .file-uid { font-size: 6pt; color: #a0aec0; margin-top: 1px; }

        /* ------ FOOTER HALAMAN ------ */
        .footer {
            position: fixed;
            bottom: 8mm;
            left: 0;
            right: 0;
            border-top: 1.5px solid #00a76f;
            padding-top: 5px;
            font-size: 6.5pt;
            color: #718096;
            display: table;
            width: 100%;
        }
        .footer-left { display: table-cell; text-align: left; }
        .footer-right { display: table-cell; text-align: right; }

        /* ------ TANDA TANGAN ------ */
        .ttd-section {
            margin-top: 20px;
            display: table;
            width: 100%;
        }
        .ttd-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 0 10px;
        }
        .ttd-label {
            font-size: 7.5pt;
            color: #4a5568;
            margin-bottom: 55px;
            font-weight: bold;
        }
        .ttd-garis {
            border-top: 1px solid #718096;
            padding-top: 4px;
            font-size: 7pt;
            color: #2d3748;
            font-weight: bold;
        }
        .ttd-jabatan {
            font-size: 6.5pt;
            color: #718096;
            margin-top: 2px;
        }

        @page {
            size: A4 landscape;
            margin: 12mm 14mm 18mm 14mm;
        }
    </style>
</head>
<body>

    {{-- ============================================================ --}}
    {{-- KOP SURAT RESMI PTPN IV --}}
    {{-- Logo dimuat menggunakan public_path() agar DomPDF dapat membaca --}}
    {{-- file gambar dari sistem file secara langsung (bukan via HTTP). --}}
    {{-- ============================================================ --}}
    <div class="kop">
        <div class="kop-logo">
            <img src="{{ public_path('assets/images/logo-ptpn4.png') }}" alt="Logo PTPN IV">
        </div>
        <div class="kop-text">
            <div class="instansi">PT. Perkebunan Nusantara IV — Regional I</div>
            <div class="judul-doc">Laporan Audit Trail Sistem</div>
            <div class="sub-judul">
                SIMTAN — Sistem Informasi Monitoring Areal Tanaman | Log Aktivitas Ingesti Data
            </div>
        </div>
        <div class="kop-meta">
            <table>
                <tr>
                    <td>No. Dokumen</td>
                    <td>: SIM-AT-{{ now()->format('Ym') }}</td>
                </tr>
                <tr>
                    <td>Tanggal Cetak</td>
                    <td>: {{ $summary['tgl_cetak'] }}</td>
                </tr>
                <tr>
                    <td>Dicetak Oleh</td>
                    <td>: {{ $summary['penginput'] }}</td>
                </tr>
                <tr>
                    <td>Klasifikasi</td>
                    <td>: INTERNAL — TERBATAS</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- PANEL RINGKASAN STATISTIK --}}
    {{-- ============================================================ --}}
    <div class="summary-bar">
        {{-- Panel Hijau PTPN: Total seluruh aktivitas ingesti --}}
        <div class="summary-cell blue">
            <div class="summary-label">Total Aktivitas</div>
            <div class="summary-value blue">{{ $summary['total'] }}</div>
        </div>
        {{-- Panel Sukses --}}
        <div class="summary-cell green">
            <div class="summary-label">Sukses / Sinkron</div>
            <div class="summary-value green">{{ $summary['sukses'] }}</div>
        </div>
        {{-- Panel Gagal --}}
        <div class="summary-cell red">
            <div class="summary-label">Gagal / Anomali</div>
            <div class="summary-value red">{{ $summary['gagal'] }}</div>
        </div>
        {{-- Panel Persentase Keberhasilan --}}
        <div class="summary-cell gray">
            <div class="summary-label">Tingkat Keberhasilan</div>
            <div class="summary-value gray">
                {{ $summary['total'] > 0 ? number_format(($summary['sukses'] / $summary['total']) * 100, 1) : '0.0' }}%
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL LOG DATA UTAMA --}}
    {{-- ============================================================ --}}
    <table class="main-table">
        <thead>
            <tr>
                <th class="center" style="width:28px;">No</th>
                <th class="center" style="width:45px;">ID Log</th>
                <th style="width:90px;">Timestamp</th>
                <th style="width:160px;">Nama Berkas</th>
                <th style="width:90px;">Pengunggah</th>
                <th style="width:95px;">Jenis Dataset</th>
                <th class="center" style="width:60px;">Volume Baris</th>
                <th class="center" style="width:65px;">Status</th>
                <th>Keterangan / Log Sistem</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $i => $log)
            <tr>
                <td class="no">{{ $i + 1 }}</td>
                {{-- ID Log menggunakan warna Hijau PTPN untuk konsistensi branding --}}
                <td class="center" style="font-weight:bold; color:#00a76f;">#{{ $log['id'] }}</td>
                <td style="color:#718096; font-size:7pt;">{{ $log['tglUpload'] }}</td>
                <td>
                    <div class="file-name">{{ Str::limit($log['namaFile'], 35) }}</div>
                    <div class="file-uid">UID-LOG-{{ str_pad($log['id'], 6, '0', STR_PAD_LEFT) }}</div>
                </td>
                <td style="font-weight:bold;">{{ $log['pengunggah'] }}</td>
                <td>
                    {{-- Badge jenis dataset dengan warna Hijau PTPN --}}
                    <span style="background:#e6f7f1; color:#00a76f; padding:1px 6px; border-radius:3px; font-size:7pt; font-weight:bold; border:1px solid #6dd4b0;">
                        {{ $log['jenisDataset'] }}
                    </span>
                </td>
                <td class="center" style="font-weight:bold;">
                    {{ number_format($log['baris']) }}
                </td>
                <td class="center">
                    @if($log['status'] === 'Sukses')
                        <span class="badge badge-success">✓ Sukses</span>
                    @else
                        <span class="badge badge-danger">✗ Gagal</span>
                    @endif
                </td>
                <td style="font-size:7pt; color:#4a5568;">
                    {{ Str::limit($log['keterangan'], 80) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="center" style="padding: 30px; color: #a0aec0; font-style: italic;">
                    Belum ada data aktivitas yang tercatat dalam sistem.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ============================================================ --}}
    {{-- TANDA TANGAN & PENGESAHAN --}}
    {{-- ============================================================ --}}
    <div class="ttd-section">
        <div class="ttd-box">
            <div class="ttd-label">Diketahui Oleh,</div>
            <div class="ttd-garis" style="border-top: 1px solid #00a76f;">_________________________________</div>
            <div class="ttd-jabatan">Asisten Investasi &amp; Pemetaan</div>
        </div>
        <div class="ttd-box">
            <div class="ttd-label">Dicetak Oleh,</div>
            <div class="ttd-garis">{{ $summary['penginput'] }}</div>
            <div class="ttd-jabatan">Administrator Sistem SIMTAN</div>
        </div>
    </div>

    {{-- Footer Halaman --}}
    <div class="footer">
        <div class="footer-left">
            SIMTAN — Sistem Informasi Monitoring Areal Tanaman | PTPN IV Regional I | DOKUMEN INTERNAL TERBATAS
        </div>
        <div class="footer-right">
            Dicetak pada {{ $summary['tgl_cetak'] }} | Halaman <span class="pagenum"></span>
        </div>
    </div>

</body>
</html>
