<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// Import Model untuk ekstraksi context data
use App\Models\DetailRekap;
use App\Models\KorelasiVegetatif;

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
     * DASHBOARD & REPORT INSIGHT: Narasi Global Tren Regional atau Spesifik Kebun
     * Terintegrasi dengan parameter 'kebun' untuk akurasi data & logging.
     */
    public function getDashboardInsight(Request $request)
    {
        try {
            $selectedSlug = $request->query('periode');
            $kebun = $request->query('kebun'); // Tangkap parameter kebun dari frontend
            $refresh = $request->has('refresh');

            // Logika penentuan mode otomatis: 
            // Jika ada parameter kebun -> kebun_summary, jika tidak -> integrasi terpadu (Global)
            $mode = $kebun ? 'kebun_summary' : 'multimodal';

            $dbKey = $this->mapPeriode[$selectedSlug] ?? $selectedSlug;

            if (!$dbKey) {
                return response()->json(['status' => 'error', 'message' => 'Dimensi waktu tidak valid.']);
            }

            // Memanggil logic analitik dengan parameter kebun (param ke-4)
            // Hal ini memastikan AIService melakukan filtering data dan logging yang benar
            $insight = $this->aiService->generateExecutiveSummary($dbKey, $mode, $refresh, $kebun);

            return response()->json([
                'status' => 'success',
                'narration' => $insight,
                'engine' => $kebun ? 'Unit Diagnostic Engine' : 'Hybrid Regional Engine'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * BLOCK INSIGHT: Diagnosa & Prediksi Spesifik Per Blok
     * Terintegrasi dengan Standard Enrichment (Topografi, Drainase, & MAP Age)
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

            $rekap = DetailRekap::where('kebun', $request->kebun)
                ->where('afdeling', $request->blok_id)
                ->where('periode', $dbKey)
                ->first();

            // 2. PROTEKSI: Jika data tidak ditemukan, jangan dipaksa
            if (!$rekap) {
                return response()->json([
                    'status' => 'success',
                    'data' => ['rekomendasi_ai' => "Data sensus unit {$request->blok_id} belum tersedia."]
                ]);
            }

            // 3. KONTEKS CERDAS UNTUK AI (Gunakan data dari $rekap)
            $enrichedContext = [
                'unit'             => $request->blok_id,
                'risiko_topografi' => $rekap->topografi ?? 'N/A',
                'survival_rate'    => ($rekap->persen_pkk_normal ?? 0) . '%',
                'status_drainase'  => ($rekap->persen_area_tergenang > 2) ? 'KRITIS (Tergenang)' : 'NORMAL',
                'map_age'          => ($rekap->tahun_tanam) ? (date('Y') - $rekap->tahun_tanam) . ' Tahun' : 'Tidak Terdata',
                'cakupan_lcc'      => ($rekap->persen_tutupan_kacangan ?? 0) . '%',
                'piringan_gulma'   => ($rekap->persen_pir_pkk_kurang_baik ?? 0) . '%',
            ];

            // 4. EKSEKUSI AI
            $analysis = $this->aiService->analyzeSpecificBlok(
                $request->kebun,
                $request->blok_id,
                $dbKey,
                $request->has('refresh'),
                $enrichedContext
            );

            return response()->json([
                'status' => 'success',
                'data' => [
                    'rekomendasi_ai' => $analysis,
                    'metadata' => $enrichedContext
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Sistem Sibuk: ' . $e->getMessage()], 500);
        }
    }

    /**
     * AI CONFIGURATION: Manajemen API Keys & Threshold Agronomi
     */
    public function updateConfig(Request $request)
    {
        try {
            $request->validate([
                'api_primary' => 'required',
                'api_key_primary' => 'required',
                'api_backup' => 'required',
                'api_key_backup' => 'required',
                'threshold_yellow' => 'required|numeric|min:0|max:100',
                'threshold_red' => 'required|numeric|min:0|max:100',
            ]);

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
            return back()->with('error', 'Gagal memperbarui konfigurasi: ' . $e->getMessage());
        }
    }
}
