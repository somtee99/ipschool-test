<?php

namespace App\Listeners;

use App\Models\Achievement;
use App\Events\CommentWritten;
use App\Events\AchievementUnlocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckForCommentAchievement
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
     * @param  \App\Events\CommentWritten  $event
     * @return void
     */
    public function handle(CommentWritten $event)
    {
        $comment = $event->comment;
        $user = $comment->user;
        $no_of_comments = $user->comments()->count();
        $achievements = Achievement::where('type', 'comment')->get();

        //set user's current achievement
        $current_achievement = $user->achievements()->where('type', 'comment')->get()->last();

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
            if($no_of_comments == $next_breakpoint){
                $user->achievements()->attach($next_achievement->id);
                AchievementUnlocked::dispatch($achievement['name'], $user);
            }
        }
    }
}
