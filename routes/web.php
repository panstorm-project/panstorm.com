<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ActivityController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::prefix('{project}')->group(function () {
        Route::post('activities', [ActivityController::class, 'store'])->name('api.activities.store');
    });
});
