<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movie;
use App\Models\Season;
use App\Models\Version;
use App\Models\Episode;
use Illuminate\Support\Facades\URL;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movie = Movie::create([
            'movie_logo' => URL::to('/image/soulLand.png'),
            'movie_name' => 'Soul Land',
        ]);

        $types = [
            'original' => [
                1 => [
                    ['episode' => 1, 'link' => 'https://youtu.be/M7lE49GNbf4?si=ZLhKpyrdYeNB3iRe'],
                    ['episode' => 2, 'link' => 'https://youtu.be/g61Iwi4Ny44?si=ESetag5UUCIXHODf'],
                ],
                2 => [
                    ['episode' => 1, 'link' => 'https://youtu.be/JpSyQmPZ1uk?si=fBX34_yO8tkrqthW'],
                ],
            ],
            'khmer' => [
                1 => [
                    ['episode' => 1, 'link' => 'https://youtu.be/R6eq2JfEg7Q?si=PL28_6dI3yQkS66v'],
                ],
                2 => [
                    ['episode' => 1, 'link' => 'https://youtu.be/MgQ5zaSXowY?si=sioJD4eNQbHtO5v7'],
                ],
            ],
        ];

        foreach ($types as $version => $seasons) {
            $type = Version::create([
                'movie_id' => $movie->id,
                'version_name' => $version,
            ]);

            foreach ($seasons as $season_number => $episodes) {
                $season = Season::create([
                    'version_id' => $type->id,
                    'season_number' => $season_number,
                ]);

                foreach ($episodes as $ep) {
                    Episode::create([
                        'season_id' => $season->id,
                        'episode' => $ep['episode'],
                        'link' => $ep['link'],
                    ]);
                }
            }
        }
    }
}
