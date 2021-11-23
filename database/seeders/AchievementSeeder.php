<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $comment_breakpoints = [1, 3, 5, 10, 20];
        $lesson_breakpoints = [1, 5, 10, 25, 50];

        foreach($comment_breakpoints as $comment_breakpoint){
            if($comment_breakpoint == 1){
                $achievement['name'] = 'First Comment Written';
            }else{
                $achievement['name'] = $comment_breakpoint . ' Comments Written';
            }
            $achievement['type'] = 'comment';
            $achievement['breakpoint'] = $comment_breakpoint;

            Achievement::create($achievement);
        }

        foreach($lesson_breakpoints as $lesson_breakpoint){
            if($lesson_breakpoint == 1){
                $achievement['name'] = 'First Lesson Watched';
            }else{
                $achievement['name'] = $lesson_breakpoint . ' Lessons Watched';
            }
            $achievement['type'] = 'lesson';
            $achievement['breakpoint'] = $lesson_breakpoint;
            
            Achievement::create($achievement);
        }
    }
}
