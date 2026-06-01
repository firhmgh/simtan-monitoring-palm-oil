<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Monitoring TBM III - DSS</title>
    <style>
        @page {
            margin: 1.2cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000;
            line-height: 1.4;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .header {
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
            width: 100%;
        }

        .comp-name {
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
        }

        .comp-address {
            font-size: 9px;
            margin-top: 2px;
        }

        .report-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .report-title {
            font-weight: bold;
            font-size: 13px;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .report-period {
            font-style: italic;
            font-size: 10px;
            margin-top: 5px;
        }

        .section-header {
            font-weight: bold;
            text-transform: uppercase;
            margin: 25px 0 8px 0;
            border-left: 5px solid #00a76f;
            padding-left: 10px;
            font-size: 11px;
        }

        /* MATRIKS PERFORMANCE - MENGGUNAKAN TABEL AGAR STABIL 4 KOLOM */
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 20px;
        }

        .matrix-table td {
            border: none;
            padding: 0 4px;
            /* Spasi antar kotak */
            width: 25%;
        }

        .matrix-card {
            border: 1px solid #000;
            padding: 12px 5px;
            text-align: center;
            background-color: #fcfcfc;
            min-height: 50px;
        }

        .matrix-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }

        .matrix-value {
            font-size: 14px;
            font-weight: bold;
            color: #00a76f;
        }

        /* AI NARRATIVE - RATA KANAN KIRI & LIST BOX */
        .ai-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            text-align: justify;
            line-height: 1.6;
        }

        .ai-item {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f8fbf9;
            border-left: 3px solid #00a76f;
            text-align: justify;
        }

        .ai-paragraph {
            text-align: justify;
            margin-bottom: 10px;
        }

        /* Tabel Data Detail */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }

        .data-table td {
            vertical-align: top;
            font-size: 9px;
            word-wrap: break-word;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-normal {
            color: #008000;
        }

        .text-warning {
            color: #b8860b;
        }

        /* Tanda Tangan */
        .footer-area {
            width: 100%;
            margin-top: 50px;
        }

        .sig-box {
            width: 45%;
            display: inline-block;
            vertical-align: top;
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 70px;
            display: block;
            font-size: 11px;
        }

        .bottom-note {
            margin-top: 40px;
            font-size: 8px;
            border-top: 1px solid #eee;
            padding-top: 8px;
            color: #777;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="comp-name">PT PERKEBUNAN NUSANTARA IV REGIONAL I</div>
        <div class="comp-address">Jalan Sei Batang Hari No.2, Simpang Tanjung, Medan Sunggal, Kota Medan, Sumatera Utara,
            Indonesia</div>
    </div>

    <div class="report-header">
        <div class="report-title">LAPORAN HASIL MONITORING TBM III & ANALISIS CERDAS</div>
        <div class="report-period">Periode Laporan: {{ $periode_label }}</div>
    </div>

    <!-- I. MATRIKS PERFORMA (GUNAKAN TABEL AGAR TIDAK HILANG) -->
    @if (in_array('summary', $active_sections))
        <div class="section-header">I. Matriks Performa Agronomi (Unit: {{ $nama_kebun }})</div>
        <table class="matrix-table">
            <tr>
                <td>
                    <div class="matrix-card">
                        <span class="matrix-label">Survival Rate</span>
                        <span class="matrix-value">{{ $survival_rate }}%</span>
                    </div>
                </td>
                <td>
                    <div class="matrix-card">
                        <span class="matrix-label">Maintenance Score</span>
                        <span class="matrix-value">{{ $maintenance_score }}</span>
                    </div>
                </td>
                <td>
                    <div class="matrix-card">
                        <span class="matrix-label">Actual SPH</span>
                        <span class="matrix-value">{{ $sph_actual }}</span>
                    </div>
                </td>
                <td>
                    <div class="matrix-card" style="border-color: #4361ee;">
                        <span class="matrix-label" style="color:#4361ee">Vigor Index</span>
                        <span class="matrix-value" style="color: #4361ee;">{{ $vigor_index }} Pts</span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- II. ANALISIS AI (RATA KANAN KIRI) -->
        <div class="section-header">II. Analisis Kausalitas & Rekomendasi AI</div>
        <div class="ai-box">
            @php
                $cleanText = str_replace('**', '', $ai_narrative);
                $lines = explode("\n", $cleanText);
            @endphp

            @foreach ($lines as $line)
                @if (trim($line) != '')
                    <div class="{{ preg_match('/^\d+\./', trim($line)) ? 'ai-item' : 'ai-paragraph' }}">
                        {{ trim($line) }}
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <!-- III. DATA TABEL DETAIL -->
    @if (in_array('block', $active_sections))
        <div class="section-header">III. Data Agregat & Spasial Per Unit Kerja</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">No. Blok</th>
                    <th width="12%">Thn Tanam</th>
                    <th width="15%">Luas (Ha)</th>
                    <th width="15%">Jml Pokok</th>
                    <th width="12%">Status</th>
                    <th>Interpretasi Analitik AI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blocks as $index => $row)
                    <tr>
                        <td align="center">{{ $index + 1 }}</td>
                        <td align="center" class="text-bold uppercase">{{ $row->afdeling }}</td>
                        <td align="center">2024</td>
                        <td align="right">{{ number_format($row->luas_ha, 2) }}</td>
                        <td align="right">{{ number_format($row->pkk_normal) }}</td>
                        <td align="center">
                            @php $isNormal = $row->persen_pkk_normal >= 95; @endphp
                            <span class="{{ $isNormal ? 'text-normal' : 'text-warning' }} text-bold">
                                {{ $isNormal ? 'NORMAL' : 'DEFISIENSI' }}
                            </span>
                        </td>
                        <td style="text-align: justify;">
                            {{ $isNormal ? 'Kondisi tajuk dan populasi memenuhi standar regional.' : 'Terdeteksi anomali pada populasi, disarankan peninjauan drainase mikro.' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" align="center">Data tidak ditemukan dalam database.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <!-- KETENTUAN -->
    <div style="font-style: italic; font-size: 9px; margin-top: 15px;">
        <strong>Ketentuan Konten Dokumen:</strong><br>
        - No. Blok: Identitas Kode Blok Tanaman Realisasi | Thn Tanam: Tahun Penanaman Sensus Lapangan<br>
        - Luas: Luasan Lahan Berdasarkan Peta Spasial (Hektar) | Jml Pokok: Sensus Akurat Jumlah Tanaman Hidup
    </div>

    <!-- TANDA TANGAN -->
    <div class="footer-area">
        <div class="sig-box">
            <strong>Diketahui Oleh,"</strong><br>
            <span class="sig-name">( ............................ )</span>
            <strong>Asisten Investasi dan Pemetaan</strong>
        </div>

        <div class="sig-box" style="float: right; text-align: left; padding-left: 50px;">
            <strong>Dibuat Oleh,</strong><br>
            <span class="sig-name">( {{ $nama_penginput }} )</span>
            <strong>Admin / Staff Penginput</strong>
        </div>
    </div>

    <div class="bottom-note">
        Dokumen ID: DSS-{{ time() }} | Dicetak pada: {{ $tanggal_cetak }}<br>
        Laporan dihasilkan otomatis oleh Sistem Decision Support System (DSS) SIMTAN.
    </div>

</body>

</html>
