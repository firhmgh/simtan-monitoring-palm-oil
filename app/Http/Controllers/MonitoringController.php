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
 * Pengelolaan Data Monitoring dengan Notifikasi Global Toast
 */
class MonitoringController extends Controller
{
    protected $chartService;

    public function __construct(ChartDataService $chartService)
    {
        $this->chartService = $chartService;
        $this->middleware('auth');
    }

    /**
     * 1. DASHBOARD UTAMA
     */
    public function index()
    {
        $hasData = SimtanForms::exists();

        if (!$hasData) {
            return view('index', ['hasData' => false]);
        }

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
     * 2. UPLOAD & INTEGRASI DATA (Memicu Toast Centered)
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
            $kodeUpload = $this->generateUniqueCode();

            $formData = [
                'kode_upload'    => $kodeUpload,
                'uploaded_by'    => Auth::id(),
                'judul_file'     => $file->getClientOriginalName(),
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
                'message'       => "Berhasil mengintegrasikan data {$request->kategori_file}.",
                'created_at'    => now()
            ]);

            DB::commit();

            /**
             * Memicu Global Toast Centered (Emerald Style)
             * Logika: Session 'success' akan ditangkap oleh default.blade.php
             */
            return redirect()->route('monitoring.import')
                ->with('success', "Integrasi Data Berhasil! Kode: {$kodeUpload}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[IMPORT ERROR] " . $e->getMessage());

            /**
             * Memicu Global Toast Top Bar (Red Style)
             */
            return back()->with('error', 'Gagal Memproses Berkas: ' . $e->getMessage());
        }
    }

    /**
     * 3. DETAIL AREAL (Drill-down)
     */
    public function detailAreal($id)
    {
        try {
            $kebun = LokasiKebuns::findOrFail($id);
            $kodeKebun = $kebun->kebun;

            return view('apps.monitoring.detail', [
                'kebun'        => $kebun,
                'infoKebun'    => $this->chartService->getInfoKebunData($kodeKebun),
                'kondisiPohon' => $this->chartService->getKondisiPohonData($kodeKebun),
                'arealTanaman' => $this->chartService->getArealTanamanData($kodeKebun),
                'lokasiKebun'  => $this->chartService->getLokasiKebunData($kodeKebun),
            ]);
        } catch (\Exception $e) {
            // Memicu Toast Error jika ID tidak ditemukan
            return back()->with('error', 'Data unit kebun tidak ditemukan dalam database.');
        }
    }

    /**
     * 4. VIEW METHODS
     */
    public function dataKebun()
    {
        return view('apps.monitoring.data-kebun');
    }
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
    public function riwayatData()
    {
        $logs = UploadLogs::with('user')->latest()->get();
        return view('apps.monitoring.riwayat-data', compact('logs'));
    }
    public function settings()
    {
        return view('apps.monitoring.settings');
    }

    /**
     * Private Helper: Generate Kode LK-XXXX
     */
    private function generateUniqueCode()
    {
        $lastRecord = SimtanForms::orderBy('id', 'desc')->first();
        $lastNumber = $lastRecord && preg_match('/LK-(\d+)$/', $lastRecord->kode_upload, $m) ? intval($m[1]) : 0;
        return 'LK-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
