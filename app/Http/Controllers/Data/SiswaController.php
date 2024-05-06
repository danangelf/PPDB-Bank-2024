<?php

namespace App\Http\Controllers\Data;

use App\Helpers\ApiDisdik;
use App\Models\Data\Siswa;
use Illuminate\Support\Str;
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
    public function synchronize($npsn)
    {
        $extra_info["token"] = env("TOKEN_API_DISDIK");
        $extra_info["npsn"] = $npsn;
        $allowed_bentuk_pendidikan = ["SMP", "MTs"];
        try{
            $response = ApiDisdik::synchPesertaDidik($npsn);
            if($response['error']){
                $log = [
                    "id" => Str::uuid(),
                    "table" => "siswa",
                    "status_result" => "failed",
                    "msg_result" => $response['message'],
                    "extra_info" => json_encode($extra_info, JSON_PRETTY_PRINT),
                    "created_by" => auth()->user()->username,
                ];
                
                LogSynchronize::create($log);
                
                return response()->json([
                    'success' => false,
                    'message' => $response['message']
                ]);
            }

            $updated = 0;
            $created = 0;

            $rawData = $response["response"];
            foreach($rawData as $item){
                // if(in_array($item["bentuk_pendidikan"], $allowed_bentuk_pendidikan)){
                    
                    $data = Siswa::where("peserta_didik_id", $item["peserta_didik_id"])->first();
                    if(!$data){
                        $data = new Siswa();
                        $data->id = Str::uuid();
                        $data->created_by = auth()->user()->username;
                        $data->sekolah_id = $item["sekolah_id"];
                        $data->nama = $item["nama"];
                        $data->npsn = $item["npsn"];
                        $data->nss = $item["nss"];
                        $data->bentuk_pendidikan_id = $item["bentuk_pendidikan_id"];
                        $data->bentuk_pendidikan = $item["bentuk_pendidikan"];
                        $data->status_sekolah_id = $item["status_sekolah_id"];
                        $data->status_sekolah = $item["status_sekolah"];
                        $data->alamat_jalan = $item["alamat_jalan"];
                        $data->rt = $item["rt"];
                        $data->rw = $item["rw"];
                        $data->nama_dusun = $item["nama_dusun"];
                        $data->kode_wilayah = $item["kode_wilayah"];
                        $data->kode_desa_kelurahan = $item["kode_desa_kelurahan"];
                        $data->desa_kelurahan = $item["desa_kelurahan"];
                        $data->kode_kecamatan = $item["kode_kecamatan"];
                        $data->kecamatan = $item["kecamatan"];
                        $data->kode_kabupaten = $item["kode_kabupaten"];
                        $data->kabupaten = $item["kabupaten"];
                        $data->kode_provinsi = $item["kode_provinsi"];
                        $data->provinsi = $item["provinsi"];
                        $data->kode_pos = $item["kode_pos"];
                        $data->lintang = $item["lintang"];
                        $data->bujur = $item["bujur"];
                        $data->nomor_telepon = $item["nomor_telepon"];
                        $data->nomor_fax = $item["nomor_fax"];
                        $data->email = $item["email"];
                        $data->website = $item["website"];
                        $data->save();
                        $created++;
                    }
                    else{
                        $data->updated_by = auth()->user()->username;
                        $data->nama = $item["nama"];
                        $data->npsn = $item["npsn"];
                        $data->nss = $item["nss"];
                        $data->bentuk_pendidikan_id = $item["bentuk_pendidikan_id"];
                        $data->bentuk_pendidikan = $item["bentuk_pendidikan"];
                        $data->status_sekolah_id = $item["status_sekolah_id"];
                        $data->status_sekolah = $item["status_sekolah"];
                        $data->alamat_jalan = $item["alamat_jalan"];
                        $data->rt = $item["rt"];
                        $data->rw = $item["rw"];
                        $data->nama_dusun = $item["nama_dusun"];
                        $data->kode_wilayah = $item["kode_wilayah"];
                        $data->kode_desa_kelurahan = $item["kode_desa_kelurahan"];
                        $data->desa_kelurahan = $item["desa_kelurahan"];
                        $data->kode_kecamatan = $item["kode_kecamatan"];
                        $data->kecamatan = $item["kecamatan"];
                        $data->kode_kabupaten = $item["kode_kabupaten"];
                        $data->kabupaten = $item["kabupaten"];
                        $data->kode_provinsi = $item["kode_provinsi"];
                        $data->provinsi = $item["provinsi"];
                        $data->kode_pos = $item["kode_pos"];
                        $data->lintang = $item["lintang"];
                        $data->bujur = $item["bujur"];
                        $data->nomor_telepon = $item["nomor_telepon"];
                        $data->nomor_fax = $item["nomor_fax"];
                        $data->email = $item["email"];
                        $data->website = $item["website"];
                        $data->save();
                        $updated++;
                    }
                // }
            }
            $extra_info["message"] = "Synchronize Siswa Success. " . $created . " created, " . $updated . " updated.";
            $log = [
                "id" => Str::uuid(),
                "table" => "siswa",
                "params" => "",
                "status_result" => "success",
                "result" => json_encode($response['response'], JSON_PRETTY_PRINT),
                "msg_result" => "",
                "extra_info" => json_encode($extra_info, JSON_PRETTY_PRINT),
                "created_by" => auth()->user()->username,
            ];
            
            LogSynchronize::create($log);

            return response()->json([
                'success' => true,
                'message' => $extra_info["message"],
                'data' => [
                    "created" => $created,
                    "updated" => $updated
                ]
            ]);
        }
        catch(\Exception $e){
            $log = [
                "id" => Str::uuid(),
                "table" => "sekolah",
                "status_result" => "failed",
                "msg_result" => $e->getMessage(),
                "extra_info" => json_encode($extra_info, JSON_PRETTY_PRINT),
                "created_by" => auth()->user()->username,
            ];
            
            LogSynchronize::create($log);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
