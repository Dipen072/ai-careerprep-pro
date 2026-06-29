<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\CodingController;
use App\Http\Controllers\CareerCoachController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister'])->name('register.post');
    Route::post('/send-otp', [AuthController::class, 'sendOtpSms'])->name('register.send-otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('register.verify-otp');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])->name('login.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password/send', [AuthController::class, 'sendResetOtp'])->name('forgot-password.send');
    Route::post('/forgot-password/verify', [AuthController::class, 'verifyResetOtp'])->name('forgot-password.verify');
    Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword'])->name('forgot-password.reset');
    Route::get('/auth/google/mock', [AuthController::class, 'googleLoginMockForm'])->name('auth.google.mock');
    Route::post('/auth/google/mock', [AuthController::class, 'googleLoginMockSubmit'])->name('auth.google.mock.post');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Onboarding routes
    Route::get('/onboarding', [DashboardController::class, 'showOnboarding'])->name('onboarding');
    Route::post('/onboarding', [DashboardController::class, 'saveOnboarding'])->name('onboarding.post');
    
    // Profile & Language Settings
    Route::post('/profile/language', function (Illuminate\Http\Request $request) {
        $request->validate(['language_preference' => 'required|in:en,hi,gu,hi_en,gu_en']);
        $user = Auth::user();
        $user->update(['language_preference' => $request->language_preference]);
        return back()->with('success', 'Language settings updated!');
    })->name('profile.language');

    // AI Mock Interview routes
    Route::get('/interviews', [InterviewController::class, 'index'])->name('interviews');
    Route::get('/interviews/setup', [InterviewController::class, 'setup'])->name('interviews.setup');
    Route::post('/interviews/create', [InterviewController::class, 'create'])->name('interviews.create');
    Route::get('/interviews/{session}/arena', [InterviewController::class, 'arena'])->name('interviews.arena');
    Route::post('/interviews/arena/{question}/answer', [InterviewController::class, 'submitAnswer'])->name('interviews.answer');
    Route::get('/interviews/arena/{question}/hint', [InterviewController::class, 'getHint'])->name('interviews.hint');
    Route::get('/interviews/{session}/report', [InterviewController::class, 'report'])->name('interviews.report');

    // Quiz routes
    Route::get('/quiz', [QuizController::class, 'index'])->name('quiz');
    Route::get('/quiz/take/{topic}/{difficulty}', [QuizController::class, 'take'])->name('quiz.take');
    Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');

    // Resume Analyzer routes
    Route::get('/resume', [ResumeController::class, 'index'])->name('resume');
    Route::post('/resume/upload', [ResumeController::class, 'upload'])->name('resume.upload');

    // Coding Lab routes
    Route::get('/coding', [CodingController::class, 'index'])->name('coding');
    Route::get('/coding/{problem}', [CodingController::class, 'show'])->name('coding.show');
    Route::post('/coding/{problem}/submit', [CodingController::class, 'submit'])->name('coding.submit');

    // Career Coach routes
    Route::get('/career-coach', [CareerCoachController::class, 'index'])->name('career-coach');
    Route::post('/career-coach/roadmap', [CareerCoachController::class, 'roadmap'])->name('career-coach.roadmap');

    // Admin Panel route
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
});
