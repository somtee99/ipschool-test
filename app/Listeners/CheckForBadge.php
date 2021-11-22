<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
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
                'name' => 'beginner',
                'breakpoint' => 0
            ],
            [
                'name' => 'intermediate',
                'breakpoint' => 4
            ],
            [
                'name' => 'advanced',
                'breakpoint' => 8
            ],
            [
                'name' => 'master',
                'breakpoint' => 10
            ],
        ]; //breakpoints for badges

        foreach($badges as $badge){
            if($no_of_achievements == $badge['breakpoint']){
                $data['name'] = $badge['name'];
                $user()->badges()->create($data);
                break;
            }
        }
    }
}