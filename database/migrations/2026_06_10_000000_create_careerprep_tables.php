<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Courses Table
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // Programming Languages, Frontend, Backend, Databases, etc.
            $table->text('description');
            $table->string('level'); // Beginner, Intermediate, Advanced
            $table->text('lessons')->nullable(); // JSON list of lessons
            $table->timestamps();
        });

        // Course Enrollments Table
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Quiz Attempts Table
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('topic');
            $table->string('difficulty');
            $table->integer('score');
            $table->integer('total_questions');
            $table->integer('correct_answers');
            $table->integer('time_spent'); // in seconds
            $table->timestamps();
        });

        // Interview Sessions Table
        Schema::create('interview_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // HR, Technical
            $table->string('technology')->nullable(); // PHP, Laravel, React, etc.
            $table->string('difficulty'); // Fresher, Junior Developer, Mid-Level Developer, Senior Developer
            $table->string('language'); // en, hi, gu, hi_en, gu_en
            $table->integer('score')->default(0);
            $table->integer('communication_score')->default(0);
            $table->integer('confidence_score')->default(0);
            $table->string('status')->default('pending'); // pending, completed
            $table->timestamps();
        });

        // Interview Questions Table
        Schema::create('interview_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_session_id')->constrained('interview_sessions')->onDelete('cascade');
            $table->text('question_text');
            $table->text('user_answer')->nullable();
            $table->integer('ai_score')->nullable();
            $table->text('ai_feedback_positive')->nullable();
            $table->text('ai_feedback_missing')->nullable();
            $table->text('ai_feedback_suggestions')->nullable();
            $table->text('ai_improved_answer')->nullable();
            $table->text('grammar_feedback')->nullable();
            $table->text('voice_analysis')->nullable();
            $table->text('camera_analysis')->nullable();
            $table->timestamps();
        });

        // Resumes Table
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->integer('ats_score')->default(0);
            $table->text('extracted_skills')->nullable(); // comma-separated or JSON
            $table->text('missing_skills')->nullable();
            $table->text('suggestions')->nullable();
            $table->timestamps();
        });

        // Coding Problems Table
        Schema::create('coding_problems', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('difficulty'); // Easy, Medium, Hard
            $table->string('company_tags')->nullable(); // Google, Amazon, etc.
            $table->text('starter_code_php')->nullable();
            $table->text('starter_code_js')->nullable();
            $table->text('starter_code_python')->nullable();
            $table->text('test_cases')->nullable(); // JSON input-output pairs
            $table->timestamps();
        });

        // Coding Submissions Table
        Schema::create('coding_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('problem_id');
            $table->string('language');
            $table->text('code');
            $table->string('status')->default('Failed');
            $table->string('runtime')->nullable();
            $table->string('memory')->nullable();
            $table->timestamps();
        });

        // User Badges Table
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('badge_name');
            $table->string('badge_icon');
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('coding_submissions');
        Schema::dropIfExists('coding_problems');
        Schema::dropIfExists('resumes');
        Schema::dropIfExists('interview_questions');
        Schema::dropIfExists('interview_sessions');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('courses');
    }
};
