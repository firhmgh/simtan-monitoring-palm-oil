<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

// Arsitektur Model
use App\Models\SimtanForm;
use App\Models\DetailRekap;
use App\Models\LokasiKebun;
use App\Models\KorelasiVegetatif;
use App\Models\UploadLog;

// Arsitektur Service & Helper
use App\Services\ChartDataService;
use App\Services\SimtanFormService;
use App\Services\SpatialDataService;
use App\Services\AIService;
use App\Helpers\ExcelDataHelper;

/**
 * MonitoringController - Scopus Q1 Standardized
 * Fokus: Decision Support System (DSS) untuk Perkebunan Presisi PTPN IV.
 * Mengintegrasikan Agronomic Performance Matrix untuk analisis korelasi.
 */
class MonitoringController extends Controller
{
    protected $chartService;
    protected $spatialService;
    protected $aiService;

    // Standar Agronomi Nasional (Referensi: PPKS & Master Plan PTPN IV)
    const STD_LB_KC_INDEX     = 0.125; // Benchmark Rasio Lingkar Batang/KC (TBM III)
    const STD_JP_KC_INDEX     = 0.040; // Benchmark Indeks Jumlah Pelepah (TBM III)
    const STD_SURVIVAL_RATE   = 98.0;
    const STD_SPH_TARGET      = 143.0;



    public function __construct(ChartDataService $chartService, SpatialDataService $spatialService, AIService $aiService)
    {
        $this->chartService = $chartService;
        $this->spatialService = $spatialService;
        $this->aiService = $aiService;
        $this->middleware('auth');
    }

