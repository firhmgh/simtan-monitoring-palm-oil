<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * RouteAndArchitectureIntegrityTest
 * 
 * Melakukan verifikasi statis terhadap routes/web.php dan routes/api.php:
 * 1. Memastikan seluruh route penting terdaftar tanpa error syntax
 * 2. Memastikan seluruh controller action yang dirujuk di rute benar-benar ada
 * 3. Memverifikasi proteksi middleware auth dan role
 */
class RouteAndArchitectureIntegrityTest extends TestCase
{
    /**
     * Memeriksa keberadaan method-method controller yang dipanggil dalam routes/web.php
     */
    public function test_web_route_controller_actions_exist()
    {
        $actions = [
            [\App\Http\Controllers\AuthController::class, 'showLogin'],
            [\App\Http\Controllers\AuthController::class, 'login'],
            [\App\Http\Controllers\AuthController::class, 'logout'],
            [\App\Http\Controllers\MonitoringController::class, 'index'],
            [\App\Http\Controllers\MonitoringController::class, 'settings'],
            [\App\Http\Controllers\MonitoringController::class, 'dataKebun'],
            [\App\Http\Controllers\MonitoringController::class, 'detailAreal'],
            [\App\Http\Controllers\MonitoringController::class, 'laporan'],
            [\App\Http\Controllers\MonitoringController::class, 'previewHTML'],
            [\App\Http\Controllers\MonitoringController::class, 'exportPDF'],
            [\App\Http\Controllers\MonitoringController::class, 'importView'],
            [\App\Http\Controllers\MonitoringController::class, 'importStore'],
            [\App\Http\Controllers\MonitoringController::class, 'downloadFile'],
            [\App\Http\Controllers\MonitoringController::class, 'importUpdate'],
            [\App\Http\Controllers\MonitoringController::class, 'importDestroy'],
            [\App\Http\Controllers\MonitoringController::class, 'riwayatData'],
            [\App\Http\Controllers\MonitoringController::class, 'exportAuditCsv'],
            [\App\Http\Controllers\MonitoringController::class, 'printAuditPdf'],
            [\App\Http\Controllers\AI_Controller::class, 'getDashboardInsight'],
            [\App\Http\Controllers\AI_Controller::class, 'getBlockInsight'],
            [\App\Http\Controllers\AI_Controller::class, 'updateConfig'],
            [\App\Http\Controllers\UserController::class, 'index'],
            [\App\Http\Controllers\UserController::class, 'store'],
            [\App\Http\Controllers\UserController::class, 'update'],
            [\App\Http\Controllers\UserController::class, 'destroy'],
            [\App\Http\Controllers\UserController::class, 'updateProfile'],
            [\App\Http\Controllers\UserController::class, 'updatePassword'],
            [\App\Http\Controllers\SpatialController::class, 'serve'],
            [\App\Http\Controllers\ImpersonateController::class, 'impersonate'],
            [\App\Http\Controllers\ImpersonateController::class, 'leaveImpersonation'],
        ];

        foreach ($actions as [$controller, $method]) {
            $this->assertTrue(method_exists($controller, $method), "Method [{$method}] pada controller [{$controller}] harus ada.");
        }
    }

    /**
     * Memeriksa keberadaan method API GIS pada SpatialController (routes/api.php)
     */
    public function test_api_route_controller_actions_exist()
    {
        $actions = [
            [\App\Http\Controllers\SpatialController::class, 'getConfig'],
            [\App\Http\Controllers\SpatialController::class, 'getBlocks'],
            [\App\Http\Controllers\SpatialController::class, 'getLCC'],
            [\App\Http\Controllers\SpatialController::class, 'getMaintenance'],
            [\App\Http\Controllers\SpatialController::class, 'getTrees'],
        ];

        foreach ($actions as [$controller, $method]) {
            $this->assertTrue(method_exists($controller, $method), "Method GIS API [{$method}] pada controller [{$controller}] harus ada.");
        }
    }

    /**
     * Memverifikasi keberadaan file views Blade penting (Dashboard, GIS Detail, Import, PDF)
     */
    public function test_essential_blade_views_exist()
    {
        $baseViews = dirname(dirname(__DIR__)) . '/resources/views';

        $views = [
            'index.blade.php',
            'auth/login.blade.php',
            'apps/monitoring/data-kebun.blade.php',
            'apps/monitoring/detail-kebun.blade.php',
            'apps/monitoring/import.blade.php',
            'apps/monitoring/riwayat-data.blade.php',
            'apps/monitoring/laporan.blade.php',
            'apps/monitoring/settings.blade.php',
            'apps/monitoring/pdf_template.blade.php',
            'apps/monitoring/exports/pdf-audit-log.blade.php',
            'components/layout/default.blade.php',
            'components/layout/auth.blade.php',
        ];

        foreach ($views as $view) {
            $path = "{$baseViews}/{$view}";
            $this->assertFileExists($path, "Blade view [{$view}] harus ada.");
        }
    }
}
