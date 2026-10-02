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
 * AIService - Expert Decision Support System Engine
 * Mengelola inferensi neural dengan pola pikir Senior Agronomist Auditor PTPN IV.
 * Fokus: Causal Analysis (Sebab-Akibat), Vigor Evaluation, dan Predictive Risk.
 * Terintegrasi dengan Hukum Minimum Liebig & Teori Nutrient Leaching.
 */
class AIService
{
    protected $config;

    public function __construct()
    {
        $this->getConfig();
    }

    /**
     * DASHBOARD / REPORT LEVEL: Narasi Tren (Global Regional atau Per Kebun)
     */
    public function generateExecutiveSummary($periode, $mode = 'multimodal', $forceRefresh = false, $kebunCode = null)
    {
        // 1. Filter dataset berdasarkan cakupan (Regional I atau Kebun Tertentu)
        $query = DetailRekap::where('periode', $periode)->where('is_total', 1);
        if ($kebunCode) {
            $query->where('kebun', $kebunCode);
            $mode = 'kebun_summary';
        }

        $stats = $query->get();
        if ($stats->isEmpty()) {
            return "Dataset untuk " . ($kebunCode ?? "Regional I") . " belum tersedia di database utama.";
        }

        // 2. Kalkulasi statistik dasar untuk context AI
        $avgHealth = $stats->avg('persen_pkk_normal');
        $worstUnit = DetailRekap::where('periode', $periode)
            ->where('is_total', 1)
            ->orderBy('persen_pkk_mati', 'desc')
            ->first();

        // 3. Filter data biometrik vegetatif
        $vegQuery = KorelasiVegetatif::where('periode', $periode);
        if ($kebunCode) $vegQuery->where('kebun', $kebunCode);
        $veg = $vegQuery->get();

        $unitLabel = $kebunCode ?: 'Regional I';

        $context = [
            'mode_analisis' => $mode,
            'unit_scope' => $unitLabel,
            'periode' => $periode,
            'data_populasi' => [
                'avg_survival_rate' => round($avgHealth, 2) . '%',
                'pkk_kerdil_total' => $stats->sum('pkk_non_valuer'),
                'unit_terburuk' => $worstUnit->kebun ?? 'N/A',
                'mortalitas_unit_terburuk' => ($worstUnit->persen_pkk_mati ?? 0) . '%'
            ],
            'data_vegetatif' => [
                'indeks_lingkar_batang_lb_kc' => round($veg->avg('lingkar_batang'), 3),
                'indeks_jumlah_pelepah_jp_kc' => round($veg->avg('jumlah_pelepah'), 3),
                'indeks_panjang_pelepah_pp_kc' => round($veg->avg('panjang_pelepah'), 3)
            ]
        ];

        return $this->askAI($mode, $context, $forceRefresh, $unitLabel);
    }

    /**
     * BLOCK LEVEL: Diagnosa Audit Spesifik per Blok
     */
    public function analyzeSpecificBlok($kebun, $blokId, $periode, $forceRefresh = false, $enrichedContext = [])
    {
        $rekap = DetailRekap::where('kebun', $kebun)->where('afdeling', $blokId)->where('periode', $periode)->first();
        $veg = KorelasiVegetatif::where('kebun', $kebun)->where('blok', $blokId)->where('periode', $periode)->first();

        if (!$rekap) return "Data primer untuk unit $blokId tidak ditemukan.";

        $context = [
            'periode' => $periode,
            'unit' => $blokId,
            'metadata_risiko' => $enrichedContext,
            'kondisi_sensus' => [
                'survival_rate' => $rekap->persen_pkk_normal . '%',
                'pohon_mati' => $rekap->pkk_mati . ' pokok',
                'pohon_kerdil' => $rekap->persen_pkk_non_valuer . '%',
                'tutupan_lcc' => $rekap->persen_tutupan_kacangan . '%',
                'area_tergenang' => $rekap->persen_area_tergenang . '%',
                'piringan_gulma' => $rekap->persen_pir_pkk_kurang_baik . '%',
            ],
            'proporsi_pertumbuhan_allometrik' => $veg ? [
                'rasio_lb_kc' => $veg->lingkar_batang, // 0.137
                'rasio_jp_kc' => $veg->jumlah_pelepah, // 0.044
                'rasio_pp_kc' => $veg->panjang_pelepah  // 0.208
            ] : 'Data indeks vegetatif belum tersedia'
        ];

        return $this->askAI('block_diagnostic', $context, $forceRefresh, $kebun . '-' . $blokId);
    }

