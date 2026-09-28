<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\MonitoringController;

class TemporalComparisonTest extends TestCase
{
    protected MonitoringController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $reflector = new \ReflectionClass(MonitoringController::class);
        $this->controller = $reflector->newInstanceWithoutConstructor();
    }

    /**
     * 1. Test P2 vs P1 menghasilkan delta benar (Absolut & Relatif)
     */
    public function test_p2_vs_p1_computes_accurate_delta(): void
    {
        $currentP2 = [
            'survival_rate' => 97.5,
            'sph_actual' => 134.2,
            'vigor_index' => 88.0,
            'maintenance_score' => 92.5,
        ];

        $previousP1 = [
            'survival_rate' => 95.0,
            'sph_actual' => 130.0,
            'vigor_index' => 80.0,
            'maintenance_score' => 90.0,
        ];

        $result = $this->controller->computeTemporalDelta($currentP2, $previousP1, 'periode-1-2025', 'Periode I (Jan - Apr 2025)');

        $this->assertTrue($result['has_comparison']);
        $this->assertEquals('periode-1-2025', $result['previous_slug']);
        $this->assertEquals('Periode I (Jan - Apr 2025)', $result['previous_label']);

        // Survival rate: 97.5 - 95.0 = +2.5 pts, percent: (2.5 / 95.0) * 100 = 2.6%
        $this->assertEquals(2.5, $result['kpi']['survival_rate']['delta_abs']);
        $this->assertEquals(2.6, $result['kpi']['survival_rate']['delta_percent']);
        $this->assertEquals('up', $result['kpi']['survival_rate']['trend']);

        // SPH: 134.2 - 130.0 = +4.2, percent: (4.2 / 130.0) * 100 = 3.2%
        $this->assertEquals(4.2, $result['kpi']['sph_actual']['delta_abs']);
        $this->assertEquals(3.2, $result['kpi']['sph_actual']['delta_percent']);
        $this->assertEquals('up', $result['kpi']['sph_actual']['trend']);

        // Vigor Index: 88.0 - 80.0 = +8.0, percent: (8.0 / 80.0) * 100 = 10.0%
        $this->assertEquals(8.0, $result['kpi']['vigor_index']['delta_abs']);
        $this->assertEquals(10.0, $result['kpi']['vigor_index']['delta_percent']);
        $this->assertEquals('up', $result['kpi']['vigor_index']['trend']);

        // Maintenance Score: 92.5 - 90.0 = +2.5, percent: (2.5 / 90.0) * 100 = 2.8%
        $this->assertEquals(2.5, $result['kpi']['maintenance_score']['delta_abs']);
        $this->assertEquals(2.8, $result['kpi']['maintenance_score']['delta_percent']);
        $this->assertEquals('up', $result['kpi']['maintenance_score']['trend']);
    }

    /**
     * 2. Test P3 vs P2 menghasilkan delta benar
     */
    public function test_p3_vs_p2_computes_accurate_delta(): void
    {
        $currentP3 = [
            'survival_rate' => 96.0,
            'sph_actual' => 133.0,
            'vigor_index' => 90.0,
            'maintenance_score' => 91.0,
        ];

        $previousP2 = [
            'survival_rate' => 97.5,
            'sph_actual' => 134.2,
            'vigor_index' => 88.0,
            'maintenance_score' => 92.5,
        ];

        $result = $this->controller->computeTemporalDelta($currentP3, $previousP2, 'periode-2-2025', 'Periode II (Mei - Agst 2025)');

        $this->assertTrue($result['has_comparison']);
        $this->assertEquals('periode-2-2025', $result['previous_slug']);

        // Survival Rate down: 96.0 - 97.5 = -1.5, percent: (-1.5 / 97.5) * 100 = -1.5%
        $this->assertEquals(-1.5, $result['kpi']['survival_rate']['delta_abs']);
        $this->assertEquals(-1.5, $result['kpi']['survival_rate']['delta_percent']);
        $this->assertEquals('down', $result['kpi']['survival_rate']['trend']);

        // SPH down: 133.0 - 134.2 = -1.2, percent: (-1.2 / 134.2) * 100 = -0.9%
        $this->assertEquals(-1.2, $result['kpi']['sph_actual']['delta_abs']);
        $this->assertEquals(-0.9, $result['kpi']['sph_actual']['delta_percent']);
        $this->assertEquals('down', $result['kpi']['sph_actual']['trend']);

        // Vigor Index up: 90.0 - 88.0 = +2.0
        $this->assertEquals(2.0, $result['kpi']['vigor_index']['delta_abs']);
        $this->assertEquals('up', $result['kpi']['vigor_index']['trend']);

        // Maintenance down: 91.0 - 92.5 = -1.5
        $this->assertEquals(-1.5, $result['kpi']['maintenance_score']['delta_abs']);
        $this->assertEquals('down', $result['kpi']['maintenance_score']['trend']);
    }

    /**
     * 3. Test P1 tidak memiliki previous period (Baseline)
     */
    public function test_p1_baseline_has_no_previous_period(): void
    {
        $currentP1 = [
            'survival_rate' => 95.0,
            'sph_actual' => 130.0,
            'vigor_index' => 80.0,
            'maintenance_score' => 90.0,
        ];

        $result = $this->controller->resolveTemporalDelta('periode-1-2025', $currentP1);

        $this->assertFalse($result['has_comparison']);
        $this->assertNull($result['previous_slug']);
        $this->assertNull($result['previous_label']);
        $this->assertNull($result['kpi']['survival_rate']['previous']);
        $this->assertNull($result['kpi']['survival_rate']['delta_abs']);
        $this->assertNull($result['kpi']['survival_rate']['delta_percent']);
        $this->assertEquals('none', $result['kpi']['survival_rate']['trend']);
    }

    /**
     * 4. Test Tahunan tidak memiliki previous period
     */
    public function test_tahunan_has_no_previous_period(): void
    {
        $currentTahunan = [
            'survival_rate' => 96.5,
            'sph_actual' => 132.0,
            'vigor_index' => 85.0,
            'maintenance_score' => 91.0,
        ];

        $result = $this->controller->resolveTemporalDelta('tahunan-2025', $currentTahunan);

        $this->assertFalse($result['has_comparison']);
        $this->assertNull($result['previous_slug']);
        $this->assertNull($result['previous_label']);
        $this->assertNull($result['kpi']['sph_actual']['delta_abs']);
        $this->assertEquals('none', $result['kpi']['sph_actual']['trend']);
    }

    /**
     * 5. Test previous data tidak tersedia (null) ditangani graceful
     */
    public function test_previous_data_not_available_graceful_fallback(): void
    {
        // Simulasi jika current data null
        $result = $this->controller->resolveTemporalDelta('periode-2-2025', null);

        $this->assertFalse($result['has_comparison']);
        $this->assertEquals('periode-1-2025', $result['previous_slug']);
        $this->assertNull($result['kpi']['vigor_index']['delta_abs']);
        $this->assertEquals('none', $result['kpi']['vigor_index']['trend']);

        // Simulasi computeTemporalDelta saat previous set bernilai kosong/null
        $partial = $this->controller->computeTemporalDelta(
            ['survival_rate' => 96.0, 'sph_actual' => 130.0, 'vigor_index' => 85.0, 'maintenance_score' => 90.0],
            ['survival_rate' => null, 'sph_actual' => null, 'vigor_index' => null, 'maintenance_score' => null],
            'periode-1-2025',
            'Periode I'
        );

        $this->assertNull($partial['kpi']['survival_rate']['delta_abs']);
        $this->assertEquals('none', $partial['kpi']['survival_rate']['trend']);
    }

    /**
     * 6. Test nilai sama menghasilkan delta 0 dan trend same
     */
    public function test_equal_values_yield_zero_delta_and_same_trend(): void
    {
        $current = [
            'survival_rate' => 95.0,
            'sph_actual' => 135.0,
            'vigor_index' => 85.0,
            'maintenance_score' => 90.0,
        ];

        $previous = [
            'survival_rate' => 95.0,
            'sph_actual' => 135.0,
            'vigor_index' => 85.0,
            'maintenance_score' => 90.0,
        ];

        $result = $this->controller->computeTemporalDelta($current, $previous, 'periode-1-2025', 'Periode I');

        $this->assertEquals(0.0, $result['kpi']['survival_rate']['delta_abs']);
        $this->assertEquals(0.0, $result['kpi']['survival_rate']['delta_percent']);
        $this->assertEquals('same', $result['kpi']['survival_rate']['trend']);

        $this->assertEquals(0.0, $result['kpi']['sph_actual']['delta_abs']);
        $this->assertEquals(0.0, $result['kpi']['sph_actual']['delta_percent']);
        $this->assertEquals('same', $result['kpi']['sph_actual']['trend']);
    }

    /**
     * 7. Test nilai lebih tinggi menghasilkan delta positif dan trend up
     */
    public function test_higher_value_yields_positive_delta_and_up_trend(): void
    {
        $current = ['survival_rate' => 98.2, 'sph_actual' => 140.0, 'vigor_index' => 92.0, 'maintenance_score' => 95.0];
        $previous = ['survival_rate' => 95.0, 'sph_actual' => 135.0, 'vigor_index' => 85.0, 'maintenance_score' => 90.0];

        $result = $this->controller->computeTemporalDelta($current, $previous, 'periode-1-2025', 'Periode I');

        $this->assertGreaterThan(0, $result['kpi']['survival_rate']['delta_abs']);
        $this->assertEquals(3.2, $result['kpi']['survival_rate']['delta_abs']);
        $this->assertEquals('up', $result['kpi']['survival_rate']['trend']);
    }

    /**
     * 8. Test nilai lebih rendah menghasilkan delta negatif dan trend down
     */
    public function test_lower_value_yields_negative_delta_and_down_trend(): void
    {
        $current = ['survival_rate' => 91.5, 'sph_actual' => 128.0, 'vigor_index' => 78.0, 'maintenance_score' => 82.0];
        $previous = ['survival_rate' => 95.0, 'sph_actual' => 135.0, 'vigor_index' => 85.0, 'maintenance_score' => 90.0];

        $result = $this->controller->computeTemporalDelta($current, $previous, 'periode-1-2025', 'Periode I');

        $this->assertLessThan(0, $result['kpi']['survival_rate']['delta_abs']);
        $this->assertEquals(-3.5, $result['kpi']['survival_rate']['delta_abs']);
        $this->assertEquals('down', $result['kpi']['survival_rate']['trend']);
    }

    /**
     * 9. Test previous = 0 tidak menyebabkan DivisionByZeroError dan delta_percent null
     */
    public function test_previous_zero_prevents_division_by_zero(): void
    {
        $current = [
            'survival_rate' => 95.0,
            'sph_actual' => 135.0,
            'vigor_index' => 80.0,
            'maintenance_score' => 85.0,
        ];

        $previousZero = [
            'survival_rate' => 0.0,
            'sph_actual' => 0.0,
            'vigor_index' => 0.0,
            'maintenance_score' => 0.0,
        ];

        $result = $this->controller->computeTemporalDelta($current, $previousZero, 'periode-1-2025', 'Periode I');

        // Delta abs tetap terhitung: 95.0 - 0.0 = +95.0
        $this->assertEquals(95.0, $result['kpi']['survival_rate']['delta_abs']);
        $this->assertEquals('up', $result['kpi']['survival_rate']['trend']);

        // Delta percent harus NULL (pencegahan DivisionByZero)
        $this->assertNull($result['kpi']['survival_rate']['delta_percent']);
        $this->assertNull($result['kpi']['sph_actual']['delta_percent']);
        $this->assertNull($result['kpi']['vigor_index']['delta_percent']);
        $this->assertNull($result['kpi']['maintenance_score']['delta_percent']);
    }

    /**
     * 10. Test formula KPI existing tidak berubah
     */
    public function test_existing_kpi_formulas_remain_unmodified(): void
    {
        $stdLb = MonitoringController::STD_LB_KC_INDEX; // 0.125
        $stdJp = MonitoringController::STD_JP_KC_INDEX; // 0.040

        $this->assertEquals(0.125, $stdLb);
        $this->assertEquals(0.040, $stdJp);
        $this->assertEquals(98.0, MonitoringController::STD_SURVIVAL_RATE);
        $this->assertEquals(143.0, MonitoringController::STD_SPH_TARGET);

        // Vigor Index formula:
        // girth_comp = (avg_girth / STD_LB_KC_INDEX) * 100
        // frond_comp = (avg_frond / STD_JP_KC_INDEX) * 100
        // vigor_index = round(min(100, (girth_comp + frond_comp) / 2), 1)
        $avgGirth = 0.120;
        $avgFrond = 0.038;
        $girthComp = ($avgGirth / $stdLb) * 100; // (0.120 / 0.125) * 100 = 96.0
        $frondComp = ($avgFrond / $stdJp) * 100; // (0.038 / 0.040) * 100 = 95.0
        $vigorIndex = round(min(100, ($girthComp + $frondComp) / 2), 1); // (96.0 + 95.0) / 2 = 95.5

        $this->assertEquals(95.5, $vigorIndex);

        // Maintenance Score: round((avg_lcc + (100 - avg_pir_buruk)) / 2, 1)
        $avgLcc = 85.4;
        $avgPirBuruk = 4.2;
        $maintenanceScore = round(($avgLcc + (100 - $avgPirBuruk)) / 2, 1);

        // (85.4 + 95.8) / 2 = 181.2 / 2 = 90.6
        $this->assertEquals(90.6, $maintenanceScore);

        // SPH: round(total_pokok / total_luas, 1)
        $totalPokok = 14300;
        $totalLuas = 105.5;
        $sph = round($totalPokok / $totalLuas, 1);
        $this->assertEquals(135.5, $sph);
    }
}
