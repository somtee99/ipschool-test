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
        $no_of_lessons_watched = $user->watched()->count();
        $no_of_comments = $user->comments()->count();

        $lesson_achievements = Achievement::where('type', 'lesson')->get();
        $comment_achievements = Achievement::where('type', 'comment')->get();

        $achievement_names = [];

        foreach($lesson_achievements as $lesson_achievement){
            if($no_of_lessons_watched >= $lesson_achievement->breakpoint){
                array_push($achievement_names, $lesson_achievement->name);
            }
        }

        foreach($comment_achievements as $comment_achievement){
            if($no_of_comments >= $comment_achievement->breakpoint){
                array_push($achievement_names, $comment_achievement->name);
            }
        }

        return $achievement_names;
    }

    public function getNextAvailableAchievements($user){
        $no_of_lessons_watched = $user->watched()->count();
        $no_of_comments = $user->comments()->count();
        $lesson_achievements = Achievement::where('type', 'lesson')->get();
        $comment_achievements = Achievement::where('type', 'comment')->get();

        foreach($lesson_achievements as $lesson_achievement){
            if($no_of_lessons_watched < $lesson_achievement->breakpoint){
                $next_lesson_achievement = $lesson_achievement->name;
                break;
            }
            $next_lesson_achievement = null;
        }

        foreach($comment_achievements as $comment_achievement){
            if($no_of_comments >= $comment_achievement->breakpoint){
                $next_comment_achievement = $comment_achievement->name;
                break;
            }
            $next_comment_achievement = null;
        }

        return [$next_lesson_achievement, $next_comment_achievement];
    }

    public function currentBadge($user){
        $no_of_badges = $user->badges()->count();
        $no_of_achievements = $user->achievements()->count();
        $badges = Badge::all();
        $badge_names = [];

        foreach($badges as $badge){
            if($no_of_achievements >= $badge->breakpoint){
                array_push($badge_names, $badge->name);
            }
        }
        
        $current_badge_name = $badge_names[count($badge_names) - 1];

        return $current_badge_name;
    }

    public function nextBadge($user){
        $no_of_badges = $user->badges()->count();
        $no_of_achievements = $user->achievements()->count();
        $badges = Badge::all();
        $badge_names = [];

        foreach($badges as $badge){
            if($no_of_achievements >= $badge->breakpoint){
                array_push($badge_names, $badge->name);
            }
        }
        $next_badge = $badges[count($badge_names) + 1];
        $next_badge_name = $next_badge->name;

        return $next_badge_name;
    }

    public function remainingForNextBadge($user){
        $no_of_badges = $user->badges()->count();
        $no_of_achievements = $user->achievements()->count();
        $badges = Badge::all();

        if($no_of_badges == count($badges)){
            $remaining = 0;
        }else{
            $next_badge_breakpoint = $badges[$no_of_badges + 1]['breakpoint'];
            $remaining = $next_badge_breakpoint - $no_of_achievements;
        }

        return $remaining;
    }
}
