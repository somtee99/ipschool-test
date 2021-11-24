<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Badge;
use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckForBadge
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
     * @param  \App\Events\AchievementUnlocked  $event
     * @return void
     */
    public function handle(AchievementUnlocked $event)
    {
        $user = $event->user;
        $no_of_achievements = $user->achievements()->count();
        $badges = Badge::all();

        //set user's current badge
        $current_badge = $user->badges()->get()->last();

        //if current there is no current badge
        if(!$current_badge){
            $current_index = 0;
        }else{
            $current_index = array_search($current_badge, array_values($badges));
        }   

        //if badge is not on the highest level
        if($current_index != count($badges)){
            $next_badge = $badges[$current_index++];
            $next_breakpoint = $next_badge['breakpoint'];

            //if next badge is reached
            if($no_of_achievements == $next_breakpoint){
                User::find($user->id)->badges()->attach($next_badge->id);
                BadgeUnlocked::dispatch($badge['name'], $user);
            }
        }

    }
}
