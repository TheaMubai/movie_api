<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Version extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'version_name',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }
}
