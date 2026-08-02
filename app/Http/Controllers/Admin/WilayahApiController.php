<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Http\JsonResponse;

class WilayahApiController extends Controller
{
    public function getProvinsi(): JsonResponse
    {
        $provinsis = Provinsi::orderBy('nama', 'asc')->get(['id', 'kode', 'nama']);
        return response()->json($provinsis);
    }

    public function getKabupaten($provinsiId): JsonResponse
    {
        $kabupatens = Kabupaten::where('provinsi_id', $provinsiId)
            ->orderBy('nama', 'asc')
            ->get(['id', 'kode', 'nama', 'provinsi_id']);
        return response()->json($kabupatens);
    }

    public function getKecamatan($kabupatenId): JsonResponse
    {
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupatenId)
            ->orderBy('nama', 'asc')
            ->get(['id', 'kode', 'nama', 'kabupaten_id']);
        return response()->json($kecamatans);
    }

    public function getDesa($kecamatanId): JsonResponse
    {
        $desas = Desa::where('kecamatan_id', $kecamatanId)
            ->orderBy('nama', 'asc')
            ->get(['id', 'kode', 'nama', 'kecamatan_id']);
        return response()->json($desas);
    }
}
