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
Route::post('fileinfo', [\App\Http\Controllers\DocuWareController::class, 'fileinfo']);
Route::fallback(function () {
    return response()->json();
});
Route::put('{any}', function () {
    return response()->json();
});
Route::delete('{any}', function () {
    return response()->json();
});
Route::patch('{any}', function () {
    return response()->json();
});
Route::options('{any}', function () {
    return response()->json();
});
