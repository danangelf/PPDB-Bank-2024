<?php

namespace App\Http\Controllers\Managements;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Managements\Roles;
use App\Http\Controllers\Controller;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('app.managements.roles._index');
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
            "role_name" => "required|string|max:100",
            "role_desc" => "",
        ]);

        $form['id'] = Str::uuid();
        $form['type'] = "common";
        $form['created_by'] = auth()->user()->username;

        $role = Roles::create($form);

        return response()->json([
            'success' => true,
            'message' => 'Successfully created new role.',
            'role' => $role,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Managements\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function show(Roles $role)
    {
        //
        return response()->json([
            'success' => true,
            'message' => 'Role Found.',
            'role' => $role,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Managements\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function edit(Roles $roles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Managements\Roles  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Roles $role)
    {
        //
        $form = $request->validate([
            "role_name" => "required|string|max:100",
            "role_desc" => "",
        ]);
        $form['created_by'] = auth()->user()->username;

        $role->update($form);

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated role.',
            'role' => $role,
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Managements\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function destroy(Roles $role)
    {
        //
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted role.',
            'role' => $role,
        ]);

    }

    /**
     * Get all roles
     */

     public function showall()
     {
         //
         $roles = Roles::all();

         return response()->json([
             'success' => true,
             'message' => 'Successfully retrieved all roles.',
             'roles' => $roles,
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
        $searchColumn = collect(['role_name', 'role_desc']);

        $currentPage = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = Roles::query();

        if($search != ''){
            $searchColumn->map(function($item, $index) use($search, $query){
                if($index == 0) $query->where($item, 'like', '%' . $search . '%');
                else $query->orWhere($item, 'like', '%' . $search . '%');

            });
        }
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
                "role_name" => $item->role_name,
                "role_desc" => $item->role_desc,
                "type" => $item->type,
                "created_at" => $created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'List of Roles',
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
