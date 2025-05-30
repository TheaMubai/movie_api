<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    public function versions()
    {
        return $this->hasMany(Version::class);
    }
}
