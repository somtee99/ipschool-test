<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Achievement;
use App\Models\Badge;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        return response()->json([
            'unlocked_achievements' => $this->getUnlockedAchievements($user),
            'next_available_achievements' => $this->getNextAvailableAchievements($user),
            'current_badge' => $this->currentBadge($user),
            'next_badge' => $this->nextBadge($user),
            'remaining_to_unlock_next_badge' => $this->remainingForNextBadge($user)
        ]);
    }

    public function getUnlockedAchievements($user){
        $achievement_names = [];

        //get user's achievements
        $user_lesson_achievements = $this->getUserLessonAchievements($user);
        $user_comment_achievements = $this->getUserCommentAchievements($user);

        //get names of lesson achievements
        foreach($user_lesson_achievements as $lesson_achievement){
            array_push($achievement_names, $lesson_achievement['name']);
        }
        //get names of comment achievements
        foreach($user_comment_achievements as $comment_achievement){
            array_push($achievement_names, $comment_achievement['name']);
        }

        return $achievement_names;
    }

    public function getNextAvailableAchievements($user){
        //get all achievements
        $lesson_achievements = Achievement::where('type', 'lesson')->get();
        $comment_achievements = Achievement::where('type', 'comment')->get();

        //get user's achievements
        $user_lesson_achievements = $this->getUserLessonAchievements($user);
        $user_comment_achievements = $this->getUserCommentAchievements($user);

        $no_of_lesson_achievements = count($user_lesson_achievements);
        //if user's lesson achievement is on the highest level
        if($no_of_lesson_achievements == count($lesson_achievements)){
            $next_lesson_achievement_name = null;
        }else{
            //get user's next lesson achievement
            $next_lesson_achievement = $lesson_achievements[$no_of_lesson_achievements];
            $next_lesson_achievement_name = $next_lesson_achievement->name;
        }

        $no_of_comment_achievements = count($user_comment_achievements);
        //if user's comment achievement is on the highest level
        if($no_of_comment_achievements == count($comment_achievements)){
            $next_comment_achievement_name = null;
        }else{
            //get user's next comment achievement
            $next_comment_achievement = $comment_achievements[$no_of_comment_achievements];
            $next_comment_achievement_name = $next_comment_achievement->name;
        }

        return [$next_lesson_achievement_name, $next_comment_achievement_name];
    }

    public function currentBadge($user){
        //get all badges
        $badges = Badge::all();

        //get user's badges
        $user_badges = $this->getUserBadges($user);
        
        //get user's current badge
        $current_badge = $user_badges[count($user_badges) - 1];
        $current_badge_name = $current_badge->name;

        return $current_badge_name;
    }

    public function nextBadge($user){
        //get all badges
        $badges = Badge::all();

        //get user's badges
        $user_badges = $this->getUserBadges($user);
        $no_of_badges = count($user_badges);

        //if user badge is on the highest level
        if($no_of_badges == count($badges)){
            $next_badge_name = null;
        }else{
            //get user's next badge
            $next_badge = $badges[$no_of_badges];
            $next_badge_name = $next_badge->name;
        }

        return $next_badge_name;
    }

    public function remainingForNextBadge($user){
        //get all badges
        $badges = Badge::all();

        //get user's badges
        $user_badges = $this->getUserBadges($user);
        $no_of_badges = count($user_badges);

        //if user's badge is on the highest level
        if($no_of_badges == count($badges)){
            $remaining = 0;
        }else{
            //get remaining to reach next badge
            $next_badge_breakpoint = $badges[$no_of_badges]['breakpoint'];
            $remaining = $next_badge_breakpoint - $no_of_achievements;
        }

        return $remaining;
    }

    public function getUserBadges($user){
        //get number of user's achievements 
        $no_of_achievements = $user->achievements()->count();
        //get all badges 
        $badges = Badge::all();
        $user_badges = [];

        //get user's unlocked badges
        foreach($badges as $badge){
            //if user has unlocked badge
            if($no_of_achievements >= $badge->breakpoint){
                array_push($user_badges, $badges);
            }
        }

        return $badges;   
    }

    public function getUserCommentAchievements($user){
        //get number of user's comments
        $no_of_comments = $user->comments()->count();

        //get all comment achievements
        $comment_achievements = Achievement::where('type', 'comment')->get();
        $user_comment_achievements = [];

        //get user's unlocked achievements
        foreach($comment_achievements as $comment_achievement){
            //if user has unlocked achievement
            if($no_of_comments >= $comment_achievement->breakpoint){
                array_push($user_comment_achievements, $comment_achievement);
            }
        }

        return $user_comment_achievements;
    }

    public function getUserLessonAchievements($user){
        //get number of user's watched lessons
        $no_of_watched_lessons = $user->watched()->count();

        //get all lesson achievements
        $lesson_achievements = Achievement::where('type', 'lesson')->get();
        $user_lesson_achievements = [];

        //get user's unlocked achievements
        foreach($lesson_achievements as $lesson_achievement){
            //if user has unlocked achievement
            if($no_of_watched_lessons >= $lesson_achievement->breakpoint){
                array_push($user_lesson_achievements, $lesson_achievement);
            }
        }

        return $user_lesson_achievements;
    }
}
