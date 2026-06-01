<?php

namespace App\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExcelDataHelper
{
    /**
     * Mengembalikan info nama kebun dan distrik berdasarkan kode standar PTPN.
     * Digunakan untuk mengubah kode teknis (misal: 1KSD) menjadi nama manusiawi.
     */
    public static function getInfoKebun($kodeKebun, $kodeDistrik, $luas)
    {
        $kodeKebun = strtoupper(trim($kodeKebun));
        $kodeDistrik = strtoupper(trim($kodeDistrik));

        // Nama Distrik disesuaikan dengan Grouping pada Gambar
        $namaDistrik = [
            '1GLS'     => 'Unit Group Labuhan Batu Selatan',
            '1GLB'     => 'Unit Group Labuhan Batu',
            '1GSH'     => 'Unit Group Asahan',
            '1GS1'     => 'Unit Group Serdang I',
            '1GS2'     => 'Unit Group Serdang II',
        ];

        // Daftar Kebun disesuaikan dengan Kode dan Nama Resmi PTPN
        $namaKebun = [
            // Meranti Tujuh
            '1KSM' => 'Kebun Sei Meranti',
            '1KBT' => 'Kebun Batang Toru',

            // Unit Group Labuhan Batu Selatan
            '1KSD' => 'Kebun Sei Daun',
            '1KTO' => 'Kebun Tanjung Olak',
            '1KAT' => 'Kebun Aek Torop',
            '1KSK' => 'Kebun Sei Kebara',
            '1KSB' => 'Kebun Sei Baruhur',
            '1KAR' => 'Kebun Air Merah',

            // Unit Group Labuhan Batu
            '1KSU' => 'Kebun Sei Unit',
            '1KAS' => 'Kebun Aek Nabara Selatan',
            '1KAN' => 'Kebun Aek Nabara Utara',
            '1KRP' => 'Kebun Rantau Prapat',
            '1KMS' => 'Kebun Merbau Selatan',
            '1KLJ' => 'Kebun Labuhan Haji',
            '1KMM' => 'Kebun Mambang Muda',

            // Unit Group Asahan
            '1KSL' => 'Kebun Sei Silau',
            '1KDP' => 'Kebun Sei Dadap',
            '1KPM' => 'Kebun Pulau Mandi',
            '1KAM' => 'Kebun Ambalutu',
            '1KHP' => 'Kebun Huta Padang',
            '1KBS' => 'Kebun Bandar Selamat',

            // Unit Group Serdang I
            '1KBN' => 'Kebun Bangun',
            '1KGR' => 'Kebun Gunung Para',
            '1KGP' => 'Kebun Gunung Pamela',
            '1KGM' => 'Kebun Gunung Monaco',
            '1KSA' => 'Kebun Silau Dunia',
            '1KBB' => 'Kebun Bandar Betsy',
            '1KDH' => 'Kebun Dusun Hulu',

            // Unit Group Serdang II
            '1KRB' => 'Kebun Rambutan',
            '1KTR' => 'Kebun Tanah Raja',
            '1KSG' => 'Kebun Sarang Giting',
            '1KSP' => 'Kebun Sei Putih',
            '1KBU' => 'Kebun Bukit Tujuh',
            '1KHG' => 'Kebun Hapesong',

            // Aceh Timur
            '1KKI' => 'Kebun Karang Inong',
            '1KJA' => 'Kebun Julok Aceh',

            // Jawa Barat & Banten
            '1KCB' => 'Kebun Cikasungka',
            '1KBO' => 'Kebun Bojong Datar',
            '1KCI' => 'Kebun Cisalak',
            '1KKE' => 'Kebun Kertajaya',
            '1KPA' => 'Kebun Parigi',
            '1KKA' => 'Kebun Kertamanah',
            '1KTB' => 'Kebun Tambaksari',
        ];

        return [
            'nama' => $namaKebun[$kodeKebun] ?? $kodeKebun,
            'distrik' => $namaDistrik[$kodeDistrik] ?? $kodeDistrik,
            'luas' => (float) $luas,
            'kode_kebun' => $kodeKebun,
        ];
    }

