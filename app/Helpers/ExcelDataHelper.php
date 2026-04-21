<?php

namespace App\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExcelDataHelper
{
    /**
     * Mengembalikan info nama kebun dan distrik berdasarkan kode
     */
    public static function getInfoKebun($kodeKebun, $kodeDistrik, $luas)
    {
        $kodeKebun = strtoupper(trim($kodeKebun));
        $kodeDistrik = strtoupper(trim($kodeDistrik));

        $namaDistrik = [
            '1DL1' => 'Distrik Labuhan Batu I',
            '1DL2' => 'Distrik Labuhan Batu II',
            '1DL3' => 'Distrik Labuhan Batu III',
            '1DS1' => 'Distrik Deli Serdang I',
            '1DS2' => 'Distrik Deli Serdang II',
            '1DSH' => 'Distrik Asahan',
        ];

        $namaKebun = [
            '1KSD' => 'Kebun Sei Daun',
            '1KSK' => 'Kebun Sei Kebara',
            '1KAN' => 'Kebun Aek Nabara Utara',
            '1KAS' => 'Kebun Aek Nabara Selatan',
            '1KLJ' => 'Kebun Labuhan Haji',
            '1KMM' => 'Kebun Mambang Muda',
            '1KMS' => 'Kebun Merbau Selatan',
            '1KRP' => 'Kebun Rantau Prapat',
            '1KBB' => 'Kebun Bandar Betsy',
            '1KBN' => 'Kebun Bangun',
            '1KDH' => 'Kebun Dusun Hulu',
            '1KGM' => 'Kebun Gunung Monaco',
            '1KGP' => 'Kebun Gunung Pamela',
            '1KGR' => 'Kebun Gunung Para',
            '1KSA' => 'Kebun Silau Dunia',
            '1KBU' => 'Kebun Batang Toru',
            '1KHG' => 'Kebun Hapesong',
            '1KRB' => 'Kebun Rambutan',
            '1KSG' => 'Kebun Sarang Giting',
            '1KSP' => 'Kebun Sei Putih',
            '1KTR' => 'Kebun Tanah Raja',
            '1KAM' => 'Kebun Ambalutu',
            '1KDP' => 'Kebun Sei Dadap',
            '1KHP' => 'Kebun Huta Padang',
            '1KPM' => 'Kebun Pulau Mandi',
            '1KSL' => 'Kebun Sei Silau',
        ];

        return [
            'nama' => $namaKebun[$kodeKebun] ?? $kodeKebun,
            'distrik' => $namaDistrik[$kodeDistrik] ?? $kodeDistrik,
            'luas' => $luas,
            'kode_kebun' => $kodeKebun,
        ];
    }

    /**
     * Data peringkat kondisi pohon (chart peringkatKondisiPohonChart)
     */
    public static function formatKondisiPohonData(Collection $data): array
    {
        $formatted = $data->map(function ($item) {
            return [
                'kebun' => $item->kebun,
                'normal' => $item->persen_pkk_normal,
                'non_valuer' => $item->persen_pkk_non_valuer,
                'mati' => $item->persen_pkk_mati,
            ];
        })->values();

        return [
            'peringkatKondisiPohonChartData' => $formatted->toArray()
        ];
    }

