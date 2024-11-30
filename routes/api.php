<?php

use App\Http\Controllers\AdminMenuController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return "welcome";
});

Route::get('/getConfig', [ConfigController::class, 'getConfig'])->middleware('throttle:100,30');
Route::get('/captchaImage', [LoginController::class, 'captchaImage'])->middleware('throttle:100,30');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:100,30');

Route::group(['middleware' => ['auth:api']], function() {
    Route::get('/admin/user/list', [AdminUserController::class, 'getAdminUserList']);
    Route::post('/admin/user/create', [AdminUserController::class, 'create']);
    Route::post('/admin/user/update', [AdminUserController::class, 'update']);
    Route::post('/admin/user/updateStatus', [AdminUserController::class, 'updateStatus']);
    Route::post('/admin/user/resetPassword', [AdminUserController::class, 'resetPassword']);
    Route::post('/admin/user/delete', [AdminUserController::class, 'delete']);

    Route::get('/admin/user/profile/info', [AdminUserController::class, 'profile']);
    Route::post('/admin/user/profile/password', [AdminUserController::class, 'changePassword']);

    Route::get('/admin/role/all', [AdminRoleController::class, 'getAllRole']);
    Route::get('/admin/role/list', [AdminRoleController::class, 'getRoleList']);
    Route::get('/admin/role/info', [AdminRoleController::class, 'getRoleInfo']);
    Route::post('/admin/role/create', [AdminRoleController::class, 'create']);
    Route::post('/admin/role/update', [AdminRoleController::class, 'update']);
    Route::post('/admin/role/updateStatus', [AdminRoleController::class, 'updateStatus']);
    Route::post('/admin/role/delete', [AdminRoleController::class, 'delete']);

    Route::get('/admin/menu/list', [AdminMenuController::class, 'getMenuList']);
    Route::get('/admin/menu/tree', [AdminMenuController::class, 'getMenuTree']);
    Route::get('/admin/menu/info', [AdminMenuController::class, 'getMenuInfo']);
    Route::post('/admin/menu/create', [AdminMenuController::class, 'create']);
    Route::post('/admin/menu/update', [AdminMenuController::class, 'update']);
    Route::post('/admin/menu/delete', [AdminMenuController::class, 'delete']);
});