    /**
     * Method Tambahan untuk Sinkronisasi Dropdown
     */
    public static function getListDistrik()
    {
        return [
            '1GLS'     => 'Unit Group Labuhan Batu Selatan',
            '1GLB'     => 'Unit Group Labuhan Batu',
            '1GSH'     => 'Unit Group Asahan',
            '1GS1'     => 'Unit Group Serdang I',
            '1GS2'     => 'Unit Group Serdang II',
        ];
    }

    public static function getDaftarKebunFull()
    {
        return [
            '1KSM' => 'Kebun Sei Meranti',
            '1KBT' => 'Kebun Batang Toru',
            '1KSD' => 'Kebun Sei Daun',
            '1KTO' => 'Kebun Tanjung Olak',
            '1KAT' => 'Kebun Aek Torop',
            '1KSK' => 'Kebun Sei Kebara',
            '1KSB' => 'Kebun Sei Baruhur',
            '1KAR' => 'Kebun Air Merah',
            '1KSU' => 'Kebun Sei Unit',
            '1KAS' => 'Kebun Aek Nabara Selatan',
            '1KAN' => 'Kebun Aek Nabara Utara',
            '1KRP' => 'Kebun Rantau Prapat',
            '1KMS' => 'Kebun Merbau Selatan',
            '1KLJ' => 'Kebun Labuhan Haji',
            '1KMM' => 'Kebun Mambang Muda',
            '1KSL' => 'Kebun Sei Silau',
            '1KDP' => 'Kebun Sei Dadap',
            '1KPM' => 'Kebun Pulau Mandi',
            '1KAM' => 'Kebun Ambalutu',
            '1KHP' => 'Kebun Huta Padang',
            '1KBS' => 'Kebun Bandar Selamat',
            '1KBN' => 'Kebun Bangun',
            '1KGR' => 'Kebun Gunung Para',
            '1KGP' => 'Kebun Gunung Pamela',
            '1KGM' => 'Kebun Gunung Monaco',
            '1KSA' => 'Kebun Silau Dunia',
            '1KBB' => 'Kebun Bandar Betsy',
            '1KDH' => 'Kebun Dusun Hulu',
            '1KRB' => 'Kebun Rambutan',
            '1KTR' => 'Kebun Tanah Raja',
            '1KSG' => 'Kebun Sarang Giting',
            '1KSP' => 'Kebun Sei Putih',
            '1KBU' => 'Kebun Bukit Tujuh',
            '1KHG' => 'Kebun Hapesong',
        ];
    }

    /**
     * Memformat data peringkat kondisi pohon untuk chart global.
     */
    public static function formatKondisiPohonData(Collection $data): array
    {
        $formatted = $data->map(function ($item) {
            return [
                'kebun' => $item->kebun,
                'normal' => (float) $item->persen_pkk_normal,
                'non_valuer' => (float) $item->persen_pkk_non_valuer,
                'mati' => (float) $item->persen_pkk_mati,
            ];
        })->values();

        return [
            'peringkatKondisiPohonChartData' => $formatted->toArray()
        ];
    }

    /**
     * Memformat data peringkat pemeliharaan untuk chart global.
     */
    public static function formatPemeliharaanData(Collection $data): array
    {
        $formatted = $data->map(function ($item) {
            return [
                'kebun' => $item->kebun,
                'kacangan' => (float) $item->persen_tutupan_kacangan,
                'pemeliharaan' => (float) $item->persen_pir_pkk_kurang_baik,
                'tergenang' => (float) $item->persen_area_tergenang,
                'anak_kayu' => (float) $item->kondisi_anak_kayu,
            ];
        })->values();

        return [
            'peringkatPemeliharaanChartData' => $formatted->toArray()
        ];
    }

