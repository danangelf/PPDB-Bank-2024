<?php

namespace App\Http\Controllers\Data;

use App\Helpers\ApiDisdik;
use App\Models\Data\Siswa;
use Illuminate\Support\Str;
use App\Models\Data\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Data\LogSynchronize;
use App\Http\Controllers\Controller;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('app.data.siswa._index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Siswa $siswa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Siswa $siswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        //
    }

    
    /**
     * Show all resources from storage with pagination.
     * 
     * 
     * @return \Illuminate\Http\Response and App\Models\Managements\Controllers
     * 
     */
    public function datatable(Request $request)
    {
        //
        //
        $searchColumn = collect(['nama', 'nik', 'nisn']);

        $currentPage = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = Siswa::query();

        if($search != ''){
            $searchColumn->map(function($item, $index) use($search, $query){
                if($index == 0) $query->where($item, 'ilike', '%' . $search . '%');
                else $query->orWhere($item, 'ilike', '%' . $search . '%');

            });
        }
        
        $query->orderBy('updated_at', 'desc');
        $query->orderBy('nama', 'asc');
        $objData = $query->paginate($perPage);
        $totalPage = $objData->lastPage();
        $totalRecord = $objData->total();

        // remap
        $objData = $objData->map(function($item){
            $jam = Carbon::parse($item->created_at)->diffInHours();
            if($jam > 24) {
                $updated_at = $item->updated_at ? Carbon::parse($item->updated_at)->format('d M Y H:i') : Carbon::parse($item->created_at)->format('d M Y H:i');
            }
            else
            {
                $updated_at = $item->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s', $item->updated_at)->diffForHumans() : Carbon::createFromFormat('Y-m-d H:i:s', $item->created_at)->diffForHumans();
            }
            return [
                "id" => $item->id,
                "peserta_didik_id" => $item->peserta_didik_id,
                "nik" => $item->nik,
                "nisn" => $item->nisn,
                "nama" => $item->nama,
                "no_kk" => $item->no_kk,
                "jenis_kelamin" => $item->jenis_kelamin,
                "tanggal_lahir" => $item->tanggal_lahir,
                "sekolah_id" => $item->sekolah_id,
                "updated_by" => $item->updated_by ? $item->updated_by : $item->created_by,
                "updated_at" => $updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'List of Controllers',
            'data' => $objData,
            'pagination' => [
                'page' => $currentPage,
                'per_page' => $perPage,
                'total_records' => $totalRecord,
                'total_page' => $totalPage
            ]
        ]);
    }
    public function synchronize($scope,$kode)
    {
        if($scope === "sekolah"){
            $response = $this->getAndStoreSiswa($kode);
        }
        else{
            $response = [
                "success" => false,
                "message" => "Scope not found"
            ];
        }
        return response()->json($response);
    }

    public function getAndStoreSiswa($npsn)
    {
        $extra_info["token"] = env("TOKEN_API_DISDIK");
        $params["npsn"] = $npsn;
        $allowed_tingkat_pendidikan = 9;
        try{
            $response = ApiDisdik::synchPesertaDidik($npsn);
            if($response['error']){
                $log = [
                    "id" => Str::uuid(),
                    "table" => "siswa",
                    "params" => json_encode($params, JSON_PRETTY_PRINT),
                    "status_result" => "failed",
                    "msg_result" => $response['message'],
                    "extra_info" => json_encode($extra_info, JSON_PRETTY_PRINT),
                    "created_by" => auth()->user()->username,
                ];
                
                LogSynchronize::create($log);
                
                return [
                    'success' => false,
                    'message' => $response['message']
                ];
            }

            $updated = 0;
            $created = 0;
            $sekolah_id = "";
            $rawData = $response["response"];
            foreach($rawData as $key => $item){
                if($key == 0){
                    $sekolah_id = trim($item["sekolah_id"]);
                }
                if($item['tingkat_pendidikan_id'] == $allowed_tingkat_pendidikan){
                    $data = Siswa::where("peserta_didik_id", trim($item["peserta_didik_id"]))->first();
                    if(!$data){
                        $data = new Siswa();
                        $data->id = Str::uuid();
                        $data->created_by = auth()->user()->username;
                        $data->peserta_didik_id = trim($item["peserta_didik_id"]);
                        $data->sekolah_id = trim($item["sekolah_id"]);
                        $data->nama = trim($item["nama"]);
                        $data->jenis_kelamin = trim($item["jenis_kelamin"]);
                        $data->nisn = trim($item["nisn"]);
                        $data->nik = trim($item["nik"]);
                        $data->no_kk = trim($item["no_kk"]);
                        $data->tempat_lahir = trim($item["tempat_lahir"]);
                        $data->tanggal_lahir = trim($item["tanggal_lahir"]);
                        $data->agama_id = trim($item["agama_id"]);
                        $data->agama = trim($item["agama"]);
                        $data->kewarganegaraan = trim($item["kewarganegaraan"]);
                        $data->alamat_jalan = trim($item["alamat_jalan"]);
                        $data->rt = trim($item["rt"]);
                        $data->rw = trim($item["rw"]);
                        $data->nama_dusun = trim($item["nama_dusun"]);
                        $data->kode_wilayah = trim($item["kode_wilayah"]);
                        $data->desa_kelurahan = trim($item["desa_kelurahan"]);
                        $data->kode_kecamatan = trim($item["kode_kecamatan"]);
                        $data->kecamatan = trim($item["kecamatan"]);
                        $data->kode_kabupaten = trim($item["kode_kabupaten"]);
                        $data->kabupaten = trim($item["kabupaten"]);
                        $data->kode_provinsi = trim($item["kode_provinsi"]);
                        $data->provinsi = trim($item["provinsi"]);
                        $data->kode_pos = trim($item["kode_pos"]);
                        $data->lintang = trim($item["lintang"]);
                        $data->bujur = trim($item["bujur"]);
                        $data->nik_ayah = trim($item["nik_ayah"]);
                        $data->nama_ayah = trim($item["nama_ayah"]);
                        $data->tahun_lahir_ayah = trim($item["tahun_lahir_ayah"]);
                        $data->pekerjaan_id_ayah = trim($item["pekerjaan_id_ayah"]);
                        $data->pekerjaan_ayah = trim($item["pekerjaan_ayah"]);
                        $data->penghasilan_id_ayah = trim($item["penghasilan_id_ayah"]);
                        $data->penghasilan_ayah = trim($item["penghasilan_ayah"]);
                        $data->jenjang_pendidikan_ayah = trim($item["jenjang_pendidikan_ayah"]);
                        $data->jenjang_pendidikan_ayah_keterangan = trim($item["jenjang_pendidikan_ayah_keterangan"]);
                        $data->nik_ibu = trim($item["nik_ibu"]);
                        $data->nama_ibu_kandung = trim($item["nama_ibu_kandung"]);
                        $data->tahun_lahir_ibu = trim($item["tahun_lahir_ibu"]);
                        $data->pekerjaan_id_ibu = trim($item["pekerjaan_id_ibu"]);
                        $data->pekerjaan_ibu = trim($item["pekerjaan_ibu"]);
                        $data->penghasilan_id_ibu = trim($item["penghasilan_id_ibu"]);
                        $data->penghasilan_ibu = trim($item["penghasilan_ibu"]);
                        $data->jenjang_pendidikan_ibu = trim($item["jenjang_pendidikan_ibu"]);
                        $data->jenjang_pendidikan_ibu_keterangan = trim($item["jenjang_pendidikan_ibu_keterangan"]);
                        $data->nik_wali = trim($item["nik_wali"]);
                        $data->nama_wali = trim($item["nama_wali"]);
                        $data->tahun_lahir_wali = trim($item["tahun_lahir_wali"]);
                        $data->pekerjaan_id_wali = trim($item["pekerjaan_id_wali"]);
                        $data->pekerjaan_wali = trim($item["pekerjaan_wali"]);
                        $data->penghasilan_id_wali = trim($item["penghasilan_id_wali"]);
                        $data->penghasilan_wali = trim($item["penghasilan_wali"]);
                        $data->jenjang_pendidikan_wali = trim($item["jenjang_pendidikan_wali"]);
                        $data->jenjang_pendidikan_wali_keterangan = trim($item["jenjang_pendidikan_wali_keterangan"]);
                        $data->nomor_telepon_rumah = trim($item["nomor_telepon_rumah"]);
                        $data->nomor_telepon_seluler = trim($item["nomor_telepon_seluler"]);
                        $data->layak_PIP = trim($item["layak_PIP"]);
                        $data->no_KIP = trim($item["no_KIP"]);
                        $data->nm_KIP = trim($item["nm_KIP"]);
                        $data->raw_json = json_encode($item, JSON_PRETTY_PRINT);
                        $data->save();
                        $created++;
                    }
                    else{
                        $data->updated_by = auth()->user()->username;
                        $data->sekolah_id = trim($item["sekolah_id"]);
                        $data->nama = trim($item["nama"]);
                        $data->jenis_kelamin = trim($item["jenis_kelamin"]);
                        $data->nisn = trim($item["nisn"]);
                        $data->nik = trim($item["nik"]);
                        $data->no_kk = trim($item["no_kk"]);
                        $data->tempat_lahir = trim($item["tempat_lahir"]);
                        $data->tanggal_lahir = trim($item["tanggal_lahir"]);
                        $data->agama_id = trim($item["agama_id"]);
                        $data->agama = trim($item["agama"]);
                        $data->kewarganegaraan = trim($item["kewarganegaraan"]);
                        $data->alamat_jalan = trim($item["alamat_jalan"]);
                        $data->rt = trim($item["rt"]);
                        $data->rw = trim($item["rw"]);
                        $data->nama_dusun = trim($item["nama_dusun"]);
                        $data->kode_wilayah = trim($item["kode_wilayah"]);
                        $data->desa_kelurahan = trim($item["desa_kelurahan"]);
                        $data->kode_kecamatan = trim($item["kode_kecamatan"]);
                        $data->kecamatan = trim($item["kecamatan"]);
                        $data->kode_kabupaten = trim($item["kode_kabupaten"]);
                        $data->kabupaten = trim($item["kabupaten"]);
                        $data->kode_provinsi = trim($item["kode_provinsi"]);
                        $data->provinsi = trim($item["provinsi"]);
                        $data->kode_pos = trim($item["kode_pos"]);
                        $data->lintang = trim($item["lintang"]);
                        $data->bujur = trim($item["bujur"]);
                        $data->nik_ayah = trim($item["nik_ayah"]);
                        $data->nama_ayah = trim($item["nama_ayah"]);
                        $data->tahun_lahir_ayah = trim($item["tahun_lahir_ayah"]);
                        $data->pekerjaan_id_ayah = trim($item["pekerjaan_id_ayah"]);
                        $data->pekerjaan_ayah = trim($item["pekerjaan_ayah"]);
                        $data->penghasilan_id_ayah = trim($item["penghasilan_id_ayah"]);
                        $data->penghasilan_ayah = trim($item["penghasilan_ayah"]);
                        $data->jenjang_pendidikan_ayah = trim($item["jenjang_pendidikan_ayah"]);
                        $data->jenjang_pendidikan_ayah_keterangan = trim($item["jenjang_pendidikan_ayah_keterangan"]);
                        $data->nik_ibu = trim($item["nik_ibu"]);
                        $data->nama_ibu_kandung = trim($item["nama_ibu_kandung"]);
                        $data->tahun_lahir_ibu = trim($item["tahun_lahir_ibu"]);
                        $data->pekerjaan_id_ibu = trim($item["pekerjaan_id_ibu"]);
                        $data->pekerjaan_ibu = trim($item["pekerjaan_ibu"]);
                        $data->penghasilan_id_ibu = trim($item["penghasilan_id_ibu"]);
                        $data->penghasilan_ibu = trim($item["penghasilan_ibu"]);
                        $data->jenjang_pendidikan_ibu = trim($item["jenjang_pendidikan_ibu"]);
                        $data->jenjang_pendidikan_ibu_keterangan = trim($item["jenjang_pendidikan_ibu_keterangan"]);
                        $data->nik_wali = trim($item["nik_wali"]);
                        $data->nama_wali = trim($item["nama_wali"]);
                        $data->tahun_lahir_wali = trim($item["tahun_lahir_wali"]);
                        $data->pekerjaan_id_wali = trim($item["pekerjaan_id_wali"]);
                        $data->pekerjaan_wali = trim($item["pekerjaan_wali"]);
                        $data->penghasilan_id_wali = trim($item["penghasilan_id_wali"]);
                        $data->penghasilan_wali = trim($item["penghasilan_wali"]);
                        $data->jenjang_pendidikan_wali = trim($item["jenjang_pendidikan_wali"]);
                        $data->jenjang_pendidikan_wali_keterangan = trim($item["jenjang_pendidikan_wali_keterangan"]);
                        $data->nomor_telepon_rumah = trim($item["nomor_telepon_rumah"]);
                        $data->nomor_telepon_seluler = trim($item["nomor_telepon_seluler"]);
                        $data->layak_PIP = trim($item["layak_PIP"]);
                        $data->no_KIP = trim($item["no_KIP"]);
                        $data->nm_KIP = trim($item["nm_KIP"]);
                        $data->raw_json = json_encode($item, JSON_PRETTY_PRINT);
                        $data->save();
                        $updated++;
                    }
                }
            }
            $extra_info["message"] = "Synchronize Siswa Success. " . $created . " created, " . $updated . " updated.";

            // update jumlah siswa pada tabel sekolah
            $jumlahSiswa = Siswa::where('sekolah_id', $sekolah_id)->count();
            $extra_info['jumlah_siswa'] = $jumlahSiswa;

            $school = Sekolah::where('sekolah_id', $sekolah_id)->first();
            if ($school) {
                $school->jml = $jumlahSiswa;
                $school->save();
            }

            // rekam log
            $log = [
                "id" => Str::uuid(),
                "table" => "siswa",
                "params" => json_encode($params, JSON_PRETTY_PRINT),
                "status_result" => "success",
                "result" => json_encode($response['response'], JSON_PRETTY_PRINT),
                "msg_result" => "",
                "extra_info" => json_encode($extra_info, JSON_PRETTY_PRINT),
                "created_by" => auth()->user()->username,
            ];

            LogSynchronize::create($log);

            return [
                'success' => true,
                'message' => $extra_info["message"],
                'data' => [
                    "created" => $created,
                    "updated" => $updated
                ]
            ];
        }
        catch(\Exception $e){
            $log = [
                "id" => Str::uuid(),
                "table" => "siswa",
                "params" => json_encode($params, JSON_PRETTY_PRINT),
                "status_result" => "failed",
                "msg_result" => $e->getMessage(),
                "extra_info" => json_encode($extra_info, JSON_PRETTY_PRINT),
                "created_by" => auth()->user()->username,
            ];
            
            LogSynchronize::create($log);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
