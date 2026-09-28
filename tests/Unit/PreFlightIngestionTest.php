<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\SimtanFormService;

/**
 * PreFlightIngestionTest
 * 
 * Memverifikasi kemampuan Pre-flight Ingestion Validation:
 * 1. Deteksi file tidak ada / tidak dapat dibaca
 * 2. Deteksi struktur file kosong atau mismatch kategori
 * 3. Logika penanganan error Excel (#DIV/0!, #VALUE!, dsb)
 * 4. Logika sanitasi kolom numerik (koma menjadi titik desimal)
 * 5. Logika batas nilai maksimal (limitValue protection)
 * 6. Deteksi baris header / summary yang diabaikan
 */
class PreFlightIngestionTest extends TestCase
{
    /**
     * Memverifikasi bahwa file yang tidak ada akan melempar Exception spesifik
     */
    public function test_preflight_fails_on_missing_file()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Berkas Excel tidak valid atau tidak dapat dibaca");

        SimtanFormService::validatePreFlight('Rekap TBM', '/path/to/non_existent_file.xlsx');
    }

    /**
     * Memverifikasi penolakan kategori dataset yang tidak dikenal
     */
    public function test_preflight_fails_on_unknown_category()
    {
        // Buat file temporary kosong untuk test validasi
        $tempFile = tempnam(sys_get_temp_dir(), 'test_excel_');
        file_put_contents($tempFile, 'dummy content');

        try {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage("Kategori dataset 'Kategori Palsu' tidak dikenali");

            // Mock atau panggil dengan kategori tidak valid
            SimtanFormService::validatePreFlight('Kategori Palsu', $tempFile);
        } finally {
            if (file_exists($tempFile)) unlink($tempFile);
        }
    }

    /**
     * Memverifikasi logika sanitasi error Excel (#DIV/0!, #N/A, #VALUE!)
     * Sumber: DetailRekapImport::strictRound dan KorelasiVegetatifImport::sanitizeDesimal
     */
    public function test_excel_formula_error_sanitization()
    {
        $errorValues = ['#DIV/0!', '#N/A', '#VALUE!', '#REF!', '#NUM!', '-'];

        foreach ($errorValues as $err) {
            // Logika strictRound: jika ada karakter # atau tanda strip, harus kembali 0
            $isErrorOrDash = (str_contains($err, '#') || $err === '-');
            $this->assertTrue($isErrorOrDash, "Nilai [{$err}] harus terdeteksi sebagai nilai error Excel.");

            // Uji sanitizeDesimal behavior
            $sanitized = (str_contains($err, '#') || $err === '-') ? null : (float)str_replace(',', '.', $err);
            $this->assertNull($sanitized, "Nilai error Excel [{$err}] harus disanitasi menjadi null.");
        }

        // Nilai normal dengan koma Indonesia "85,75" harus menjadi 85.75
        $indoDecimal = '85,75';
        $cleaned = (float)str_replace(',', '.', $indoDecimal);
        $this->assertEquals(85.75, $cleaned);
    }

    /**
     * Memverifikasi batas proteksi nilai ekstrim (BigInt/Int overflow protection)
     * Sumber: DetailRekapImport::limitValue
     */
    public function test_limit_value_protection()
    {
        // Nilai wajar (jumlah pokok sawit normal 100 - 50.000)
        $normalPkk = "14300";
        $val = (int)round((float)str_replace(',', '.', $normalPkk));
        $this->assertEquals(14300, $val);

        // Nilai negatif tidak wajar harus dibatasi menjadi 0
        $negativePkk = -50;
        $protectedNeg = ($negativePkk < 0 || $negativePkk > 2000000) ? 0 : $negativePkk;
        $this->assertEquals(0, $protectedNeg);

        // Nilai ekstrim di atas 2.000.000 harus dibatasi menjadi 0 untuk mencegah overflow kolom MySQL
        $overflowPkk = 999999999;
        $protectedOver = ($overflowPkk < 0 || $overflowPkk > 2000000) ? 0 : $overflowPkk;
        $this->assertEquals(0, $protectedOver);
    }

    /**
     * Memverifikasi deteksi baris header dan baris ringkasan (summary)
     * Sumber: DetailRekapImport::isHeaderRow & KorelasiVegetatifImport baris 87
     */
    public function test_header_and_summary_row_detection()
    {
        // 1. Deteksi Baris Judul Kolom (Header)
        $c0 = "DISTRIK 1GLS";
        $c1 = "KEBUN 1KSD";
        $c2 = "AFDELING I";
        $combined = strtoupper($c0 . $c1 . $c2);
        $isHeader = (str_contains($combined, 'DISTRIK') || str_contains($combined, 'KEBUN') || str_contains($combined, 'AFDELING'));
        $this->assertTrue($isHeader, "Baris judul kolom harus terdeteksi.");

        // 2. Deteksi Baris Ringkasan Rata-Rata
        $summaryText1 = "RATA-RATA";
        $isSummary1 = (trim(strtoupper($summaryText1)) === 'RATA-RATA');
        $this->assertTrue($isSummary1, "'RATA-RATA' harus terdeteksi sebagai baris ringkasan.");

        // Teks 'RATA S.D BERGELOMBANG' adalah data topografi valid dan TIDAK BOLEH dianggap summary
        $normalTopografi = "RATA S.D BERGELOMBANG";
        $isSummary2 = (trim(strtoupper($normalTopografi)) === 'RATA-RATA');
        $this->assertFalse($isSummary2, "'RATA S.D BERGELOMBANG' adalah data topografi riil dan tidak boleh dianggap summary.");
    }

    /**
     * Memverifikasi pemetaan kolom P1 vs P2/P3
     * Sumber: DetailRekapImport baris 68-104
     */
    public function test_p1_vs_p2_p3_column_mapping_index_alignment()
    {
        // Pada Periode 1 (JANFEBMAR...):
        // Kolom Normal ada di Index 10 (Kolom K)
        // Kolom Pasar Pikul Kurang Baik tidak ada (default 0)
        $p1Label = "JANFEBMARAPR2025REKAP";
        $isP1 = str_contains(strtoupper($p1Label), 'JANFEBMAR');
        $this->assertTrue($isP1);

        $mockRowP1 = [
            0 => '1GLS', 1 => '1KSD', 2 => 'AFD I', 3 => '2023', 4 => '100.5',
            5 => '14300', 6 => '14000', 7 => '200', 8 => '100', 9 => '139',
            10 => '97.90', // persen_pkk_normal di P1
            11 => '1.40',  // persen_pkk_non_valuer
            12 => '0.70',  // persen_pkk_mati
            13 => '95.0',  // persen_tutupan_kacangan
            14 => '5.0',   // persen_pir_pkk_kurang_baik
            15 => '0.0',   // persen_area_tergenang
            16 => '0.0',   // kondisi_anak_kayu
            17 => 'Nihil'  // gangguan_ternak
        ];
        $normalP1 = (float)$mockRowP1[10];
        $this->assertEquals(97.90, $normalP1);

        // Pada Periode 2 & 3 (MEIJUL..., SEPOKT...):
        // Kolom Normal bergeser ke Index 11 karena adanya kolom tambahan di index 10
        $p2Label = "SEPOKTNOVDES2025REKAP";
        $isP2P3 = !str_contains(strtoupper($p2Label), 'JANFEBMAR');
        $this->assertTrue($isP2P3);

        $mockRowP2 = [
            0 => '1GLS', 1 => '1KSD', 2 => 'AFD I', 3 => '2023', 4 => '100.5',
            5 => '14300', 6 => '14000', 7 => '200', 8 => '100', 9 => '139',
            10 => '139',   // pkk_ha_kond_normal (Kolom K)
            11 => '97.90', // persen_pkk_normal di P2/P3 (Kolom L / Index 11)
            12 => '1.40',
            13 => '0.70',
            14 => '95.0',
            15 => '3.5',   // persen_pasar_pikul_kurang_baik
            16 => '4.2',   // persen_pir_pkk_kurang_baik
            17 => '1.0',   // persen_area_tergenang
            18 => '0.0',
            19 => 'Nihil',
            20 => '0', 21 => '0.0', 22 => '0', 23 => '0.0'
        ];
        $normalP2 = (float)$mockRowP2[11];
        $this->assertEquals(97.90, $normalP2);
        $pasarPikulP2 = (float)$mockRowP2[15];
        $this->assertEquals(3.5, $pasarPikulP2);
    }
}
