<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MovieController;

//Route::middleware('api')->group(function () {
Route::get('/movies', [MovieController::class, 'index']);
Route::delete('/movies/delete/{id}', [MovieController::class, 'destroy']);
//});
