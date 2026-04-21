<?php

namespace App\Http\Controllers;

use App\Models\DetailRekaps;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 

class ReportController extends Controller
{
    /**
     * Pratinjau Laporan 
     */
    public function preview()
    {
        $reports = DetailRekaps::with('simtanForm')->get();
        return view('reports.preview', compact('reports'));
    }

    /**
     * Ekspor PDF 
     */
    public function downloadPDF(Request $request)
    {
        $data = DetailRekaps::all();
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