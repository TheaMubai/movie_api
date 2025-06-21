<form action="{{ url("/movies/{$movie_id}/versions/{$version_name}/seasons/{$season_number}/episodes/{$episode_id}") }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this episode?')">🗑️</button>
</form>
