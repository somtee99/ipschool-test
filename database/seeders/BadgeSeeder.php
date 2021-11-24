<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //set badge names and breakpoints
        $badges = [
            [
                'name' => 'Beginner',
                'breakpoint' => 0
            ],
            [
                'name' => 'Intermediate',
                'breakpoint' => 4
            ],
            [
                'name' => 'Advanced',
                'breakpoint' => 8
            ],
            [
                'name' => 'Master',
                'breakpoint' => 10
            ],
        ]; 

        foreach($badges as $badge){
            //create badge
            Badge::create($badge);
        }
    }
}