    /**
     * Memformat data vegetatif dengan Logika Outlier Detection.
     * Mencegah grafik "rusak" akibat salah input angka yang tidak masuk akal (TBM III).
     */
    public static function formatKorelasiVegetatifData(Collection $data): array
    {
        $labels = [];
        $lingkarBatang = [];
        $jumlahPelepah = [];
        $panjangPelepah = [];

        // Threshold Agronomi TBM III
        $MAX_LINGKAR_BATANG = 250.0;
        $MAX_JUMLAH_PELEPAH = 120.0;
        $MAX_PANJANG_PELEPAH = 10.0;

        foreach ($data as $item) {
            if ($item->lingkar_batang === null && $item->jumlah_pelepah === null) continue;

            $isOutlier = false;
            if (
                (float)$item->lingkar_batang > $MAX_LINGKAR_BATANG ||
                (float)$item->jumlah_pelepah > $MAX_JUMLAH_PELEPAH ||
                (float)$item->panjang_pelepah > $MAX_PANJANG_PELEPAH
            ) {
                $isOutlier = true;
            }

            if ($isOutlier) {
                Log::warning("Outlier diabaikan pada Kebun {$item->kebun} Blok {$item->blok}");
                continue;
            }

            // PERBAIKAN DI SINI: Tambahkan TBM dan Topografi
            $labelParts = array_filter([
                $item->tahun,
                $item->tbm,        // Tambahkan ini
                $item->kebun,
                $item->topografi,  // Tambahkan ini (KUNCI UTAMA)
                $item->blok
            ]);

            $labels[] = implode(' - ', $labelParts); // Hasil: "2023 - TBM II - 1KAM - RATA S.D BERGELOMBANG - 26B"

            $lingkarBatang[] = (float) $item->lingkar_batang;
            $jumlahPelepah[] = (float) $item->jumlah_pelepah;
            $panjangPelepah[] = (float) $item->panjang_pelepah;
        }

        return [
            'korelasiVegetatifLabels' => $labels,
            'korelasiVegetatifLingkarBatang' => $lingkarBatang,
            'korelasiVegetatifJumlahPelepah' => $jumlahPelepah,
            'korelasiVegetatifPanjangPelepah' => $panjangPelepah,
        ];
    }

    /**
     * Format Pie Chart Kondisi Pohon (Halaman Detail Kebun)
     */
    public static function getKondisiPohonData(Collection $collection)
    {
        $totalRow = $collection->first();
        if (!$totalRow) return [];

        return [
            // Tambahkan round() untuk membatasi desimal dari database
            'PKK NORMAL' => round((float) ($totalRow['persen_pkk_normal'] ?? 0), 2),
            'PKK NON VALUER' => round((float) ($totalRow['persen_pkk_non_valuer'] ?? 0), 2),
            'PKK MATI' => round((float) ($totalRow['persen_pkk_mati'] ?? 0), 2),
        ];
    }

    /**
     * Format Pie Chart Areal Tanaman (Halaman Detail Kebun)
     */
    public static function getArealTanamanData(Collection $collection)
    {
        $totalRow = $collection->first();
        if (!$totalRow) return [];

        return [
            'Kacangan' => round((float) ($totalRow['persen_tutupan_kacangan'] ?? 0), 2),
            'Pemeliharaan Kurang Baik' => round((float) ($totalRow['persen_pir_pkk_kurang_baik'] ?? 0), 2),
            'Areal Tergenang' => round((float) ($totalRow['persen_area_tergenang'] ?? 0), 2),
            'Anak Kayu' => round((float) ($totalRow['kondisi_anak_kayu'] ?? 0), 2),
        ];
    }

    /**
     * Memproses Koordinat Lokasi Kebun untuk Leaflet JS / Google Maps.
     */
    public static function getLokasiKebun(Collection $collection)
    {
        if ($collection->isEmpty()) return [];

        return $collection->groupBy('kebun')->map(function ($items, $kebun) {
            return [
                'kebun' => $kebun,
                'lokasi' => $items->filter(fn($i) => !empty($i->latitude))
                    ->map(function ($item) {
                        $jenis = strtoupper($item->jenis_lokasi);
                        $kategori = match (true) {
                            str_contains($item->nama_lokasi, 'AFD') && $jenis === 'KANTOR AFDELING' => 'kantor-afdeling',
                            $jenis === 'KANTOR KEBUN' => 'kantor-kebun',
                            default => 'lainnya',
                        };

                        return [
                            'label' => $item->nama_lokasi,
                            'kategori' => $kategori,
                            'latitude' => (float) $item->latitude,
                            'longitude' => (float) $item->longitude,
                        ];
                    })->values()
            ];
        })->values();
    }

    /**
     * Utility: Membersihkan Header Excel agar bisa dibaca Database.
     * Contoh: "Luas (Ha)" -> "luas_ha"
     */
    public static function normalizeKeys(Collection $row)
    {
        return $row->mapWithKeys(function ($val, $key) {
            $key = strtolower(trim($key));
            $key = str_replace([' ', '/', '%', '(', ')'], ['_', '_', 'persen_', '', ''], $key);
            return [$key => $val];
        });
    }
}
