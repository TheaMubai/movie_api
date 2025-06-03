<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MovieController;

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
Route::get('/movies', [MovieController::class, 'index']);
