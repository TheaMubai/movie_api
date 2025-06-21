@extends('layouts.app')

@section('content')
<h3>➕ Add Season to {{ $version_name }} ({{ $movie_name }})</h3>

<form method="POST" action="{{ url("/movies/{$movie_id}/versions/{$version_name}/seasons") }}">
    @csrf
    <div class="mb-3">
        <label for="season_number" class="form-label">Season Number</label>
        <input type="number" name="season_number" id="season_number" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Add Season</button>
    <a href="{{ url("/movies/{$movie_id}") }}" class="btn btn-secondary">Back</a>
</form>
@endsection
