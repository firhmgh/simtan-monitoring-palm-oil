<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;

class AI_Controller extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Untuk Dashboard Utama (Narasi Global)
     */
    public function getDashboardInsight(Request $request)
    {
        try {
            $mode = $request->query('mode', 'multimodal');
            $periode = $request->query('periode');

            // Kirim parameter ke service
            $insight = $this->aiService->generateExecutiveSummary($mode, $periode);

            return response()->json([
                'status' => 'success',
                'narration' => $insight,
                'engine' => 'GPT-4o Agronomy-Trained'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Untuk Detail Blok (Diagnosa Spesifik)
     */
    public function getBlockInsight(Request $request)
    {
        $request->validate([
            'kebun' => 'required',
            'blok_id' => 'required'
        ]);

        $analysis = $this->aiService->analyzeSpecificBlok($request->kebun, $request->blok_id);

        return response()->json([
            'status' => 'success',
            'data' => $analysis
        ]);
    }
}
