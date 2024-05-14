<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Data\Siswa;
use App\Models\Data\Sekolah;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DapodikController extends Controller
{
    /**
     * get data sekolah dari hasil singkronisasi data dapodik
     * 
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function getSekolah($id)
    {
        $sekolah = Sekolah::where('npsn', $id)->orWhere('sekolah_id', $id)->first();

        if (!$sekolah) {
            return response()->json([
                'message' => 'Sekolah not found',
                'status' => 404
            ], 404);
        }

        unset($sekolah->id);
        unset($sekolah->jml);
        unset($sekolah->raw_json);

        return response()->json([
            'status' => 200,
            'message' => 'Data sekolah hasil singkronisasi dapodik',
            'data' => $sekolah
        ], 200);
    }

    /**
     * get data siswa dari hasil singkronisasi data dapodik
     * 
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function getSiswa($id)
    {
        $siswa = Siswa::where('nisn', $id)->orWhere('nik', $id)->first();

        if (!$siswa) {
            return response()->json([
                'message' => 'Siswa not found',
                'status' => 404
            ], 404);
        }

        unset($siswa->id);
        unset($siswa->raw_json);

        return response()->json([
            'status' => 200,
            'message' => 'Data siswa hasil singkronisasi dapodik',
            'data' => $siswa
        ], 200);
    }
}