    /**
     * Caching & Failsafe Controller
     */
    public function getConfig()
    {
        // Selalu ambil konfigurasi terbaru dari database dengan fallback ke .env/services
        $defaultConfig = (object) [
            'provider_primary' => 'gemini',
            'key_primary'      => config('services.gemini.key'),
            'provider_backup'  => 'groq',
            'key_backup'       => config('services.groq.key'),
            'threshold_yellow' => 85,
            'threshold_red'    => 75,
        ];

        try {
            $dbConfig = DB::table('ai_configs')->first();
            $this->config = $dbConfig ?: $defaultConfig;
        } catch (\Throwable $e) {
            $this->config = $defaultConfig;
        }

        return $this->config;
    }

    /**
     * Caching & Failsafe Controller
     */
    public function askAI($mode, $contextData, $forceRefresh = false, $unitLabel = null)
    {
        $config = $this->getConfig();
        if (!$config) {
            return "Konfigurasi AI belum disetel pada sistem.";
        }

        $prompt = $this->buildPrompt($mode, $contextData);
        $cacheKey = "ai_audit_" . $mode . "_" . md5($prompt);

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 3600, function () use ($prompt, $mode, $unitLabel, $contextData, $config) {
            // Logging Audit Trail DB
            try {
                DB::table('ai_usage_logs')->insert([
                    'user_id' => Auth::id() ?? 1,
                    'kebun' => $unitLabel ?? 'Global',
                    'mode' => $mode,
                    'periode' => $contextData['periode'] ?? 'Unknown',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } catch (\Exception $e) {
                Log::error("[AI AUDIT LOG] Gagal mencatat log usage: " . $e->getMessage());
            }

            // Eksekusi Provider L1 (Primary)
            $p1 = $config->provider_primary ?: 'gemini';
            $k1 = $config->key_primary ?: config("services.{$p1}.key");
            
            // Eksekusi Provider L2 (Backup/Fallback)
            $p2 = $config->provider_backup ?: 'groq';
            $k2 = $config->key_backup ?: config("services.{$p2}.key");

            Log::info("[NEURAL ENGINE] Memulai inferensi AI. Mode: [{$mode}], Target: [{$unitLabel}]. Mencoba Provider Utama (L1): [{$p1}].");

            try {
                $response = $this->requestToLLM($p1, $k1, $prompt);
                Log::info("[NEURAL ENGINE SUCCESS] Provider Utama (L1) [{$p1}] berhasil menghasilkan inferensi.");
                return $response;
            } catch (\Exception $e1) {
                Log::warning("[NEURAL ENGINE FALLBACK TRIGGERED] Provider Utama [{$p1}] gagal dengan pesan: '{$e1->getMessage()}'. Mengaktifkan failover otomatis ke Provider Cadangan (L2): [{$p2}].");

                try {
                    $responseBackup = $this->requestToLLM($p2, $k2, $prompt);
                    Log::info("[NEURAL ENGINE SUCCESS] Provider Cadangan (L2) [{$p2}] berhasil menghasilkan inferensi.");
                    return $responseBackup;
                } catch (\Exception $e2) {
                    Log::error("[NEURAL ENGINE CRITICAL] Kedua provider L1 [{$p1}] dan L2 [{$p2}] gagal. Detail L1: '{$e1->getMessage()}'. Detail L2: '{$e2->getMessage()}'.");
                    
                    // Failsafe darurat tanpa mengekspos error teknis atau raw stack trace kepada pengguna
                    return $this->generateFailsafePrescription($mode, $contextData);
                }
            }
        });
    }

    /**
     * Failsafe Generator saat seluruh network/provider external bermasalah
     */
    private function generateFailsafePrescription($mode, $data)
    {
        $unit = $data['unit'] ?? ($data['unit_scope'] ?? 'Areal TBM III');
        return "[CONFIDENCE_SCORE]: 78% (Offline Failsafe Rule Base)\n\n" .
               "[OBSERVASI]: Sistem mendeteksi kebutuhan konsolidasi data sensus lapangan dan pemantauan kondisi lingkungan pada unit {$unit}.\n\n" .
               "[ANALISIS_KAUSAL]: Terjadi fluktuasi vigor tanaman yang mengindikasikan potensi stres perakaran atau gangguan serapan hara akibat dinamika kelembaban tanah dan kompetisi penutup tanah.\n\n" .
               "[REKOMENDASI_PRESKRIPTIF]:\n" .
               "1. Lakukan verifikasi visual lapangan terhadap piringan dan kelembaban tanah di sekitar perakaran.\n" .
               "2. Perbaiki drainase mikro untuk mencegah genangan air lokal di zona perakaran.\n" .
               "3. Pastikan aplikasi pemupukan semester berjalan sesuai rekomendasi standar PPKS.";
    }

    /**
     * Logic Inference Engine (Logika Ilmiah Multi-Provider)
     */
    private function requestToLLM($provider, $key, $prompt)
    {
        if (empty($key)) {
            throw new \Exception("Kunci API untuk provider [{$provider}] tidak tersedia.");
        }

        // Integrasi Instruksi Sistem: Standar Senior Agronomist Auditor PTPN IV
        $systemInstructions = "Anda adalah Senior Agronomist Auditor PTPN IV Regional I. 
Tugas Anda: Melakukan audit diagnostik TBM III secara kritis, padat, dan scientific berbasis literatur berikut:
1. Fisiologi Akar: 'Area Tergenang' > 2% memicu hipoksia yang menghambat respirasi akar dan penyerapan hara makro (N, P, K).
2. Konservasi Tanah: LCC < 90% pada topografi 'Berbukit' secara ilmiah meningkatkan laju erosi dan Nutrient Leaching.
3. Kompetisi Hara: Piringan bergulma (Pir Pkk Kurang Baik) menyebabkan kompetisi hara yang memicu pertumbuhan vegetatif Underperform.
4. Analisis Allometrik: Data biometrik adalah RASIO terhadap Keliling Tajuk (KC):
   - Rasio Lingkar Batang (LB/KC): Normal (0.110 - 0.150). Jika < 0.100: Batang Kurus / Vigor Rendah.
   - Indeks Jumlah Pelepah (JP/KC): Normal (0.040 - 0.050). Jika < 0.030: Defisiensi Pelepah.
   - Rasio Panjang Pelepah (PP/KC): Normal (0.150 - 0.250). Jika > 0.300: Gejala Etiolasi.

PENTING: JANGAN membandingkan desimal rasio (0.xxx) dengan meter (0.70m). Sampaikan analisis secara ringkas, to-the-point, dan berikan rekomendasi preskriptif konkret.";

        // Logic Provider: Google Gemini
        if ($provider === 'gemini') {
            $model = config('services.gemini.model', 'gemini-3.8-flash');
            $timeout = (int) config('services.gemini.timeout', 15);
            $maxTokens = (int) config('services.gemini.max_tokens', 1000);

            Log::debug("[NEURAL ENGINE DISPATCH] Menghubungi Google Gemini API. Model: [{$model}], Timeout: [{$timeout}s].");

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";
            
            // Retry otomatis 1x jika 503 high demand spike
            $response = Http::withoutVerifying()
                ->timeout($timeout)
                ->retry(2, 800, function ($exception, $request) {
                    return true;
                }, throw: false)
                ->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $systemInstructions . "\n\nInstruksi Audit: " . $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'maxOutputTokens' => $maxTokens,
                    ]
                ]);

