<form action="{{ url("/movies/{$movie_id}/versions/{$version_name}") }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this version?')">🗑️</button>
</form>