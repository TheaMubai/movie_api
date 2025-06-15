<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Version;
use App\Models\Season;
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    // Show all movies
    public function index()
    {
        $movies = Movie::with('versions.seasons.episodes')->get();

        $data = $movies->map(function ($movie) {
            return $this->formatMovie($movie);
        });

        return response()->json($data);
    }

    // Show movie by ID
    public function show($id)
    {
        $movie = Movie::with('versions.seasons.episodes')->findOrFail($id);
        return response()->json($this->formatMovie($movie));
    }

    // Create a movie
    public function store(Request $request)
    {
        $request->validate([
            'movie_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'movie_name' => 'required|string',
            'types' => 'required|array',
        ]);

        // Store the uploaded image to 'public/images' and get path
        $path = $request->file('movie_logo')->store('public/images');
        $url = Storage::url($path); // This will give /storage/images/filename.jpg

        $movie = Movie::create([
            'movie_logo' => asset($url),
            'movie_name' => $request->movie_name,
        ]);

        $this->syncVersions($movie, $request->types);

        return response()->json(['message' => 'Movie created successfully', 'movie_id' => $movie->id], 201);
    }


    // Update a movie by ID
    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        $movie->update($request->only('movie_logo', 'movie_name'));

        if ($request->has('types')) {
            // Delete old nested data
            foreach ($movie->versions as $version) {
                foreach ($version->seasons as $season) {
                    $season->episodes()->delete();
                }
                $version->seasons()->delete();
            }
            $movie->versions()->delete();

            // Recreate nested data
            $this->syncVersions($movie, $request->types);
        }

        return response()->json(['message' => 'Movie updated successfully']);
    }

    // Delete a movie by ID
    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);

        foreach ($movie->versions as $version) {
            foreach ($version->seasons as $season) {
                $season->episodes()->delete();
            }
            $version->seasons()->delete();
        }

        $movie->versions()->delete();
        $movie->delete();

        return response()->json(['message' => 'Movie deleted successfully']);
    }

    // Helper to format movie structure
    private function formatMovie($movie)
    {
        return [
            'movie_id' => $movie->id,
            'movie_logo' => $movie->movie_logo,
            'movie_name' => $movie->movie_name,
            'type' => $movie->versions->map(function ($version) {
                $seasonData = [];
                foreach ($version->seasons as $season) {
                    $seasonData[$season->season_number] = [
                        'season_id' => $season->id,
                        'episodes' => $season->episodes->map(function ($ep) {
                            return [
                                'episode_id' => $ep->id,
                                'episode' => $ep->episode,
                                'link' => $ep->link,
                            ];
                        }),
                    ];
                }

                return [
                    'version_id' => $version->id,
                    'version' => $version->version_name,
                    'season' => $seasonData,
                ];
            }),
        ];
    }

    // Helper to store nested data
    private function syncVersions($movie, $types)
    {
        foreach ($types as $versionName => $seasons) {
            $version = $movie->versions()->create(['version_name' => $versionName]);

            foreach ($seasons as $seasonNumber => $episodes) {
                $season = $version->seasons()->create(['season_number' => $seasonNumber]);

                foreach ($episodes as $ep) {
                    $season->episodes()->create([
                        'episode' => $ep['episode'],
                        'link' => $ep['link'],
                    ]);
                }
            }
        }
    }
    public function addEpisodes(Request $request, $movie_id, $version_name, $season_number)
    {
        $request->validate([
            'episodes' => 'required|array',
            'episodes.*.episode' => 'required|integer',
            'episodes.*.link' => 'required|url',
        ]);

        $movie = Movie::findOrFail($movie_id);

        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();

        $season = $version->seasons()->where('season_number', $season_number)->firstOrFail();

        foreach ($request->episodes as $ep) {
            $season->episodes()->create([
                'episode' => $ep['episode'],
                'link' => $ep['link'],
            ]);
        }

        return response()->json(['message' => 'Episodes added successfully']);
    }
    // public function addSeason(Request $request, $movie_id, $version_name)
    // {
    //     $request->validate([
    //         'season_number' => 'required|integer',
    //         'episodes' => 'required|array',
    //         'episodes.*.episode' => 'required|integer',
    //         'episodes.*.link' => 'required|url',
    //     ]);

    //     $movie = Movie::findOrFail($movie_id);

    //     $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();

    //     // Check if the season already exists
    //     $existingSeason = $version->seasons()->where('season_number', $request->season_number)->first();
    //     if ($existingSeason) {
    //         return response()->json(['message' => 'Season already exists'], 409);
    //     }

    //     // Create the new season
    //     $season = $version->seasons()->create([
    //         'season_number' => $request->season_number,
    //     ]);

    //     // Add episodes to the new season
    //     foreach ($request->episodes as $ep) {
    //         $season->episodes()->create([
    //             'episode' => $ep['episode'],
    //             'link' => $ep['link'],
    //         ]);
    //     }

    //     return response()->json(['message' => 'New season and episodes added successfully']);
    // }
    // public function addVersion(Request $request, $movie_id)
    // {
    //     $request->validate([
    //         'version_name' => 'required|string|unique:versions,version_name,NULL,id,movie_id,' . $movie_id,
    //         'seasons' => 'nullable|array',
    //         'seasons.*.season_number' => 'required|integer',
    //         'seasons.*.episodes' => 'required|array',
    //         'seasons.*.episodes.*.episode' => 'required|integer',
    //         'seasons.*.episodes.*.link' => 'required|url',
    //     ]);

    //     $movie = Movie::findOrFail($movie_id);

    //     // Create new version
    //     $version = $movie->versions()->create([
    //         'version_name' => $request->version_name,
    //     ]);

    //     // Add optional seasons + episodes
    //     if ($request->has('seasons')) {
    //         foreach ($request->seasons as $seasonData) {
    //             $season = $version->seasons()->create([
    //                 'season_number' => $seasonData['season_number'],
    //             ]);

    //             foreach ($seasonData['episodes'] as $ep) {
    //                 $season->episodes()->create([
    //                     'episode' => $ep['episode'],
    //                     'link' => $ep['link'],
    //                 ]);
    //             }
    //         }
    //     }

    //     return response()->json(['message' => 'New version added successfully']);
    // }
    // public function updateEpisode(Request $request, $movie_id, $version_id, $season_id, $episode_id)
    // {
    //     $request->validate([
    //         'episode' => 'required|integer',
    //         'link' => 'required|url',
    //     ]);

    //     // Ensure relationships are valid
    //     $movie = Movie::findOrFail($movie_id);
    //     $version = $movie->versions()->where('id', $version_id)->firstOrFail();
    //     $season = $version->seasons()->where('id', $season_id)->firstOrFail();
    //     $episode = $season->episodes()->where('id', $episode_id)->firstOrFail();

    //     // Update episode
    //     $episode->update([
    //         'episode' => $request->episode,
    //         'link' => $request->link,
    //     ]);

    //     return response()->json(['message' => 'Episode updated successfully']);
    // }
    // public function updateVersion(Request $request, $movie_id, $version_id)
    // {
    //     $request->validate([
    //         'version_name' => 'required|string',
    //     ]);

    //     $movie = Movie::findOrFail($movie_id);
    //     $version = $movie->versions()->where('id', $version_id)->firstOrFail();

    //     $version->update([
    //         'version_name' => $request->version_name,
    //     ]);

    //     return response()->json(['message' => 'Version updated successfully']);
    // }
    // public function updateSeason(Request $request, $movie_id, $version_id, $season_id)
    // {
    //     $request->validate([
    //         'season_number' => 'required|integer',
    //     ]);

    //     $movie = Movie::findOrFail($movie_id);
    //     $version = $movie->versions()->where('id', $version_id)->firstOrFail();
    //     $season = $version->seasons()->where('id', $season_id)->firstOrFail();

    //     $season->update([
    //         'season_number' => $request->season_number,
    //     ]);

    //     return response()->json(['message' => 'Season updated successfully']);
    // }
    public function updateVersion(Request $request, $movie_id, $version_name)
    {
        $request->validate([
            'version_name' => 'required|string',
        ]);

        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();

        $version->update(['version_name' => $request->version_name]);

        return response()->json(['message' => 'Version updated successfully']);
    }
    public function updateSeason(Request $request, $movie_id, $version_name, $season_number)
    {
        $request->validate([
            'season_number' => 'required|integer',
        ]);

        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();
        $season = $version->seasons()->where('season_number', $season_number)->firstOrFail();

        $season->update(['season_number' => $request->season_number]);

        return response()->json(['message' => 'Season updated successfully']);
    }
    public function updateEpisode(Request $request, $movie_id, $version_name, $season_number, $episode_id)
    {
        $request->validate([
            'episode' => 'required|integer',
            'link' => 'required|url',
        ]);

        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();
        $season = $version->seasons()->where('season_number', $season_number)->firstOrFail();
        $episode = $season->episodes()->where('id', $episode_id)->firstOrFail();

        $episode->update([
            'episode' => $request->episode,
            'link' => $request->link,
        ]);

        return response()->json(['message' => 'Episode updated successfully']);
    }
    public function addVersion(Request $request, $movie_id)
    {
        $request->validate([
            'version_name' => 'required|string|unique:versions,version_name,NULL,id,movie_id,' . $movie_id,
        ]);

        $movie = Movie::findOrFail($movie_id);

        $version = $movie->versions()->create([
            'version_name' => $request->version_name,
        ]);

        return response()->json(['message' => 'Version added successfully', 'version' => $version]);
    }
    public function addSeason(Request $request, $movie_id, $version_name)
    {
        $request->validate([
            'season_number' => 'required|integer',
        ]);

        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();

        $season = $version->seasons()->create([
            'season_number' => $request->season_number,
        ]);

        return response()->json(['message' => 'Season added successfully', 'season' => $season]);
    }
    public function deleteVersion($movie_id, $version_name)
    {
        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();

        // Delete all related seasons and episodes
        foreach ($version->seasons as $season) {
            $season->episodes()->delete();
        }
        $version->seasons()->delete();
        $version->delete();

        return response()->json(['message' => 'Version deleted successfully']);
    }
    public function deleteSeason($movie_id, $version_name, $season_number)
    {
        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();
        $season = $version->seasons()->where('season_number', $season_number)->firstOrFail();

        $season->episodes()->delete();
        $season->delete();

        return response()->json(['message' => 'Season deleted successfully']);
    }
    public function deleteEpisode($movie_id, $version_name, $season_number, $episode_id)
    {
        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();
        $season = $version->seasons()->where('season_number', $season_number)->firstOrFail();
        $episode = $season->episodes()->where('id', $episode_id)->firstOrFail();

        $episode->delete();

        return response()->json(['message' => 'Episode deleted successfully']);
    }
    public function showVersion($movie_id, $version_name)
    {
        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->with('seasons.episodes')->firstOrFail();

        return response()->json([
            'version_name' => $version->version_name,
            'seasons' => $version->seasons->map(function ($season) {
                return [
                    'season_number' => $season->season_number,
                    'episodes' => $season->episodes->map(function ($ep) {
                        return [
                            'episode_id' => $ep->id,
                            'episode' => $ep->episode,
                            'link' => $ep->link,
                        ];
                    }),
                ];
            }),
        ]);
    }
    public function showSeason($movie_id, $version_name, $season_number)
    {
        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();
        $season = $version->seasons()->where('season_number', $season_number)->with('episodes')->firstOrFail();

        return response()->json([
            'season_number' => $season->season_number,
            'episodes' => $season->episodes->map(function ($ep) {
                return [
                    'episode_id' => $ep->id,
                    'episode' => $ep->episode,
                    'link' => $ep->link,
                ];
            }),
        ]);
    }
    public function showEpisode($movie_id, $version_name, $season_number, $episode_id)
    {
        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();
        $season = $version->seasons()->where('season_number', $season_number)->firstOrFail();
        $episode = $season->episodes()->where('id', $episode_id)->firstOrFail();

        return response()->json([
            'episode_id' => $episode->id,
            'episode' => $episode->episode,
            'link' => $episode->link,
        ]);
    }
}
