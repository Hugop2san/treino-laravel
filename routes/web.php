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

Route::prefix('dashboard')->group(function () {

    Route::get('/', function () {
        return view('dashboard.dashboard');
    })->name('dashboard');

    //rotas usuario
    require __DIR__ . '/usuario.php';

    Route::get('/product', function () {
        return view('createproduct.createproduct');
    })->name('dashboard.product');

    Route::get('/venda', function () {
        return view('createsale.createsale');
    })->name('dashboard.venda');
});
