<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;

class MovieController extends Controller
{


    public function index()
    {
        $movies = Movie::with('versions.seasons.episodes')->get();

        return response()->json($movies->map(function ($movie) {
            return [
                'movie_logo' => $movie->movie_logo,
                'movie_name' => $movie->movie_name,
                'type' => $movie->versions->map(function ($version) {
                    return [
                        'version' => $version->version,
                        'season' => $version->seasons->mapWithKeys(function ($season) {
                            return [
                                $season->season_number => $season->episodes->map(function ($episode) {
                                    return [
                                        'episode' => $episode->episode,
                                        'link' => $episode->link
                                    ];
                                })
                            ];
                        })
                    ];
                })
            ];
        }));
    }
    public function destroy($id)
    {
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }

        $movie->delete();

        return response()->json(['message' => 'Movie deleted successfully']);
    }
}
