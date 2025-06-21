<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class MovieController extends Controller
{
    // Show all movies page (Blade)
    public function index()
    {
        $movies = Movie::with('versions.seasons.episodes')->get();

        $data = $movies->map(function ($movie) {
            return $this->formatMovie($movie);
        });

        // Pass as array (not JSON)
        return view('movies.index', ['movies' => $data]);
    }

    // Show single movie detail page (Blade)
    public function show($id)
    {
        $movie = Movie::with('versions.seasons.episodes')->findOrFail($id);
        $movieData = $this->formatMovie($movie);

        return view('movies.show', ['movie' => $movieData]);
    }

    // Show create movie form (Blade)
    public function create()
    {
        return view('movies.create');
    }

    // Store new movie (handle POST)


    public function store(Request $request)
    {
        $request->validate([
            'movie_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'movie_name' => 'required|string',
            'types' => 'required|string',
        ]);

        $types = json_decode($request->input('types'), true);

        if (!is_array($types)) {
            return back()->with('error', 'Invalid JSON format in Movie Types field.');
        }

        // Move image to public/image/
        $filename = time() . '_' . $request->file('movie_logo')->getClientOriginalName();
        $request->file('movie_logo')->move(public_path('image'), $filename);

        // ✅ Use URL::to to get full global path
        $url = URL::to('/image/' . $filename);

        $movie = Movie::create([
            'movie_logo' => $url,
            'movie_name' => $request->movie_name,
        ]);

        $this->syncVersions($movie, $types);

        return redirect('/movies')->with('success', 'Movie created successfully!');
    }

    // Show edit movie form (Blade)
    public function edit($id)
    {
        $movie = Movie::with('versions.seasons.episodes')->findOrFail($id);
        $movieData = $this->formatMovie($movie);

        return view('movies.edit', ['movie' => $movieData]);
    }

    // Update movie (handle PUT)
    public function update(Request $request, $id)
    {
        $request->validate([
            'movie_name' => 'required|string',
            'movie_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'types' => 'nullable|string', // only update nested if provided
        ]);

        $movie = Movie::findOrFail($id);

        $data = ['movie_name' => $request->movie_name];

        // ✅ If a new logo is uploaded
        if ($request->hasFile('movie_logo')) {
            $filename = time() . '_' . $request->file('movie_logo')->getClientOriginalName();
            $request->file('movie_logo')->move(public_path('image'), $filename);
            $data['movie_logo'] = URL::to('/image/' . $filename);
        }

        $movie->update($data);

        // ✅ Optionally update nested versions/seasons/episodes
        if ($request->has('types')) {
            $types = json_decode($request->input('types'), true);
            if (!is_array($types)) {
                return back()->with('error', 'Invalid JSON format in Movie Types field.');
            }

            // Delete old nested data
            foreach ($movie->versions as $version) {
                foreach ($version->seasons as $season) {
                    $season->episodes()->delete();
                }
                $version->seasons()->delete();
            }
            $movie->versions()->delete();

            // Recreate nested data
            $this->syncVersions($movie, $types);
        }

        return redirect('/movies')->with('success', 'Movie updated successfully!');
    }




    // Delete movie (handle DELETE)
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

        return redirect()->route('movies.index')
            ->with('success', 'Movie deleted successfully!');
    }

    // Version create form (Blade)
    public function createVersion($movie_id)
    {
        return view('versions.create', compact('movie_id'));
    }

    // Add Version (handle POST)
    public function addVersion(Request $request, $movie_id)
    {
        $request->validate([
            'version_name' => 'required|string|unique:versions,version_name,NULL,id,movie_id,' . $movie_id,
        ]);

        $movie = Movie::findOrFail($movie_id);

        $movie->versions()->create([
            'version_name' => $request->version_name,
        ]);

        return redirect()->route('movies.show', $movie_id)
            ->with('success', 'Version added successfully!');
    }

    // Version edit form (Blade)
    public function editVersion($movie_id, $version_name)
    {
        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();

        return view('versions.edit', compact('movie_id', 'version'));
    }

    // Update Version (handle PUT)
    public function updateVersion(Request $request, $movie_id, $version_name)
    {
        $request->validate([
            'version_name' => 'required|string',
        ]);

        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();

        $version->update(['version_name' => $request->version_name]);

        return redirect()->route('movies.show', $movie_id)
            ->with('success', 'Version updated successfully!');
    }

    // Delete Version (handle DELETE)
    public function deleteVersion($movie_id, $version_name)
    {
        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();

        foreach ($version->seasons as $season) {
            $season->episodes()->delete();
        }
        $version->seasons()->delete();
        $version->delete();

        return redirect()->route('movies.show', $movie_id)
            ->with('success', 'Version deleted successfully!');
    }

    // Season create form (Blade)
    public function createSeason($movie_id, $version_name)
    {
        return view('seasons.create', compact('movie_id', 'version_name'));
    }

    // Add Season (handle POST)
    public function addSeason(Request $request, $movie_id, $version_name)
    {
        $request->validate([
            'season_number' => 'required|integer',
        ]);

        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();

        $version->seasons()->create([
            'season_number' => $request->season_number,
        ]);

        return redirect()->route('movies.show', $movie_id)
            ->with('success', 'Season added successfully!');
    }

    // Season edit form (Blade)
    public function editSeason($movie_id, $version_name, $season_number)
    {
        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();
        $season = $version->seasons()->where('season_number', $season_number)->firstOrFail();

        return view('seasons.edit', compact('movie_id', 'version_name', 'season_number'));
    }

    // Update Season (handle PUT)
    public function updateSeason(Request $request, $movie_id, $version_name, $season_number)
    {
        $request->validate([
            'season_number' => 'required|integer',
        ]);

        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();
        $season = $version->seasons()->where('season_number', $season_number)->firstOrFail();

        $season->update(['season_number' => $request->season_number]);

        return redirect()->route('movies.show', $movie_id)
            ->with('success', 'Season updated successfully!');
    }

    // Delete Season (handle DELETE)
    public function deleteSeason($movie_id, $version_name, $season_number)
    {
        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();
        $season = $version->seasons()->where('season_number', $season_number)->firstOrFail();

        $season->episodes()->delete();
        $season->delete();

        return redirect()->route('movies.show', $movie_id)
            ->with('success', 'Season deleted successfully!');
    }

    // Episode create form (Blade)
    public function createEpisode($movie_id, $version_name, $season_number)
    {
        return view('episodes.create', compact('movie_id', 'version_name', 'season_number'));
    }
    public function deleteEpisode($movie_id, $version_name, $season_number, $episode_id)
    {
        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();
        $season = $version->seasons()->where('season_number', $season_number)->firstOrFail();
        $episode = $season->episodes()->where('id', $episode_id)->firstOrFail();

        $episode->delete();

        return redirect()->route('movies.show', $movie_id)
            ->with('success', 'Episode deleted successfully!');
    }

    // Add Episode (handle POST)
    public function addEpisodes(Request $request, $movie_id, $version_name, $season_number)
    {
        $request->validate([
            'episode' => 'required|integer',
            'link' => 'required|url',
        ]);

        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();
        $season = $version->seasons()->where('season_number', $season_number)->firstOrFail();

        $season->episodes()->create([
            'episode' => $request->episode,
            'link' => $request->link,
        ]);

        return redirect()->route('movies.show', $movie_id)
            ->with('success', 'Episode added successfully!');
    }

    // Episode edit form (Blade)
    public function editEpisode($movie_id, $version_name, $season_number, $episode_id)
    {
        $movie = Movie::findOrFail($movie_id);
        $version = $movie->versions()->where('version_name', $version_name)->firstOrFail();
        $season = $version->seasons()->where('season_number', $season_number)->firstOrFail();
        $episode = $season->episodes()->where('id', $episode_id)->firstOrFail();

        return view('episodes.edit', compact('movie_id', 'version_name', 'season_number', 'episode'));
    }

    // Update Episode (handle PUT)
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

        return redirect()->route('movies.show', $movie_id)
            ->with('success', 'Episode updated successfully!');
    }

    // Helper to format movie structure for views
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

    // Helper to sync versions/seasons/episodes on create or update
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
}
