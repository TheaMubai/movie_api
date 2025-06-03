<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_id',
        'episode',
        'link',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
