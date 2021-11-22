<?php

namespace App\Listeners;

use App\Events\LessonWatched;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class checkForLessonAchievement
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
        $comment = $event->comment;
        $no_of_watched_lessons = $comment->user()->watched()->count();
        $achievement_breakpoints = [1, 5, 10, 25, 50];  //breakpoints for watched lessons

        foreach($achievement_breakpoints as $achievement_breakpoint){
            if($no_of_comments == $achievement_breakpoint){
                if($achievement_breakpoint == 1){
                    $achievement['name'] = 'First Lesson Watched';
                }else{
                    $achievement['name'] = $achievement_breakpoint . ' Lessons Watched';
                }
                $comment->user()->achievements()->create($achievement);
            }
        }
    }
}
