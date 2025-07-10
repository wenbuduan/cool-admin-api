<?php

use App\Http\Controllers\AdminMenuController;
use App\Http\Controllers\AdminOrganizationController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\AdminDictionaryController;
use App\Http\Controllers\AdminDictionaryDataController;
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

Route::post('/admin/doc/upload', [DocController::class, 'upload']);

// Route::get('/admin/auth/user', [AdminMenuController::class, 'getUserInfo']);
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/system/dictionary', [AdminDictionaryController::class, 'getDictionaryList']);
    Route::post('/system/dictionary', [AdminDictionaryController::class, 'create']);
    Route::put('/system/dictionary', [AdminDictionaryController::class, 'update']);
    Route::delete('/system/dictionary/{dictId}', [AdminDictionaryController::class, 'delete']);

    Route::get('/system/dictionary-data/page', [AdminDictionaryDataController::class, 'getDictionaryData']);
    Route::post('/system/dictionary-data', [AdminDictionaryDataController::class, 'create']);
    Route::put('/system/dictionary-data', [AdminDictionaryDataController::class, 'update']);
    Route::delete('/system/dictionary-data/batch', [AdminDictionaryDataController::class, 'delete']);

    Route::get('/system/user/existence', [AdminUserController::class, 'checkUserExistence']);
    Route::get('/system/dictionary-data', [AdminUserController::class, 'getDictionaryByFilter']);
    Route::get('/system/user/page', [AdminUserController::class, 'getAdminUserList']);
    Route::post('/system/user/create', [AdminUserController::class, 'create']);
    Route::put('/system/user/update', [AdminUserController::class, 'update']);
    Route::put('system/user/status', [AdminUserController::class, 'updateStatus']);
    Route::post('/admin/user/resetPassword', [AdminUserController::class, 'resetPassword']);
    Route::delete('/system/user/delete', [AdminUserController::class, 'delete']);

    Route::get('/admin/user/profile/info', [AdminUserController::class, 'profile']);
    Route::post('/admin/user/profile/password', [AdminUserController::class, 'changePassword']);

    Route::get('/system/role/all', [AdminRoleController::class, 'getAllRole']);
    Route::get('/system/role/page', [AdminRoleController::class, 'getRoleList']);
    Route::get('system/role/info', [AdminRoleController::class, 'getRoleInfo']);
    Route::post('/system/role', [AdminRoleController::class, 'create']);
    Route::put('/system/role/update', [AdminRoleController::class, 'update']);
    Route::post('/admin/role/updateStatus', [AdminRoleController::class, 'updateStatus']);
    Route::delete('/admin/role/{roleId}', [AdminRoleController::class, 'delete']);
    Route::delete('/system/role/batch', [AdminRoleController::class, 'batchDelete']);
    Route::get('/system/role/menu/{roleId}', [AdminRoleController::class, 'getMenusWithRole']);
    Route::put('/system/role/menu/{roleId}', [AdminRoleController::class, 'updateMenu']);


    Route::get('/admin/auth/user', [AdminMenuController::class, 'getUserInfo']);
    Route::get('/system/menu', [AdminMenuController::class, 'getMenuList']);
    Route::get('/admin/menu/tree', [AdminMenuController::class, 'getMenuTree']);
    Route::get('/admin/menu/info', [AdminMenuController::class, 'getMenuInfo']);
    Route::post('/system/menu/add', [AdminMenuController::class, 'create']);
    Route::put('/system/menu/edit', [AdminMenuController::class, 'update']);
    Route::delete('/system/menu/delete/{menuId}', [AdminMenuController::class, 'delete']);

    Route::get('/system/organization', [AdminOrganizationController::class, 'getOrganizationList']);
    Route::post('/system/organization', [AdminOrganizationController::class, 'create']);
});
