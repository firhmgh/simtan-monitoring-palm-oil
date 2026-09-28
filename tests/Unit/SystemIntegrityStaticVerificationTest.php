<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * SystemIntegrityStaticVerificationTest
 * 
 * Melakukan verifikasi statis terhadap keberadaan file, class, model, 
 * arsitektur service, struktur konfigurasi periode, dan integritas GeoJSON fisik.
 */
class SystemIntegrityStaticVerificationTest extends TestCase
{
    /**
     * Verifikasi integritas Model Eloquent Utama
     */
    public function test_core_models_exist()
    {
        $expectedModels = [
            \App\Models\User::class,
            \App\Models\Role::class,
            \App\Models\SimtanForm::class,
            \App\Models\DetailRekap::class,
            \App\Models\KorelasiVegetatif::class,
            \App\Models\LokasiKebun::class,
            \App\Models\UploadLog::class,
        ];

        foreach ($expectedModels as $modelClass) {
            $this->assertTrue(class_exists($modelClass), "Model [{$modelClass}] harus ada.");
        }
    }

    /**
     * Verifikasi integritas Controller Inti
     */
    public function test_core_controllers_exist()
    {
        $expectedControllers = [
            \App\Http\Controllers\AuthController::class,
            \App\Http\Controllers\MonitoringController::class,
            \App\Http\Controllers\AI_Controller::class,
            \App\Http\Controllers\SpatialController::class,
            \App\Http\Controllers\UserController::class,
            \App\Http\Controllers\ImpersonateController::class,
        ];

        foreach ($expectedControllers as $controllerClass) {
            $this->assertTrue(class_exists($controllerClass), "Controller [{$controllerClass}] harus ada.");
        }
    }

    /**
     * Verifikasi integritas Service Layer
     */
    public function test_core_services_exist()
    {
        $expectedServices = [
            \App\Services\ChartDataService::class,
            \App\Services\SimtanFormService::class,
            \App\Services\SpatialDataService::class,
            \App\Services\AIService::class,
        ];

        foreach ($expectedServices as $serviceClass) {
            $this->assertTrue(class_exists($serviceClass), "Service [{$serviceClass}] harus ada.");
        }
    }

    /**
     * Verifikasi integritas Excel Import Parsers
     */
    public function test_excel_import_parsers_exist()
    {
        $expectedImports = [
            \App\Imports\TbmImport::class,
            \App\Imports\DetailRekapImport::class,
            \App\Imports\KorelasiVegetatifImport::class,
            \App\Imports\LokasiKebunImport::class,
        ];

        foreach ($expectedImports as $importClass) {
            $this->assertTrue(class_exists($importClass), "Import parser [{$importClass}] harus ada.");
        }
    }

    /**
     * Verifikasi keberadaan dan struktur berkas GeoJSON Spasial di storage/app/spatial/
     */
    public function test_spatial_geojson_files_exist_and_valid()
    {
        $baseSpatial = dirname(dirname(__DIR__)) . '/storage/app/spatial';
        $this->assertDirectoryExists($baseSpatial, "Direktori spatial harus ada di storage/app/spatial");

        // Periksa 3 unit kebun percontohan
        $kebuns = ['1KDH', '1KPM', '1KRP'];
        $layerTypes = ['BATAS', 'BLOK', 'KACANGAN', 'KONPOKOK', 'PEMELIHARAAN'];

        foreach ($kebuns as $kebun) {
            $dir = $baseSpatial . '/' . $kebun;
            $this->assertDirectoryExists($dir, "Direktori kebun {$kebun} harus ada.");

            foreach ($layerTypes as $layer) {
                $file = "{$dir}/{$kebun}_TBM2023_{$layer}.geojson";
                $this->assertFileExists($file, "File GeoJSON [{$file}] harus ada.");
                
                // Verifikasi validitas berkas GeoJSON secara aman (streaming/header check) tanpa memicu memory exhaustion
                $this->assertGreaterThan(0, filesize($file), "File GeoJSON [{$file}] tidak boleh kosong.");
                $fp = fopen($file, 'r');
                $head = fread($fp, 512);
                fclose($fp);
                $this->assertStringContainsString('FeatureCollection', $head, "File GeoJSON [{$file}] harus bertipe FeatureCollection.");
            }
        }
    }

    /**
     * Verifikasi Konfigurasi Periode SIMTAN
     */
    public function test_simtan_configuration_structure()
    {
        $configPath = dirname(dirname(__DIR__)) . '/config/simtan.php';
        $this->assertFileExists($configPath);

        $config = require $configPath;
        $this->assertIsArray($config);
        $this->assertArrayHasKey('map_periode', $config);

        $expectedSlugs = ['periode-1-2025', 'periode-2-2025', 'periode-3-2025', 'tahunan-2025'];
        foreach ($expectedSlugs as $slug) {
            $this->assertArrayHasKey($slug, $config['map_periode']);
            $this->assertArrayHasKey('db_key', $config['map_periode'][$slug]);
            $this->assertArrayHasKey('label', $config['map_periode'][$slug]);
        }
    }
}
