<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('transfer', [\App\Http\Controllers\DocuWareController::class, 'transfer']);
Route::post('update', [\App\Http\Controllers\DocuWareController::class, 'update']);
Route::get('transfer', function () {
    return 'Working';
});
