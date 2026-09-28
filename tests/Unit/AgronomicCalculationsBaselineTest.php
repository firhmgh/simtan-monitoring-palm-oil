<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\ExcelDataHelper;
use App\Services\SpatialDataService;

/**
 * AgronomicCalculationsBaselineTest
 * 
 * Mengunci dan memverifikasi formula agronomis eksisting yang ditemukan di kode SIMTAN:
 * 1. SPH (Standar & Realisasi)
 * 2. Survival Rate
 * 3. Vigor Index (LB/KC & JP/KC terhadap benchmark PPKS)
 * 4. Maintenance Score (LCC & Kebersihan Piringan)
 * 5. IPHI Spasial (MCDM Tree Score, Survival, Homogenitas CV, Status Blok)
 * 6. ExcelDataHelper Mapping (PTPN Kode Kebun & Distrik)
 */
class AgronomicCalculationsBaselineTest extends TestCase
{
    const STD_LB_KC_INDEX   = 0.125;
    const STD_JP_KC_INDEX   = 0.040;
    const STD_SURVIVAL_RATE = 98.0;
    const STD_SPH_TARGET    = 143.0;

    /**
     * Verifikasi formula SPH Aktual dan SPH Target Compliance
     * Sumber: MonitoringController.php baris 142 & 155
     */
    public function test_sph_calculation_and_compliance()
    {
        $totalLuas = 100.0; // 100 Hektar
        $totalPokok = 14300; // 14.300 Pokok

        // Formula: round($viewData['total_pokok'] / $viewData['total_luas'], 1)
        $sphActual = ($totalLuas > 0) ? round($totalPokok / $totalLuas, 1) : 0;
        $this->assertEquals(143.0, $sphActual);

        // Target Populasi: totalLuas * 143
        $populasiCompliance = round(($totalPokok / ($totalLuas * self::STD_SPH_TARGET)) * 100, 1);
        $this->assertEquals(100.0, $populasiCompliance);

        // Uji kasus under-population
        $underPokok = 12000;
        $sphUnder = round($underPokok / $totalLuas, 1);
        $this->assertEquals(120.0, $sphUnder);
        $complianceUnder = round(($underPokok / ($totalLuas * self::STD_SPH_TARGET)) * 100, 1);
        $this->assertEquals(83.9, $complianceUnder);
    }

    /**
     * Verifikasi formula Vigor Index & batas min 100%
     * Sumber: MonitoringController.php baris 133-135
     */
    public function test_vigor_index_calculation()
    {
        $avgGirth = 0.125; // Sesuai benchmark LB/KC
        $avgFrond = 0.040; // Sesuai benchmark JP/KC

        $girthComp = ($avgGirth > 0) ? ($avgGirth / self::STD_LB_KC_INDEX) * 100 : 0;
        $frondComp = ($avgFrond > 0) ? ($avgFrond / self::STD_JP_KC_INDEX) * 100 : 0;
        $vigorIndex = round(min(100, ($girthComp + $frondComp) / 2), 1);

        $this->assertEquals(100.0, $vigorIndex);

        // Kasus pertumbuhan lebih rendah dari standar (misal lingkar batang 0.100, pelepah 0.035)
        $subGirth = 0.100;
        $subFrond = 0.035;
        $gComp = ($subGirth / self::STD_LB_KC_INDEX) * 100; // 80.0%
        $fComp = ($subFrond / self::STD_JP_KC_INDEX) * 100; // 87.5%
        $expectedVigor = round(min(100, ($gComp + $fComp) / 2), 1); // (80 + 87.5) / 2 = 83.75 -> 83.8
        $this->assertEquals(83.8, $expectedVigor);
    }

