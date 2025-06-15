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
            'movie_logo' => URL::to('/image/battle through the heavens.png'),
            'movie_name' => 'battle through the heavens',
        ]);

        $types = [
            'original' => [
                1 => [
                    ['episode' => 1, 'link' => 'https://youtu.be/gSgyw2-WchI?si=9udhrBkkp4dhJ-qs'],
                    ['episode' => 2, 'link' => 'https://youtu.be/UMU-ZFpjUkA?si=-LX-NEjJ0w4HYL99'],
                    ['episode' => 3, 'link' => 'https://youtu.be/1FJyYLsxIi4?si=qRXcBHO7XdDyCtUU'],
                ],
            ],
            'khmer' => [
                1 => [
                    ['episode' => 1, 'link' => 'https://youtu.be/zKkP64tdOHE?si=RFquOXZEprGF2Xvt'],
                    ['episode' => 2, 'link' => 'https://youtu.be/udSPK8Ym9sQ?si=KflbEKUZvZhwVU2G'],
                    ['episode' => 3, 'link' => 'https://youtu.be/H4uxBtCYzmM?si=-lFR-I4kPnSntmFr'],
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
