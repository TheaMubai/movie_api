<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearDatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks to avoid constraint errors
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables in order from child to parent
        \App\Models\Episode::truncate();
        \App\Models\Season::truncate();
        \App\Models\Version::truncate();
        \App\Models\Movie::truncate();

        // Enable foreign key checks again
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
