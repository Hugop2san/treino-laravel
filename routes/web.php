<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/prompt', [AnalysisController::class, 'ask'])->name('dashboard.prompt.ask');

    Route::prefix('analises')->name('dashboard.analises.')->group(function () {
        Route::get('/', [AnalysisController::class, 'index'])->name('index');
    });

    Route::prefix('user')->name('dashboard.user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('product')->name('dashboard.product.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('venda')->name('dashboard.venda.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::post('/', [SalesController::class, 'store'])->name('store');
        Route::get('/{venda}/edit', [SalesController::class, 'edit'])->name('edit');
        Route::put('/{venda}', [SalesController::class, 'update'])->name('update');
        Route::delete('/{venda}', [SalesController::class, 'destroy'])->name('destroy');
    });
});
