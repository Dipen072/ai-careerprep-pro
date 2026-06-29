@extends('layouts.app')

@section('page_title', 'AI Resume Intelligence')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- LEFT COLUMN: Upload Box -->
    <div class="glassmorphism rounded-3xl p-6 border border-white/10 h-fit space-y-6">
        <div>
            <h3 class="text-lg font-bold text-white">Upload Resume</h3>
            <p class="text-xs text-gray-400 mt-0.5">Scans skills and counts ATS keyword compatibility</p>
        </div>

        <form action="{{ route('resume.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <!-- Drag and Drop Styled Area -->
            <label class="border-2 border-dashed border-white/10 hover:border-brandCyan/40 bg-white/5 rounded-2xl p-8 flex flex-col items-center justify-center cursor-pointer transition-colors text-center group">
                <input type="file" name="resume" required class="sr-only" onchange="displayFileName(this)">
                <div class="w-12 h-12 rounded-full bg-brandCyan/10 text-brandCyan flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                </div>
                <span class="text-sm font-semibold text-white" id="file-label">Choose PDF or DOCX file</span>
                <span class="text-xs text-gray-400 mt-1">Maximum size: 2MB</span>
            </label>

            <button type="submit" class="w-full py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-xl font-bold text-white shadow-lg transition-opacity flex justify-center items-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Scan & Parse Resume
            </button>
        </form>

        @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-200 text-xs rounded-xl p-3 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif
    </div>

    <!-- RIGHT COLUMN: Analysis Dashboard -->
    <div class="lg:col-span-2 space-y-6">
        @if($latestResume)
        @php
            $analysis = $latestResume->full_analysis ?? [];
            $score = $analysis['✅ ATS Score (0-100)'] ?? $latestResume->ats_score ?? 75;
            $reason = $analysis['Reason for Score'] ?? 'Resume successfully scanned against criteria.';
            $missingKeywords = $analysis['Missing Keywords'] ?? (trim($latestResume->missing_skills) ? explode(', ', $latestResume->missing_skills) : []);
            $matchedKeywords = $analysis['Matched Keywords'] ?? (trim($latestResume->extracted_skills) ? explode(', ', $latestResume->extracted_skills) : []);
            $improvements = $analysis['Top 10 Improvements'] ?? (trim($latestResume->suggestions) ? explode("\n", $latestResume->suggestions) : []);
            $summary = $analysis['Resume Summary'] ?? 'Candidate has foundational technical knowledge.';
            $strengths = $analysis['Strengths'] ?? ['Strong core concepts', 'Relevant project experience'];
            $weaknesses = $analysis['Weaknesses'] ?? ['Needs more quantified achievements', 'Lack of advanced testing frameworks'];
            $sectionsMissing = $analysis['Resume Sections Missing'] ?? ['None'];
            $recommendedCertifications = $analysis['Recommended Certifications'] ?? ['Laravel Certified Developer', 'AWS Cloud Practitioner'];
            $recommendedSkills = $analysis['Recommended Skills'] ?? ['PHPUnit / Pest Testing', 'Docker Containerization', 'Redis Caching'];
            $recommendedProjects = $analysis['Recommended Projects'] ?? ['Full-stack E-commerce API with Sanctum', 'Real-time WebSocket notifications'];
            $decision = $analysis['Final Hiring Decision'] ?? 'Shortlist for Technical Assessment';

            // New structured variables
            $parsedInfo = $analysis['parsed_info'] ?? [];
            $parsedName = $parsedInfo['name'] ?? Auth::user()->name;
            $parsedEmail = $parsedInfo['email'] ?? Auth::user()->email;
            $parsedPhone = $parsedInfo['phone'] ?? '+91 XXXXX XXXXX';
            $parsedGithub = $parsedInfo['github'] ?? '#';
            $parsedLinkedin = $parsedInfo['linkedin'] ?? '#';
            
            $parsedEducation = $parsedInfo['education'] ?? [];
            $parsedExperience = $parsedInfo['experience'] ?? [];
            $parsedProjects = $parsedInfo['projects'] ?? [];
            $parsedCertifications = $parsedInfo['certifications'] ?? [];
            $parsedInternships = $parsedInfo['internships'] ?? [];
            $parsedSoftSkills = $parsedInfo['soft_skills'] ?? [];
            $parsedLanguages = $parsedInfo['languages'] ?? [];

            $detectedRole = $analysis['target_role_detection']['role'] ?? 'Backend Developer';
            $detectedReason = $analysis['target_role_detection']['reason'] ?? 'Detected based on technologies in the resume.';

            $readiness = $analysis['readiness_scores'] ?? [
                'ats_score' => $score,
                'technical_readiness' => $score - 5,
                'hr_readiness' => $score + 2,
                'communication_readiness' => 80,
                'coding_readiness' => $score - 8,
                'overall_readiness' => $score - 2
            ];

            $atsBreakdown = $analysis['ats_score_breakdown'] ?? [
                'structure_score' => 85,
                'keywords_score' => 80,
                'skills_score' => 75,
                'experience_score' => 80,
                'projects_score' => 85,
                'education_score' => 90,
                'formatting_score' => 80,
                'readability_score' => 85
            ];

            $missingSkillsAnalysis = $analysis['missing_skills_analysis'] ?? [];
            $projectAnalysis = $analysis['project_analysis'] ?? [];
            $learningRoadmap = $analysis['personalized_learning_roadmap'] ?? [];
            $interviewQuestions = $analysis['personalized_interview_questions'] ?? [];
        @endphp

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2 border-b border-white/10 pb-4 mb-6">
            <button class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all bg-gradient-to-r from-brandCyan to-brandPurple text-white shadow-lg" onclick="switchTab(event, 'dashboard-tab')">
                <i class="fa-solid fa-chart-pie mr-1.5"></i> ATS Dashboard
            </button>
            <button class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all text-gray-400 hover:text-white hover:bg-white/5 border border-transparent" onclick="switchTab(event, 'parsed-tab')">
                <i class="fa-solid fa-user-check mr-1.5"></i> Parsed CV Details
            </button>
            <button class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all text-gray-400 hover:text-white hover:bg-white/5 border border-transparent" onclick="switchTab(event, 'projects-tab')">
                <i class="fa-solid fa-cubes mr-1.5"></i> Project SWOT
            </button>
            <button class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all text-gray-400 hover:text-white hover:bg-white/5 border border-transparent" onclick="switchTab(event, 'roadmap-tab')">
                <i class="fa-solid fa-route mr-1.5"></i> Learning Roadmap
            </button>
            <button class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all text-gray-400 hover:text-white hover:bg-white/5 border border-transparent" onclick="switchTab(event, 'questions-tab')">
                <i class="fa-solid fa-comments mr-1.5"></i> Interview Questions
            </button>
        </div>

        <!-- TAB 1: Dashboard -->
        <div id="dashboard-tab" class="tab-content space-y-6">
            
            <!-- Target Role Banner -->
            <div class="relative bg-gradient-to-tr from-brandCyan/20 to-brandPurple/20 border border-white/10 rounded-3xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 overflow-hidden">
                <div class="relative z-10 space-y-1">
                    <span class="text-[10px] bg-brandCyan/20 text-brandCyan px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Detected Target Profile</span>
                    <h3 class="text-2xl font-black text-white tracking-tight">{{ $detectedRole }}</h3>
                    <p class="text-xs text-gray-300 leading-relaxed max-w-xl">{{ $detectedReason }}</p>
                </div>
                <div class="relative z-10 shrink-0">
                    <span class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-3xl shadow-inner">
                        🎯
                    </span>
                </div>
            </div>

            <!-- Readiness Scores Grid -->
            <div class="glassmorphism rounded-3xl p-6 border border-white/10">
                <h4 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-gauge-high text-brandCyan"></i> AI Interview Readiness Breakdown
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    @php
                        $metrics = [
                            ['label' => 'ATS Score', 'value' => $readiness['ats_score'] ?? $score, 'icon' => 'fa-file-invoice', 'color' => 'from-brandCyan to-blue-500'],
                            ['label' => 'Technical', 'value' => $readiness['technical_readiness'] ?? 70, 'icon' => 'fa-laptop-code', 'color' => 'from-indigo-500 to-brandPurple'],
                            ['label' => 'HR Fit', 'value' => $readiness['hr_readiness'] ?? 75, 'icon' => 'fa-users', 'color' => 'from-pink-500 to-rose-500'],
                            ['label' => 'Communication', 'value' => $readiness['communication_readiness'] ?? 80, 'icon' => 'fa-comments', 'color' => 'from-emerald-500 to-teal-500'],
                            ['label' => 'Coding Lab', 'value' => $readiness['coding_readiness'] ?? 65, 'icon' => 'fa-terminal', 'color' => 'from-amber-500 to-orange-500'],
                            ['label' => 'Overall Prep', 'value' => $readiness['overall_readiness'] ?? 72, 'icon' => 'fa-circle-check', 'color' => 'from-brandCyan to-brandPurple']
                        ];
                    @endphp
                    @foreach($metrics as $metric)
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center space-y-3 hover:scale-[1.03] transition-transform duration-300">
                        <div class="mx-auto w-8 h-8 rounded-xl bg-white/5 flex items-center justify-center text-xs text-gray-300">
                            <i class="fa-solid {{ $metric['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider leading-none mb-1">{{ $metric['label'] }}</div>
                            <div class="text-lg font-black bg-gradient-to-r {{ $metric['color'] }} bg-clip-text text-transparent">{{ $metric['value'] }}%</div>
                        </div>
                        <!-- Micro progress bar -->
                        <div class="w-full bg-white/10 h-1 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r {{ $metric['color'] }} h-full" style="width: {{ $metric['value'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Hiring Decision Callout -->
            @php
                $decisionLower = strtolower($decision);
                $isShortlist = strpos($decisionLower, 'shortlist') !== false || strpos($decisionLower, 'hire') !== false;
                $isReject = strpos($decisionLower, 'reject') !== false || strpos($decisionLower, 'no hire') !== false;
                $calloutClass = $isShortlist ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-300' : ($isReject ? 'bg-rose-500/10 border-rose-500/20 text-rose-300' : 'bg-amber-500/10 border-amber-500/20 text-amber-300');
                $calloutIcon = $isShortlist ? 'fa-circle-check text-emerald-400' : ($isReject ? 'fa-circle-xmark text-rose-400' : 'fa-circle-exclamation text-amber-400');
            @endphp
            <div class="rounded-2xl p-4 border {{ $calloutClass }} space-y-2.5 transition-all">
                <div class="flex items-center gap-2.5 font-extrabold text-sm">
                    <i class="fa-solid {{ $calloutIcon }} text-lg"></i>
                    <span>Final Screening Status: {{ $decision }}</span>
                </div>
            </div>

            <!-- Profile Summary & Reason for Score -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="glassmorphism rounded-3xl p-5 border border-white/10 space-y-2">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-user text-brandCyan"></i> Profile Executive Summary
                    </h4>
                    <p class="text-xs text-gray-200 leading-relaxed bg-white/5 border border-white/5 rounded-2xl p-4 min-h-[100px]">
                        {{ $summary }}
                    </p>
                </div>
                <div class="glassmorphism rounded-3xl p-5 border border-white/10 space-y-2">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-calculator text-brandCyan"></i> ATS Evaluation Logic
                    </h4>
                    <p class="text-xs text-gray-200 leading-relaxed bg-white/5 border border-white/5 rounded-2xl p-4 min-h-[100px]">
                        {{ $reason }}
                    </p>
                </div>
            </div>

            <!-- Keyword Compatibility (Matched vs Missing) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Matched Keywords -->
                <div class="glassmorphism rounded-3xl p-5 border border-white/10 space-y-3.5">
                    <h4 class="text-sm font-bold text-emerald-400 flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i> Matched Technical Skills
                    </h4>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($matchedKeywords as $keyword)
                            @if(trim($keyword))
                            <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-300 rounded-lg text-xs font-semibold border border-emerald-500/20 shadow-sm">
                                {{ $keyword }}
                            </span>
                            @endif
                        @empty
                            <span class="text-xs text-gray-400 italic">No skills matched yet.</span>
                        @endforelse
                    </div>
                </div>

                <!-- Missing Keywords -->
                <div class="glassmorphism rounded-3xl p-5 border border-white/10 space-y-3.5">
                    <h4 class="text-sm font-bold text-amber-400 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i> Missing Keywords / Gaps
                    </h4>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($missingKeywords as $keyword)
                            @if(trim($keyword))
                            <span class="px-2.5 py-1 bg-amber-500/10 text-amber-300 rounded-lg text-xs font-semibold border border-amber-500/20 shadow-sm">
                                {{ $keyword }}
                            </span>
                            @endif
                        @empty
                            <span class="text-xs text-emerald-400 italic">None! Excellent keyword coverage.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- SWOT Analysis Grid (Strengths vs Weaknesses) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Strengths -->
                <div class="glassmorphism rounded-3xl p-5 border border-white/10 space-y-3.5">
                    <h4 class="text-sm font-bold text-emerald-400 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved"></i> Strengths
                    </h4>
                    <ul class="text-xs text-gray-300 space-y-2 list-disc pl-4 leading-relaxed">
                        @foreach($strengths as $strength)
                            @if(trim($strength)) <li>{{ $strength }}</li> @endif
                        @endforeach
                    </ul>
                </div>

                <!-- Weaknesses -->
                <div class="glassmorphism rounded-3xl p-5 border border-white/10 space-y-3.5">
                    <h4 class="text-sm font-bold text-rose-400 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i> Weaknesses
                    </h4>
                    <ul class="text-xs text-gray-300 space-y-2 list-disc pl-4 leading-relaxed">
                        @foreach($weaknesses as $weakness)
                            @if(trim($weakness)) <li>{{ $weakness }}</li> @endif
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Action Plan: Top Improvements -->
            <div class="glassmorphism rounded-3xl p-6 border border-white/10 space-y-4">
                <div class="flex justify-between items-center border-b border-white/5 pb-3">
                    <h4 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-brandCyan"></i> Top Improvements Required
                    </h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-xs text-gray-300">
                    @foreach($improvements as $index => $imp)
                        @if(trim($imp))
                        <div class="flex items-start gap-3 bg-white/5 border border-white/5 hover:border-white/10 p-3 rounded-2xl transition-all">
                            <span class="w-5 h-5 rounded-full bg-brandCyan/10 text-brandCyan flex items-center justify-center font-bold text-[10px] shrink-0 border border-brandCyan/20">
                                {{ $index + 1 }}
                            </span>
                            <p class="leading-relaxed">{{ $imp }}</p>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>

        <!-- TAB 2: Parsed CV Details -->
        <div id="parsed-tab" class="tab-content hidden space-y-6">
            <div class="glassmorphism rounded-3xl p-6 border border-white/10 space-y-6">
                <!-- Header / Contact Card -->
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 border-b border-white/10 pb-6">
                    <div class="space-y-1">
                        <h3 class="text-2xl font-black text-white">{{ $parsedName }}</h3>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-400">
                            <span><i class="fa-solid fa-envelope mr-1.5 text-brandCyan"></i>{{ $parsedEmail }}</span>
                            <span><i class="fa-solid fa-phone mr-1.5 text-brandCyan"></i>{{ $parsedPhone }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        @if($parsedGithub && $parsedGithub !== '#')
                        <a href="{{ $parsedGithub }}" target="_blank" class="px-3 py-1.5 bg-white/5 border border-white/10 hover:bg-white/10 rounded-xl text-xs text-white font-semibold flex items-center gap-1.5 transition-colors">
                            <i class="fa-brands fa-github text-sm"></i> GitHub
                        </a>
                        @endif
                        @if($parsedLinkedin && $parsedLinkedin !== '#')
                        <a href="{{ $parsedLinkedin }}" target="_blank" class="px-3 py-1.5 bg-[#0a66c2]/10 border border-[#0a66c2]/20 hover:bg-[#0a66c2]/20 rounded-xl text-xs text-white font-semibold flex items-center gap-1.5 transition-colors">
                            <i class="fa-brands fa-linkedin-in text-sm text-[#0a66c2]"></i> LinkedIn
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Education & Experience Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Work Experience Timeline -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-briefcase text-brandCyan"></i> Work Experience Timeline
                        </h4>
                        @if(empty($parsedExperience))
                            <p class="text-xs text-gray-400 italic bg-white/5 border border-white/5 rounded-2xl p-4">No experience entries found.</p>
                        @else
                            <div class="border-l-2 border-white/10 pl-4 space-y-6">
                                @foreach($parsedExperience as $exp)
                                <div class="relative">
                                    <!-- Timeline Dot -->
                                    <span class="absolute w-3 h-3 bg-brandCyan border border-darkBg rounded-full -left-[22px] top-1"></span>
                                    <div class="space-y-1">
                                        <div class="text-xs font-bold text-white">{{ $exp['role'] ?? 'Role' }}</div>
                                        <div class="text-[10px] text-brandCyan font-semibold">{{ $exp['company'] ?? 'Company' }} | {{ $exp['duration'] ?? 'Duration' }}</div>
                                        <p class="text-[11px] text-gray-300 leading-relaxed">{{ $exp['responsibilities'] ?? '' }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Education Details -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-graduation-cap text-brandPurple"></i> Academic History
                        </h4>
                        @if(empty($parsedEducation))
                            <p class="text-xs text-gray-400 italic bg-white/5 border border-white/5 rounded-2xl p-4">No education entries found.</p>
                        @else
                            <div class="border-l-2 border-white/10 pl-4 space-y-6">
                                @foreach($parsedEducation as $edu)
                                <div class="relative">
                                    <!-- Timeline Dot -->
                                    <span class="absolute w-3 h-3 bg-brandPurple border border-darkBg rounded-full -left-[22px] top-1"></span>
                                    <div class="space-y-0.5">
                                        <div class="text-xs font-bold text-white">{{ $edu['degree'] ?? 'Degree' }}</div>
                                        <div class="text-[10px] text-brandPurple font-semibold">{{ $edu['institution'] ?? 'Institution' }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $edu['year'] ?? 'Year' }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Internships / Certifications -->
                        @if(!empty($parsedInternships) || !empty($parsedCertifications))
                        <div class="pt-4 space-y-4 border-t border-white/10">
                            @if(!empty($parsedInternships))
                            <div class="space-y-2">
                                <h5 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Internships</h5>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($parsedInternships as $internship)
                                    <span class="px-2 py-0.5 bg-white/5 border border-white/10 rounded text-[10px] text-gray-300 font-semibold">{{ $internship }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(!empty($parsedCertifications))
                            <div class="space-y-2">
                                <h5 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Certifications</h5>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($parsedCertifications as $cert)
                                    <span class="px-2 py-0.5 bg-white/5 border border-white/10 rounded text-[10px] text-gray-300 font-semibold">{{ $cert }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Languages & Soft Skills section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-white/10 pt-6">
                    <!-- Soft Skills -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Soft Skills</h4>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($parsedSoftSkills as $ss)
                            <span class="px-2.5 py-1 bg-white/5 border border-white/10 rounded-xl text-xs text-gray-200 font-semibold">{{ $ss }}</span>
                            @empty
                            <span class="text-xs text-gray-400 italic">No soft skills parsed.</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Languages -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Languages</h4>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($parsedLanguages as $lang)
                            <span class="px-2.5 py-1 bg-white/5 border border-white/10 rounded-xl text-xs text-gray-200 font-semibold">{{ $lang }}</span>
                            @empty
                            <span class="text-xs text-gray-400 italic">No languages parsed.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: Project SWOT -->
        <div id="projects-tab" class="tab-content hidden space-y-6">
            
            <!-- Missing Skills Table explaining WHY they are important -->
            @if(!empty($missingSkillsAnalysis))
            <div class="glassmorphism rounded-3xl p-6 border border-white/10 space-y-4">
                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-amber-400"></i> Missing Skills & Industry Importance
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs text-gray-300">
                        <thead>
                            <tr class="border-b border-white/10 text-gray-400 uppercase text-[10px] tracking-wider">
                                <th class="py-2.5">Skill Name</th>
                                <th class="py-2.5">Why it is Critical</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($missingSkillsAnalysis as $ms)
                            <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                <td class="py-3 font-extrabold text-white pr-4">
                                    <span class="px-2 py-1 bg-amber-500/10 text-amber-300 rounded border border-amber-500/20">{{ $ms['skill'] }}</span>
                                </td>
                                <td class="py-3 leading-relaxed">{{ $ms['importance_explanation'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Project Analysis Cards -->
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-cubes text-brandCyan"></i> Resume Projects SWOT & Code Upgrades
                </h4>
                @if(empty($projectAnalysis))
                    <div class="glassmorphism rounded-3xl p-12 border border-white/10 text-center text-gray-400">
                        <span>📦</span>
                        <h4 class="font-bold mt-2 text-white">No Project Analysis Available</h4>
                        <p class="text-xs mt-1">Make sure you include structured project descriptions in your resume.</p>
                    </div>
                @else
                    @foreach($projectAnalysis as $proj)
                    <div class="glassmorphism rounded-3xl p-6 border border-white/10 space-y-4 hover:scale-[1.005] transition-transform duration-300">
                        <div class="flex justify-between items-center border-b border-white/5 pb-3">
                            <h4 class="text-base font-extrabold text-white flex items-center gap-2">
                                <i class="fa-solid fa-circle-nodes text-brandCyan"></i> {{ $proj['project_name'] }}
                            </h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Strengths -->
                            <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-2xl p-4 space-y-2">
                                <h5 class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check"></i> Strengths
                                </h5>
                                <p class="text-[11px] text-gray-300 leading-relaxed">{{ $proj['strengths'] }}</p>
                            </div>

                            <!-- Technical Improvements -->
                            <div class="bg-indigo-500/5 border border-indigo-500/10 rounded-2xl p-4 space-y-2">
                                <h5 class="text-xs font-bold text-brandCyan uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-pencil"></i> Refactoring Ideas
                                </h5>
                                <ul class="text-[11px] text-gray-300 space-y-1.5 list-none">
                                    @foreach($proj['improvements'] as $imp)
                                    <li class="flex items-start gap-1">
                                        <span class="text-brandCyan mt-0.5">•</span>
                                        <span>{{ $imp }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Suggested Missing Features -->
                            <div class="bg-amber-500/5 border border-amber-500/10 rounded-2xl p-4 space-y-2">
                                <h5 class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-plus"></i> Missing Features
                                </h5>
                                <ul class="text-[11px] text-gray-300 space-y-1.5 list-none">
                                    @foreach($proj['suggested_missing_features'] as $feat)
                                    <li class="flex items-start gap-1">
                                        <span class="text-amber-400 mt-0.5">•</span>
                                        <span>{{ $feat }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- TAB 4: Learning Roadmap -->
        <div id="roadmap-tab" class="tab-content hidden space-y-6">
            <div class="glassmorphism rounded-3xl p-6 border border-white/10 space-y-4">
                <div>
                    <h4 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-route text-brandPurple"></i> Targeted Learning Roadmap
                    </h4>
                    <p class="text-xs text-gray-400 mt-0.5">A month-by-month checklist to bridge your skill gaps for the target profile</p>
                </div>

                @if(empty($learningRoadmap))
                    <p class="text-xs text-gray-400 italic bg-white/5 border border-white/5 rounded-2xl p-4">No customized roadmap data available.</p>
                @else
                    <div class="space-y-6 pt-4">
                        @foreach($learningRoadmap as $index => $node)
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:border-brandPurple/30 transition-colors space-y-3">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-white/5 pb-2.5">
                                <div class="flex items-center gap-2.5">
                                    <span class="px-2.5 py-0.5 bg-brandPurple/20 text-brandPurple rounded-lg text-xs font-bold border border-brandPurple/20">{{ $node['period'] }}</span>
                                    <h5 class="text-sm font-bold text-white">{{ $node['topic'] }}</h5>
                                </div>
                            </div>
                            <p class="text-xs text-gray-300 leading-relaxed">{{ $node['description'] }}</p>
                            
                            <!-- Action Items Checklist -->
                            @if(isset($node['key_actions']) && is_array($node['key_actions']))
                            <div class="space-y-1.5 pt-2">
                                <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Key Actions Checklist</div>
                                @foreach($node['key_actions'] as $actionIndex => $action)
                                <label class="flex items-start gap-2.5 cursor-pointer text-xs text-gray-300 hover:text-white select-none">
                                    <input type="checkbox" class="mt-0.5 rounded border-white/20 bg-white/5 text-brandPurple focus:ring-brandPurple focus:ring-offset-darkBg" onclick="saveChecklistProgress(this)">
                                    <span>{{ $action }}</span>
                                </label>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- TAB 5: Interview Questions -->
        <div id="questions-tab" class="tab-content hidden space-y-6">
            <div class="glassmorphism rounded-3xl p-6 border border-white/10 space-y-4">
                <div>
                    <h4 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-comments text-brandCyan"></i> Personalized Interview Questions
                    </h4>
                    <p class="text-xs text-gray-400 mt-0.5">5 custom questions mapped to your skills, experience and projects</p>
                </div>

                @if(empty($interviewQuestions))
                    <p class="text-xs text-gray-400 italic bg-white/5 border border-white/5 rounded-2xl p-4">No customized interview questions available.</p>
                @else
                    <div class="space-y-3 pt-2">
                        @foreach($interviewQuestions as $index => $q)
                        <div class="flex items-start gap-3 bg-white/5 border border-white/10 hover:border-white/20 p-4 rounded-2xl transition-all">
                            <span class="w-6 h-6 rounded-full bg-brandCyan/10 text-brandCyan flex items-center justify-center font-bold text-xs shrink-0 border border-brandCyan/20">
                                Q{{ $index + 1 }}
                            </span>
                            <div class="space-y-1.5">
                                <p class="text-xs text-white font-semibold leading-relaxed">{{ $q }}</p>
                                <a href="{{ route('interviews.setup') }}" class="inline-flex items-center text-[10px] text-brandCyan font-bold hover:underline gap-1">
                                    Practice answering <i class="fa-solid fa-chevron-right text-[8px]"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Action CTAs & Upload History -->
        <div class="flex flex-col gap-6 pt-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('interviews.setup') }}" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-brandPurple to-brandCyan hover:opacity-90 text-white font-extrabold rounded-xl text-center text-sm shadow-md transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rocket"></i> Interview Me on my Resume
                </a>
            </div>

            <!-- History of uploads -->
            <div class="glassmorphism rounded-3xl p-6 border border-white/10">
                <h4 class="font-bold text-sm text-white mb-3">Upload History</h4>
                <div class="space-y-2">
                    @foreach($resumes as $res)
                    <div class="flex justify-between items-center p-3 bg-white/5 border border-white/10 hover:border-white/20 rounded-xl transition-colors text-xs">
                        <span class="font-medium text-gray-200">Resume_v{{ $res->id }}.pdf</span>
                        <div class="flex items-center gap-4">
                            <span class="text-gray-400">{{ $res->created_at->format('M d, Y') }}</span>
                            <span class="font-bold text-brandCyan">{{ $res->ats_score }}% ATS</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        <div class="glassmorphism rounded-3xl p-12 border border-white/10 text-center text-gray-400">
            <span class="text-6xl">📄</span>
            <h3 class="text-lg font-bold text-white mt-4">No Resume Uploaded Yet</h3>
            <p class="text-sm mt-1 max-w-sm mx-auto">Upload your developer resume in PDF/DOCX format to calculate your score and check for keyword compatibility gaps.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function displayFileName(input) {
        const label = document.getElementById('file-label');
        if (input.files && input.files[0]) {
            label.innerText = input.files[0].name;
            label.classList.remove('text-white');
            label.classList.add('text-brandCyan');
        }
    }

    function switchTab(evt, tabId) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        // Show current tab content
        document.getElementById(tabId).classList.remove('hidden');
        // Remove active styles from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.className = 'tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all text-gray-400 hover:text-white hover:bg-white/5 border border-transparent';
        });
        // Add active styles to clicked button
        evt.currentTarget.className = 'tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all bg-gradient-to-r from-brandCyan to-brandPurple text-white shadow-lg';
    }

    function saveChecklistProgress(checkbox) {
        // Local persistence of checklist state or simple visual feedback
        if (checkbox.checked) {
            checkbox.nextElementSibling.classList.add('line-through', 'text-gray-500');
        } else {
            checkbox.nextElementSibling.classList.remove('line-through', 'text-gray-500');
        }
    }
</script>
@endsection
