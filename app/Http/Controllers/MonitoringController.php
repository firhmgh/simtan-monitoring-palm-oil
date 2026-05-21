<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
use App\Helpers\ExcelDataHelper;

/**
 * MonitoringController - Scopus Q1 Standardized
 * Fokus: Decision Support System (DSS) untuk Perkebunan Presisi PTPN IV.
 */
class MonitoringController extends Controller
{
    protected $chartService;
    protected $spatialService;

    // Standar Agronomi Nasional (Referensi: PPKS)
    const STD_SURVIVAL_RATE   = 98.0;
    const STD_LINGKAR_BATANG  = 12.0;

    /**
     * MASTER PEMETAAN PERIODE (Professional Layer)
     * Menghubungkan Slug URL -> Database Key -> Label Manusia
     */
    protected $mapPeriode = [
        'periode-1-2025' => ['db_key' => 'JANFEBMARAPR2025REKAP', 'label' => 'Periode I (Jan - Apr 2025)'],
        'periode-2-2025' => ['db_key' => 'MEIJULJUNAGST2025REKAP', 'label' => 'Periode II (Mei - Agst 2025)'],
        'periode-3-2025' => ['db_key' => 'SEPOKTNOVDES2025REKAP',  'label' => 'Periode III (Sep - Des 2025)'],
        'tahunan-2025'   => ['db_key' => 'Tahun 2025',             'label' => 'Konsolidasi Tahun 2025'],
    ];

    public function __construct(ChartDataService $chartService, SpatialDataService $spatialService)
    {
        $this->chartService = $chartService;
        $this->spatialService = $spatialService;
        $this->middleware('auth');
    }

    /**
     * 1. DASHBOARD UTAMA (Analytical Engine)
     */
    public function index(Request $request)
    {
        $selectedSlug = $request->query('periode', session('monitoring_periode_slug', 'periode-3-2025'));
        if (!array_key_exists($selectedSlug, $this->mapPeriode)) {
            $selectedSlug = 'periode-3-2025';
        }

        $dbKey = $this->mapPeriode[$selectedSlug]['db_key'];
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

        if ($hasData) {
            $stats = DetailRekap::where('is_total', 1)->where('periode', $dbKey)
                ->selectRaw('SUM(luas_ha) as luas, SUM(pkk_normal) as pokok, AVG(persen_pkk_normal) as health')->first();

            $viewData['total_luas']  = (float) ($stats->luas ?? 0);
            $viewData['total_pokok'] = (int) ($stats->pokok ?? 0);
            $viewData['avg_health']  = round($stats->health ?? 0, 1);

            if ($viewData['total_luas'] > 0) {
                $viewData['populasi_compliance'] = round(($viewData['total_pokok'] / ($viewData['total_luas'] * 143)) * 100, 1);
            }
            $viewData['health_compliance'] = round(($viewData['avg_health'] / self::STD_SURVIVAL_RATE) * 100, 1);

            $viewData = array_merge(
                $viewData,
                $this->chartService->getPeringkatKondisiPohonData($dbKey),
                $this->chartService->getPeringkatPemeliharaanData($dbKey),
                $this->chartService->getKorelasiVegetatifChartData($dbKey),
                $this->chartService->getLuasArealTahunTanamData($dbKey),
                $this->chartService->getPopulasiPerformanceData($dbKey),
                $this->chartService->getLuasArealTahunTanamPerKebunData($dbKey)
            );

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

        $agregat = [
            'survival_rate' => $viewData['avg_health'],
            'avg_girth' => $hasData ? (KorelasiVegetatif::where('periode', $dbKey)->avg('lingkar_batang') ?: 0) : 0,
            'compliance_rate' => $viewData['health_compliance'],
            'deviasi_girth' => 0,
            'deviasi_survival' => round($viewData['avg_health'] - self::STD_SURVIVAL_RATE, 2)
        ];
        $agregat['deviasi_girth'] = round($agregat['avg_girth'] - self::STD_LINGKAR_BATANG, 2);

        return view('index', array_merge($viewData, [
            'hasData' => $hasData,
            'activeSlug' => $selectedSlug,
            'listPeriode' => $this->mapPeriode,
            'agregat' => $agregat,
            'benchmarks' => ['std_survival' => self::STD_SURVIVAL_RATE, 'std_girth' => self::STD_LINGKAR_BATANG]
        ]));
    }

    /**
     * 2. DAFTAR KEBUN (Table View)
     */
    public function dataKebun(Request $request)
    {
        $selectedSlug = $request->query('periode', session('monitoring_periode_slug', 'periode-3-2025'));
        $dbKey = $this->mapPeriode[$selectedSlug]['db_key'] ?? 'SEPOKTNOVDES2025REKAP';
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

            // PENTING: Assign total_luas agar muncul di Blade
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
            'populasi_compliance' => $totalLuas > 0 ? round(($stats->pokok / ($totalLuas * 143)) * 100, 1) : 0,
            'health_compliance' => round(($stats->health ?? 0) / self::STD_SURVIVAL_RATE * 100, 1),
            'agronomy_compliance' => round(($stats->health ?? 0) / self::STD_SURVIVAL_RATE * 100, 1),
        ];

        return view('apps.monitoring.data-kebun', [
            'kebun' => $kebun,
            'kpi' => $kpi,
            'distrikList' => ExcelDataHelper::getListDistrik(),
            'activeSlug' => $selectedSlug,
            'listPeriode' => $this->mapPeriode
        ]);
    }

