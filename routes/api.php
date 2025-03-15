<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ActivityController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('{project}')->group(function () {
        Route::post('activities', [ActivityController::class, 'store'])->name('api.activities.store');
    });
});
