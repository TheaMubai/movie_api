@extends('layouts.app')

@section('content')
<h3>➕ Add Episode to Season {{ $season_number }} - {{ $version_name }}</h3>

<form method="POST" action="{{ url("/movies/{$movie_id}/versions/{$version_name}/seasons/{$season_number}/episodes") }}">
    @csrf

    <div class="mb-3">
        <label for="episode" class="form-label">Episode Number</label>
        <input type="number" name="episode" id="episode" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="link" class="form-label">YouTube Link</label>
        <input type="url" name="link" id="link" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Add Episode</button>
    <a href="{{ url("/movies/{$movie_id}") }}" class="btn btn-secondary">Back</a>
</form>
@endsection
