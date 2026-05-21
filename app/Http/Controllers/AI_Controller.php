<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AI_Controller - Decision Support System Interface
 * Menangani request analitik dari Dashboard dan Detail Blok
 * serta manajemen konfigurasi Neural Engine (Gemini/Groq).
 */
class AI_Controller extends Controller
{
    protected $aiService;

    /**
     * Mapping Slug dari URL ke Database Key
     */
    protected $mapPeriode = [
        'periode-1-2025' => 'JANFEBMARAPR2025REKAP',
        'periode-2-2025' => 'MEIJULJUNAGST2025REKAP',
        'periode-3-2025' => 'SEPOKTNOVDES2025REKAP',
        'tahunan-2025'   => 'Tahun 2025',
    ];

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * DASHBOARD INSIGHT: Narasi Global Tren Regional
     * Mendukung pemrosesan berdasarkan MODE (Multimodal/Growth/Survival)
     * dan fitur Force Refresh untuk bypass cache.
     */
    public function getDashboardInsight(Request $request)
    {
        try {
            $selectedSlug = $request->query('periode');

            // INTEGRASI: Tangkap mode dari dropdown frontend (default: multimodal)
            $mode = $request->query('mode', 'multimodal');

            // Cek apakah user menekan tombol Refresh AI (Manual)
            $refresh = $request->has('refresh');

            $dbKey = $this->mapPeriode[$selectedSlug] ?? $selectedSlug;

            if (!$dbKey) {
                return response()->json(['status' => 'error', 'message' => 'Dimensi waktu tidak valid.']);
            }

            // INTEGRASI: Kirim dbKey, mode, dan flag refresh ke service
            $insight = $this->aiService->generateExecutiveSummary($dbKey, $mode, $refresh);

            return response()->json([
                'status' => 'success',
                'narration' => $insight,
                'engine' => 'Hybrid Agronomy Engine'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * BLOCK INSIGHT: Diagnosa & Prediksi Spesifik Per Blok
     */
    public function getBlockInsight(Request $request)
    {
        try {
            $request->validate([
                'kebun' => 'required',
                'blok_id' => 'required',
                'periode' => 'required'
            ]);

            $dbKey = $this->mapPeriode[$request->periode] ?? $request->periode;
            $refresh = $request->has('refresh');

            $analysis = $this->aiService->analyzeSpecificBlok(
                $request->kebun,
                $request->blok_id,
                $dbKey,
                $refresh
            );

            return response()->json([
                'status' => 'success',
                'data' => $analysis
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * AI CONFIGURATION: Manajemen API Keys & Threshold Agronomi
     * Sinkron dengan form pada settings.blade.php
     */
    public function updateConfig(Request $request)
    {
        try {
            // Validasi Input
            $request->validate([
                'api_primary' => 'required',
                'api_key_primary' => 'required',
                'api_backup' => 'required',
                'api_key_backup' => 'required',
                'threshold_yellow' => 'required|numeric|min:0|max:100',
                'threshold_red' => 'required|numeric|min:0|max:100',
            ]);

            // Melakukan update pada row pertama tabel ai_configs
            DB::table('ai_configs')->updateOrInsert(
                ['id' => 1],
                [
                    'provider_primary' => $request->api_primary,
                    'key_primary'      => $request->api_key_primary,
                    'provider_backup'  => $request->api_backup,
                    'key_backup'       => $request->api_key_backup,
                    'threshold_yellow' => $request->threshold_yellow,
                    'threshold_red'    => $request->threshold_red,
                    'updated_at'       => now()
                ]
            );

            return back()->with('success', 'Konfigurasi Neural Engine dan Parameter Agronomi Berhasil Disinkronisasi.');
        } catch (\Exception $e) {
            return back()->withErrors(['system_error' => 'Gagal memperbarui konfigurasi: ' . $e->getMessage()]);
        }
    }
}
