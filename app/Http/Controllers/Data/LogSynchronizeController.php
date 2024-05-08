<?php

namespace App\Http\Controllers\Data;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Data\LogSynchronize;
use App\Http\Controllers\Controller;

class LogSynchronizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('app.data.logsynchronize._index');
    }

    /**
     * Display the specified resource.
     */
    public function show(LogSynchronize $logsynchronize)
    {
        //
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Log Synchronize',
            'data' => $logsynchronize
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function details(LogSynchronize $logsynchronize)
    {
        //
        $rawData = json_decode($logsynchronize->result);
        return response()->json($rawData);
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
        $searchColumn = collect(['table', 'params', 'status_result', 'msg_result', 'created_by']);

        $currentPage = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = LogSynchronize::query();

        if($search != ''){
            $searchColumn->map(function($item, $index) use($search, $query){
                if($index == 0) $query->where($item, 'ilike', '%' . $search . '%');
                else $query->orWhere($item, 'ilike', '%' . $search . '%');

            });
        }
        
        $query->orderBy('created_at', 'desc');
        $objData = $query->paginate($perPage);
        $totalPage = $objData->lastPage();
        $totalRecord = $objData->total();

        // remap
        $objData = $objData->map(function($item){
            $jam = $item->created_at !== null ? Carbon::parse($item->created_at)->diffInHours() : "-";
            if($jam > 24 AND $item->created_at !== null) {
                $created_at = Carbon::parse($item->created_at)->format('d M Y H:i');
            }
            else
            {
                $created_at = $item->created_at !== null ? Carbon::createFromFormat('Y-m-d H:i:s', $item->created_at)->diffForHumans() : "-";
            }
            return [
                "id" => $item->id,
                "table" => $item->table,
                "params" => $item->params,
                "status_result" => $item->status_result,
                "msg_result" => Str::limit($item->msg_result, 50),
                "extra_info" => $item->extra_info,
                "created_by" => $item->created_by,
                "created_at" => $created_at,
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
}