    /**
     * 1. DASHBOARD UTAMA (Analytical Engine)
     */
    public function index(Request $request)
    {
        $selectedSlug = $request->query('periode', session('monitoring_periode_slug', 'periode-3-2025'));
        if (!array_key_exists($selectedSlug, config('simtan.map_periode'))) {
            $selectedSlug = 'periode-3-2025';
        }

        $dbKey = config("simtan.map_periode.{$selectedSlug}.db_key");
        session(['monitoring_periode_slug' => $selectedSlug]);

        Log::info("Analytic Dashboard initiated", ['slug' => $selectedSlug, 'db_key' => $dbKey]);

        $hasData = DetailRekap::where('periode', $dbKey)->exists();

        $viewData = [
            'total_luas' => 0,
            'total_pokok' => 0,
            'avg_health' => 0,
            'populasi_compliance' => 0,
            'health_compliance' => 0,
            'peringkatKondisiPohonChartData' => [],
            'peringkatPemeliharaanChartData' => [],
            'korelasiVegetatifLabels' => [],
            'korelasiVegetatifLingkarBatang' => [],
            'korelasiVegetatifJumlahPelepah' => [],
            'korelasiVegetatifPanjangPelepah' => [],
            'tahunTanam' => [],
            'totalLuas' => [],
            'populasiLabels' => [],
            'populasiTarget' => [],
            'populasiActual' => [],
            'namaKebunTerluas' => [],
            'series' => [],
            'latestKebun' => collect()
        ];

        // Inisialisasi Matrix Agregat (Layer Decision Support)
        $agregat = [
            'vigor_index' => 0,
            'maintenance_score' => 0,
            'risk_index' => 0,
            'sph_actual' => 0,
            'survival_rate' => 0,
            'avg_girth' => 0,
            'compliance_rate' => 0,
            'deviasi_girth' => 0,
            'deviasi_survival' => 0,
            'correlation_insight' => 'Data tidak memadai untuk inferensi'
        ];

        if ($hasData) {
            // A. Ekstraksi Data Sensus & Pemeliharaan (Data Fusion)
            $stats = DetailRekap::where('is_total', 1)->where('periode', $dbKey)
                ->selectRaw('
                    SUM(luas_ha) as luas, 
                    SUM(pkk_normal) as pokok, 
                    AVG(persen_pkk_normal) as health,
                    AVG(persen_tutupan_kacangan) as avg_lcc,
                    AVG(persen_pir_pkk_kurang_baik) as avg_pir_buruk,
                    AVG(persen_area_tergenang) as avg_tergenang
                ')->first();

            $viewData['total_luas']  = (float) ($stats->luas ?? 0);
            $viewData['total_pokok'] = (int) ($stats->pokok ?? 0);
            $viewData['avg_health']  = round($stats->health ?? 0, 1);

            // B. Ekstraksi Data Biometrik (Korelasi Vegetatif)
            $vegStats = KorelasiVegetatif::where('periode', $dbKey)
                ->selectRaw('AVG(lingkar_batang) as girth, AVG(jumlah_pelepah) as frond')
                ->first();

            $agregat['avg_girth'] = (float) ($vegStats->girth ?? 0);
            $avg_frond = (float) ($vegStats->frond ?? 0);

            // C. PERHITUNGAN LOGIKA MATRIX (Rekomendasi Cerdas)

            // 1. Vigor Index (Kepatuhan Girth + Pelepah)
            $girth_comp = ($agregat['avg_girth'] > 0) ? ($agregat['avg_girth'] / self::STD_LB_KC_INDEX) * 100 : 0;
            $frond_comp = ($avg_frond > 0) ? ($avg_frond / self::STD_JP_KC_INDEX) * 100 : 0;
            $agregat['vigor_index'] = round(min(100, ($girth_comp + $frond_comp) / 2), 1);

            // 2. Maintenance Score (LCC & Kebersihan Piringan)
            $agregat['maintenance_score'] = round((($stats->avg_lcc ?? 0) + (100 - ($stats->avg_pir_buruk ?? 0))) / 2, 1);

            // 3. Risk & Productivity
            $agregat['risk_index'] = round($stats->avg_tergenang ?? 0, 2);
            $agregat['sph_actual'] = ($viewData['total_luas'] > 0) ? round($viewData['total_pokok'] / $viewData['total_luas'], 1) : 0;

            // 4. Insight Kausalitas (Untuk AI Narrative Context)
            if ($agregat['maintenance_score'] > 90 && $agregat['vigor_index'] < 80) {
                $agregat['correlation_insight'] = "Anomali: Pemeliharaan standar namun pertumbuhan stagnan (Potensi Masalah Genetika/Tanah).";
            } elseif ($agregat['risk_index'] > 5) {
                $agregat['correlation_insight'] = "Risiko Tinggi: Drainase buruk menghambat respirasi akar.";
            } else {
                $agregat['correlation_insight'] = "Normal: Pertumbuhan sejalan dengan intensitas pemeliharaan.";
            }

            // D. Hitung Compliance & Deviasi Original
            if ($viewData['total_luas'] > 0) {
                $viewData['populasi_compliance'] = round(($viewData['total_pokok'] / ($viewData['total_luas'] * self::STD_SPH_TARGET)) * 100, 1);
            }
            $viewData['health_compliance'] = round(($viewData['avg_health'] / self::STD_SURVIVAL_RATE) * 100, 1);
            $agregat['compliance_rate'] = $viewData['health_compliance'];
            $agregat['survival_rate'] = $viewData['avg_health'];
            $agregat['deviasi_girth'] = round($agregat['avg_girth'] - self::STD_LB_KC_INDEX, 3);
            $agregat['deviasi_survival'] = round($viewData['avg_health'] - self::STD_SURVIVAL_RATE, 2);

            // E. Integrasi Data Chart
            $viewData = array_merge(
                $viewData,
                $this->chartService->getPeringkatKondisiPohonData($dbKey),
                $this->chartService->getPeringkatPemeliharaanData($dbKey),
                $this->chartService->getKorelasiVegetatifChartData($dbKey),
                $this->chartService->getLuasArealTahunTanamData($dbKey),
                $this->chartService->getPopulasiPerformanceData($dbKey),
                $this->chartService->getLuasArealTahunTanamPerKebunData($dbKey)
            );

            // F. Unit Prioritas
            $viewData['latestKebun'] = DetailRekap::where('periode', $dbKey)->where('is_total', 1)
                ->orderBy('persen_pkk_normal', 'asc')->take(5)->get()->map(function ($r) {
                    $info = ExcelDataHelper::getInfoKebun($r->kebun, $r->distrik, 0);
                    $r->nama_kebun = $info['nama'];
                    $lokasi = LokasiKebun::where('kebun', $r->kebun)->first();
                    $r->kebun_id = $lokasi ? $lokasi->id : null;
                    $r->status_kesehatan = $r->persen_pkk_normal >= 95 ? 'Optimal' : ($r->persen_pkk_normal >= 85 ? 'Peringatan' : 'Kritis');
                    return $r;
                });
        }

        return view('index', array_merge($viewData, [
            'hasData' => $hasData,
            'activeSlug' => $selectedSlug,
            'listPeriode' => config('simtan.map_periode'),
            'agregat' => $agregat,
            'benchmarks' => [
                'std_survival' => self::STD_SURVIVAL_RATE,
                'std_girth' => self::STD_LB_KC_INDEX,
                'std_sph' => self::STD_SPH_TARGET
            ]
        ]));
    }

    /**
     * 2. DAFTAR KEBUN (Table View - Semua Logic Utuh)
     */
    public function dataKebun(Request $request)
    {
        $selectedSlug = $request->query('periode', session('monitoring_periode_slug', 'periode-3-2025'));
        $dbKey = config("simtan.map_periode.{$selectedSlug}.db_key") ?? 'SEPOKTNOVDES2025REKAP';
        session(['monitoring_periode_slug' => $selectedSlug]);

        $query = DetailRekap::where('periode', $dbKey)->where('is_total', 1);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('kebun', 'like', "%{$s}%")->orWhere('distrik', 'like', "%{$s}%");
            });
        }

        if ($request->filled('distrik') && $request->distrik !== 'Semua Distrik') {
            $query->where('distrik', $request->distrik);
        }

        $kebun = $query->orderBy('persen_pkk_normal', 'asc')->get()->map(function ($item) use ($dbKey) {
            $info = ExcelDataHelper::getInfoKebun($item->kebun, $item->distrik, 0);
            $item->nama_kebun = $info['nama'];
            $item->nama_distrik = $info['distrik'];
            $lokasi = LokasiKebun::where('kebun', $item->kebun)->first();
            $item->id = $lokasi ? $lokasi->id : $item->kebun;
            $item->total_luas = $item->luas_ha;
            $item->total_blok = DetailRekap::where('kebun', $item->kebun)->where('periode', $dbKey)->where('is_total', 0)->count();
            $item->status_label = $item->persen_pkk_normal >= self::STD_SURVIVAL_RATE ? 'Optimal' : ($item->persen_pkk_normal >= 90 ? 'Peringatan' : 'Kritis');
            $item->status_color = $item->persen_pkk_normal >= self::STD_SURVIVAL_RATE ? 'bg-success' : ($item->persen_pkk_normal >= 90 ? 'bg-warning' : 'bg-danger');
            return $item;
        });

        $stats = DetailRekap::where('is_total', 1)->where('periode', $dbKey)->selectRaw('SUM(luas_ha) as luas, SUM(pkk_normal) as pokok, AVG(persen_pkk_normal) as health')->first();
        $totalLuas = (float)($stats->luas ?? 0);
        $kpi = [
            'total_luas' => $totalLuas,
            'total_pokok' => (int)($stats->pokok ?? 0),
            'avg_health' => round($stats->health ?? 0, 1),
            'populasi_compliance' => $totalLuas > 0 ? round(($stats->pokok / ($totalLuas * self::STD_SPH_TARGET)) * 100, 1) : 0,
            'health_compliance' => round(($stats->health ?? 0) / self::STD_SURVIVAL_RATE * 100, 1),
            'agronomy_compliance' => round(($stats->health ?? 0) / self::STD_SURVIVAL_RATE * 100, 1),
        ];

        return view('apps.monitoring.data-kebun', [
            'kebun' => $kebun,
            'kpi' => $kpi,
            'distrikList' => ExcelDataHelper::getListDistrik(),
            'activeSlug' => $selectedSlug,
            'listPeriode' => config('simtan.map_periode')
        ]);
    }

    /**
     * 3. SMART DETAIL AREAL (Semua Logic Utuh)
     */
    public function detailAreal(Request $request, $id = null)
    {
        try {
            $slug = $request->query('periode', session('monitoring_periode_slug', 'periode-3-2025'));
            if (!array_key_exists($slug, config('simtan.map_periode'))) {
                $slug = 'periode-3-2025';
            }
            $dbKey = config("simtan.map_periode.{$slug}.db_key");
            session(['monitoring_periode_slug' => $slug]);

            if (!$id) {
                $worstUnit = DetailRekap::where('periode', $dbKey)->where('is_total', 1)->orderBy('persen_pkk_normal', 'asc')->first();
                if ($worstUnit) {
                    $lokasi = LokasiKebun::where('kebun', $worstUnit->kebun)->first();
                    return redirect()->route('monitoring.detail', ['id' => $lokasi ? $lokasi->id : $worstUnit->kebun, 'periode' => $slug]);
                }
                return redirect()->route('monitoring.data-kebun')->with('error', 'Dataset Spasial belum tersedia.');
            }

            $kebunModel = is_numeric($id) ? LokasiKebun::find($id) : LokasiKebun::where('kebun', $id)->first();
            if (!$kebunModel) {
                $kode = strtoupper($id);
                $info = ExcelDataHelper::getInfoKebun($kode, '', 0);
                $kebunModel = (object)['id' => $kode, 'kebun' => $kode, 'nama_kebun' => $info['nama'], 'nama_distrik' => 'N/A', 'distrik' => ''];
            } else {
                $info = ExcelDataHelper::getInfoKebun($kebunModel->kebun, $kebunModel->distrik, 0);
                $kebunModel->nama_kebun = $info['nama'];
                $kebunModel->nama_distrik = $info['distrik'];
            }

            $kodeKebun = $kebunModel->kebun;
            $configPeta = LokasiKebun::where('kebun', $kodeKebun)->whereNotNull('tile_url')->first();
            $kebunModel->tile_url = $configPeta ? $configPeta->tile_url : null;

            $availableLayerTypes = DB::table('kebun_layers')
                ->where('kebun_code', $kodeKebun)
                ->where('is_active', 1)
                ->pluck('layer_type')
                ->toArray();

            // Integrasi Data Spasial Lengkap (Batas, Blok, Pemeliharaan, LCC, dan Pohon)
            return view('apps.monitoring.detail-kebun', [
                'kebun'          => $kebunModel,
                'infoKebun'      => $this->chartService->getInfoKebunData($kodeKebun, $dbKey),
                'kondisiPohon'   => $this->chartService->getKondisiPohonData($kodeKebun, $dbKey),
                'arealTanaman'   => $this->chartService->getArealTanamanData($kodeKebun, $dbKey),
                'vegetatif'      => $this->chartService->getKorelasiVegetatifPerKebun($kodeKebun, $dbKey),

                // Sync GeoJSON Layers
                'availableLayers' => $availableLayerTypes,
                // 'geoJSON'        => $this->spatialService->getGeoJSON($kodeKebun, 'batas'),
                // 'geoJSON_blok'   => $this->spatialService->getGeoJSON($kodeKebun, 'blok'),
                // 'geoJSON_pemel'  => $this->spatialService->getGeoJSON($kodeKebun, 'pemeliharaan'),
                // 'geoJSON_lcc'    => $this->spatialService->getGeoJSON($kodeKebun, 'kacangan'),
                // 'geoJSON_pohon'  => $this->spatialService->getGeoJSON($kodeKebun, 'konpokok'),

                'lokasiPoints'   => LokasiKebun::where('kebun', $kodeKebun)->where('jenis_lokasi', '!=', 'MAP_METADATA')->get(),
                'statusCounts'   => $this->chartService->getBlockAnalysisData($kodeKebun, $dbKey)['statusCounts'],
                'blockStatuses'  => $this->chartService->getBlockAnalysisData($kodeKebun, $dbKey)['blockStatuses'],
                'activeSlug'     => $slug,
                'listPeriode'    => config('simtan.map_periode')
            ]);
        } catch (\Exception $e) {
            Log::error("[DETAIL ERROR] " . $e->getMessage());
            return redirect()->route('monitoring.data-kebun')->with('error', 'Gagal memuat detail.');
        }
    }

    /**
     * 4. SISTEM INGESTI DATA (Import) - Utuh 100%
     */
    public function importView()
    {
        return view('apps.monitoring.import', ['history' => UploadLog::with(['form', 'user'])->latest()->take(10)->get(), 'listPeriode' => config('simtan.map_periode')]);
    }

    public function importStore(Request $request)
    {
        $rules = ['file_excel' => 'required|file|max:10240', 'kategori_file' => 'required', 'judul_file' => 'required', 'personel' => 'required'];
        if ($request->kategori_file !== 'Lokasi Kebun') $rules['periode_data'] = 'required';
        $request->validate($rules);
        $file = $request->file('file_excel');
        $path = $file->store('uploads/simtan', 'public');
        $periodeValue = ($request->kategori_file === 'Lokasi Kebun') ? 'MASTER' : $request->periode_data;

        DB::beginTransaction();
        try {
            // 1. Validasi header & struktur kolom berkas Excel
            SimtanFormService::validateHeader($request->kategori_file, $file);
            $kode = $this->generateUniqueCode($request->kategori_file);
            
            // 2. Unggah data dan masukkan ke database
            $form = SimtanFormService::handleUpload(['kode_upload' => $kode, 'uploaded_by' => Auth::id(), 'personel_pj' => $request->personel, 'judul_file' => $request->judul_file, 'tanggal_upload' => now(), 'kategori_file' => $request->kategori_file, 'periode_data' => $periodeValue, 'notes' => $request->notes, 'file_path' => $path], $file);
            $rowCount = $this->getProcessedRowCount($request->kategori_file, $form->id);
            
            if ($rowCount === 0) {
                throw new \Exception("Data tidak ditemukan atau baris kosong.");
            }
            
            // 3. Catat log pengunggahan berhasil
            UploadLog::create(['simtan_form_id' => $form->id, 'user_id' => Auth::id(), 'nama_file' => $file->getClientOriginalName(), 'jenis_dataset' => $request->kategori_file, 'rows_imported' => $rowCount, 'status' => 'Success', 'message' => "Integrasi {$rowCount} baris data berhasil."]);
            
            DB::commit();
            return redirect()->route('monitoring.import')->with('success', "Berkas berhasil diimpor. Sebanyak {$rowCount} baris data berhasil diintegrasikan ke dalam basis data.");
        } catch (\Exception $e) {
            DB::rollBack();
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            
            // Catat log pengunggahan gagal dengan pesan kesalahan detil
            DB::table('upload_log')->insert(['user_id' => Auth::id(), 'nama_file' => $file->getClientOriginalName(), 'jenis_dataset' => $request->kategori_file, 'rows_imported' => 0, 'status' => 'Failed', 'message' => $e->getMessage(), 'created_at' => now()]);
            
            return back()->withInput()->with('error', 'Gagal memproses berkas. Silakan periksa kembali format Excel Anda: ' . $e->getMessage());
        }
    }

    public function importUpdate(Request $request, $id)
    {
        $request->validate(['judul_file' => 'required', 'kategori_file' => 'required', 'personel' => 'required']);
        try {
            $form = SimtanForm::findOrFail($id);
            $periode = ($request->kategori_file === 'Lokasi Kebun') ? 'MASTER' : ($request->periode_data ?? $form->periode_data);
            
            $form->update([
                'judul_file' => $request->judul_file, 
                'kategori_file' => $request->kategori_file, 
                'periode_data' => $periode, 
                'personel_pj' => $request->personel, 
                'notes' => ($form->notes ? $form->notes . " | " : "") . "Update: " . Auth::user()->name
            ]);
            
            return redirect()->route('monitoring.import')->with('success', 'Data metadata berkas berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui konfigurasi metadata: ' . $e->getMessage());
        }
    }

    public function importDestroy($id)
    {
        try {
            $form = SimtanForm::findOrFail($id);
            $form->delete();
            return redirect()->route('monitoring.import')->with('success', 'Data dan berkas Excel berhasil dihapus secara permanen dari sistem.');
        } catch (\Exception $e) {
            return redirect()->route('monitoring.import')->with('error', 'Gagal menghapus data berkas: ' . $e->getMessage());
        }
    }

    public function downloadFile($id)
    {
        try {
            $form = SimtanForm::findOrFail($id);
            if ($form->file_path && file_exists($p = storage_path('app/public/' . $form->file_path))) {
                return response()->download($p);
            }
            return back()->with('error', 'Gagal mengunduh berkas: Berkas fisik tidak ditemukan di sistem penyimpanan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh berkas: ' . $e->getMessage());
        }
    }

    public function riwayatData()
    {
        $logs = UploadLog::with(['user', 'form'])->latest()->get()->map(fn($l) => [
            'id' => $l->id,
            'tglUpload' => $l->created_at->format('d/m/y H:i'),
            'namaFile' => $l->nama_file,
            'pengunggah' => $l->user->name ?? 'System',
            'jenisDataset' => $l->jenis_dataset,
            'baris' => (int)$l->rows_imported,
            'status' => $l->status === 'Success' ? 'Sukses' : 'Gagal',
            'formId' => $l->simtan_form_id
        ]);
        return view('apps.monitoring.riwayat-data', ['logsJson' => $logs, 'listPengunggah' => collect($logs)->pluck('pengunggah')->unique()->values(), 'listJenis' => collect($logs)->pluck('jenisDataset')->unique()->values()]);
    }

    /**
     * 5. PELAPORAN (DSS Logic & Export)
     */
    public function laporan()
    {
        return view('apps.monitoring.laporan', [
            'listPeriode' => config('simtan.map_periode'),
            'listKebun'   => ExcelDataHelper::getDaftarKebunFull()
        ]);
    }

    /**
     * PRIVATE HELPER: Centralized Report Logic
     * Menjamin Preview HTML dan Cetak PDF selalu SINKRON.
     */
    private function prepareReportData(Request $request)
    {
        $kebunCode = trim($request->query('kebun'));
        $periodeSlug = trim($request->query('periode'));
        $includeAI = $request->query('include_ai') === 'true';
        $active_sections = json_decode($request->query('active_sections', '[]'), true) ?: ['summary', 'recom', 'block', 'trend', 'veg'];

        $dbKey = config("simtan.map_periode.{$periodeSlug}.db_key") ?? $periodeSlug;
        $daftarSemuaKebun = ExcelDataHelper::getDaftarKebunFull();
        $namaKebunLengkap = $daftarSemuaKebun[$kebunCode] ?? $kebunCode;

        // 1. Ekstraksi Data
        $blocks = DetailRekap::where('kebun', $kebunCode)->where('periode', $dbKey)->where('is_total', 0)->get();
        if ($blocks->isEmpty()) throw new \Exception("Data rincian unit $kebunCode tidak ditemukan untuk periode ini.");

        $stats = DetailRekap::where('kebun', $kebunCode)->where('periode', $dbKey)->where('is_total', 1)->first();
        $veg = KorelasiVegetatif::where('kebun', $kebunCode)->where('periode', $dbKey)->first();

        // 2. Kalkulasi Matrix Performa
        $vigor_index = ($veg) ? (($veg->lingkar_batang / self::STD_LB_KC_INDEX) * 100) : 0;
        $vigor_index = min(100, $vigor_index);
        $maintenance_score = ($stats) ? (($stats->persen_tutupan_kacangan + (100 - $stats->persen_pir_pkk_kurang_baik)) / 2) : 0;

        // 3. Integrasi Narasi AI
        $ai_narrative = null;
        if ($includeAI) {
            try {
                $raw = $this->aiService->generateExecutiveSummary($dbKey, 'multimodal', false, $kebunCode);
                $clean = str_replace(['**', '#', '>', '`'], '', $raw);
                $ai_narrative = preg_replace('/(\d+\.)/', "\n$1", $clean);
            } catch (\Exception $e) {
                $ai_narrative = "Analisis AI tidak tersedia sementara waktu.";
            }
        }

        return [
            'nama_kebun'        => $namaKebunLengkap,
            'periode_label'     => config("simtan.map_periode.{$periodeSlug}.label") ?? $dbKey,
            'blocks'            => $blocks,
            'ai_narrative'      => $ai_narrative,
            'survival_rate'     => round($stats->persen_pkk_normal ?? 0, 1),
            'maintenance_score' => round($maintenance_score, 1),
            'vigor_index'       => round($vigor_index, 1) ?? 0,
            'sph_actual'        => ($stats && $stats->luas_ha > 0) ? round($stats->pkk_normal / $stats->luas_ha, 1) : 0,
            'active_sections'   => $active_sections,
            'nama_penginput'    => Auth::user()->name ?? 'System Administrator',
            'tanggal_cetak'     => now()->translatedFormat('d F Y H:i') . ' WIB'
        ];
    }

    public function previewHTML(Request $request)
    {
        try {
            $data = $this->prepareReportData($request);
            return view('apps.monitoring.pdf_template', $data)->render();
        } catch (\Exception $e) {
            return response("<div style='color:red; padding:20px; font-weight:bold; background:#fff; border:1px solid red; border-radius:10px;'>Gagal memuat preview: {$e->getMessage()}</div>", 500);
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            $data = $this->prepareReportData($request);
            $pdf = Pdf::loadView('apps.monitoring.pdf_template', $data)->setPaper('a4', 'portrait');
            return $pdf->stream("Laporan_DSS_TBM3_{$data['nama_kebun']}.pdf");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function settings()
    {
        return view('apps.monitoring.settings', ['aiConfig' => DB::table('ai_configs')->first()]);
    }

    public function exportAuditCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Audit_Trail_SIMTAN_' . now()->format('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['No', 'ID Log', 'Timestamp', 'Nama Berkas', 'Pengunggah', 'Jenis Dataset', 'Volume Baris', 'Status', 'Keterangan']);

            $logs = UploadLog::with(['user'])->latest()->get();
            
            foreach ($logs as $i => $log) {
                fputcsv($file, [
                    $i + 1,
                    '#' . $log->id,
                    $log->created_at->format('d/m/y H:i'),
                    $log->nama_file,
                    $log->user->name ?? 'System',
                    $log->jenis_dataset,
                    $log->rows_imported,
                    $log->status === 'Success' ? 'Sukses' : 'Gagal',
                    $log->message
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function printAuditPdf()
    {
        try {
            $logs = UploadLog::with(['user'])->latest()->get()->map(fn($l) => [
                'id' => $l->id,
                'tglUpload' => $l->created_at->format('d/m/y H:i'),
                'namaFile' => $l->nama_file,
                'pengunggah' => $l->user->name ?? 'System',
                'jenisDataset' => $l->jenis_dataset,
                'baris' => (int)$l->rows_imported,
                'status' => $l->status === 'Success' ? 'Sukses' : 'Gagal',
                'keterangan' => $l->message
            ]);

            $summary = [
                'tgl_cetak' => now()->translatedFormat('d F Y H:i') . ' WIB',
                'penginput' => Auth::user()->name ?? 'System Administrator',
                'total' => $logs->count(),
                'sukses' => $logs->where('status', 'Sukses')->count(),
                'gagal' => $logs->where('status', 'Gagal')->count(),
            ];

            $pdf = Pdf::loadView('apps.monitoring.exports.pdf-audit-log', [
                'logs' => $logs,
                'summary' => $summary
            ])->setPaper('a4', 'landscape');

            return $pdf->stream("Laporan_Audit_Trail_SIMTAN_" . now()->format('Ymd_His') . ".pdf");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghasilkan PDF: ' . $e->getMessage());
        }
    }

    private function getProcessedRowCount($k, $f)
    {
        return match ($k) {
            'Rekap TBM' => DetailRekap::where('simtan_form_id', $f)->count(),
            'Korelasi Vegetatif' => KorelasiVegetatif::where('simtan_form_id', $f)->count(),
            'Lokasi Kebun' => LokasiKebun::where('simtan_form_id', $f)->count(),
            default => 0
        };
    }

    private function generateUniqueCode($k)
    {
        $p = match ($k) {
            'Rekap TBM' => 'RT',
            'Korelasi Vegetatif' => 'KV',
            'Lokasi Kebun' => 'LK',
            default => 'DOC'
        };
        $d = now()->format('Ym');
        $last = SimtanForm::where('kode_upload', 'LIKE', "{$p}-{$d}%")->orderBy('id', 'desc')->first();
        $num = $last ? intval(last(explode('-', $last->kode_upload))) + 1 : 1;
        return "{$p}-{$d}-" . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
