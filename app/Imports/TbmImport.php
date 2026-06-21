<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

/**
 * TbmImport (The Orchestrator)
 * Modular Upload Strategy.
 * Mampu memilah sheet berdasarkan metadata 'periode_data' yang dikirim dari controller.
 */
class TbmImport implements WithMultipleSheets, SkipsUnknownSheets
{
    protected $simtanFormId;
    protected $kodeUpload;
    protected $selectedPeriode;

    /**
     * @param int $simtanFormId ID parent form
     * @param string $kodeUpload Kode unik upload
     * @param string $selectedPeriode Value dari <select name="periode_data">
     */
    public function __construct($simtanFormId, $kodeUpload, $selectedPeriode)
    {
        $this->simtanFormId = $simtanFormId;
        $this->kodeUpload = $kodeUpload;
        $this->selectedPeriode = $selectedPeriode;
    }

    /**
     * Logika pemilihan sheet dinamis
     */
    public function sheets(): array
    {
        $sheets = [];

        // Daftar nama sheet fisik yang ada di file Excel
        $mapSheet = [
            'JANFEBMARAPR2025REKAP',
            'MEIJULJUNAGST2025REKAP',
            'SEPOKTNOVDES2025REKAP',
        ];

        // LOGIKA 1: JIKA USER MEMILIH TAHUNAN (AMBIL SEMUA)
        if ($this->selectedPeriode === 'Tahun 2025') {
            foreach ($mapSheet as $sheetName) {
                $sheets[$sheetName] = new DetailRekapImport(
                    $this->simtanFormId,
                    $this->kodeUpload,
                    $sheetName
                );
            }
        }
        // LOGIKA 2: JIKA USER MEMILIH PERIODE SPESIFIK (P1, P2, atau P3)
        else {
            if (in_array($this->selectedPeriode, $mapSheet)) {
                $sheets[$this->selectedPeriode] = new DetailRekapImport(
                    $this->simtanFormId,
                    $this->kodeUpload,
                    $this->selectedPeriode
                );
            }
        }

        /**
         * Keunggulan :
         * 1. Metadata-Driven: Data yang diambil ditentukan oleh input pengguna.
         * 2. Selective Processing: Menghemat memori server jika hanya 1 sheet yang perlu diproses.
         * 3. Consistency: Memastikan label periode di database sinkron dengan nama sheet.
         */
        return $sheets;
    }

    /**
     * Jika ada sheet di excel tapi tidak masuk dalam daftar di atas, abaikan saja
     */
    public function onUnknownSheet($sheetName)
    {
        // Silent skip
    }
}
