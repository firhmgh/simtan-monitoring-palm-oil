<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

// Import Model
use App\Models\SimtanForms;
use App\Models\DetailRekaps;
use App\Models\LokasiKebuns;
use App\Models\UploadLogs;

// Import Services
use App\Services\ChartDataService;
use App\Services\SimtanFormService;
use App\Services\SpatialDataService;

/**
 * MonitoringController
 * Pengelolaan Data Monitoring dengan Arsitektur Service-Layer
 * Berfungsi sebagai "Traffic Controller" antara View, Business Logic, dan GIS Logic
 */
class MonitoringController extends Controller
{
    protected $chartService;
    protected $spatialService;

    /**
     * Dependency Injection ChartDataService & SpatialDataService
     */
    public function __construct(ChartDataService $chartService, SpatialDataService $spatialService)
    {
        $this->chartService = $chartService;
        $this->spatialService = $spatialService;
        $this->middleware('auth');
    }

    /**
     * 1. DASHBOARD UTAMA (Index)
     * Menampilkan ringkasan chart global, KPI utama, dan Performa Agregat
     */
    public function index()
    {
        $hasData = SimtanForms::exists();

        if (!$hasData) {
            return view('index', ['hasData' => false]);
        }

        // A. Ambil data Chart Lengkap (Termasuk Stacked Chart Sebaran Luas)
        $chartData = array_merge(
            $this->chartService->getPeringkatKondisiPohonData(),
            $this->chartService->getPeringkatPemeliharaanData(),
            $this->chartService->getKorelasiVegetatifChartData(),
            $this->chartService->getLuasArealTahunTanamData(),
            $this->chartService->getPopulasiPerformanceData(),
            $this->chartService->getLuasArealTahunTanamPerKebunData()
        );

        // B. Hitung KPI Utama untuk Header Cards
        $kpiData = [
            'hasData'        => true,
            'total_luas'     => DetailRekaps::where('is_total', 1)->sum('luas_ha') ?: 0,
            'total_pokok'    => DetailRekaps::where('is_total', 1)->sum('pkk_normal') ?: 0,
            'avg_health'     => round(DetailRekaps::where('is_total', 1)->avg('persen_pkk_normal'), 1) ?: 0,
            'areal_produksi' => DetailRekaps::where('is_total', 1)->sum('luas_ha') ?: 0,
        ];

        // C. Data untuk Tabel Monitoring Terkini (5 Unit Kebun Terakhir)
        $latestKebuns = LokasiKebuns::latest()->take(5)->get();

        // D. Data Performa Agregat
        $agregat = [
            'survival_rate' => round(DetailRekaps::where('is_total', 1)->avg('persen_pkk_normal'), 2) ?: 0,
            'avg_girth'     => round(DB::table('korelasi_vegetatifs')->avg('lingkar_batang'), 2) ?: 0,
        ];

        return view('index', array_merge($kpiData, $chartData, [
            'latestKebuns' => $latestKebuns,
            'agregat'      => $agregat
        ]));
    }

