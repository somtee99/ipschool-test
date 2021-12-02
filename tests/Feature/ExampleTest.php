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

    /**
     * Creates a particular number of lessons and dispatches the last lesson. Returns the user 
     * and the dispatched lesson
     *
     * @param int $no_of_lessons_watched bool $watched
     * @return void
     */
    public function dispatchLessonOnWatchEvent(int $no_of_lessons_watched, bool $watched = true)
    {
        //create lessons from factory
        $lessons = Lesson::factory()->count($no_of_lessons_watched)->create();
        $lesson = $lessons->last();

        $user = $this->attachLessonsToUser($lessons);

        Event::fake([
            LessonWatched::class
        ]);

        //if user watched a lesson
        if($lesson){
            //run lesson watched event
            event(new LessonWatched($lesson, $user));

            Event::assertDispatched(LessonWatched::class);
        }

        return [
            'lesson' => $lesson, 
            'user' => $user
        ];
    }

    public function attachLessonsToUser($lessons, $watched = true){
        //attach lessons to a user
        $user = User::factory()
                    ->count(1)
                    ->hasAttached($lessons, ['watched' => $watched])
                    ->create()->first();
        
        return $user;
    }

    public function attachUserToComments($user, $no_of_comments){
        //create comments from factory for user
        $comments = Comment::factory()->count($no_of_comments)
            ->for($user)->create();

        return $comments;
    }

    public function dispatchCommentWrittenEvent(int $no_of_comments_written)
    {
        //create user from factory
        $user = User::factory()->create();
        
        $comments = $this->attachUserToComments($user, $no_of_comments_written);

        $comment = $comments->last();

        Event::fake([
            CommentWritten::class
        ]);

        //if user has a comment
        if($comment){
            //run comment written event
            event(new CommentWritten($comment, $user));
            Event::assertDispatched(CommentWritten::class);
        }    

        return [
            'comment' => $comment, 
            'user' => $user
        ];
    }

    public function dispatchAchievementUnlockedEvent(int $no_of_achievements)
    {
        //set achievements
        $achievements = Achievement::all()->take($no_of_achievements);
        $achievement = $achievements->last();

        if($no_of_achievements){
            //attach achievements to a user from factory
            $user = User::factory()
            ->count($no_of_achievements)
            ->hasAttached($achievements)
            ->create()->first();
        }else{
            //create a default user from factory
            $user = User::factory()->create();
        }

        Event::fake([
            AchievementUnlocked::class
        ]);

        //if user has an achievement
        if($achievement){
            //run achievement unlocked event
            event(new AchievementUnlocked($achievement->name, $user));
            
            Event::assertDispatched(AchievementUnlocked::class);
        }

        return [
            'achievement' => $achievement, 
            'user' => $user
        ];
    }

    public function test_lesson_achievement_unlocked()
    {
        //set testing variables
        $no_of_lessons_watched = 9;
        $expected_data = [
            'unlocked_achievements' => [],
            'next_available_achievements' => ['First Lesson Watched']
        ];

        //dispatch lesson on watch event
        $event = $this->dispatchLessonOnWatchEvent($no_of_lessons_watched, true);

        //send request
        $response = $this->get("/users/{$event['user']['id']}/achievements");

        //assert expected data from response
        $response->assertStatus(200)->assertJson($expected_data);
    }


    public function test_comment_achievement_unlocked()
    {
        //set testing variables
        $no_of_comments_written = 4;
        $expected_data = [
            'unlocked_achievements' => ['First Comment Written', '3 Comments Written',
            ],
            'next_available_achievements' => ['First Lesson Watched', '5 Comments Written']
        ];

        //dispatch comment written event
        $event = $this->dispatchCommentWrittenEvent($no_of_comments_written);

        //send http request
        $response = $this->get("/users/{$event['user']['id']}/achievements");

        //assert expected data from response
        $response->assertStatus(200)->assertJson($expected_data);
    }


    public function test_badge_unlocked()
    {
        //set testing variables
        $no_of_achievements = 15;
        $expected_data = [
            'current_badge' => 'Master',
            'next_badge' => null,
            'remaining_to_unlock_next_badge' => 0
        ];

        //dispatch achievement unlocked event
        $event = $this->dispatchAchievementUnlockedEvent($no_of_achievements);
        
        //send http request
        $response = $this->get("/users/{$event['user']['id']}/achievements");

        //assert expected data from response
        $response->assertStatus(200)->assertJson($expected_data);
    }

    
}
