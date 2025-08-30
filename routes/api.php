<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MileageRateController;
use App\Http\Controllers\ImportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes for authenticated users
Route::middleware(['auth:sanctum'])->group(function () {
    // Labels autocomplete endpoint
    Route::get('/labels/search', function (Request $request) {
        $search = $request->get('q', '');
        
        $labels = auth()->user()->labels()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', '%' . $search . '%');
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['name', 'color']);
            
        return response()->json($labels);
    });
});