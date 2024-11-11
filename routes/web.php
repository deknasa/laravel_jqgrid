<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\DetailPenjualanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ReportController;


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

Route::get('/', [IndexController::class, 'index']);

Route::get('/dataPenjualan', [IndexController::class, 'getPenjualan']);
Route::get('/dataDetail/{id}', [IndexController::class, 'getDetail']);

Route::get('/dataPenjualan/{id}', [IndexController::class, 'getPenjualanById']);
Route::post('/dataPenjualan', [IndexController::class, 'createPenjualan']);
Route::put('/dataPenjualan/{id}', [IndexController::class, 'updatePenjualan']);
Route::delete('/dataPenjualan/{id}', [IndexController::class, 'deletePenjualan']);

Route::get('/dataPelanggan', [PelangganController::class, 'getAllPelanggan']);

// Route::get('/dataDetailPenjualan/{id}', [DetailPenjualanController::class, 'getDetailPenjualan']);
// Route::post('/dataDetailPenjualan/{id}', [DetailPenjualanController::class, 'createDetailPenjualan']);
// Route::post('/updateDetail/{id}', [DetailPenjualanController::class, 'updateDetail']);
// Route::delete('/dataDetailPenjualan/{id}', [DetailPenjualanController::class, 'deleteDetail']);

Route::get('/exportData', [ExportController::class, 'exportData']);
Route::get('/reportData', [ReportController::class, 'reportData']);

// Route::get('/a', function() {
//     return view('tes');
// });

// Route::get('/aa/{nama}', function($nama) {
//     return 'hai'. $nama;
// });