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
        // 1. Add career_path to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('career_path')->nullable()->after('user_type');
        });

        // 2. Add projects to resumes table
        Schema::table('resumes', function (Blueprint $table) {
            $table->text('projects')->nullable()->after('suggestions');
        });

        // 3. Add strong_areas, weak_areas, and recommended_topics to interview_sessions table
        Schema::table('interview_sessions', function (Blueprint $table) {
            $table->text('strong_areas')->nullable()->after('status');
            $table->text('weak_areas')->nullable()->after('strong_areas');
            $table->text('recommended_topics')->nullable()->after('weak_areas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('career_path');
        });

        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn('projects');
        });

        Schema::table('interview_sessions', function (Blueprint $table) {
            $table->dropColumn(['strong_areas', 'weak_areas', 'recommended_topics']);
        });
    }
};
