<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//public routes
Route::middleware('guest')->group(function () {
    Route::get('/', [App\Http\Controllers\LoginController::class, 'index'])->name('login');
    Route::post('/', [App\Http\Controllers\LoginController::class, 'authenticate'])->middleware('throttle:10,5');

});

// protected routes for all roles
Route::middleware('auth')->group(function () {
    Route::get('/logout', [App\Http\Controllers\LoginController::class, 'destroy'])->name('logout');
    Route::get('/roleplay/switch/{role_id}', [App\Http\Controllers\RolePlayController::class, 'switch'])->name('switch')->middleware('isActiveUser');
    Route::get('/home', [App\Http\Controllers\DashboardController::class, 'index'])->name('home')->middleware('isActiveUser');
    Route::get('/changepassword', [App\Http\Controllers\ChangepasswordController::class, 'index'])->name('changepassword')->middleware('isActiveUser');
    Route::put('/changepassword/{user}', [App\Http\Controllers\ChangepasswordController::class, 'update'])->middleware('isActiveUser')
    ->where(['user' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile')->middleware('isActiveUser');
    Route::put('/profile/{user}', [App\Http\Controllers\ProfileController::class, 'update'])->middleware('isActiveUser')
    ->where(['user' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);
});

//protected routes with roleplay
Route::middleware(['auth','roleplay','isActiveUser'])->group(function () {

    Route::prefix('managements')->group(function(){
        Route::get('/user/datatable', [App\Http\Controllers\Managements\UserController::class, 'datatable']);
        Route::resource('/user', App\Http\Controllers\Managements\UserController::class)->except(['create', 'edit'])
        ->where(['user' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);

        Route::get('/functions/showall', [App\Http\Controllers\Managements\FunctionsController::class, 'showall']);
        Route::get('/functions/datatable', [App\Http\Controllers\Managements\FunctionsController::class, 'datatable']);
        Route::resource('/functions', App\Http\Controllers\Managements\FunctionsController::class)->except(['create', 'edit'])
        ->where(['function' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);

        

        Route::get('/controllers/showall', [App\Http\Controllers\Managements\ControllersController::class, 'showall']);
        Route::get('/controllers/datatable', [App\Http\Controllers\Managements\ControllersController::class, 'datatable']);
        Route::resource('/controllers', App\Http\Controllers\Managements\ControllersController::class)->except(['create', 'edit'])
        ->where(['controller' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);

        Route::get('/modules/datatable', [App\Http\Controllers\Managements\ModulesController::class, 'datatable']);
        Route::resource('/modules', App\Http\Controllers\Managements\ModulesController::class)->except(['create', 'edit'])
        ->where(['module' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);

        Route::get('/roles/showall', [App\Http\Controllers\Managements\RolesController::class, 'showall']);
        Route::get('/roles/datatable', [App\Http\Controllers\Managements\RolesController::class, 'datatable']);
        Route::resource('/roles', App\Http\Controllers\Managements\RolesController::class)->except(['create', 'edit'])
        ->where(['role' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);

        Route::get('/actions/{id}', [App\Http\Controllers\Managements\ActionsController::class, 'index']);
        Route::post('/actions', [App\Http\Controllers\Managements\ActionsController::class, 'store']);
        Route::get('/actions/showall/{id}', [App\Http\Controllers\Managements\ActionsController::class, 'showall']);
        Route::delete('/actions/{id}', [App\Http\Controllers\Managements\ActionsController::class, 'destroy']);

        Route::get('/permissions/showall/{id}', [App\Http\Controllers\Managements\PermissionsController::class, 'showall']);
        Route::resource('/permissions', App\Http\Controllers\Managements\PermissionsController::class)->except(['create', 'edit','show'])
        ->where(['permission' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);

        Route::get('/menugroups/datatable', [App\Http\Controllers\Managements\MenugroupsController::class, 'datatable']);
        Route::get('/menugroups/showall', [App\Http\Controllers\Managements\MenugroupsController::class, 'showall']);
        Route::resource('/menugroups', App\Http\Controllers\Managements\MenugroupsController::class)->except(['create', 'edit'])
        ->where(['menugroup' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);

        
        Route::get('/menus/datatable', [App\Http\Controllers\Managements\MenusController::class, 'datatable']);
        Route::get('/menus/showall', [App\Http\Controllers\Managements\MenusController::class, 'showall']);
        Route::resource('/menus', App\Http\Controllers\Managements\MenusController::class)->except(['create', 'edit'])
        ->where(['menu' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);

    });

    Route::prefix('data')->group(function(){
        Route::get('/kecamatan/synch', [App\Http\Controllers\Data\KecamatanController::class, 'synchronize']);
        Route::get('/kecamatan/showall', [App\Http\Controllers\Data\KecamatanController::class, 'showall']);
        Route::get('/kecamatan/datatable', [App\Http\Controllers\Data\KecamatanController::class, 'datatable']);
        Route::resource('/kecamatan', App\Http\Controllers\Data\KecamatanController::class)->except(['create', 'edit']);


        Route::get('/sekolah/showall/{kode_kecamatan}', [App\Http\Controllers\Data\SekolahController::class, 'showall']);
        Route::get('/sekolah/synch/{kode_kecamatan}', [App\Http\Controllers\Data\SekolahController::class, 'synchronize']);
        Route::get('/sekolah/datatable', [App\Http\Controllers\Data\SekolahController::class, 'datatable']);
        Route::resource('/sekolah', App\Http\Controllers\Data\SekolahController::class)->except(['create', 'edit']);

        Route::get('/siswa/synch/{kode_kecamatan}', [App\Http\Controllers\Data\SiswaController::class, 'synchronize']);
        Route::get('/siswa/datatable', [App\Http\Controllers\Data\SiswaController::class, 'datatable']);
        Route::resource('/siswa', App\Http\Controllers\Data\SiswaController::class)->except(['create', 'edit']);
        
        Route::get('/logsynchronize/datatable', [App\Http\Controllers\Data\LogSynchronizeController::class, 'datatable']);
        Route::get('/logsynchronize', [App\Http\Controllers\Data\LogSynchronizeController::class, 'index']);
        Route::get('/logsynchronize/{logsynchronize}', [App\Http\Controllers\Data\LogSynchronizeController::class, 'show']);
        Route::get('/logsynchronize/details/{logsynchronize}', [App\Http\Controllers\Data\LogSynchronizeController::class, 'details']);


    });
});

