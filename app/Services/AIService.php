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
        // Konfigurasi engine dari database ai_configs
        $this->config = DB::table('ai_configs')->first();
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
    public function askAI($mode, $contextData, $forceRefresh = false, $unitLabel = null)
    {
        if (!$this->config) return "Konfigurasi AI tidak ditemukan. Sila atur di menu Settings.";

        $prompt = $this->buildPrompt($mode, $contextData);
        $cacheKey = "ai_audit_" . $mode . "_" . md5($prompt);

        if ($forceRefresh) Cache::forget($cacheKey);

        return Cache::remember($cacheKey, 3600, function () use ($prompt, $mode, $unitLabel, $contextData) {
            // Logging Audit Trail
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
                Log::error("Log AI Error: " . $e->getMessage());
            }

            try {
                return $this->requestToLLM($this->config->provider_primary, $this->config->key_primary, $prompt);
            } catch (\Exception $e) {
                Log::warning("Primary AI failed: " . $e->getMessage());
                try {
                    return $this->requestToLLM($this->config->provider_backup, $this->config->key_backup, $prompt);
                } catch (\Exception $e2) {
                    return "Neural Engine Error: " . $e2->getMessage();
                }
            }
        });
    }

    /**
     * Logic Inference Engine (Logika Ilmiah Paling Lengkap)
     */
    private function requestToLLM($provider, $key, $prompt)
    {
        if (empty($key)) throw new \Exception("API Key belum dikonfigurasi.");

        // Integrasi Instruksi Sistem: Menggabungkan Literatur Agronomi dengan Standar Rasio Allometrik
        $systemInstructions = "Anda adalah Senior Agronomist Auditor PTPN IV Regional I. 
    Tugas Anda: Melakukan audit diagnostik TBM III secara kritis dan scientific berbasis literatur berikut:
    1. Fisiologi Akar: 'Area Tergenang' > 2% memicu kondisi hipoksia yang menghambat respirasi akar dan penyerapan hara makro (N, P, K).
    2. Konservasi Tanah: LCC < 90% pada topografi 'Berbukit' secara ilmiah meningkatkan laju erosi dan Nutrient Leaching (pencucian hara).
    3. Kompetisi Hara: Piringan bergulma (Pir Pkk Kurang Baik) menyebabkan kompetisi unsur hara yang mengakibatkan pertumbuhan vegetatif Underperform.
    4. Analisis Allometrik (Indeks Pertumbuhan): Data biometrik yang Anda terima adalah RASIO (Index) terhadap Keliling Tajuk (KC). 
       Gunakan parameter evaluasi berikut:
       - Rasio Lingkar Batang (LB/KC): Normal (0.110 - 0.150). Jika < 0.100, diagnosa sebagai 'Batang Kurus / Vigor Rendah'.
       - Indeks Jumlah Pelepah (JP/KC): Normal (0.040 - 0.050). Jika < 0.030, diagnosa sebagai 'Defisiensi Produksi Pelepah'.
       - Rasio Panjang Pelepah (PP/KC): Normal (0.150 - 0.250). Jika > 0.300, diagnosa sebagai 'Gejala Etiolasi' (pelepah terlalu panjang dan lemah).

    PENTING: JANGAN membandingkan angka desimal rasio (seperti 0.137 atau 0.044) dengan standar nilai absolut meter (seperti 0.70m) atau jumlah helai absolut. Evaluasi mutlak didasarkan pada rentang indeks rasio di atas. Karakteristik audit: Kritis, berbasis bukti (evidence-based), dan berikan rekomendasi preskriptif spesifik.";

        // Logic Provider: Google Gemini
        if ($provider === 'gemini') {
            $url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key={$key}";
            $response = Http::withoutVerifying()->post($url, [
                'contents' => [
                    ['parts' => [['text' => $systemInstructions . "\n\nInstruksi Audit: " . $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.4
                ]
            ]);

            $result = $response->json();
            if (isset($result['error'])) {
                throw new \Exception($result['error']['message']);
            }
            return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Gagal memproses respon Google Gemini.";
        }

        // Logic Provider: Groq (Llama)
        if ($provider === 'groq') {
            $response = Http::withoutVerifying()->withToken($key)->post("https://api.groq.com/openai/v1/chat/completions", [
                'model' => 'llama-3.1-8b-instant',
                'messages' => [
                    ['role' => 'system', 'content' => $systemInstructions],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.3,
            ]);

            $result = $response->json();
            if (isset($result['error'])) {
                throw new \Exception($result['error']['message']);
            }
            return $result['choices'][0]['message']['content'] ?? "Gagal memproses respon Groq.";
        }

        throw new \Exception("Provider AI [{$provider}] tidak didukung dalam sistem.");
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

            WAJIB MENGIKUTI FORMAT OUTPUT BERIKUT:
            1. [CONFIDENCE_SCORE]: Berikan nilai 0-100% berdasarkan kelengkapan data (Jika data vegetatif N/A, maksimal score 75%).
            2. [OBSERVASI]: Ringkasan kondisi anomali yang ditemukan.
            3. [ANALISIS_KAUSAL]: Jelaskan hubungan sebab-akibat (Contoh: Topografi Berbukit + LCC Rendah -> Risiko Pencucian Hara Tinggi).
            4. [REKOMENDASI_PRESKRIPTIF]: Langkah teknis operasional yang harus diambil.

            INSTRUKSI KHUSUS:
            - Gunakan terminologi Agronomi Senior.
            - JANGAN bandingkan angka RASIO (0.xxx) dengan meter (0.70m).
            - Jika 'Area Tergenang' > 0, hubungkan dengan risiko respirasi akar.";

            case 'kebun_summary':
                return "AUDIT RINGKASAN KEBUN {$data['unit_scope']}: " . json_encode($data) . "
                Tugas: Lakukan evaluasi spesifik terhadap performa agronomi unit kebun ini secara mandiri. 
                Bandingkan biometrik vegetatif dengan standar PPKS. 
                Berikan rekomendasi preskriptif langsung untuk manajer unit kebun tersebut.";

            case 'growth':
                return "ANALISIS VIGOR VEGETATIF PER REGIONAL: " . json_encode($data['data_vegetatif']) . "
                Bandingkan rata-rata Girth terhadap target 0.70m. 
                Jika terdapat deviasi, jelaskan kemungkinan faktor dominan (Genetik vs Environment). 
                Tentukan kategori: Superior, Average, atau Underperform.";

            case 'survival':
                return "AUDIT MORTALITAS & KONSOLIDASI BLOK: " . json_encode($data['data_populasi']) . "
                Gunakan Hukum Minimum Liebig: Identifikasi variabel mana yang paling membatasi kelangsungan hidup pohon. 
                Analisis risiko opportunity loss jika unit " . $data['data_populasi']['unit_terburuk'] . " tidak segera ditangani.";

            default: // multimodal
                return "EXECUTIVE MULTIMODAL INFERENCE: " . json_encode($data) . "
                Tugas: Menghubungkan kualitas perawatan lapangan (Sensus) dengan output biologis tanaman (Girth). 
                Jelaskan dampak anomali di unit terburuk terhadap masa TM (Tanaman Menghasilkan) di masa depan.";
        }
    }
}
