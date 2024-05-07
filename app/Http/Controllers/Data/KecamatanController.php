<?php

namespace App\Http\Controllers\Data;

use App\Helpers\ApiDisdik;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Data\Kecamatan;
use Illuminate\Support\Carbon;
use App\Models\Data\LogSynchronize;
use App\Http\Controllers\Controller;

class KecamatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('app.data.kecamatan._index');
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
        $form = $request->validate([
            'kode_kabupaten' => 'required|numeric',
            'kabupaten' => 'required|string|max:255',
            'kode_kecamatan' => 'required|numeric',
            'kecamatan' => 'required|string|max:255',
        ]);

        $form['id'] = Str::uuid();
        $form['created_by'] = auth()->user()->username;

        $data = Kecamatan::create($form);

        return response()->json([
            'success' => true,
            'message' => 'Data Kecamatan Created Successfully',
            'data' => $data
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Kecamatan $kecamatan)
    {
        //
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Kecamatan',
            'data' => $kecamatan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kecamatan $kecamatan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kecamatan $kecamatan)
    {
        //
        $form = $request->validate([
            'kode_kabupaten' => 'required|numeric',
            'kabupaten' => 'required|string|max:255',
            'kode_kecamatan' => 'required|numeric',
            'kecamatan' => 'required|string|max:255',
        ]);

        $form['updated_by'] = auth()->user()->username;

        $kecamatan->update($form);

        return response()->json([
            'success' => true,
            'message' => 'Data Kecamatan Updated Successfully',
            'data' => $kecamatan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kecamatan $kecamatan)
    {
        //
        $kecamatan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Kecamatan Deleted Successfully',
        ]);
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
        $searchColumn = collect(['kabupaten', 'kode_kabupaten', 'kecamatan', 'kode_kecamatan']);

        $currentPage = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = Kecamatan::query();

        if($search != ''){
            $searchColumn->map(function($item, $index) use($search, $query){
                if($index == 0) $query->where($item, 'ilike', '%' . $search . '%');
                else $query->orWhere($item, 'ilike', '%' . $search . '%');

            });
        }
        
        $query->orderBy('kode_kabupaten', 'desc');
        $query->orderBy('kode_kecamatan', 'desc');
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
                "kode_kecamatan" => $item->kode_kecamatan,
                "kecamatan" => $item->kecamatan,
                "kode_kabupaten" => $item->kode_kabupaten,
                "kabupaten" => $item->kabupaten,
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

    public function synchronize()
    {
        $extra_info["token"] = env("TOKEN_API_DISDIK");
        try{
            $response = ApiDisdik::synchKecamatan();
            if($response['error']){
                $log = [
                    "id" => Str::uuid(),
                    "table" => "kecamatan",
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
                $kec = Kecamatan::where("kode_kecamatan", $item["kode_kecamatan"])->first();
                if(!$kec){
                    $kec = new Kecamatan();
                    $kec->id = Str::uuid();
                    $kec->created_by = auth()->user()->username;
                    $kec->kode_kecamatan = trim($item["kode_kecamatan"]);
                    $kec->kecamatan = trim($item["kecamatan"]);
                    $kec->kode_kabupaten = trim($item["kode_kabupaten"]);
                    $kec->kabupaten = trim($item["kabupaten"]);
                    $kec->save();
                    $created++;
                }
                else{
                    $kec->updated_by = auth()->user()->username;
                    $kec->kecamatan = trim($item["kecamatan"]);
                    $kec->kode_kabupaten = trim($item["kode_kabupaten"]);
                    $kec->kabupaten = trim($item["kabupaten"]);
                    $kec->save();
                    $updated++;
                }
            }

            $log = [
                "id" => Str::uuid(),
                "table" => "kecamatan",
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
                "table" => "kecamatan",
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

    public function showall()
    {
        $kabkota = Kecamatan::query()
                    ->select('kode_kabupaten', 'kabupaten')
                    ->groupBy('kode_kabupaten','kabupaten')->get();
        $kecamatan = Kecamatan::all();

        return response()->json([
            'success' => true,
            'message' => 'List of Kecamatan',
            'data' => [
                "kabkota" => $kabkota,
                "kecamatan" => $kecamatan
            ]
        ]);
    }
}
