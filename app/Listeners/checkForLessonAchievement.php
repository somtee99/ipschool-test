<?php

namespace App\Listeners;

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
        $lesson = $event->comment;
        $user = $event-user;
        $no_of_watched_lessons = $user()->watched()->count();
        $achievement_breakpoints = [1, 5, 10, 25, 50];  //breakpoints for watched lessons

        foreach($achievement_breakpoints as $achievement_breakpoint){
            if($no_of_watched_lessons == $achievement_breakpoint){
                if($achievement_breakpoint == 1){
                    $achievement['name'] = 'First Lesson Watched';
                }else{
                    $achievement['name'] = $achievement_breakpoint . ' Lessons Watched';
                }
                $user()->achievements()->create($achievement);
                AchievementUnlocked::dispatch($achievement['name'], $user);
                break;
            }
        }
    }
}
