<?php

use Illuminate\Support\Facades\Route;



Route::Prefix('/', function () 
{
    return redirect()->route('welcome');

    //se o cliente logado
    Route::prefix('middleware')->group(function () {

        Route::get('/', function () {
            return view('dashboard.dashboard');})->name('dashboard');
    });

});
