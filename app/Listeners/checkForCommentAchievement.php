<?php

namespace App\Listeners;

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
        $no_of_comments = $comment->user()->comments()->count();
        $achievement_breakpoints = [1, 3, 5, 10, 20]; //breakpoints for comments

        foreach($achievement_breakpoints as $achievement_breakpoint){
            if($no_of_comments == $achievement_breakpoint){
                if($achievement_breakpoint == 1){
                    $achievement['name'] = 'First Comment Written';
                }else{
                    $achievement['name'] = $achievement_breakpoint . ' Comments Written';
                }
                $achievement['type'] = 'comment';
                $comment->user()->achievements()->create($achievement);
                AchievementUnlocked::dispatch($achievement['name'], $comment->user);
                break;
            }
        }
    }
}
