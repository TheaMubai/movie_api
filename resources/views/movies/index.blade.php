@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Movies List</h1>

    <a href="{{ url('/movies/create') }}" class="btn btn-primary mb-3">Add New Movie</a>
    
    @foreach ($movies as $movie)
        <div class="card mb-4">
            <div class="card-body">
                <h3>{{ $movie['movie_name'] }}</h3>
                <img src="{{ $movie['movie_logo'] }}" class="img-thumbnail mb-3" style="max-width: 200px;">

                @foreach ($movie['type'] as $version)
                    <div class="mb-2">
                        <strong>Version:</strong> {{ $version['version'] }}
                        @foreach ($version['season'] as $seasonNumber => $season)
                            <div class="ms-3">
                                <strong>Season {{ $seasonNumber }}</strong>
                                <ul>
                                    @foreach ($season['episodes'] as $ep)
                                        <li>Episode {{ $ep['episode'] }} -
                                            <a href="{{ $ep['link'] }}" target="_blank">Watch</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                <a href="{{ url("/movies/{$movie['movie_id']}") }}" class="btn btn-info btn-sm">View</a>
                <a href="{{ url("/movies/{$movie['movie_id']}/edit") }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ url("/movies/{$movie['movie_id']}") }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this movie?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection
