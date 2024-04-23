<?php

namespace App\Http\Controllers\Managements;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Managements\Controllers;

class ControllersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('app.managements.controllers._index');
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
        $form = $request->validate([
            "controller_name" => "required|max:100",
            "controller_desc" => "string",
        ]);

        $form['id'] = Str::uuid();
        $form['type'] = "common";
        $form['created_by'] = auth()->user()->username;

        $controller = Controllers::create($form);

        return response()->json([
            'success' => true,
            'message' => 'Successfully created new controller.',
            'controller' => $controller,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Managements\Controllers  $controller
     * @return \Illuminate\Http\Response
     */
    public function show(Controllers $controller)
    {
        //
        return response()->json([
            'success' => true,
            'message' => 'Controller found.',
            'controller' => $controller,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Managements\Controllers  $controllers
     * @return \Illuminate\Http\Response
     */
    public function edit(Controllers $controllers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Managements\Controllers  $controller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Controllers $controller)
    {
        //
        $form = $request->validate([
            "controller_name" => "required|max:100",
            "controller_desc" => "string",
        ]);

        $form['type'] = "common";
        $form['updated_by'] = auth()->user()->username;

        $controller->update($form);

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated controller.',
            'controller' => $controller,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Managements\Controllers  $controllers
     * @return \Illuminate\Http\Response
     */
    public function destroy(Controllers $controller)
    {
        //
        $controller->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted controller.',
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
        $controllers = Controllers::all();

        return response()->json([
            'success' => true,
            'message' => 'Controllers found.',
            'controllers' => $controllers,
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
        $searchColumn = collect(['controller_name', 'controller_desc']);

        $currentPage = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = Controllers::query();

        if($search != ''){
            $searchColumn->map(function($item, $index) use($search, $query){
                if($index == 0) $query->where($item, 'like', '%' . $search . '%');
                else $query->orWhere($item, 'like', '%' . $search . '%');

            });
        }
        
        $query->orderBy('created_at', 'desc');
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
                "controller_name" => $item->controller_name,
                "controller_desc" => $item->controller_desc,
                "type" => $item->type,
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
