@extends('layouts.app')

@section('content')
<h3>✏️ Edit Version</h3>

<form method="POST" action="{{ url("/movies/{$movie_id}/versions/{$version_name}") }}">
    @csrf @method('PUT')

    <div class="mb-3">
        <label for="version" class="form-label">Version Name</label>
        <input type="text" name="version" id="version" class="form-control" value="{{ $version_name }}" required>
    </div>

    <button type="submit" class="btn btn-primary">Update Version</button>
    <a href="{{ url("/movies/{$movie_id}") }}" class="btn btn-secondary">Back</a>
</form>
@endsection