    /**
     * 2. UPLOAD & INTEGRASI DATA
     */
    public function importStore(Request $request)
    {
        $request->validate([
            'file_excel'    => 'required|file|mimes:xlsx,xls|max:10240',
            'kategori_file' => 'required|string',
            'periode_data'  => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file_excel');
            $kodeUpload = $this->generateUniqueCode($request->kategori_file);

            $formData = [
                'kode_upload'    => $kodeUpload,
                'uploaded_by'    => Auth::id(),
                'judul_file'     => $request->judul_file ?? $file->getClientOriginalName(),
                'tanggal_upload' => now(),
                'kategori_file'  => $request->kategori_file,
                'periode_data'   => $request->periode_data,
                'notes'          => $request->notes ?? null,
            ];

            SimtanFormService::handleUpload($formData, $file);

            UploadLogs::create([
                'user_id'       => Auth::id(),
                'nama_file'     => $formData['judul_file'],
                'status'        => 'Success',
                'message'       => "Berhasil mengintegrasikan data {$request->kategori_file} dengan kode {$kodeUpload}.",
                'created_at'    => now()
            ]);

            DB::commit();

            return redirect()->route('monitoring.import')
                ->with('success', "Integrasi Data Berhasil! Kode: {$kodeUpload}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[IMPORT ERROR] " . $e->getMessage());
            return back()->with('error', 'Gagal Memproses Berkas: ' . $e->getMessage());
        }
    }

    /**
     * 3. DETAIL AREAL (Drill-down Spasial & GIS)
     * Mengintegrasikan data tabular dengan data Geospasial (GeoJSON & XYZ Tiles)
     */
    public function detailAreal($id)
    {
        try {
            $kebun = LokasiKebuns::findOrFail($id);
            $kodeKebun = $kebun->kebun;

            // Data Statistik & Atribut (ChartService)
            $infoKebun = $this->chartService->getInfoKebunData($kodeKebun);
            $kondisiPohon = $this->chartService->getKondisiPohonData($kodeKebun);
            $arealTanaman = $this->chartService->getArealTanamanData($kodeKebun);

            // Data Spasial & Peta (SpatialDataService)
            $geoJSON = $this->spatialService->getBlockGeoJSON($kodeKebun);
            $tileConfig = $this->spatialService->getOrthophotoConfig($kodeKebun);
            $statusAnalysis = $this->chartService->getBlockAnalysisData($kodeKebun);

            return view('apps.monitoring.detail', [
                'kebun'         => $kebun,
                'infoKebun'     => $infoKebun,
                'kondisiPohon'  => $kondisiPohon,
                'arealTanaman'  => $arealTanaman,
                'geoJSON'       => $geoJSON,
                'tileConfig'    => $tileConfig,
                'statusCounts'  => $statusAnalysis['statusCounts'],
                'blockStatuses' => $statusAnalysis['blockStatuses']
            ]);
        } catch (\Exception $e) {
            Log::error("[SPATIAL ERROR] " . $e->getMessage());
            return back()->with('error', 'Gagal memuat data spasial unit kebun.');
        }
    }

    /**
     * 4. DAFTAR KEBUN
     */
    public function dataKebun(Request $request)
    {
        // DATA DUMMY (Sesuai permintaan integrasi sebelumnya)
        $kebuns = collect([]);
        $kpi = [
            'total_luas'  => 2450,
            'total_pokok' => 342300,
            'avg_health'  => 82.8,
            'total_count' => 8
        ];
        $distrikList = ['Distrik Labuhan Batu I', 'Distrik Labuhan Batu II', 'Distrik Asahan'];

        return view('apps.monitoring.data-kebun', compact('kebuns', 'kpi', 'distrikList'));
    }

    /**
     * 5. VIEW METHODS
     */
    public function detailKebun()
    {
        // Biasanya diarahkan ke unit contoh pertama atau dashboard detail khusus
        return view('apps.monitoring.detail-kebun');
    }

    public function laporan()
    {
        return view('apps.monitoring.laporan');
    }
    public function importView()
    {
        return view('apps.monitoring.import');
    }
    public function settings()
    {
        return view('apps.monitoring.settings');
    }
    public function riwayatData()
    {
        $logs = UploadLogs::with('user')->latest()->get();
        return view('apps.monitoring.riwayat-data', compact('logs'));
    }

    /**
     * Private Helper: Generate Unique Code
     */
    private function generateUniqueCode($kategori)
    {
        $prefixMap = [
            'Rekap TBM'          => 'RT',
            'Korelasi Vegetatif' => 'KV',
            'Lokasi Kebun'       => 'LK',
        ];

        $prefix = $prefixMap[$kategori] ?? 'DOC';
        $datePart = now()->format('Ym');

        $lastRecord = SimtanForms::where('kode_upload', 'LIKE', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastRecord) {
            $segments = explode('-', $lastRecord->kode_upload);
            $lastNumber = intval(end($segments));
        }

        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return "{$prefix}-{$datePart}-{$newNumber}";
    }
}
