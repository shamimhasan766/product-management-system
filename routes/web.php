<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [ProductController::class, 'index'])->name('dashboard');

    Route::prefix('products')->name('products.')->group(function () {
        Route::post('store', [ProductController::class, 'store'])->name('store');
        Route::post('update/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('delete/{product}', [ProductController::class, 'delete'])->name('delete');
        Route::get('trashed', [ProductController::class, 'trashed'])->name('trashed');
        Route::get('restore/{id}', [ProductController::class, 'restore'])->name('restore');
        Route::delete('permanent-delete/{id}', [ProductController::class, 'permanent_delete'])->name('permanent-delete');
    });
});


require __DIR__.'/settings.php';
