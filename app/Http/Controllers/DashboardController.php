<?php

namespace App\Http\Controllers;

use App\Models\Data\Peserta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display front page after login
     * 
     */
    public function index()
    {
        $current_role_name = session()->get('current_role_name');
        $user = auth()->user();
        // if($current_role_name == 'Admin' || $current_role_name == 'Superadmin'){
            return view('dashboard.admin._index', compact('user','current_role_name'));
        // }
        // else{
        //     return view('dashboard');
        // }
    }
}
