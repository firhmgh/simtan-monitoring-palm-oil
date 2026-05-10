<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// Model Architecture (Singular)
use App\Models\SimtanForm;
use App\Models\DetailRekap;
use App\Models\LokasiKebun;
use App\Models\KorelasiVegetatif;
use App\Models\UploadLog;

// Service Layer & Helpers
use App\Services\ChartDataService;
use App\Services\SimtanFormService;
use App\Services\SpatialDataService;
use App\Helpers\ExcelDataHelper;

/**
 * MonitoringController
 * Standar: Enterprise Resource Planning (ERP) - PTPN IV Regional I
 * Fitur: Multi-layer Accountability, Transactional Safety, Spatial Integration, 
 *        & Human-Readable Data Transformation (via ExcelDataHelper).
 */
class MonitoringController extends Controller
{
    protected $chartService;
    protected $spatialService;

    public function __construct(ChartDataService $chartService, SpatialDataService $spatialService)
    {
        $this->chartService = $chartService;
        $this->spatialService = $spatialService;
        $this->middleware('auth');
    }

    /**
     * 1. DASHBOARD UTAMA (Analytical Engine)
     */
    public function index()
    {
        $hasData = SimtanForm::exists();

        if (!$hasData) {
            return view('index', ['hasData' => false]);
        }

        // Penarikan Dataset Dashboard via Service
        $chartData = array_merge(
            $this->chartService->getPeringkatKondisiPohonData(),
            $this->chartService->getPeringkatPemeliharaanData(),
            $this->chartService->getKorelasiVegetatifChartData(),
            $this->chartService->getLuasArealTahunTanamData(),
            $this->chartService->getPopulasiPerformanceData(),
            $this->chartService->getLuasArealTahunTanamPerKebunData()
        );

        // Agregasi KPI Utama
        $kpiData = [
            'hasData'        => true,
            'total_luas'     => DetailRekap::where('is_total', 1)->sum('luas_ha') ?: 0,
            'total_pokok'    => DetailRekap::where('is_total', 1)->sum('pkk_normal') ?: 0,
            'avg_health'     => round(DetailRekap::where('is_total', 1)->avg('persen_pkk_normal'), 1) ?: 0,
            'areal_produksi' => DetailRekap::where('is_total', 1)->sum('luas_ha') ?: 0,
        ];

        // Ambil kebun terbaru dan transformasikan kodenya menggunakan Helper
        $latestKebuns = LokasiKebun::latest()->take(5)->get()->map(function ($kebun) {
            $info = ExcelDataHelper::getInfoKebun($kebun->kebun, $kebun->distrik, 0);
            $kebun->nama_kebun = $info['nama'];
            $kebun->nama_distrik = $info['distrik'];
            return $kebun;
        });

        $agregat = [
            'survival_rate' => round(DetailRekap::where('is_total', 1)->avg('persen_pkk_normal'), 2) ?: 0,
            'avg_girth'     => round(KorelasiVegetatif::avg('lingkar_batang'), 2) ?: 0,
        ];

        return view('index', array_merge($kpiData, $chartData, [
            'latestKebuns' => $latestKebuns,
            'agregat'      => $agregat
        ]));
    }

    /**
     * 2. DAFTAR KEBUN (Table View dengan Search & Transformation)
     */
    public function dataKebun(Request $request)
    {
        $query = LokasiKebun::query();

        // Fitur Search (Kebun atau Distrik)
        if ($request->filled('search')) {
            $query->where('kebun', 'like', '%' . $request->search . '%')
                ->orWhere('distrik', 'like', '%' . $request->search . '%');
        }

        $rawKebuns = $query->get();

        // Transformasi Data menggunakan ExcelDataHelper agar user melihat Nama Asli (bukan kode)
        $kebuns = $rawKebuns->map(function ($item) {
            $info = ExcelDataHelper::getInfoKebun($item->kebun, $item->distrik, 0);

            $item->nama_kebun = $info['nama'];
            $item->nama_distrik = $info['distrik'];

            // Hitung statistik per kebun secara dinamis
            $item->total_blok = DetailRekap::where('kebun', $item->kebun)->where('is_total', 0)->count();
            $item->total_luas = DetailRekap::where('kebun', $item->kebun)->where('is_total', 1)->sum('luas_ha') ?: 0;

            // Logic Status berdasarkan rata-rata kesehatan
            $health = DetailRekap::where('kebun', $item->kebun)->where('is_total', 1)->avg('persen_pkk_normal') ?: 0;
            $item->status_label = $health >= 90 ? 'Baik' : ($health >= 70 ? 'Perhatian' : 'Kritis');
            $item->status_color = $health >= 90 ? 'bg-success' : ($health >= 70 ? 'bg-warning' : 'bg-danger');

            return $item;
        });

        $kpi = [
            'total_luas'  => DetailRekap::where('is_total', 1)->sum('luas_ha') ?: 0,
            'total_pokok' => DetailRekap::where('is_total', 1)->sum('pkk_normal') ?: 0,
            'avg_health'  => round(DetailRekap::where('is_total', 1)->avg('persen_pkk_normal'), 1) ?: 0,
            'total_count' => LokasiKebun::count() ?: 0
        ];

        // Daftar Distrik untuk Filter (Transformasi Kode ke Nama)
        $distrikList = LokasiKebun::distinct()->pluck('distrik')->map(function ($kode) {
            $info = ExcelDataHelper::getInfoKebun('', $kode, 0);
            return $info['distrik'];
        })->prepend('Semua Distrik');

        return view('apps.monitoring.data-kebun', compact('kebuns', 'kpi', 'distrikList'));
    }

