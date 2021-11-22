<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        return $user->achievements()->name;
    }

    public function getNextAvailableAchievements($user){
        $lesson_breakpoints = [1, 5, 10, 25, 50];
        $comment_breakpoints = [1, 3, 5, 10, 20];
        $no_of_lesson_achievements = $user->achievements()->where('type', 'lesson')->count();
        $no_of_comment_achievements = $user->achievements()->where('type', 'comment')->count();

        if($no_of_lesson_achievements == 0){
            $next_lesson_achievement = 'First Lesson Watched';
        }else{
            $next_lesson_achievement = $lesson_breakpoints[$no_of_lesson_achievements + 1] . ' Lessons Watched';
        }
        
        if($no_of_comment_achievements == 0){
            $next_comment_achievement = 'First Comment Written';
        }else{
            $next_comment_achievement = $comment_breakpoints[$no_of_comment_achievements + 1] . ' Comments Written';
        }

        return [$next_lesson_achievement, $next_comment_achievement];
    }

    public function currentBadge($user){
        return $user->badges()->latest()->first()->name;
    }

    public function nextBadge($user){
        $no_of_badges = $user->badges()->count();
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

        if($no_of_badges == 0){
            $next_badge = 'Beginner';
        }else{
            $next_badge = $badges[$no_of_badges + 1]['name'];
        }

        return $next_badge;
    }

    public function remainingForNextBadge($user){
        $no_of_badges = $user->badges()->count();
        $no_of_achievements = $user->achievements()->count();
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

        $next_badge_breakpoint = $badges[$no_of_badges + 1]['breakpoint'];
        $remaining = $next_badge_breakpoint - $no_of_achievements;

        return $remaining;
    }
}
