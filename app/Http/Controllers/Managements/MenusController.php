<?php

namespace App\Http\Controllers\Managements;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Managements\Menus;
use App\Http\Controllers\Controller;

class MenusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('app.managements.menus._index');
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
            "menugroup_id" => 'required',
            "menu_label" => 'required',
            "menu_order" => 'required|numeric',
            "action_id" => 'required',
            "route" => 'required'
        ]);

        $menu = Menus::create([
            "id" => Str::uuid(),
            "menugroup_id" => $request->menugroup_id,
            "menu_label" => $request->menu_label,
            "menu_icon" => $request->menu_icon,
            "menu_desc" => $request->menu_desc,
            "menu_order" => $request->menu_order,
            "action_id" => $request->action_id,
            "route" => $request->route,
            "type" => 'common',
            "created_by" => auth()->user()->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Menu has been created',
            'menu' => $menu
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Managements\Menus  $menus
     * @return \Illuminate\Http\Response
     */
    public function show(Menus $menu)
    {
        //
        return response()->json([
            'status' => true,
            'message' => 'Menu has been retrieved',
            'menu' => $menu
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Managements\Menus  $menus
     * @return \Illuminate\Http\Response
     */
    public function edit(Menus $menus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Managements\Menus  $menus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menus $menu)
    {
        //
        $request->validate([
            "menugroup_id" => 'required',
            "menu_label" => 'required',
            "menu_order" => 'required|numeric',
            "action_id" => 'required',
            "route" => 'required'
        ]);

        $menu->update([
            "menugroup_id" => $request->menugroup_id,
            "menu_label" => $request->menu_label,
            "menu_icon" => $request->menu_icon,
            "menu_desc" => $request->menu_desc,
            "menu_order" => $request->menu_order,
            "action_id" => $request->action_id,
            "route" => $request->route,
            "updated_by" => auth()->user()->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Menu has been updated',
            'menu' => $menu
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Managements\Menus  $menus
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menus $menu)
    {
        //
        $menu->delete();

        return response()->json([
            'status' => true,
            'message' => 'Menu has been deleted',
            'menu' => $menu
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
        $searchColumn = collect(['menugroups.menugroup_label', 'menus.menu_label', 'menus.menu_desc']);

        $currentPage = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = Menus::query()
        ->select(['menus.id','menugroups.menugroup_label', 'menus.menu_label', 'menus.menu_desc','menus.menu_order','menus.route','menus.type','menus.created_at'])
        ->join('menugroups', 'menus.menugroup_id', '=', 'menugroups.id')
        ->orderBy('menugroups.menugroup_order', 'asc');

        if($search != ''){
            $searchColumn->map(function($item, $index) use($search, $query){
                if($index == 0) $query->where($item, 'like', '%' . $search . '%');
                else $query->orWhere($item, 'like', '%' . $search . '%');

            });
        }
        
        $query->orderBy('menu_order', 'asc');
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
                "menu_order" => $item->menu_order,
                "menugroup_label" => $item->menugroup_label,
                "menu_label" => $item->menu_label,
                "menu_desc" => $item->menu_desc,
                "route" => $item->route,
                "type" => $item->type,
                "created_at" => $created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'List of Menus',
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
