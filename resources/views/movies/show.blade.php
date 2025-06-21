@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $movie['movie_name'] }}</h2>
    <img src="{{ $movie['movie_logo'] }}" class="img-fluid mb-4" style="max-width: 250px;">

    {{-- ADD VERSION --}}
    <form action="{{ url("/movies/{$movie['movie_id']}/versions") }}" method="POST" class="mb-4">
        @csrf
        <div class="input-group">
            <input type="text" name="version_name" class="form-control" placeholder="New version name" required>
            <button class="btn btn-primary" type="submit">Add Version</button>
        </div>
    </form>

    @foreach ($movie['type'] as $version)
        <div class="card mb-3">
            <div class="card-body">
                <h5>Version: <strong>{{ $version['version'] }}</strong></h5>

                {{-- DELETE VERSION --}}
                <form action="{{ url("/movies/{$movie['movie_id']}/versions/{$version['version']}") }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this version?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Delete Version</button>
                </form>

                {{-- ADD SEASON --}}
                <form action="{{ url("/movies/{$movie['movie_id']}/versions/{$version['version']}/seasons") }}" method="POST" class="mt-3">
                    @csrf
                    <div class="input-group">
                        <input type="number" name="season_number" class="form-control" placeholder="Season #" required>
                        <button class="btn btn-success" type="submit">Add Season</button>
                    </div>
                </form>

                @foreach ($version['season'] as $seasonNumber => $season)
                    <div class="mt-4 ms-3">
                        <h6>Season {{ $seasonNumber }}</h6>

                        {{-- DELETE SEASON --}}
                        <form action="{{ url("/movies/{$movie['movie_id']}/versions/{$version['version']}/seasons/{$seasonNumber}") }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this season?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">Delete Season</button>
                        </form>

                        {{-- ADD EPISODE --}}
                        <form action="{{ url("/movies/{$movie['movie_id']}/versions/{$version['version']}/seasons/{$seasonNumber}/episodes") }}" method="POST" class="mt-2 row g-2">
                            @csrf
                            <div class="col-md-2">
                                <input type="number" name="episode" class="form-control" placeholder="Ep #" required>
                            </div>
                            <div class="col-md-6">
                                <input type="url" name="link" class="form-control" placeholder="Episode link" required>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-success w-100">Add Episode</button>
                            </div>
                        </form>


                        {{-- LIST EPISODES --}}
                        <ul class="mt-2">
                            @foreach ($season['episodes'] as $ep)
                                <li>
                                    Ep {{ $ep['episode'] }} —
                                    <a href="{{ $ep['link'] }}" target="_blank">Watch</a>

                                    {{-- DELETE EPISODE --}}
                                    <form action="{{ url("/movies/{$movie['movie_id']}/versions/{$version['version']}/seasons/{$seasonNumber}/episodes/{$ep['episode_id']}") }}" method="POST" class="d-inline" onsubmit="return confirm('Delete episode?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link text-danger btn-sm p-0">Delete</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <a href="{{ url('/movies') }}" class="btn btn-secondary">Back to List</a>
@endsection
