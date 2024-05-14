<?php

namespace App\Http\Controllers\Data;

use App\Helpers\ApiDisdik;
use Illuminate\Support\Str;
use App\Models\Data\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Data\LogSynchronize;
use App\Http\Controllers\Controller;
use App\Models\Data\Kecamatan;

class SekolahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('app.data.sekolah._index');
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
    public function show(Sekolah $sekolah)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sekolah $sekolah)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sekolah $sekolah)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sekolah $sekolah)
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
        $searchColumn = collect(['nama', 'npsn']);

        $currentPage = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = Sekolah::query();

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
                "sekolah_id" => $item->sekolah_id,
                "nama" => $item->nama,
                "npsn" => $item->npsn,
                "kode_kabupaten" => $item->kode_kabupaten,
                "kabupaten" => $item->kabupaten,
                "kode_kecamatan" => $item->kode_kecamatan,
                "kecamatan" => $item->kecamatan,
                "bentuk_pendidikan" => $item->bentuk_pendidikan,
                "status_sekolah" => $item->status_sekolah,
                "jml" => $item->jml,
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
    public function synchronize($kode_kecamatan)
    {
        $extra_info["token"] = env("TOKEN_API_DISDIK");
        $params["kode_kecamatan"] = $kode_kecamatan;
        $allowed_bentuk_pendidikan = ["SMP", "MTs"];
        try{
            $response = ApiDisdik::synchSekolah($kode_kecamatan);
            if($response['error']){
                $log = [
                    "id" => Str::uuid(),
                    "table" => "sekolah",
                    "params" => json_encode($params, JSON_PRETTY_PRINT),
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
                if(in_array($item["bentuk_pendidikan"], $allowed_bentuk_pendidikan)){
                    
                    $data = Sekolah::where("sekolah_id", $item["sekolah_id"])->first();
                    if(!$data){
                        $data = new Sekolah();
                        $data->id = Str::uuid();
                        $data->created_by = auth()->user()->username;
                        $data->sekolah_id = trim($item["sekolah_id"]);
                        $data->nama = trim($item["nama"]);
                        $data->npsn = trim($item["npsn"]);
                        $data->nss = trim($item["nss"]);
                        $data->bentuk_pendidikan_id = trim($item["bentuk_pendidikan_id"]);
                        $data->bentuk_pendidikan = trim($item["bentuk_pendidikan"]);
                        $data->status_sekolah_id = trim($item["status_sekolah_id"]);
                        $data->status_sekolah = trim($item["status_sekolah"]);
                        $data->alamat_jalan = trim($item["alamat_jalan"]);
                        $data->rt = trim($item["rt"]);
                        $data->rw = trim($item["rw"]);
                        $data->nama_dusun = trim($item["nama_dusun"]);
                        $data->kode_wilayah = trim($item["kode_wilayah"]);
                        $data->kode_desa_kelurahan = trim($item["kode_desa_kelurahan"]);
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
                        $data->nomor_telepon = trim($item["nomor_telepon"]);
                        $data->nomor_fax = trim($item["nomor_fax"]);
                        $data->email = trim($item["email"]);
                        $data->website = trim($item["website"]);
                        $data->raw_json = json_encode($item, JSON_PRETTY_PRINT);
                        $data->save();
                        $created++;
                    }
                    else{
                        $data->updated_by = auth()->user()->username;
                        $data->nama = trim($item["nama"]);
                        $data->npsn = trim($item["npsn"]);
                        $data->nss = trim($item["nss"]);
                        $data->bentuk_pendidikan_id = trim($item["bentuk_pendidikan_id"]);
                        $data->bentuk_pendidikan = trim($item["bentuk_pendidikan"]);
                        $data->status_sekolah_id = trim($item["status_sekolah_id"]);
                        $data->status_sekolah = trim($item["status_sekolah"]);
                        $data->alamat_jalan = trim($item["alamat_jalan"]);
                        $data->rt = trim($item["rt"]);
                        $data->rw = trim($item["rw"]);
                        $data->nama_dusun = trim($item["nama_dusun"]);
                        $data->kode_wilayah = trim($item["kode_wilayah"]);
                        $data->kode_desa_kelurahan = trim($item["kode_desa_kelurahan"]);
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
                        $data->nomor_telepon = trim($item["nomor_telepon"]);
                        $data->nomor_fax = trim($item["nomor_fax"]);
                        $data->email = trim($item["email"]);
                        $data->website = trim($item["website"]);
                        $data->raw_json = json_encode($item, JSON_PRETTY_PRINT);
                        $data->save();
                        $updated++;
                    }
                }
            }
            $extra_info["message"] = "Synchronize Sekolah Success. " . $created . " created, " . $updated . " updated.";

            // update jumlah sekolah pada tabel kecamatan
            $jumlahSekolah = Sekolah::where('kode_kecamatan', $kode_kecamatan)->count();
            $extra_info['jumlah_sekolah'] = $jumlahSekolah;

            $kecamatan = Kecamatan::where('kode_kecamatan', $kode_kecamatan)->first();
            if ($kecamatan) {
                $kecamatan->jml = $jumlahSekolah;
                $kecamatan->save();
            }

            $log = [
                "id" => Str::uuid(),
                "table" => "sekolah",
                "params" => json_encode($params, JSON_PRETTY_PRINT),
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
                "params" => json_encode($params, JSON_PRETTY_PRINT),
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

    public function showall($kode_kecamatan)
    {
        $data = Sekolah::where('kode_kecamatan', '=', $kode_kecamatan)->get();
        return response()->json([
            'success' => true,
            'message' => 'List of Sekolah',
            'data' => $data
        ]);
    }
}
