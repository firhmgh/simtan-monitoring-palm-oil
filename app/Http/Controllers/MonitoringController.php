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

/**
 * MonitoringController
 * Pengelolaan Data Monitoring dengan Arsitektur Service-Layer
 * Berfungsi sebagai "Traffic Controller" antara View dan Business Logic
 */
class MonitoringController extends Controller
{
    protected $chartService;

    /**
     * Dependency Injection ChartDataService
     */
    public function __construct(ChartDataService $chartService)
    {
        $this->chartService = $chartService;
        $this->middleware('auth');
    }

    /**
     * 1. DASHBOARD UTAMA (Index)
     * Menampilkan ringkasan chart global dan KPI utama
     */
    public function index()
    {
        $hasData = SimtanForms::exists();

        if (!$hasData) {
            return view('index', ['hasData' => false]);
        }

        // Mengambil seluruh data chart global melalui ChartDataService
        $chartData = array_merge(
            $this->chartService->getPeringkatKondisiPohonData(),
            $this->chartService->getPeringkatPemeliharaanData(),
            $this->chartService->getKorelasiVegetatifChartData(),
            $this->chartService->getLuasArealTahunTanamData(),
            $this->chartService->getPopulasiPerformanceData()
        );

        $kpiData = [
            'hasData'        => true,
            'total_luas'     => DetailRekaps::where('is_total', 1)->sum('luas_ha') ?: 0,
            'total_pokok'    => DetailRekaps::where('is_total', 1)->sum('pkk_normal') ?: 0,
            'avg_health'     => round(DetailRekaps::where('is_total', 1)->avg('persen_pkk_normal'), 1) ?: 0,
            'areal_produksi' => DetailRekaps::where('is_total', 1)->sum('luas_ha') ?: 0,
        ];

        return view('index', array_merge($kpiData, $chartData));
    }

    /**
     * 2. UPLOAD & INTEGRASI DATA
     * Menangani proses ingesti file Excel ke dalam sistem
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

            // Generate Kode Berdasarkan Kategori (Prefix RT, KV, atau LK)
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

            // Delegasikan pemrosesan file ke Service
            SimtanFormService::handleUpload($formData, $file);

            // Logging Aktivitas
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
     * 3. DETAIL AREAL (Drill-down)
     * Menampilkan performa spesifik untuk satu unit kebun tertentu
     */
    public function detailAreal($id)
    {
        try {
            $kebun = LokasiKebuns::findOrFail($id);
            $kodeKebun = $kebun->kebun;
            $infoKebun = $this->chartService->getInfoKebunData($kodeKebun);
            $kondisiPohon = $this->chartService->getKondisiPohonData($kodeKebun);
            $arealTanaman = $this->chartService->getArealTanamanData($kodeKebun);
            $spasialData = $this->chartService->getBlockAnalysisData($kodeKebun);

            return view('apps.monitoring.detail', [
                'kebun' => $kebun,
                'infoKebun' => $infoKebun,
                'kondisiPohon' => $kondisiPohon,
                'arealTanaman' => $arealTanaman,
                'statusCounts' => $spasialData['statusCounts'],
                'blockStatuses' => $spasialData['blockStatuses']
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Data unit kebun tidak ditemukan.');
        }
    }

    /**
     * 4. DAFTAR KEBUN (Dinamis)
     * Menampilkan tabel seluruh kebun dengan fungsi filter dan pencarian
     */
    public function dataKebun(Request $request)
    {
        /* 
    ==========================================================================
    LOGIKA BACKEND 
    ==========================================================================

    $query = LokasiKebuns::query();

    if ($request->filled('distrik') && $request->distrik != 'Semua Distrik') {
        $query->where('distrik', $request->distrik);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('kebun', 'like', '%' . $search . '%') // Pastikan nama kolom benar
              ->orWhere('distrik', 'like', '%' . $search . '%');
        });
    }

    $kebuns = $query->orderBy('kebun', 'asc')->get();

    $kpi = [
        'total_luas'  => DetailRekaps::where('is_total', 1)->sum('luas_ha') ?: 0,
        'total_pokok' => DetailRekaps::where('is_total', 1)->sum('pkk_normal') ?: 0,
        'avg_health'  => round(DetailRekaps::where('is_total', 1)->avg('persen_pkk_normal'), 1) ?: 0,
        'total_count' => LokasiKebuns::count()
    ];

    $distrikList = LokasiKebuns::select('distrik')->distinct()->pluck('distrik');
    
    ==========================================================================
    */

        // DATA DUMMY
        $kebuns = collect([]); // Collection kosong
        $kpi = [
            'total_luas'  => 2450,
            'total_pokok' => 342300,
            'avg_health'  => 82.8,
            'total_count' => 8
        ];
        $distrikList = [
            'Distrik Labuhan Batu I',
            'Distrik Labuhan Batu II',
            'Distrik Labuhan Batu III',
            'Distrik Deli Serdang I',
            'Distrik Deli Serdang II'
        ];

        return view('apps.monitoring.data-kebun', compact('kebuns', 'kpi', 'distrikList'));
    }

    /**
     * 5. VIEW METHODS (Routing Sederhana)
     */
    public function detailKebun()
    {
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
     * Private Helper: Generate Unique Code (Standard Enterprise)
     * Format: PREFIX-YYYYMM-0001 (Contoh: RT-202604-0001)
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

        // Cari record terakhir dengan prefix yang sama
        $lastRecord = SimtanForms::where('kode_upload', 'LIKE', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastRecord) {
            $lastNumber = 0;
        } else {
            $segments = explode('-', $lastRecord->kode_upload);
            $lastNumber = intval(end($segments));
        }

        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$datePart}-{$newNumber}";
    }
}
