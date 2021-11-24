<?php

namespace Tests\Feature;

use Tests\TestCase;
use Event;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Comment;
use App\Models\Achievement;
use App\Models\Badge;
use App\Events\LessonWatched;
use App\Events\CommentWritten;
use App\Events\AchievementUnlocked;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $user = User::factory()->create();
        
        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);
    }

    public function dispatchLessonOnWatchEvent(int $no_of_lessons_watched, bool $watched = true)
    {
        $lessons = Lesson::factory()->count($no_of_lessons_watched)->create();
        $lesson = $lessons->last();

        $user = User::factory()
                    ->count(1)
                    ->hasAttached($lessons, ['watched' => $watched])
                    ->create()->first();

        Event::fake([
            LessonWatched::class
        ]);

        event(new LessonWatched($lesson, $user));

        Event::assertDispatched(LessonWatched::class);

        return [
            'lesson' => $lesson, 
            'user' => $user
        ];
    }

    public function dispatchCommentWrittenEvent(int $no_of_comments_written)
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count($no_of_comments_written)
            ->for($user)->create();

        $comment = $comments->last();

        Event::fake([
            CommentWritten::class
        ]);

        event(new CommentWritten($comment, $user));

        Event::assertDispatched(CommentWritten::class);

        return [
            'comment' => $comment, 
            'user' => $user
        ];
    }

    public function dispatchAchievementUnlockedEvent(int $no_of_achievements)
    {
        $achievements = Achievement::where('id', '<=', $no_of_achievements)->get();

        $user = User::factory()
            ->count($no_of_achievements)
            ->hasAttached($achievements)
            ->create()->first();

        $achievement = $achievements->last();

        Event::fake([
            AchievementUnlocked::class
        ]);

        event(new AchievementUnlocked($achievement->name, $user));

        Event::assertDispatched(AchievementUnlocked::class);

        return [
            'achievement' => $achievement, 
            'user' => $user
        ];
    }

    public function test_lesson_achievement_unlocked()
    {
        $no_of_lessons_watched = 5;
        $expected_data = [
            'unlocked_achievements' => ['First Lesson Watched', '5 Lessons Watched'],
            'next_available_achievements' => ['10 Lessons Watched']
        ];

        $event = $this->dispatchLessonOnWatchEvent($no_of_lessons_watched, true);

        $response = $this->get("/users/{$event['user']['id']}/achievements");

        $response->assertStatus(200)->assertJson($expected_data);
    }


    public function test_comment_achievement_unlocked()
    {
        $no_of_comments_written = 20;
        $expected_data = [
            'unlocked_achievements' => ['First Comment Written', '3 Comments Written',
            '5 Comments Written', '10 Comments Written', '20 Comments Written'],
            'next_available_achievements' => []
        ];

        $event = $this->dispatchCommentWrittenEvent($no_of_comments_written);

        $response = $this->get("/users/{$event['user']['id']}/achievements");

        $response->assertStatus(200)->assertJson($expected_data);
    }


    public function test_badge_unlocked()
    {
        $no_of_achievements = 10;
        $expected_data = [
            'current_badge' => 'Master',
            'next_badge' => null,
            'remaining_to_unlock_next_badge' => 0
        ];

        $event = $this->dispatchAchievementUnlockedEvent($no_of_achievements);

        $response = $this->get("/users/{$event['user']['id']}/achievements");

        $response->assertStatus(200)->assertJson($expected_data);
    }

    
}
