<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InterviewSession;
use App\Models\QuizAttempt;
use App\Models\Resume;
use App\Models\CodingSubmission;
use App\Models\UserBadge;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (empty($user->career_path)) {
            return redirect()->route('onboarding');
        }

        // Calculate scores
        $atsScore = Resume::where('user_id', $user->id)->latest()->first()?->ats_score ?? 0;
        
        $interviews = InterviewSession::where('user_id', $user->id)->where('status', 'completed')->get();
        $interviewScore = $interviews->count() > 0 ? round($interviews->avg('score')) : 0;
        $communicationScore = $interviews->count() > 0 ? round($interviews->avg('communication_score')) : 0;
        $confidenceScore = $interviews->count() > 0 ? round($interviews->avg('confidence_score')) : 0;

        $quizzes = QuizAttempt::where('user_id', $user->id)->get();
        $quizScore = $quizzes->count() > 0 ? round(($quizzes->sum('correct_answers') / $quizzes->sum('total_questions')) * 100) : 0;

        $submissions = CodingSubmission::where('user_id', $user->id)->get();
        $codingScore = $submissions->count() > 0 ? round(($submissions->where('status', 'Passed')->count() / $submissions->count()) * 100) : 0;

        $badges = UserBadge::where('user_id', $user->id)->latest()->take(6)->get();

        // Generate mock weekly progress
        $weeklyProgress = [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'data' => [20, 35, $user->xp_points % 50, 45, 60, $user->xp_points % 75, $user->xp_points]
        ];

        // Recommendations based on missing skills or selected role
        $role = $user->user_type == 'fresher' ? 'Junior Laravel Developer' : 'Senior Laravel Developer';
        $recommendedSkills = ['Docker', 'AWS', 'React', 'Unit Testing', 'Redis'];

        return view('dashboard.index', compact(
            'user',
            'atsScore',
            'interviewScore',
            'quizScore',
            'codingScore',
            'communicationScore',
            'confidenceScore',
            'badges',
            'weeklyProgress',
            'role',
            'recommendedSkills'
        ));
    }

    public function showOnboarding()
    {
        $user = Auth::user();
        if (!empty($user->career_path)) {
            return redirect()->route('dashboard');
        }
        return view('dashboard.onboarding', compact('user'));
    }

    public function saveOnboarding(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:fresher,experienced',
            'career_path' => 'required|string',
            'skills' => 'required|array|min:1',
        ]);

        $user = Auth::user();
        $user->update([
            'user_type' => $request->user_type,
            'career_path' => $request->career_path,
            'skills' => $request->skills,
        ]);

        return redirect()->route('dashboard')->with('success', 'Profile configuration completed successfully!');
    }
}