    /**
     * 3. SMART DETAIL AREAL (Unified Navigation with Tile Configuration)
     */
    public function detailAreal(Request $request, $id = null)
    {
        try {
            // 1. Sinkronisasi Periode (Session-Persistent)
            $slug = $request->query('periode', session('monitoring_periode_slug', 'periode-3-2025'));
            if (!array_key_exists($slug, $this->mapPeriode)) {
                $slug = 'periode-3-2025';
            }
            $dbKey = $this->mapPeriode[$slug]['db_key'];
            session(['monitoring_periode_slug' => $slug]);

            // 2. LOGIKA SIDEBAR: Jika ID Kosong, cari unit dengan kesehatan terendah (DSS Strategy)
            if (!$id) {
                $worstUnit = DetailRekap::where('periode', $dbKey)
                    ->where('is_total', 1)
                    ->orderBy('persen_pkk_normal', 'asc')
                    ->first();

                if ($worstUnit) {
                    $lokasi = LokasiKebun::where('kebun', $worstUnit->kebun)->first();
                    if ($lokasi) return redirect()->route('monitoring.detail', ['id' => $lokasi->id, 'periode' => $slug]);
                    // Jika master lokasi kosong, gunakan kodenya sebagai ID
                    return redirect()->route('monitoring.detail', ['id' => $worstUnit->kebun, 'periode' => $slug]);
                }
                $fallback = LokasiKebun::first();
                if (!$fallback) return redirect()->route('monitoring.data-kebun')->with('error', 'Dataset Spasial belum tersedia.');
                return redirect()->route('monitoring.detail', ['id' => $fallback->id, 'periode' => $slug]);
            }

            // 3. Pencarian Model (Mendukung ID Numerik atau Kode Teks)
            $kebunModel = is_numeric($id) ? LokasiKebun::find($id) : LokasiKebun::where('kebun', $id)->first();

            if (!$kebunModel) {
                $kode = strtoupper($id);
                $info = ExcelDataHelper::getInfoKebun($kode, '', 0);
                $kebunModel = (object)[
                    'id' => $kode,
                    'kebun' => $kode,
                    'nama_kebun' => $info['nama'],
                    'nama_distrik' => 'Wilayah Belum Terdaftar',
                    'distrik' => ''
                ];
            } else {
                $info = ExcelDataHelper::getInfoKebun($kebunModel->kebun, $kebunModel->distrik, 0);
                $kebunModel->nama_kebun = $info['nama'];
                $kebunModel->nama_distrik = $info['distrik'];
            }

            $kodeKebun = $kebunModel->kebun;

            $vegetatif = $this->chartService->getKorelasiVegetatifPerKebun($kodeKebun, $dbKey);

            // 4. LOGIKA INTEGRASI TILE URL (Pembaruan Dinamis)
            // Cari baris metadata peta untuk unit kebun tersebut
            $configPeta = LokasiKebun::where('kebun', $kodeKebun)
                ->whereNotNull('tile_url')
                ->first();

            // Simpan tile_url ke dalam object kebunModel agar bisa dibaca Blade
            $kebunModel->tile_url = $configPeta ? $configPeta->tile_url : null;

            // 5. Ingesti Data Titik & Analitik via Service
            $lokasiPoints = LokasiKebun::where('kebun', $kodeKebun)
                ->where('jenis_lokasi', '!=', 'MAP_METADATA') // Kecualikan baris metadata dari marker
                ->get(['nama_lokasi', 'latitude', 'longitude', 'jenis_lokasi']);

            $viewData = [
                'kebun'         => $kebunModel,
                'infoKebun'     => $this->chartService->getInfoKebunData($kodeKebun, $dbKey),
                'kondisiPohon' => $this->chartService->getKondisiPohonData($kodeKebun, $dbKey),
                'arealTanaman'  => $this->chartService->getArealTanamanData($kodeKebun, $dbKey),
                'vegetatif'     => $vegetatif,
                'geoJSON'       => $this->spatialService->getGeoJSON($kodeKebun, 'batas'),
                'geoJSON_pemel' => $this->spatialService->getGeoJSON($kodeKebun, 'pemeliharaan'),
                'geoJSON_lcc'   => $this->spatialService->getGeoJSON($kodeKebun, 'kacangan'),
                'lokasiPoints'  => $lokasiPoints,
                'statusCounts'  => $this->chartService->getBlockAnalysisData($kodeKebun, $dbKey)['statusCounts'],
                'blockStatuses' => $this->chartService->getBlockAnalysisData($kodeKebun, $dbKey)['blockStatuses'],
                'activeSlug'    => $slug,
                'listPeriode'   => $this->mapPeriode
            ];

            return view('apps.monitoring.detail-kebun', $viewData);
        } catch (\Exception $e) {
            Log::error("[DETAIL ERROR] " . $e->getMessage());
            return redirect()->route('monitoring.data-kebun')->with('error', 'Terjadi kesalahan sistem memuat data analitik.');
        }
    }

