<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SpatialDataService;

class SpatialController extends Controller
{
    protected $spatialService;

    public function __construct(SpatialDataService $spatialService)
    {
        $this->spatialService = $spatialService;
        $this->middleware('auth');
    }

    /**
     * API: Mengambil Batas Blok (Poligon) beserta Data Kesehatan
     */
    public function getBlocks($kode_kebun)
    {
        $data = $this->spatialService->getBlockGeoJSON($kode_kebun);

        if (!$data) {
            return response()->json(['error' => 'Data spasial blok tidak ditemukan'], 404);
        }

        return response()->json($data);
    }

    /**
     * API: Mengambil Titik Individu Pohon (Point)
     * (Persiapan jika nanti Anda punya file GeoJSON khusus pohon)
     */
    public function getTrees($kode_kebun)
    {
        // Logika ini bisa dikembangkan jika Anda sudah memiliki data point pohon
        return response()->json(['message' => 'Layer titik pohon sedang dalam tahap sinkronisasi']);
    }
}