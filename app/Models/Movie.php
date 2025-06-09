<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_logo',
        'movie_name',
    ];

    public function versions()
    {
        return $this->hasMany(Version::class);
    }
}
