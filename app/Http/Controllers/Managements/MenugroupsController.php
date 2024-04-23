<?php

namespace App\Http\Controllers\Managements;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Managements\Menugroups;

class MenugroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('app.managements.menugroups._index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            "menugroup_label" => "required",
            "menugroup_desc" => "",
            "menugroup_order" => "required|numeric",
        ]);

        $menugroups = Menugroups::create([
            "id" => Str::uuid(),
            "menugroup_label" => $request->menugroup_label,
            "menugroup_desc" => $request->menugroup_desc,
            "menugroup_order" => $request->menugroup_order,
            "type" => "common",
            "created_by" => auth()->user()->id,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Menugroup successfully created",
            "menugroups" => $menugroups
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Managements\Menugroups  $menugroups
     * @return \Illuminate\Http\Response
     */
    public function show(Menugroups $menugroup)
    {
        //
        return response()->json([
            "status" => true,
            "message" => "Menugroup successfully retrieved",
            "menugroups" => $menugroup
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Managements\Menugroups  $menugroups
     * @return \Illuminate\Http\Response
     */
    public function edit(Menugroups $menugroups)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Managements\Menugroups  $menugroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menugroups $menugroup)
    {
        //
        $request->validate([
            "menugroup_label" => "required",
            "menugroup_desc" => "",
            "menugroup_order" => "required|numeric",
        ]);

        $menugroup->update([
            "menugroup_label" => $request->menugroup_label,
            "menugroup_desc" => $request->menugroup_desc,
            "menugroup_order" => $request->menugroup_order,
            "updated_by" => auth()->user()->id,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Menugroup successfully updated",
            "menugroups" => $menugroup
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Managements\Menugroups  $menugroups
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menugroups $menugroup)
    {
        //
        $menugroup->delete();

        return response()->json([
            "status" => true,
            "message" => "Menugroup successfully deleted",
            "menugroups" => $menugroup
        ]);
    }

    /**
     * Show all resources from storage.
     * 
     * 
     * @return \Illuminate\Http\Response and App\Models\Managements\Controllers
     * 
     */
    public function showall()
    {
        //
        $menugroups = Menugroups::all();

        return response()->json([
            "status" => true,
            "message" => "Menugroups successfully retrieved",
            "menugroups" => $menugroups
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
        //
        $searchColumn = collect(['menugroup_label', 'menugroup_desc']);

        $currentPage = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = Menugroups::query();

        if($search != ''){
            $searchColumn->map(function($item, $index) use($search, $query){
                if($index == 0) $query->where($item, 'like', '%' . $search . '%');
                else $query->orWhere($item, 'like', '%' . $search . '%');

            });
        }
        
        $query->orderBy('menugroup_order', 'asc');
        $objData = $query->paginate($perPage);
        $totalPage = $objData->lastPage();
        $totalRecord = $objData->total();

        // remap
        $objData = $objData->map(function($item){
            $jam = Carbon::parse($item->created_at)->diffInHours();
            if($jam > 24) {
                $created_at = Carbon::parse($item->created_at)->format('d M Y H:i');
            }
            else
            {
                $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $item->created_at)->diffForHumans();
            }
            return [
                "id" => $item->id,
                "menugroup_label" => $item->menugroup_label,
                "menugroup_desc" => $item->menugroup_desc,
                "menugroup_order" => $item->menugroup_order,
                "type" => $item->type,
                "created_at" => $created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'List of Menugroups',
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
