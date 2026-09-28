<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Imports\TbmImport;
use App\Models\SimtanForm;

/**
 * TbmSheetResolverTest
 * 
 * Memverifikasi ketahanan method sheets() pada TbmImport terhadap variasi input:
 * 1. Exact existing P1 name (JANFEBMARAPR2025REKAP)
 * 2. Exact existing P2 name (MEIJULJUNAGST2025REKAP)
 * 3. Exact existing P3 name (SEPOKTNOVDES2025REKAP)
 * 4. Exact existing annual name (Tahun 2025)
 * 5. Trailing whitespace ("JANFEBMARAPR2025REKAP  ")
 * 6. Leading whitespace ("  MEIJULJUNAGST2025REKAP")
 * 7. Case variations ("janfebmarapr2025rekap", "tahun 2025")
 * 8. Invalid sheet name ("SHEET_PALSU_2025")
 * 9. Structural verification method purgePhysicalFile() pada SimtanForm
 */
class TbmSheetResolverTest extends TestCase
{
    /**
     * Uji pemetaan sheet dengan nama eksak P1
     */
    public function test_exact_existing_p1_sheet_resolution()
    {
        $importer = new TbmImport(1, 'RT-2025-0001', 'JANFEBMARAPR2025REKAP');
        $sheets = $importer->sheets();

        $this->assertIsArray($sheets);
        $this->assertCount(1, $sheets);
        $this->assertArrayHasKey('JANFEBMARAPR2025REKAP', $sheets);
    }

    /**
     * Uji pemetaan sheet dengan nama eksak P2
     */
    public function test_exact_existing_p2_sheet_resolution()
    {
        $importer = new TbmImport(1, 'RT-2025-0001', 'MEIJULJUNAGST2025REKAP');
        $sheets = $importer->sheets();

        $this->assertCount(1, $sheets);
        $this->assertArrayHasKey('MEIJULJUNAGST2025REKAP', $sheets);
    }

    /**
     * Uji pemetaan sheet dengan nama eksak P3
     */
    public function test_exact_existing_p3_sheet_resolution()
    {
        $importer = new TbmImport(1, 'RT-2025-0001', 'SEPOKTNOVDES2025REKAP');
        $sheets = $importer->sheets();

        $this->assertCount(1, $sheets);
        $this->assertArrayHasKey('SEPOKTNOVDES2025REKAP', $sheets);
    }

    /**
     * Uji pemetaan sheet tahunan (harus memetakan seluruh 3 sheet)
     */
    public function test_exact_existing_annual_sheet_resolution()
    {
        $importer = new TbmImport(1, 'RT-2025-0001', 'Tahun 2025');
        $sheets = $importer->sheets();

        $this->assertCount(3, $sheets);
        $this->assertArrayHasKey('JANFEBMARAPR2025REKAP', $sheets);
        $this->assertArrayHasKey('MEIJULJUNAGST2025REKAP', $sheets);
        $this->assertArrayHasKey('SEPOKTNOVDES2025REKAP', $sheets);
    }

    /**
     * Uji toleransi terhadap trailing whitespace
     */
    public function test_trailing_whitespace_sheet_resolution()
    {
        $importer = new TbmImport(1, 'RT-2025-0001', 'JANFEBMARAPR2025REKAP   ');
        $sheets = $importer->sheets();

        $this->assertCount(1, $sheets);
        $this->assertArrayHasKey('JANFEBMARAPR2025REKAP', $sheets);
    }

    /**
     * Uji toleransi terhadap leading whitespace
     */
    public function test_leading_whitespace_sheet_resolution()
    {
        $importer = new TbmImport(1, 'RT-2025-0001', '   MEIJULJUNAGST2025REKAP');
        $sheets = $importer->sheets();

        $this->assertCount(1, $sheets);
        $this->assertArrayHasKey('MEIJULJUNAGST2025REKAP', $sheets);
    }

    /**
     * Uji toleransi terhadap huruf kecil (case insensitivity)
     */
    public function test_case_variation_sheet_resolution()
    {
        // P1 huruf kecil
        $importerP1 = new TbmImport(1, 'RT-2025-0001', 'janfebmarapr2025rekap');
        $sheetsP1 = $importerP1->sheets();
        $this->assertCount(1, $sheetsP1);
        $this->assertArrayHasKey('JANFEBMARAPR2025REKAP', $sheetsP1);

        // Tahunan huruf kecil
        $importerAnnual = new TbmImport(1, 'RT-2025-0001', 'tahun 2025');
        $sheetsAnnual = $importerAnnual->sheets();
        $this->assertCount(3, $sheetsAnnual);
    }

    /**
     * Uji nama sheet yang tidak valid (harus menghasilkan array kosong)
     */
    public function test_invalid_sheet_name_returns_empty()
    {
        $importer = new TbmImport(1, 'RT-2025-0001', 'SHEET_PALSU_2025');
        $sheets = $importer->sheets();

        $this->assertIsArray($sheets);
        $this->assertCount(0, $sheets);
    }

    /**
     * Verifikasi struktural metode purgePhysicalFile pada SimtanForm
     */
    public function test_simtan_form_has_purge_physical_file_method()
    {
        $this->assertTrue(method_exists(SimtanForm::class, 'purgePhysicalFile'));

        // Instansiasi model tanpa file_path harus me-return false secara aman tanpa error
        $form = new SimtanForm();
        $result = $form->purgePhysicalFile();
        $this->assertFalse($result);
    }

    /**
     * Verifikasi bahwa SimtanFormService::handleUpload memiliki return type declaration array
     * untuk mengembalikan [$form, $oldFilePath] tanpa physical purge di dalam method
     */
    public function test_handle_upload_signature_returns_array()
    {
        $reflection = new \ReflectionMethod(\App\Services\SimtanFormService::class, 'handleUpload');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull($returnType, 'handleUpload harus mendeklarasikan return type.');
        $this->assertEquals('array', $returnType->getName(), 'handleUpload harus mengembalikan array [$form, $oldFilePath].');
    }
}
