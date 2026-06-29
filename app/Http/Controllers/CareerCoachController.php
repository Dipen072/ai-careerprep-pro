<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Auth;

class CareerCoachController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function index()
    {
        $roles = [
            'Fullstack PHP Developer',
            'React Frontend Developer',
            'MERN Stack Developer',
            'Backend Developer',
            'Python Data Analyst',
            'DevOps Cloud Engineer',
            'QA Automation Engineer',
            'Cyber Security Specialist'
        ];

        return view('coach.index', compact('roles'));
    }

    public function roadmap(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $role = $request->role;
        $user = Auth::user();

        // Get user's current skills
        $skills = $user->skills;
        if (empty($skills)) {
            $skills = ['HTML', 'CSS', 'JavaScript'];
        }

        // Call Gemini Service
        $roadmapData = $this->gemini->generateRoadmap($role, $skills, $user->user_type ?? 'fresher');

        // Earn XP
        $user->increment('xp_points', 30);

        // Earn Career Navigator Badge
        $user->badges()->firstOrCreate([
            'badge_name' => 'Navigator',
            'badge_icon' => '🧭',
            'description' => "Generated a customized career roadmap for {$role}!",
        ]);

        $timeline = $roadmapData['timeline'] ?? [];
        $skillGap = $roadmapData['skill_gap'] ?? [];

        return view('coach.roadmap', compact('role', 'timeline', 'skillGap'));
    }
}
