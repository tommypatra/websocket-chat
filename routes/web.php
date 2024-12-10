<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GrupController;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('chat');
});

Route::get('/login', [LoginController::class, 'loginForm'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'cekAkun'])->middleware('guest')->name('login.cek');

Route::group(['middleware' => 'auth'], function () {
    // Route::get('/notif/{user_id}', [ChatController::class, 'notif'])->name('notif');

    Route::get('/chat', [ChatController::class, 'form'])->name('chat');
    Route::get('/send-chat', [ChatController::class, 'send'])->name('send');
    Route::post('/simpan-grup', [GrupController::class, 'simpan'])->name('grup.simpan');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});
