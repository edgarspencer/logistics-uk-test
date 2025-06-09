<?php

use App\Http\Controllers\API\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('tasks')->group(function () {
    // Public endpoints
    Route::get('/', [TaskController::class, 'index']);
    Route::post('/', [TaskController::class, 'store']);
    Route::get('/{task}', [TaskController::class, 'show']);
    
    // Secured endpoints
    Route::put('/{task}/{token}', [TaskController::class, 'update']);
    Route::delete('/{task}/{token}', [TaskController::class, 'destroy']);
});