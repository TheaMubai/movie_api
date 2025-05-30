<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    public function version()
    {
        return $this->belongsTo(Version::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
}
