<?php

namespace App\Services;

use App\Models\DetailRekap;
use App\Models\KorelasiVegetatif;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

/**
 * AIService - Smart Decision Support System Engine
 * Mengelola inferensi neural menggunakan Google Gemini & Groq API.
 * Mendukung analisis multimodal, pertumbuhan (growth), dan mortalitas (survival).
 */
class AIService
{
    protected $config;

    public function __construct()
    {
        // Mengambil konfigurasi engine (API Key & Provider) dari database
        $this->config = DB::table('ai_configs')->first();
    }

    /**
     * DASHBOARD LEVEL: Narasi Berdasarkan Mode Analisis yang Dipilih User
     */
    public function generateExecutiveSummary($periode, $mode = 'multimodal', $forceRefresh = false)
    {
        // 1. Pre-processing: Ambil data statistik populasi
        $stats = DetailRekap::where('periode', $periode)->where('is_total', 1)->get();
        if ($stats->isEmpty()) {
            return "Dataset monitoring untuk periode ini belum tersedia untuk dianalisis oleh AI.";
        }

        $avgHealth = $stats->avg('persen_pkk_normal');
        $worstUnit = DetailRekap::where('periode', $periode)
            ->where('is_total', 1)
            ->orderBy('persen_pkk_mati', 'desc')
            ->first();

        // 2. Ambil data vegetatif (Biometrik Growth) asli dari database
        $veg = KorelasiVegetatif::where('periode', $periode)->get();

        // 3. Susun Konteks (Fusi Data) - FIX: Menggunakan key 'avg_girth' agar sinkron dengan buildPrompt
        $context = [
            'mode_analisis' => $mode,
            'periode' => $periode,
            'data_populasi' => [
                'avg_survival_rate' => round($avgHealth, 2) . '%',
                'pkk_kerdil_total' => $stats->sum('pkk_non_valuer'),
                'unit_terburuk' => $worstUnit->kebun ?? 'N/A',
                'mortalitas_unit_terburuk' => ($worstUnit->persen_pkk_mati ?? 0) . '%'
            ],
            'data_vegetatif' => [
                'avg_girth' => round($veg->avg('lingkar_batang'), 3) . ' m',
                'avg_jumlah_pelepah' => round($veg->avg('jumlah_pelepah'), 1),
                'avg_panjang_pelepah' => round($veg->avg('panjang_pelepah'), 3) . ' m'
            ]
        ];

        // INTEGRASI: Gunakan variabel $mode agar tercatat dengan benar di database
        return $this->askAI($mode, $context, $forceRefresh, 'Regional I');
    }

    /**
     * BLOCK LEVEL: Diagnosa Preskriptif per Unit Blok
     */
    public function analyzeSpecificBlok($kebun, $blokId, $periode, $forceRefresh = false)
    {
        $data = DetailRekap::where('kebun', $kebun)
            ->where('blok', $blokId)
            ->where('periode', $periode)
            ->first();

        if (!$data) return null;

        $context = [
            'unit' => $kebun,
            'blok' => $blokId,
            'sr' => $data->persen_pkk_normal . '%',
            'lcc_coverage' => $data->persen_tutupan_kacangan . '%',
            'dead_rate' => $data->persen_pkk_mati . '%'
        ];

        return $this->askAI('block_diagnostic', $context, $forceRefresh, $kebun . '-' . $blokId);
    }

