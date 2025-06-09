<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()
    {
        // Eager load the necessary relationships
        $movies = Movie::with('versions.seasons.episodes')->get();

        // Format and return the movie data
        $data = $movies->map(function ($movie) {
            return [
                'movie_logo' => $movie->movie_logo,
                'movie_name' => $movie->movie_name,
                'type' => $movie->versions->map(function ($version) {
                    $seasons = [];

                    foreach ($version->seasons as $season) {
                        $episodes = $season->episodes->map(function ($ep) {
                            return [
                                'episode' => $ep->episode,
                                'link' => $ep->link,
                            ];
                        });

                        $seasons[$season->season_number] = $episodes;
                    }

                    return [
                        'version' => $version->version_name,
                        'season' => $seasons,
                    ];
                }),
            ];
        });

        return response()->json($data);
    }
}
