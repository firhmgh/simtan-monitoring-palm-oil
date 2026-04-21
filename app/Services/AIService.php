<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Mengirim data ke OpenAI untuk dianalisis
     */
    public function askAI($mode, $data)
    {
        $apiKey = config('services.openai.key'); 
        
        if (!$apiKey) {
            return "API Key OpenAI belum dikonfigurasi di file .env.";
        }

        $prompt = $this->buildPrompt($mode, $data);

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o', // Anda bisa menggunakan gpt-3.5-turbo untuk hemat biaya
                    'messages' => [
                        [
                            'role' => 'system', 
                            'content' => 'Anda adalah pakar agronomi PTPN IV Regional I. Tugas Anda menganalisis data vegetatif dan operasional sawit TBM III. Berikan jawaban yang teknis, padat, dan solutif (preskriptif).'
                        ],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'];
            }

            Log::error('OpenAI Error: ' . $response->body());
            return "Mesin AI sedang sibuk. (Error: " . $response->status() . ")";

        } catch (\Exception $e) {
            Log::error('AI Service Exception: ' . $e->getMessage());
            return "Gagal menghubungkan ke Neural Engine. Pastikan koneksi internet tersedia.";
        }
    }

    /**
     * Membangun narasi prompt berdasarkan mode diagnostik
     */
    private function buildPrompt($mode, $data)
    {
        $prompt = "Lakukan analisis terhadap data monitoring TBM III berikut ini:\n\n";
        
        foreach ($data as $item) {
            $prompt .= "- Unit: {$item->kebun}, Lingkar Batang: {$item->lingkar_batang} cm, Jumlah Pelepah: {$item->jumlah_pelepah}, Panjang Pelepah: {$item->panjang_pelepah} m.\n";
        }

        if ($mode === 'growth') {
            $prompt .= "\nFokus: Analisis Vigor Pertumbuhan. Identifikasi unit mana yang pertumbuhannya di bawah standar dan berikan saran pemupukan spesifik.";
        } elseif ($mode === 'survival') {
            $prompt .= "\nFokus: Risiko Mortalitas. Berikan peringatan jika data vegetatif menunjukkan indikasi tanaman tidak sehat.";
        } elseif ($mode === 'maintenance') {
            $prompt .= "\nFokus: Efisiensi Pemeliharaan. Hubungkan data pertumbuhan ini dengan potensi efektivitas piringan dan pasar pikul.";
        } else {
            $prompt .= "\nFokus: Analisis Multimodal Terpadu. Berikan kesimpulan menyeluruh mengenai kondisi Regional I saat ini.";
        }

        $prompt .= "\nFormat jawaban: Maksimal 3 kalimat singkat dan tegas.";

        return $prompt;
    }
}