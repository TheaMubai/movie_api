<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'season_number',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
}