    /**
     * 4. SISTEM INGESTI DATA (Import)
     */
    public function importView()
    {
        return view('apps.monitoring.import', [
            'history'     => UploadLog::with(['form', 'user'])->latest()->take(10)->get(),
            'listPeriode' => $this->mapPeriode
        ]);
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
            SimtanFormService::validateHeader($request->kategori_file, $file);
            $kode = $this->generateUniqueCode($request->kategori_file);
            $form = SimtanFormService::handleUpload(['kode_upload' => $kode, 'uploaded_by' => Auth::id(), 'personel_pj' => $request->personel, 'judul_file' => $request->judul_file, 'tanggal_upload' => now(), 'kategori_file' => $request->kategori_file, 'periode_data' => $periodeValue, 'notes' => $request->notes, 'file_path' => $path], $file);

            $rowCount = $this->getProcessedRowCount($request->kategori_file, $form->id);
            if ($rowCount === 0) throw new \Exception("Gagal: Data tidak ditemukan.");

            UploadLog::create(['simtan_form_id' => $form->id, 'user_id' => Auth::id(), 'nama_file' => $file->getClientOriginalName(), 'jenis_dataset' => $request->kategori_file, 'rows_imported' => $rowCount, 'status' => 'Success', 'message' => "Integrasi {$rowCount} baris."]);
            DB::commit();
            return redirect()->route('monitoring.import')->with('success', "Berhasil!");
        } catch (\Exception $e) {
            DB::rollBack();
            if (Storage::disk('public')->exists($path)) Storage::disk('public')->delete($path);
            DB::table('upload_log')->insert(['user_id' => Auth::id(), 'nama_file' => $file->getClientOriginalName(), 'jenis_dataset' => $request->kategori_file, 'rows_imported' => 0, 'status' => 'Failed', 'message' => $e->getMessage(), 'created_at' => now()]);
            return back()->withErrors(['system_error' => $e->getMessage()])->withInput();
        }
    }

    public function importUpdate(Request $request, $id)
    {
        $request->validate(['judul_file' => 'required', 'kategori_file' => 'required', 'personel' => 'required']);
        try {
            $form = SimtanForm::findOrFail($id);
            $periode = ($request->kategori_file === 'Lokasi Kebun') ? 'MASTER' : ($request->periode_data ?? $form->periode_data);
            $form->update(['judul_file' => $request->judul_file, 'kategori_file' => $request->kategori_file, 'periode_data' => $periode, 'personel_pj' => $request->personel, 'notes' => ($form->notes ? $form->notes . " | " : "") . "Update: " . Auth::user()->name]);
            return redirect()->route('monitoring.import')->with('success', 'Metadata diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['system_error' => $e->getMessage()]);
        }
    }

    public function importDestroy($id)
    {
        $form = SimtanForm::findOrFail($id);
        if ($form->file_path && Storage::disk('public')->exists($form->file_path)) Storage::disk('public')->delete($form->file_path);
        $form->delete();
        return redirect()->route('monitoring.import')->with('success', 'Data dimusnahkan.');
    }

    public function downloadFile($id)
    {
        $form = SimtanForm::findOrFail($id);
        if ($form->file_path && file_exists($p = storage_path('app/public/' . $form->file_path))) return response()->download($p);
        return back()->withErrors(['system_error' => 'Berkas tidak ditemukan.']);
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

    public function laporan()
    {
        return view('apps.monitoring.laporan');
    }
    public function settings()
    {
        return view('apps.monitoring.settings');
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

    private function translateToHuman($error)
    {
        if (str_contains($error, 'Mismatch')) return $error;
        if (str_contains($error, 'null')) return "Gagal: Kolom wajib kosong pada Excel.";
        return "Gagal: Format berkas tidak sesuai standar PTPN IV.";
    }
}
