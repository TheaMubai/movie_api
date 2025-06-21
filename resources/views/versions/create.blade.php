@extends('layouts.app')

@section('content')
<h3>➕ Add Version to {{ $movie_name }}</h3>

<form method="POST" action="{{ url("/movies/{$movie_id}/versions") }}">
    @csrf
    <div class="mb-3">
        <label for="version" class="form-label">Version Name</label>
        <input type="text" name="version" id="version" class="form-control" placeholder="e.g. original, dubbed" required>
    </div>

    <button type="submit" class="btn btn-success">Add Version</button>
    <a href="{{ url("/movies/{$movie_id}") }}" class="btn btn-secondary">Back</a>
</form>
@endsection
