<?php

namespace App\Listeners;

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
        $no_of_achievements = $user()->achievements()->count();
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
        ]; //breakpoints for badges

        foreach($badges as $badge){
            if($no_of_achievements == $badge['breakpoint']){
                $data['name'] = $badge['name'];
                $user()->badges()->create($data);
                BadgeUnlocked::dispatch($badge['name'], $user);
                break;
            }
        }
    }
}
