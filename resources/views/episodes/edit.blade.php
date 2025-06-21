@extends('layouts.app')

@section('content')
<h3>✏️ Edit Episode</h3>

<form method="POST" action="{{ url("/movies/{$movie_id}/versions/{$version_name}/seasons/{$season_number}/episodes/{$episode_id}") }}">
    @csrf @method('PUT')

    <div class="mb-3">
        <label for="episode" class="form-label">Episode Number</label>
        <input type="number" name="episode" id="episode" class="form-control" value="{{ $episode['episode'] }}" required>
    </div>

    <div class="mb-3">
        <label for="link" class="form-label">YouTube Link</label>
        <input type="url" name="link" id="link" class="form-control" value="{{ $episode['link'] }}" required>
    </div>

    <button type="submit" class="btn btn-primary">Update Episode</button>
    <a href="{{ url("/movies/{$movie_id}") }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
