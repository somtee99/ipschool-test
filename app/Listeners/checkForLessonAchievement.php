<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Achievement;
use App\Events\LessonWatched;
use App\Events\AchievementUnlocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckForLessonAchievement
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\LessonWatched  $event
     * @return void
     */
    public function handle(LessonWatched $event)
    {
        $lesson = $event->lesson;
        $user = $event->user;
        $no_of_watched_lessons = $user->watched()->get()->count();
        $achievements = Achievement::where('type', 'lesson')->get();

        //set user's current achievement
        $current_achievement = $user->achievements()->where('type', 'lesson')->get()->last();

        //if current there is no current achievement
        if(!$current_achievement){
            $current_index = 0;
        }else{
            $current_index = array_search($current_achievement, array_values($achievements));
        }   

        //if achievement is not on the highest level
        if($current_index != count($achievements)){
            $next_achievement = $achievements[$current_index++];
            $next_breakpoint = $next_achievement['breakpoint'];

            //if next achievement is reached
            if($no_of_watched_lessons == $next_breakpoint){
                $user->achievements()->attach($next_achievement->id);
                AchievementUnlocked::dispatch($next_achievement['name'], $user);
            }
        }
    }
}
