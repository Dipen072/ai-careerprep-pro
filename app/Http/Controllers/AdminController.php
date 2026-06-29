<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\InterviewSession;
use App\Models\QuizAttempt;
use App\Models\CodingSubmission;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        // Simple authorization check
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        // Metrics aggregation
        $totalUsers = User::count();
        $totalInterviews = InterviewSession::count();
        $totalQuizzes = QuizAttempt::count();
        $totalSubmissions = CodingSubmission::count();
        
        $avgInterviewScore = round(InterviewSession::where('status', 'completed')->avg('score') ?? 0);
        $avgQuizScore = round(QuizAttempt::avg('score') ?? 0);

        // Language distribution
        $languages = [
            'en' => User::where('language_preference', 'en')->count(),
            'hi' => User::where('language_preference', 'hi')->count(),
            'gu' => User::where('language_preference', 'gu')->count(),
            'hi_en' => User::where('language_preference', 'hi_en')->count(),
            'gu_en' => User::where('language_preference', 'gu_en')->count(),
        ];

        // All users list
        $users = User::latest()->get();

        return view('admin.index', compact(
            'totalUsers',
            'totalInterviews',
            'totalQuizzes',
            'totalSubmissions',
            'avgInterviewScore',
            'avgQuizScore',
            'languages',
            'users'
        ));
    }
}