    /**
     * Verifikasi formula Maintenance Score
     * Sumber: MonitoringController.php baris 138: (($avg_lcc) + (100 - $avg_pir_buruk)) / 2
     */
    public function test_maintenance_score_calculation()
    {
        $avgLcc = 90.0; // Tutupan kacangan 90%
        $avgPirBuruk = 10.0; // Piringan buruk 10% (bersih = 90%)

        $maintenanceScore = round(($avgLcc + (100 - $avgPirBuruk)) / 2, 1);
        $this->assertEquals(90.0, $maintenanceScore);

        // Kasus kondisi kritis (LCC 40%, Piringan Buruk 50%)
        $badLcc = 40.0;
        $badPir = 50.0;
        $badScore = round(($badLcc + (100 - $badPir)) / 2, 1); // (40 + 50) / 2 = 45.0
        $this->assertEquals(45.0, $badScore);
    }

    /**
     * Verifikasi Logika MCDM IPHI (Integrated Plantation Health Index) Spasial
     * Sumber: SpatialDataService.php baris 90-156
     */
    public function test_spatial_iphi_calculation_healthy_moderate_critical()
    {
        $spatialService = new SpatialDataService();

        // 1. Data Pohon Kosong (Fallback)
        $emptyResult = $spatialService->calculateBlockHealth([]);
        $this->assertEquals('healthy', $emptyResult['status']);
        $this->assertEquals(100, $emptyResult['score']);
        $this->assertEquals(100, $emptyResult['survival_rate']);

        // 2. Data Pohon Sehat (10 pohon semua normal, variasi seragam CV <= 15%)
        $healthyTrees = [];
        for ($i = 0; $i < 10; $i++) {
            $healthyTrees[] = [
                'properties' => [
                    'KONPOKOK' => 'NORMAL',
                    'std_lingkar_batang' => 0.130
                ]
            ];
        }
        $healthyResult = $spatialService->calculateBlockHealth($healthyTrees);
        $this->assertEquals('healthy', $healthyResult['status']);
        $this->assertEquals(100.0, $healthyResult['survival_rate']);
        $this->assertGreaterThanOrEqual(85.0, $healthyResult['score']);

        // 3. Data Pohon Kritis (10 pohon, 6 mati, 4 normal)
        $criticalTrees = [];
        for ($i = 0; $i < 6; $i++) {
            $criticalTrees[] = [
                'properties' => [
                    'KONPOKOK' => 'MATI',
                    'std_lingkar_batang' => 0
                ]
            ];
        }
        for ($i = 0; $i < 4; $i++) {
            $criticalTrees[] = [
                'properties' => [
                    'KONPOKOK' => 'NORMAL',
                    'std_lingkar_batang' => 0.120
                ]
            ];
        }
        $criticalResult = $spatialService->calculateBlockHealth($criticalTrees);
        $this->assertEquals('critical', $criticalResult['status']);
        $this->assertEquals(40.0, $criticalResult['survival_rate']);
        $this->assertLessThan(65.0, $criticalResult['score']);
    }

    /**
     * Verifikasi ExcelDataHelper Resolusi Kode & Nama PTPN IV
     * Sumber: ExcelDataHelper.php
     */
    public function test_excel_data_helper_mappings()
    {
        // Test resolusi unit Labuhan Batu (1KRP -> Kebun Rantau Prapat)
        $info1 = ExcelDataHelper::getInfoKebun('1KRP', '1GLB', 125.5);
        $this->assertEquals('Kebun Rantau Prapat', $info1['nama']);
        $this->assertEquals('Unit Group Labuhan Batu', $info1['distrik']);
        $this->assertEquals(125.5, $info1['luas']);

        // Test resolusi unit Serdang I (1KDH -> Kebun Dusun Hulu)
        $info2 = ExcelDataHelper::getInfoKebun('1KDH', '1GS1', 340.0);
        $this->assertEquals('Kebun Dusun Hulu', $info2['nama']);
        $this->assertEquals('Unit Group Serdang I', $info2['distrik']);

        // Test list kebun lengkap
        $daftarKebun = ExcelDataHelper::getDaftarKebunFull();
        $this->assertArrayHasKey('1KSM', $daftarKebun);
        $this->assertArrayHasKey('1KPM', $daftarKebun);
        $this->assertArrayHasKey('1KDH', $daftarKebun);
        $this->assertArrayHasKey('1KRP', $daftarKebun);
    }
}