    /**
     * Core Engine Logic dengan Mode-Specific Caching & Failsafe
     */
    public function askAI($mode, $contextData, $forceRefresh = false, $unitLabel = null)
    {
        if (!$this->config) return "Konfigurasi AI tidak ditemukan. Sila atur di menu Settings.";

        $prompt = $this->buildPrompt($mode, $contextData);

        // Cache key menyertakan MODE agar hasil tidak tertukar saat ganti dropdown
        $cacheKey = "ai_res_" . $mode . "_" . md5($prompt);

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 3600, function () use ($prompt, $mode, $unitLabel, $contextData) {

            // LOG PENGGUNAAN KE DATABASE (Audit Trail Scopus)
            try {
                DB::table('ai_usage_logs')->insert([
                    'user_id'    => Auth::id() ?? 1,
                    'kebun'      => $unitLabel ?? 'Global',
                    'mode'       => $mode,
                    'periode'    => $contextData['periode'] ?? 'Custom',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } catch (\Exception $e) {
                Log::error("Gagal mencatat log AI: " . $e->getMessage());
            }

            try {
                // Mencoba Layanan Utama (L1)
                return $this->requestToLLM($this->config->provider_primary, $this->config->key_primary, $prompt);
            } catch (\Exception $e) {
                Log::warning("AI Primary Provider Gagal, mencoba backup. Error: " . $e->getMessage());
                try {
                    // Failsafe ke Layanan Cadangan (L2)
                    return $this->requestToLLM($this->config->provider_backup, $this->config->key_backup, $prompt);
                } catch (\Exception $e2) {
                    return "Gagal melakukan analisis neural. Silakan periksa validitas API Key di Settings.";
                }
            }
        });
    }

    /**
     * Request Machine: Menangani integrasi ke API Gemini (v1) dan Groq
     */
    private function requestToLLM($provider, $key, $prompt)
    {
        if (empty($key)) throw new \Exception("API Key untuk $provider kosong.");

        $systemInstructions = "Anda adalah Pakar Agronomi PTPN IV. Berikan analisis teknis yang tajam, sangat singkat (maks 3 kalimat) dalam Bahasa Indonesia profesional.";

        if ($provider === 'gemini') {
            $url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key={$key}";
            $response = Http::withoutVerifying()->post($url, [
                'contents' => [
                    ['parts' => [['text' => $systemInstructions . "\n\nInstruksi: " . $prompt]]]
                ],
                'generationConfig' => ['temperature' => 0.4]
            ]);

            if ($response->failed()) throw new \Exception("Gemini Error: " . ($response->json()['error']['message'] ?? 'Unknown'));

            return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? "Respon Gemini Kosong";
        }

        if ($provider === 'groq') {
            $response = Http::withoutVerifying()->withToken($key)->post("https://api.groq.com/openai/v1/chat/completions", [
                'model' => 'llama-3.1-8b-instant',
                'messages' => [
                    ['role' => 'system', 'content' => $systemInstructions],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
            ]);

            if ($response->failed()) throw new \Exception("Groq Error: " . ($response->json()['error']['message'] ?? 'Unknown'));

            return $response->json()['choices'][0]['message']['content'] ?? "Respon Groq Kosong";
        }

        throw new \Exception("Provider $provider tidak didukung.");
    }

    /**
     * Prompt Engineering: Switch logic berdasarkan pilihan mode di Frontend
     */
    private function buildPrompt($mode, $data)
    {
        switch ($mode) {
            case 'growth':
                return "Analisis pertumbuhan VIGOR TUMBUH. Data: " . json_encode($data['data_vegetatif']) . ". Bandingkan lingkar batang terhadap standar TBM III (min 0.70 m). Evaluasi apakah pertumbuhan tergolong vigor atau stagnan.";

            case 'survival':
                return "Analisis MORTALITAS. Data: " . json_encode($data['data_populasi']) . ". Fokus pada unit " . $data['data_populasi']['unit_terburuk'] . " dan risiko kematian pohon berdasarkan populasi kerdil.";

            case 'block_diagnostic':
                return "Analisis Blok Spesifik: " . json_encode($data) . ". Berikan 1 alasan ilmiah potensi masalah dan 1 instruksi prioritas bagi asisten kebun.";

            default: // multimodal
                return "Analisis MULTIMODAL. Korelasikan SR (" . $data['data_populasi']['avg_survival_rate'] . ") dengan rata-rata lingkar batang (" . $data['data_vegetatif']['avg_girth'] . "). Sebutkan satu insight strategis untuk Regional I.";
        }
    }
}
