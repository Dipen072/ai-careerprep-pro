@extends('layouts.app')

@section('page_title')
    {{ __('messages.dashboard') }}
@endsection

@section('content')
<div class="space-y-8">
    
    <!-- Welcome banner with dynamic Hindi/Gujarati greeting based on locale -->
    <div class="glassmorphism rounded-3xl p-6 relative overflow-hidden flex items-center justify-between border border-white/10">
        <div class="relative z-10 space-y-2">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white">
                {{ __('messages.welcome') }}, {{ $user->name }}! 👋
            </h2>
            <p class="text-gray-300 text-sm max-w-xl">
                @if(App::getLocale() === 'gu')
                    તમારી ઇન્ટરવ્યુ અને કરિયર તૈયારીને ઝડપી બનાવવા માટે AI ટૂલ્સનો ઉપયોગ કરો.
                @elseif(App::getLocale() === 'hi')
                    अपने इंटरव्यू और करियर की तैयारी को बेहतर बनाने के लिए एआई टूल्स का उपयोग करें।
                @else
                    Accelerate your career preparation with our unified AI tools tailored for tech interviews, aptitude rounds, and ATS resume scans.
                @endif
            </p>
        </div>
        <div class="hidden md:block w-36 h-36 relative shrink-0">
            <div class="absolute inset-0 bg-brandPurple/30 blur-2xl rounded-full"></div>
            <span class="absolute inset-0 flex items-center justify-center text-7xl select-none animate-bounce">🚀</span>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        <!-- ATS -->
        <div class="glassmorphism rounded-2xl p-5 border border-white/10 hover:border-brandCyan/40 transition-colors flex flex-col justify-between">
            <div class="flex items-center justify-between text-brandCyan">
                <span class="text-xs font-semibold tracking-wider uppercase opacity-80">{{ __('messages.ats_score') }}</span>
                <i class="fa-regular fa-file-lines text-lg"></i>
            </div>
            <div class="mt-4 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-white">{{ $atsScore }}</span>
                <span class="text-xs text-gray-400 font-semibold">/100</span>
            </div>
        </div>

        <!-- Interview -->
        <div class="glassmorphism rounded-2xl p-5 border border-white/10 hover:border-brandPurple/40 transition-colors flex flex-col justify-between">
            <div class="flex items-center justify-between text-brandPurple">
                <span class="text-xs font-semibold tracking-wider uppercase opacity-80">{{ __('messages.interview_score') }}</span>
                <i class="fa-solid fa-microphone-lines text-lg"></i>
            </div>
            <div class="mt-4 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-white">{{ $interviewScore }}</span>
                <span class="text-xs text-gray-400 font-semibold">/100</span>
            </div>
        </div>

        <!-- Quiz -->
        <div class="glassmorphism rounded-2xl p-5 border border-white/10 hover:border-emerald-500/40 transition-colors flex flex-col justify-between">
            <div class="flex items-center justify-between text-emerald-400">
                <span class="text-xs font-semibold tracking-wider uppercase opacity-80">{{ __('messages.quiz_score') }}</span>
                <i class="fa-solid fa-list-check text-lg"></i>
            </div>
            <div class="mt-4 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-white">{{ $quizScore }}</span>
                <span class="text-xs text-gray-400 font-semibold">/100</span>
            </div>
        </div>

        <!-- Coding -->
        <div class="glassmorphism rounded-2xl p-5 border border-white/10 hover:border-amber-500/40 transition-colors flex flex-col justify-between">
            <div class="flex items-center justify-between text-amber-400">
                <span class="text-xs font-semibold tracking-wider uppercase opacity-80">{{ __('messages.coding_score') }}</span>
                <i class="fa-solid fa-code text-lg"></i>
            </div>
            <div class="mt-4 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-white">{{ $codingScore }}</span>
                <span class="text-xs text-gray-400 font-semibold">/100</span>
            </div>
        </div>

        <!-- Communication -->
        <div class="glassmorphism rounded-2xl p-5 border border-white/10 hover:border-pink-500/40 transition-colors flex flex-col justify-between">
            <div class="flex items-center justify-between text-pink-400">
                <span class="text-xs font-semibold tracking-wider uppercase opacity-80">{{ __('messages.comm_score') }}</span>
                <i class="fa-regular fa-comment-dots text-lg"></i>
            </div>
            <div class="mt-4 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-white">{{ $communicationScore }}</span>
                <span class="text-xs text-gray-400 font-semibold">/100</span>
            </div>
        </div>

        <!-- Confidence -->
        <div class="glassmorphism rounded-2xl p-5 border border-white/10 hover:border-teal-400/40 transition-colors flex flex-col justify-between">
            <div class="flex items-center justify-between text-teal-400">
                <span class="text-xs font-semibold tracking-wider uppercase opacity-80">{{ __('messages.conf_score') }}</span>
                <i class="fa-regular fa-face-smile text-lg"></i>
            </div>
            <div class="mt-4 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-white">{{ $confidenceScore }}</span>
                <span class="text-xs text-gray-400 font-semibold">/100</span>
            </div>
        </div>
    </div>

    <!-- Middle Grid: Weekly progress and Recommended Skills -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Weekly progress Chart.js -->
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 lg:col-span-2">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-simple text-brandCyan"></i>
                {{ __('messages.weekly_progress') }}
            </h3>
            <div class="h-64 relative w-full">
                <canvas id="progressChart"></canvas>
            </div>
        </div>

        <!-- Recommended Skills & Roadmaps -->
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-brandPurple"></i>
                    {{ __('messages.skills_recommended') }}
                </h3>
                <p class="text-xs text-gray-400 mb-4">
                    Based on your profile as an aspiring <span class="text-brandCyan font-semibold">{{ $role }}</span>:
                </p>
                <div class="flex flex-wrap gap-2.5">
                    @foreach($recommendedSkills as $skill)
                    <span class="px-3 py-1.5 bg-white/5 border border-white/10 rounded-xl text-xs font-semibold text-gray-200">
                        {{ $skill }}
                    </span>
                    @endforeach
                </div>
            </div>
            <div class="mt-6">
                <a href="{{ route('career-coach') }}" class="w-full flex items-center justify-center gap-2 py-3 bg-brandPurple/20 hover:bg-brandPurple/30 border border-brandPurple/30 text-brandPurple rounded-xl text-sm font-semibold transition-all">
                    <span>View Learning Roadmap</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Action 1 -->
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 flex flex-col justify-between hover:border-brandPurple/30 transition-all group">
            <div class="space-y-3">
                <div class="w-12 h-12 bg-brandPurple/10 text-brandPurple rounded-2xl flex items-center justify-center border border-brandPurple/20 text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <h4 class="text-lg font-bold">{{ __('messages.start_new_interview') }}</h4>
                <p class="text-sm text-gray-400">Practice behavioral or custom technical interview questions in EN, Hindi, or Gujarati.</p>
            </div>
            <a href="{{ route('interviews.setup') }}" class="mt-6 py-2.5 px-4 bg-brandPurple/25 hover:bg-brandPurple/40 border border-brandPurple/40 text-brandPurple font-semibold rounded-xl text-sm transition-all flex items-center justify-center gap-2">
                Launch Arena <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <!-- Action 2 -->
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 flex flex-col justify-between hover:border-brandCyan/30 transition-all group">
            <div class="space-y-3">
                <div class="w-12 h-12 bg-brandCyan/10 text-brandCyan rounded-2xl flex items-center justify-center border border-brandCyan/20 text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-regular fa-file-pdf"></i>
                </div>
                <h4 class="text-lg font-bold">{{ __('messages.upload_resume') }}</h4>
                <p class="text-sm text-gray-400">Get instant AI skill matching, ATS compatibility score and key upgrade recommendations.</p>
            </div>
            <a href="{{ route('resume') }}" class="mt-6 py-2.5 px-4 bg-brandCyan/25 hover:bg-brandCyan/40 border border-brandCyan/40 text-brandCyan font-semibold rounded-xl text-sm transition-all flex items-center justify-center gap-2">
                Open Analyzer <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <!-- Action 3 -->
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 flex flex-col justify-between hover:border-emerald-500/30 transition-all group">
            <div class="space-y-3">
                <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center border border-emerald-500/20 text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h4 class="text-lg font-bold">{{ __('messages.take_quiz') }}</h4>
                <p class="text-sm text-gray-400">Attempt random topic-specific multiple choice questions to boost your IT foundation scores.</p>
            </div>
            <a href="{{ route('quiz') }}" class="mt-6 py-2.5 px-4 bg-emerald-500/25 hover:bg-emerald-500/40 border border-emerald-500/40 text-emerald-400 font-semibold rounded-xl text-sm transition-all flex items-center justify-center gap-2">
                Start Quiz <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>

    <!-- Badges Row -->
    <div class="glassmorphism rounded-3xl p-6 border border-white/10">
        <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
            <i class="fa-solid fa-award text-yellow-500"></i>
            {{ __('messages.badges_earned') }}
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            @forelse($badges as $badge)
            <div class="flex flex-col items-center justify-center p-4 bg-white/5 border border-white/10 rounded-2xl text-center">
                <span class="text-3xl mb-2">{{ $badge->badge_icon }}</span>
                <h5 class="text-xs font-bold text-white truncate max-w-full">{{ $badge->badge_name }}</h5>
                <p class="text-[10px] text-gray-400 mt-0.5 line-clamp-2">{{ $badge->description }}</p>
            </div>
            @empty
            <div class="col-span-full py-4 text-center text-gray-400 text-sm">
                No badges earned yet. Complete interviews and quizzes to earn badges!
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js CDN for fast plotting -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($weeklyProgress['labels']),
            datasets: [{
                label: 'XP Progression',
                data: @json($weeklyProgress['data']),
                borderColor: '#06b6d4',
                backgroundColor: 'rgba(6, 182, 212, 0.15)',
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#a855f7',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: {
                            family: 'Outfit'
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: {
                            family: 'Outfit'
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
