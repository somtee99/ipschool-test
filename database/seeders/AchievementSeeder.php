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
        $this->createCommentAchievements();
        $this->createLessonAchievements();
        
    }

    public function createCommentAchievements(){
        //set comment achievement breakpoints 
        $comment_breakpoints = [1, 3, 5, 10, 20];

        foreach($comment_breakpoints as $comment_breakpoint){
            //if it is the first breakpoint
            if($comment_breakpoint == 1){
                $achievement['name'] = 'First Comment Written';
            }else{
                $achievement['name'] = $comment_breakpoint . ' Comments Written';
            }
            $achievement['type'] = 'comment';
            $achievement['breakpoint'] = $comment_breakpoint;

            //create comment achievement
            Achievement::create($achievement);
        }
    }

    public function createLessonAchievements(){
        //set lesson achievement breakpoints 
        $lesson_breakpoints = [1, 5, 10, 25, 50];

        foreach($lesson_breakpoints as $lesson_breakpoint){
            //if it is the first breakpoint
            if($lesson_breakpoint == 1){
                $achievement['name'] = 'First Lesson Watched';
            }else{
                $achievement['name'] = $lesson_breakpoint . ' Lessons Watched';
            }
            $achievement['type'] = 'lesson';
            $achievement['breakpoint'] = $lesson_breakpoint;
            
            //create comment achievement
            Achievement::create($achievement);
        }
    }
}
