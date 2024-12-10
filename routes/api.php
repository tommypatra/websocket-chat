<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/notif/{user_id}', [ApiController::class, 'notif'])->name('notif');
Route::get('/get-users', [ApiController::class, 'getUsers'])->name('get.users');
// Route::get('/get-teman/{user_id}', [ApiController::class, 'getFriends'])->name('get.teman');
Route::match(['get', 'post'], '/get-teman/{user_id}', [ApiController::class, 'getFriends'])->name('get.teman');
Route::match(['get', 'post'], '/get-groups/{user_id}', [ApiController::class, 'getGroups'])->name('get.groups');
Route::get('/get-all/{user_id}', [ApiController::class, 'getAll'])->name('get.all');
Route::post('/cari-orang', [ApiController::class, 'getOrang'])->name('cari.orang');
Route::post('/jadikan-teman', [ApiController::class, 'saveFriends'])->name('jadikan.teman');
Route::post('/get-users', [ApiController::class, 'getUsers'])->name('cari.users');
Route::post('/update-status-baca', [ApiController::class, 'updateStatusBaca'])->name('update.status.baca');
Route::get('/daftar-obrolan/{ruang_obrolan_id}', [ApiController::class, 'daftarObrolan'])->name('daftar.obrolan');
Route::post('/cari-ruang-obrolan', [ApiController::class, 'cariRuanganObrolan'])->name('cari.ruang.obrolan');
Route::post('/kirim-obrolan', [ApiController::class, 'kirimObrolan'])->name('kirim.obrolan');
