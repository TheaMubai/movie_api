@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Movie: {{ $movie['movie_name'] }}</h2>

    <form action="{{ url("/movies/{$movie['movie_id']}") }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Movie Name</label>
            <input type="text" name="movie_name" class="form-control" value="{{ $movie['movie_name'] }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Replace Logo (optional)</label>
            <input type="file" name="movie_logo" class="form-control" accept="image/*">
            <small>Current: <img src="{{ $movie['movie_logo'] }}" style="max-height: 60px;"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">Movie Types (JSON)</label>
            <textarea name="types" rows="10" class="form-control">@json(collect($movie['type'])->mapWithKeys(function($v) {
                return [$v['version'] => collect($v['season'])->mapWithKeys(function($s, $sn) {
                    return [$sn => $s['episodes']];
                })];
            }), JSON_PRETTY_PRINT)</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ url('/movies') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
