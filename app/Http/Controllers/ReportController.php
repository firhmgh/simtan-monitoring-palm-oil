<?php

namespace App\Http\Controllers;

use App\Models\DetailRekap;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Pratinjau Laporan 
     */
    public function preview()
    {
        $reports = DetailRekap::with('simtanForm')->get();
        return view('reports.preview', compact('reports'));
    }

    /**
     * Ekspor PDF 
     */
    public function downloadPDF(Request $request)
    {
        $data = DetailRekap::all();
        $pdf = Pdf::loadView('reports.pdf_template', compact('data'));

        return $pdf->download('Laporan_Monitoring_TBM_III.pdf');
    }

    /**
     * Ekspor Excel
     */
    public function downloadExcel()
    {
        // Integrasi dengan Maatwebsite\Excel
        return "Proses Download Excel...";
    }
}
