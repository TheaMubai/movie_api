@extends('layouts.app')

@section('content')
<h3>✏️ Edit Season</h3>

<form method="POST" action="{{ url("/movies/{$movie_id}/versions/{$version_name}/seasons/{$season_number}") }}">
    @csrf @method('PUT')

    <div class="mb-3">
        <label for="season_number" class="form-label">Season Number</label>
        <input type="number" name="season_number" id="season_number" class="form-control" value="{{ $season_number }}" required>
    </div>

    <button type="submit" class="btn btn-primary">Update Season</button>
    <a href="{{ url("/movies/{$movie_id}") }}" class="btn btn-secondary">Back</a>
</form>
@endsection
