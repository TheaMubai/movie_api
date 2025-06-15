<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MovieController;

Route::get('/movies', [MovieController::class, 'index']);             // Show all
Route::post('/movies', [MovieController::class, 'store']);            // Create
Route::get('/movies/{id}', [MovieController::class, 'show']);         // Show by ID
Route::put('/movies/{id}', [MovieController::class, 'update']);       // Update by ID
Route::delete('/movies/{id}', [MovieController::class, 'destroy']);   // delete by ID
Route::post('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}/episodes', [MovieController::class, 'addEpisodes']);
Route::post('/movies/{movie_id}/versions/{version_name}/seasons', [MovieController::class, 'addSeason']);
Route::post('/movies/{movie_id}/versions', [MovieController::class, 'addVersion']);
Route::put('/movies/{movie_id}/versions/{version_id}/seasons/{season_id}/episodes/{episode_id}', [MovieController::class, 'updateEpisode']); //update Episode by ID
Route::put('/movies/{movie_id}/versions/{version_id}', [MovieController::class, 'updateVersion']);
Route::put('/movies/{movie_id}/versions/{version_id}/seasons/{season_id}', [MovieController::class, 'updateSeason']);