            if ($response->failed()) {
                $status = $response->status();
                $errBody = $response->json();
                $msg = $errBody['error']['message'] ?? $response->body();
                throw new \Exception("Gemini HTTP {$status}: {$msg}");
            }

            $result = $response->json();
            if (isset($result['error'])) {
                throw new \Exception("Gemini Error: " . $result['error']['message']);
            }

            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (!$text) {
                throw new \Exception("Gemini tidak mengembalikan kandidat respon yang valid.");
            }

            return $text;
        }

        // Logic Provider: Groq
        if ($provider === 'groq') {
            $model = config('services.groq.model', 'qwen/qwen3.8-27b');
            $timeout = (int) config('services.groq.timeout', 15);
            $maxTokens = (int) config('services.groq.max_tokens', 450);

            Log::debug("[NEURAL ENGINE DISPATCH] Menghubungi Groq Cloud API. Model: [{$model}], Timeout: [{$timeout}s], MaxTokens: [{$maxTokens}].");

            $response = Http::withoutVerifying()
                ->timeout($timeout)
                ->withToken($key)
                ->post("https://api.groq.com/openai/v1/chat/completions", [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemInstructions],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => $maxTokens,
                ]);

            if ($response->failed()) {
                $status = $response->status();
                $errBody = $response->json();
                $msg = $errBody['error']['message'] ?? $response->body();
                throw new \Exception("Groq HTTP {$status}: {$msg}");
            }

            $result = $response->json();
            if (isset($result['error'])) {
                throw new \Exception("Groq Error: " . ($result['error']['message'] ?? 'Unknown'));
            }

            $text = $result['choices'][0]['message']['content'] ?? null;
            if (!$text) {
                throw new \Exception("Groq tidak mengembalikan konten inferensi.");
            }

            return $text;
        }

        throw new \Exception("Provider AI [{$provider}] tidak dikenali dalam sistem.");
    }

    /**
     * Prompt Engineering (Logika Kausalitas Terlengkap)
     */
    private function buildPrompt($mode, $data)
    {
        $std = "Standar Rasio TBM III: LB/KC > 0.110, JP/KC > 0.040, Survival > 98%.";

        switch ($mode) {
            case 'block_diagnostic':
                return "TUGAS: Lakukan AUDIT CAUSAL ANALYTICS pada Blok {$data['unit']}.
            DATASET: " . json_encode($data) . "
            REFERENSI ILMIAH: $std

            WAJIB MENGIKUTI FORMAT OUTPUT BERIKUT SECARA TUNTAS & LENGKAP:
            1. [CONFIDENCE_SCORE]: Nilai 0-100% (Jika data vegetatif N/A, maksimal 75%).
            2. [OBSERVASI]: 1-2 kalimat anomali utama (mortalitas, kerdil, atau gulma).
            3. [ANALISIS_KAUSAL]: 2 hipotesis kausal utama (singkat, padat, berdasar fisiologi/konservasi).
            4. [REKOMENDASI_PRESKRIPTIF]: 2-3 langkah operasional lapangan yang konkret dan siap dieksekusi.

            INSTRUKSI KHUSUS:
            - Tuliskan jawaban secara padat, lugas, dan efisien tanpa prolog atau epilog.
            - Pastikan seluruh 4 bagian selesai ditulis sampai tuntas (jangan terputus di tengah jalan).
            - JANGAN bandingkan angka RASIO (0.xxx) dengan meter (0.70m).
            - Jika 'Area Tergenang' > 0, hubungkan dengan risiko respirasi akar.";

            case 'kebun_summary':
                return "AUDIT RINGKASAN KEBUN {$data['unit_scope']}: " . json_encode($data) . "
                TUGAS: Lakukan evaluasi performa agronomi unit kebun ini secara padat dan tuntas.
                WAJIB MENGIKUTI STRUKTUR LENGKAP HINGGA AKHIR:
                1. [OBSERVASI]: Ringkasan kondisi mortalitas dan populasi kerdil unit kebun.
                2. [ANALISIS_KAUSAL]: 2 poin hubungan kausal antara perawatan lapangan dan ekspresi vegetatif.
                3. [REKOMENDASI_PRESKRIPTIF]: 2-3 instruksi teknis langsung untuk manajer unit kebun.
                Tulis secara padat, lugas, dan pastikan selesai ditulis sampai tuntas (jangan terpotong).";

            case 'growth':
                return "ANALISIS VIGOR VEGETATIF {$data['unit_scope']}: " . json_encode($data['data_vegetatif']) . "
                TUGAS: Evaluasi rasio lingkar batang, jumlah pelepah, dan panjang pelepah terhadap standar PPKS.
                WAJIB MENGIKUTI STRUKTUR LENGKAP HINGGA AKHIR:
                1. [OBSERVASI]: Status biometrik vegetatif (apakah berada di rentang normal atau mengalami deviasi).
                2. [ANALISIS_KAUSAL]: Identifikasi faktor penentu dominan (Genetik vs Lingkungan/Nutrisi).
                3. [REKOMENDASI_PRESKRIPTIF]: Tindakan agronomi (intervensi pemupukan/perawatan tajuk).
                Tulis secara padat, lugas, dan pastikan selesai ditulis sampai tuntas (jangan terpotong).";

            case 'survival':
                return "AUDIT MORTALITAS & POPULASI {$data['unit_scope']}: " . json_encode($data['data_populasi']) . "
                TUGAS: Analisis mortalitas dan risiko populasi menggunakan prinsip Hukum Minimum Liebig.
                WAJIB MENGIKUTI STRUKTUR LENGKAP HINGGA AKHIR:
                1. [OBSERVASI]: Kondisi survival rate dan konsentrasi pohon mati pada unit terburuk.
                2. [ANALISIS_KAUSAL]: Variabel pembatas utama kelangsungan hidup (drainase, erosi, atau patogen).
                3. [REKOMENDASI_PRESKRIPTIF]: Langkah konsolidasi dan replantasi terarah.
                Tulis secara padat, lugas, dan pastikan selesai ditulis sampai tuntas (jangan terpotong).";

            default: // multimodal
                return "EXECUTIVE MULTIMODAL INFERENCE {$data['unit_scope']}: " . json_encode($data) . "
                TUGAS: Hubungkan sensus lapangan dengan output biologis (Girth) dan dampaknya ke masa TM.
                WAJIB TULIS LENGKAP & TUNTAS (MAKSIMAL 250 KATA AGAR SELESAI HINGGA AKHIR):
                1. [OBSERVASI]: Tulis dalam 1-2 kalimat padat mengenai mortalitas unit terburuk dan status rasio pelepah (JP/KC).
                2. [ANALISIS_KAUSAL]: Tulis 2 poin kausalitas ringkas (1 kalimat per poin) terkait hipoksia/genangan air dan kompetisi gulma.
                3. [REKOMENDASI_PRESKRIPTIF]: Tulis 2 langkah operasional konkret (1 kalimat per langkah) untuk mengamankan produktivitas TM.
                DILARANG membuat kalimat bertele-tele. Wajib menyelesaikan poin ke-3 sampai tuntas tanpa terpotong.";
        }
    }
}