    /**
     * 3. INGESTI VIEW (Halaman Import & Ringkasan Riwayat)
     */
    public function importView()
    {
        $history = UploadLog::with(['form', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('apps.monitoring.import', compact('history'));
    }

    /**
     * 4. UPLOAD & INTEGRASI DATA (Atomic Ingestion dengan Rollback)
     */
    public function importStore(Request $request)
    {
        $request->validate([
            'file_excel'    => 'required|file|mimes:xlsx,xls|max:10240',
            'kategori_file' => 'required|string',
            'periode_data'  => 'required|string',
            'judul_file'    => 'required|string',
            'personel'      => 'required|string',
        ]);

        $file = $request->file('file_excel');
        $path = $file->store('uploads/simtan', 'public');

        DB::beginTransaction();

        try {
            SimtanFormService::validateHeader($request->kategori_file, $file);
            $kodeUpload = $this->generateUniqueCode($request->kategori_file);

            $formData = [
                'kode_upload'    => $kodeUpload,
                'uploaded_by'    => Auth::id(),
                'personel_pj'    => $request->personel,
                'judul_file'     => $request->judul_file,
                'tanggal_upload' => now(),
                'kategori_file'  => $request->kategori_file,
                'periode_data'   => $request->periode_data,
                'notes'          => $request->notes ?? null,
                'file_path'      => $path,
            ];

            $form = SimtanFormService::handleUpload($formData, $file);

            $rowCount = $this->getProcessedRowCount($request->kategori_file, $form->id);

            if ($rowCount === 0) {
                throw new \Exception("Gagal: Berkas Excel terbaca namun tidak ada baris data valid yang terdeteksi.");
            }

            UploadLog::create([
                'simtan_form_id' => $form->id,
                'user_id'        => Auth::id(),
                'nama_file'      => $file->getClientOriginalName(),
                'jenis_dataset'  => $request->kategori_file,
                'rows_imported'  => $rowCount,
                'status'         => 'Success',
                'message'        => "Berhasil mengintegrasikan {$rowCount} baris data.",
            ]);

            DB::commit();
            return redirect()->route('monitoring.import')->with('success', "Integrasi Berhasil! Kode: {$kodeUpload}");
        } catch (\Exception $e) {
            DB::rollBack();
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $humanMessage = $this->translateToHuman($e->getMessage());

            UploadLog::create([
                'simtan_form_id' => null,
                'user_id'        => Auth::id(),
                'nama_file'      => $file->getClientOriginalName(),
                'jenis_dataset'  => $request->kategori_file,
                'rows_imported'  => 0,
                'status'         => 'Failed',
                'message'        => $humanMessage,
            ]);

            Log::error("[INGESTION ERROR] " . $e->getMessage());
            return back()->withErrors(['system_error' => $humanMessage])->withInput();
        }
    }

    /**
     * 5. DETAIL KEBUN & SPASIAL VIEWS
     */
    public function detailAreal($id)
    {
        try {
            $kebunModel = LokasiKebun::findOrFail($id);

            // Transformasi nama kebun untuk tampilan detail
            $infoName = ExcelDataHelper::getInfoKebun($kebunModel->kebun, $kebunModel->distrik, 0);
            $kebunModel->nama_kebun = $infoName['nama'];
            $kebunModel->nama_distrik = $infoName['distrik'];

            $kodeKebun = $kebunModel->kebun;
            $infoKebun = $this->chartService->getInfoKebunData($kodeKebun);
            $kondisiPohon = $this->chartService->getKondisiPohonData($kodeKebun);
            $arealTanaman = $this->chartService->getArealTanamanData($kodeKebun);

            $geoJSON = $this->spatialService->getBlockGeoJSON($kodeKebun);
            $tileConfig = $this->spatialService->getOrthophotoConfig($kodeKebun);
            $statusAnalysis = $this->chartService->getBlockAnalysisData($kodeKebun);

            return view('apps.monitoring.detail', [
                'kebun'         => $kebunModel,
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
     * 6. UPDATE METADATA (Koreksi Administrasi)
     */
    public function importUpdate(Request $request, $id)
    {
        $request->validate([
            'judul_file'    => 'required|string',
            'kategori_file' => 'required|string',
            'periode_data'  => 'required|string',
            'personel'      => 'required|string',
        ]);

        try {
            $form = SimtanForm::findOrFail($id);
            $form->update([
                'judul_file'    => $request->judul_file,
                'kategori_file' => $request->kategori_file,
                'periode_data'  => $request->periode_data,
                'personel_pj'   => $request->personel,
                'notes'         => ($form->notes ? $form->notes . " | " : "") . "Koreksi metadata oleh " . Auth::user()->name . " tgl " . now()->format('d/m/Y'),
            ]);

            return redirect()->route('monitoring.import')->with('success', 'Metadata berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['system_error' => 'Gagal update: ' . $e->getMessage()]);
        }
    }

    /**
     * 7. DATA DESTRUCTION (Hapus & Cleanup)
     */
    public function importDestroy($id)
    {
        DB::beginTransaction();
        try {
            $form = SimtanForm::findOrFail($id);
            if ($form->file_path && Storage::disk('public')->exists($form->file_path)) {
                Storage::disk('public')->delete($form->file_path);
            }
            $form->delete();
            DB::commit();
            return redirect()->route('monitoring.import')->with('success', 'Data berhasil dimusnahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['system_error' => 'Gagal hapus: ' . $e->getMessage()]);
        }
    }

    /**
     * 8. DOWNLOAD BERKAS ASLI
     */
    public function downloadFile($id)
    {
        $form = SimtanForm::findOrFail($id);
        if ($form->file_path) {
            $fullPath = storage_path('app/public/' . $form->file_path);
            if (file_exists($fullPath)) {
                $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                $name = str_replace(' ', '_', $form->judul_file) . '.' . ($ext ?: 'xlsx');
                return response()->download($fullPath, $name);
            }
        }
        return back()->withErrors(['system_error' => 'Berkas fisik tidak ditemukan.']);
    }

    /**
     * 9. RIWAYAT DATA (Full Audit Logs)
     */
    public function riwayatData()
    {
        $logs = UploadLog::with('form.user')->latest()->get();
        return view('apps.monitoring.riwayat-data', compact('logs'));
    }

    /**
     * VIEW ROUTING & HELPERS
     */
    public function detailKebun()
    {
        return view('apps.monitoring.detail-kebun');
    }
    public function laporan()
    {
        return view('apps.monitoring.laporan');
    }
    public function settings()
    {
        return view('apps.monitoring.settings');
    }

    private function getProcessedRowCount($kategori, $formId)
    {
        return match ($kategori) {
            'Rekap TBM' => DetailRekap::where('simtan_form_id', $formId)->count(),
            'Korelasi Vegetatif' => KorelasiVegetatif::where('simtan_form_id', $formId)->count(),
            'Lokasi Kebun' => LokasiKebun::where('simtan_form_id', $formId)->count(),
            default => 0,
        };
    }

    private function translateToHuman($error)
    {
        if (str_contains($error, 'Mismatch')) return $error;
        if (str_contains($error, 'null') || str_contains($error, 'cannot be null'))
            return "Gagal: Kolom wajib (Blok/Afdeling) kosong pada Excel.";
        return "Gagal: Format berkas tidak sesuai standar sistem PTPN IV.";
    }

    private function generateUniqueCode($kategori)
    {
        $prefix = match ($kategori) {
            'Rekap TBM' => 'RT',
            'Korelasi Vegetatif' => 'KV',
            'Lokasi Kebun' => 'LK',
            default => 'DOC'
        };

        $datePart = now()->format('Ym');

        $lastRecord = SimtanForm::where('kode_upload', 'LIKE', "{$prefix}-{$datePart}%")
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
