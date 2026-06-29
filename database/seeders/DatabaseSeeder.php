<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserBadge;
use App\Models\InterviewSession;
use App\Models\InterviewQuestion;
use App\Models\QuizAttempt;
use App\Models\CodingSubmission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::create([
            'name' => 'Admin Dipen',
            'email' => 'admin@careerprep.com',
            'mobile' => '9876543210',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'user_type' => 'experienced',
            'career_path' => 'Backend Developer',
            'language_preference' => 'en',
            'streak' => 12,
            'xp_points' => 1850,
            'skills' => ['PHP', 'Laravel', 'AWS', 'Docker'],
        ]);

        // 2. Create Student User
        $student = User::create([
            'name' => 'Dipen Patel',
            'email' => 'dipen@careerprep.com',
            'mobile' => '9988776655',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'user_type' => 'fresher',
            'career_path' => 'Backend Developer',
            'language_preference' => 'gu',
            'streak' => 4,
            'xp_points' => 450,
            'skills' => ['PHP', 'Laravel', 'MySQL', 'HTML', 'CSS', 'JavaScript'],
        ]);

        // Create resume with project for student
        $student->resumes()->create([
            'file_path' => 'resumes/mock_resume.pdf',
            'ats_score' => 85,
            'extracted_skills' => 'PHP, Laravel, MySQL, HTML, CSS, JavaScript',
            'missing_skills' => 'Docker, AWS',
            'suggestions' => "Include unit testing experiences.\nAdd cloud deployment projects.",
            'projects' => json_encode([
                "Car Rental Website - A full-stack web application built with PHP and Laravel featuring vehicle browsing, booking calendars, and payment integrations."
            ]),
        ]);

        // 3. Create and award User Badges
        $student->badges()->create([
            'badge_name' => 'First Blood',
            'badge_icon' => '🏆',
            'description' => 'Completed your very first AI mock interview session!',
        ]);

        $student->badges()->create([
            'badge_name' => 'ATS Ready',
            'badge_icon' => '📄',
            'description' => 'Uploaded and scanned your first resume for ATS scores!',
        ]);

        // 4. Create past interview session
        $session = InterviewSession::create([
            'user_id' => $student->id,
            'type' => 'Technical',
            'technology' => 'Laravel',
            'difficulty' => 'Fresher',
            'language' => 'gu',
            'status' => 'completed',
            'score' => 78,
            'communication_score' => 82,
            'confidence_score' => 85,
        ]);

        // Seed 1 question for it
        $session->questions()->create([
            'question_text' => 'What is Laravel Eloquent ORM?',
            'user_answer' => 'Eloquent ORM is Laravels ActiveRecord implementation for working with databases easily.',
            'ai_score' => 78,
            'ai_feedback_positive' => "Clearly defined ORM and Laravel association.\nAccurate definition.",
            'ai_feedback_missing' => 'Did not mention relationships or database models.',
            'ai_feedback_suggestions' => 'Add details about model relationships.',
            'ai_improved_answer' => 'Eloquent ORM is a powerful ActiveRecord ORM that allows you to interact with databases using PHP models.',
            'grammar_feedback' => 'Good structured sentence.',
            'voice_analysis' => 'Speed: 130 wpm. Clarity: 88%.',
            'camera_analysis' => 'Good focus. Alert posture.',
        ]);

        // 5. Seed past Quiz Attempt
        QuizAttempt::create([
            'user_id' => $student->id,
            'topic' => 'Laravel',
            'difficulty' => 'Beginner',
            'score' => 30,
            'total_questions' => 3,
            'correct_answers' => 3,
            'time_spent' => 45,
        ]);

        // 6. Seed past Code Submission
        CodingSubmission::create([
            'user_id' => $student->id,
            'problem_id' => 'two-sum',
            'language' => 'php',
            'code' => 'function twoSum($nums, $target) { ... }',
            'status' => 'Accepted',
            'runtime' => '45ms',
            'memory' => '12.4MB',
        ]);
    }
}
