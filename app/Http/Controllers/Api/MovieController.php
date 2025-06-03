<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::with('types.seasons.episodes')->get();

        $data = $movies->map(function ($movie) {
            return [
                'movie_logo' => $movie->movie_logo,
                'movie_name' => $movie->movie_name,
                'type' => $movie->types->map(function ($type) {
                    $seasons = [];
                    foreach ($type->seasons as $season) {
                        $episodes = $season->episodes->map(function ($ep) {
                            return [
                                'episode' => $ep->episode,
                                'link' => $ep->link
                            ];
                        });
                        $seasons[$season->season_number] = $episodes;
                    }
                    return [
                        'version' => $type->version,
                        'season' => $seasons
                    ];
                }),
            ];
        });

        return response()->json($data);
    }
}
