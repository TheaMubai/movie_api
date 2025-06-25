@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add New Movie</h2>
    
    <form action="{{ url('/movies') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Movie Name</label>
            <input type="text" name="movie_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Movie Logo (Image URL)</label>
            <input type="text" name="movie_logo" class="form-control" placeholder="https://example.com/logo.jpg" required>
        </div>

        <div class="alert alert-info">
            This form assumes a fixed JSON input format for types (version/season/episodes). If needed, use JavaScript to make it dynamic.
        </div>

        <div class="mb-3">
            <label class="form-label">Movie Types (JSON)</label>
            <textarea name="types" rows="10" class="form-control" placeholder='{
  "original": {
    "1": [
      { "episode": 1, "link": "https://example.com/ep1" }
    ]
  }
}' required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Movie</button>
        <a href="{{ url('/movies') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
