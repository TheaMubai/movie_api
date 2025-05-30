<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movie = \App\Models\Movie::create([
            'movie_name' => 'Soul Land',
            'movie_logo' => url('images/soulLand.png'),
        ]);

        $version = $movie->versions()->create(['version' => 'Khmer']);

        $season = $version->seasons()->create(['season_number' => 1]);

        $season->episodes()->createMany([
            ['episode' => 1, 'link' => 'https://youtu.be/R6eq2JfEg7Q?si=U_wPL8n7DQEosyfV'],
            ['episode' => 2, 'link' => 'https://youtu.be/MgQ5zaSXowY?si=Py0pVCbf59jEVH2b'],
        ]);
    }
}