    /**
     * Data peringkat pemeliharaan (chart peringkatPemeliharaanChart)
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
     * Data korelasi vegetatif (chart korelasiVegetatif) dengan Outlier Detection
     */
    public static function formatKorelasiVegetatifData(Collection $data): array
    {
        $labels = [];
        $lingkarBatang = [];
        $jumlahPelepah = [];
        $panjangPelepah = [];

        /** 
         * THRESHOLD DEFINITION (Standar Agronomi Kelapa Sawit TBM)
         * Data yang melebihi angka ini akan dianggap outlier/salah input.
         */
        $MAX_LINGKAR_BATANG = 250.0; // cm (Jika > 2.5 meter, data tidak wajar untuk TBM)
        $MAX_JUMLAH_PELEPAH = 120.0; // pcs (Normal TBM III berkisar 30-50 pelepah)
        $MAX_PANJANG_PELEPAH = 10.0;  // meter (Pelepah sawit TBM jarang melebihi 10m)

        foreach ($data as $item) {
            if (
                $item->lingkar_batang === null &&
                $item->jumlah_pelepah === null &&
                $item->panjang_pelepah === null
            ) {
                continue;
            }

            // --- LOGIKA OUTLIER DETECTION ---
            $isOutlier = false;
            $violationReason = "";

            if ((float)$item->lingkar_batang > $MAX_LINGKAR_BATANG) {
                $isOutlier = true;
                $violationReason .= "Lingkar Batang: {$item->lingkar_batang}cm; ";
            }
            if ((float)$item->jumlah_pelepah > $MAX_JUMLAH_PELEPAH) {
                $isOutlier = true;
                $violationReason .= "Jml Pelepah: {$item->jumlah_pelepah}; ";
            }
            if ((float)$item->panjang_pelepah > $MAX_PANJANG_PELEPAH) {
                $isOutlier = true;
                $violationReason .= "Panjang Pelepah: {$item->panjang_pelepah}m; ";
            }

            // Jika dideteksi outlier, catat ke log sistem dan jangan masukkan ke grafik
            if ($isOutlier) {
                Log::warning("Data Integritas: Outlier terdeteksi pada Kebun {$item->kebun} Blok {$item->blok}. Alasan: {$violationReason}");
                continue; // Lewati baris ini agar grafik tidak rusak (skewed)
            }
            // --------------------------------

            // Bentuk label sesuai format yang diinginkan
            $labelParts = [];
            if (!empty($item->tahun)) $labelParts[] = $item->tahun;
            if (!empty($item->tbm)) $labelParts[] = $item->tbm;
            if (!empty($item->kebun)) $labelParts[] = $item->kebun;
            if (!empty($item->topografi)) $labelParts[] = $item->topografi;
            if (!empty($item->blok)) $labelParts[] = $item->blok;

            $labels[] = implode(' - ', $labelParts);

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
     * Data kondisi pohon (chart kondisiPohonChart)
     */
    public static function getKondisiPohonData(Collection $collection)
    {
        $totalRow = $collection->first();
        if (!$totalRow) return [];

        return [
            'PKK NORMAL' => (float) ($totalRow['persen_pkk_normal'] ?? 0),
            'PKK NON VALUER/ KERDIL' => (float) ($totalRow['persen_pkk_non_valuer'] ?? 0),
            'PKK MATI' => (float) ($totalRow['persen_pkk_mati'] ?? 0),
        ];
    }

    /**
     * Data areal tanaman (chart arealTanamanChart)
     */
    public static function getArealTanamanData(Collection $collection)
    {
        $totalRow = $collection->first();
        if (!$totalRow) return [];

        return [
            'Kacangan' => (float) ($totalRow['persen_tutupan_kacangan'] ?? 0),
            'Pemeliharaan yang Kurang Baik' => (float) ($totalRow['persen_pir_pkk_kurang_baik'] ?? 0),
            'Areal Tergenang' => (float) ($totalRow['persen_area_tergenang'] ?? 0),
            'Anak Kayu' => (float) ($totalRow['kondisi_anak_kayu'] ?? 0),
        ];
    }

    /**
     * Lokasi Kebun
     */
    public static function getLokasiKebun(Collection $collection)
    {
        if ($collection->isEmpty()) {
            return [];
        }

        return $collection
            ->groupBy('kebun')
            ->map(function ($items, $kebun) {
                return [
                    'kebun' => $kebun,
                    'lokasi' => $items->filter(function ($item) {
                        return !empty($item->latitude) && !empty($item->longitude);
                    })
                        ->map(function ($item) {
                            $namaLokasi = $item->nama_lokasi;
                            $jenisLokasi = strtoupper($item->jenis_lokasi);

                            $isAfd = str_contains($namaLokasi, 'AFD') && $jenisLokasi === 'KANTOR AFDELING';
                            $label = $isAfd ? "{$jenisLokasi} - {$namaLokasi}" : $namaLokasi;

                            if ($isAfd) {
                                $kategori = 'kantor-afdeling';
                            } elseif ($jenisLokasi === 'KANTOR KEBUN') {
                                $kategori = 'kantor-kebun';
                            } else {
                                $kategori = 'lainnya';
                            }

                            return [
                                'label' => $label,
                                'kategori' => $kategori,
                                'latitude' => (float) $item->latitude,
                                'longitude' => (float) $item->longitude,
                            ];
                        })
                        ->values()
                ];
            })
            ->values();
    }

    /**
     * Normalisasi key untuk collection Excel
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
