<form method="POST" action="{{ url("/movies/{$movie['movie_id']}") }}" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this movie?')">🗑️</button>
</form>
