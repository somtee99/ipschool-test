<?php

namespace App\Providers;

use App\Events\LessonWatched;
use App\Events\CommentWritten;
use App\Listeners\CheckForCommentAchievement;
use App\Listeners\CheckForLessonAchievement;
use App\Listeners\CheckForBadge;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        CommentWritten::class => [
            CheckForCommentAchievement::class
        ],
        LessonWatched::class => [
            CheckForLessonAchievement::class
        ],
        AchievementUnlocked::class => [
            CheckForBadge::class
        ],
        BadgeUnlocked::class => [
            
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
