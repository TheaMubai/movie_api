<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }
}
