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
// Route::post('/movies/{movie_id}/versions/{version_name}/seasons', [MovieController::class, 'addSeason']);
// Route::post('/movies/{movie_id}/versions', [MovieController::class, 'addVersion']);
// Route::put('/movies/{movie_id}/versions/{version_id}/seasons/{season_id}/episodes/{episode_id}', [MovieController::class, 'updateEpisode']); //update Episode by ID
// Route::put('/movies/{movie_id}/versions/{version_id}', [MovieController::class, 'updateVersion']);
// Route::put('/movies/{movie_id}/versions/{version_id}/seasons/{season_id}', [MovieController::class, 'updateSeason']);
// Add new version to a movie
Route::post('/movies/{movie_id}/versions', [MovieController::class, 'addVersion']);
// Add new season to a version (identified by version_name)
Route::post('/movies/{movie_id}/versions/{version_name}/seasons', [MovieController::class, 'addSeason']);
// Update an episode using version_name and season_number
Route::put('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}/episodes/{episode_id}', [MovieController::class, 'updateEpisode']);
// Update a version by version_name
Route::put('/movies/{movie_id}/versions/{version_name}', [MovieController::class, 'updateVersion']);
// Update a season by version_name and season_number
Route::put('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}', [MovieController::class, 'updateSeason']);
// Delete a version
Route::delete('/movies/{movie_id}/versions/{version_name}', [MovieController::class, 'deleteVersion']);
// Delete a season
Route::delete('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}', [MovieController::class, 'deleteSeason']);
// Delete an episode
Route::delete('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}/episodes/{episode_id}', [MovieController::class, 'deleteEpisode']);
// Show version by version_name
Route::get('/movies/{movie_id}/versions/{version_name}', [MovieController::class, 'showVersion']);

// Show season by version_name and season_number
Route::get('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}', [MovieController::class, 'showSeason']);

// Show episode by version_name, season_number, and episode_id
Route::get('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}/episodes/{episode_id}', [MovieController::class, 'showEpisode']);
