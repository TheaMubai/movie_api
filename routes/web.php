<?php

use App\Http\Controllers\Web\MovieController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Show movie list
Route::get('/movies/create', [MovieController::class, 'create']);
Route::get('/movies/{id}', [MovieController::class, 'show'])->name('movies.show');
Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');

Route::post('/movies', [MovieController::class, 'store']);
//Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::get('/movies/{id}/edit', [MovieController::class, 'edit']);
Route::put('/movies/{id}', [MovieController::class, 'update']);
Route::delete('/movies/{id}', [MovieController::class, 'destroy']);

// Version
Route::get('/movies/{movie_id}/versions/create', [MovieController::class, 'createVersion']);
Route::post('/movies/{movie_id}/versions', [MovieController::class, 'addVersion']);
Route::get('/movies/{movie_id}/versions/{version_name}/edit', [MovieController::class, 'editVersion']);
Route::put('/movies/{movie_id}/versions/{version_name}', [MovieController::class, 'updateVersion']);
Route::delete('/movies/{movie_id}/versions/{version_name}', [MovieController::class, 'deleteVersion']);

// Season
Route::get('/movies/{movie_id}/versions/{version_name}/seasons/create', [MovieController::class, 'createSeason']);
Route::post('/movies/{movie_id}/versions/{version_name}/seasons', [MovieController::class, 'addSeason']);
Route::get('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}/edit', [MovieController::class, 'editSeason']);
Route::put('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}', [MovieController::class, 'updateSeason']);
Route::delete('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}', [MovieController::class, 'deleteSeason']);

// Episode
Route::get('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}/episodes/create', [MovieController::class, 'createEpisode']);
Route::post('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}/episodes', [MovieController::class, 'addEpisodes']);
Route::get('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}/episodes/{episode_id}/edit', [MovieController::class, 'editEpisode']);
Route::put('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}/episodes/{episode_id}', [MovieController::class, 'updateEpisode']);
Route::delete('/movies/{movie_id}/versions/{version_name}/seasons/{season_number}/episodes/{episode_id}', [MovieController::class, 'deleteEpisode']);
