<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Comment;
use App\Models\Achievement;
use App\Events\LessonWatched;
use App\Events\CommentWritten;
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

    public function dispatchLessonOnWatchEvent(int $no_of_lessons_watched, bool $watched = true){
        $lessons = Lesson::factory()->count($no_of_lessons_watched)->create();
        $lesson = $lessons->last();

        $user = User::factory()
                    ->count(1)
                    ->hasAttached($lessons, ['watched' => $watched])
                    ->create()->first();

        LessonWatched::dispatch($lesson, $user);

        return [
            'lesson' => $lesson, 
            'user' => $user
        ];
    }

    public function dispatchCommentWrittenEvent(int $no_of_comments_written){
        $user = User::factory()->create();
        $comments = Comment::factory()->count($no_of_comments_written)
            ->for($user)->create();

        $comment = $comments->last();

        CommentWritten::dispatch($comment, $user);

        return [
            'comment' => $comment, 
            'user' => $user
        ];
    }

    public function test_lesson_achievement_unlocked()
    {
        $no_of_lessons_watched = 4;
        $expected_data = [
            'unlocked_achievements' => ['First Lesson Watched']
        ];

        $event = $this->dispatchLessonOnWatchEvent($no_of_lessons_watched, true);

        $response = $this->get("/users/{$event['user']['id']}/achievements");

        $response->assertStatus(200)->assertJson($expected_data);
    }

    public function test_if_lesson_achievement_is_stored()
    {
        $no_of_lessons_watched = 4;
        $achievement = null;

        //get possible achievement
        $achievements = Achievement::where('type', 'lesson')->get();
        foreach($achievements as $possible_achievement){
            //if achievement requirements is met
            if($no_of_lessons_watched == $possible_achievement->breakpoint){
                $achievement = $possible_achievement;
            }
        }

        //dispatch lesson on watch event
        $event = $this->dispatchLessonOnWatchEvent($no_of_lessons_watched, true);

        if($achievement){
            $this->assertDatabaseHas('achievement_user', [
                'user_id' => $event['user']['id'],
                'achievement_id' => $achievement->id
            ]);
        }else{
            $this->expectNotToPerformAssertions();
        }
        
    }

    public function test_comment_achievement_unlocked()
    {
        $no_of_comments_written = 4;
        $expected_data = [
            'unlocked_achievements' => ['First Comment Written']
        ];

        $event = $this->dispatchCommentWrittenEvent($no_of_comments_written);

        $response = $this->get("/users/{$event['user']['id']}/achievements");

        $response->assertStatus(200)->assertJson($expected_data);
    }

    public function test_if_comment_achievement_is_stored()
    {
        $no_of_comments_written = 4;
        $achievement = null;

        //get possible achievement
        $achievements = Achievement::where('type', 'comment')->get();
        foreach($achievements as $possible_achievement){
            //if achievement requirements is met
            if($no_of_comments_written == $possible_achievement->breakpoint){
                $achievement = $possible_achievement;
            }
        }

        //dispatch lesson on watch event
        $event = $this->dispatchCommentWrittenEvent($no_of_comments_written);

        if($achievement){
            $this->assertDatabaseHas('achievement_user', [
                'user_id' => $event['user']['id'],
                'achievement_id' => $achievement->id
            ]);
        }else{
            $this->expectNotToPerformAssertions();
        }
        
    }
}
