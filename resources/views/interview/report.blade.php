@extends('layouts.app')

@section('page_title', 'AI Performance Report')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">
    <!-- Header Summary Card -->
    <div class="glassmorphism rounded-3xl p-6 border border-white/10 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-2 text-center md:text-left">
            <span class="px-3 py-1 bg-brandPurple/20 border border-brandPurple/30 text-brandPurple rounded-full text-xs font-bold uppercase">
                Mock Interview Report
            </span>
            <h2 class="text-2xl font-bold">Feedback Summary</h2>
            <p class="text-xs text-gray-400">
                Session Type: <span class="text-brandCyan font-semibold">{{ $session->type }}</span> 
                @if($session->technology) ({{ $session->technology }}) @endif · 
                Difficulty: {{ $session->difficulty }}
            </p>
        </div>

        <div class="flex gap-4">
            <!-- Avg Score Circle -->
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-brandCyan to-brandPurple p-0.5 shadow-lg shadow-brandPurple/10">
                    <div class="w-full h-full bg-darkBg rounded-full flex flex-col items-center justify-center">
                        <span class="text-2xl font-extrabold text-white">{{ $session->score }}</span>
                        <span class="text-[8px] text-gray-400 font-semibold tracking-wider uppercase">Score</span>
                    </div>
                </div>
            </div>

            <!-- Comm Score Circle -->
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-pink-500 to-rose-500 p-0.5 shadow-lg shadow-pink-900/10">
                    <div class="w-full h-full bg-darkBg rounded-full flex flex-col items-center justify-center">
                        <span class="text-2xl font-extrabold text-white">{{ $session->communication_score }}</span>
                        <span class="text-[8px] text-gray-400 font-semibold tracking-wider uppercase">Comm</span>
                    </div>
                </div>
            </div>

            <!-- Conf Score Circle -->
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-teal-400 to-emerald-500 p-0.5 shadow-lg shadow-teal-900/10">
                    <div class="w-full h-full bg-darkBg rounded-full flex flex-col items-center justify-center">
                        <span class="text-2xl font-extrabold text-white">{{ $session->confidence_score }}</span>
                        <span class="text-[8px] text-gray-400 font-semibold tracking-wider uppercase">Conf</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Interview Completion Report -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Strong Areas -->
        <div class="glassmorphism rounded-3xl p-6 border border-emerald-500/20 bg-emerald-500/5 space-y-3">
            <h4 class="text-base font-bold text-emerald-400 flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> Strong Areas
            </h4>
            @if(is_array($session->strong_areas) && count($session->strong_areas) > 0)
                <ul class="space-y-2">
                    @foreach($session->strong_areas as $area)
                        <li class="text-sm text-gray-200 flex items-start gap-2">
                            <span class="text-emerald-400 font-bold mt-0.5">•</span>
                            <span>{{ $area }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-xs text-gray-400">No specific strong areas identified yet.</p>
            @endif
        </div>

        <!-- Weak Areas -->
        <div class="glassmorphism rounded-3xl p-6 border border-rose-500/20 bg-rose-500/5 space-y-3">
            <h4 class="text-base font-bold text-rose-400 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> Weak Areas
            </h4>
            @if(is_array($session->weak_areas) && count($session->weak_areas) > 0)
                <ul class="space-y-2">
                    @foreach($session->weak_areas as $area)
                        <li class="text-sm text-gray-200 flex items-start gap-2">
                            <span class="text-rose-400 font-bold mt-0.5">•</span>
                            <span>{{ $area }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-xs text-gray-400">No specific weak areas identified yet.</p>
            @endif
        </div>

        <!-- Recommended Topics -->
        <div class="glassmorphism rounded-3xl p-6 border border-brandCyan/20 bg-brandCyan/5 space-y-3">
            <h4 class="text-base font-bold text-brandCyan flex items-center gap-2">
                <i class="fa-solid fa-lightbulb"></i> Recommended Topics
            </h4>
            @if(is_array($session->recommended_topics) && count($session->recommended_topics) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($session->recommended_topics as $topic)
                        <span class="px-2.5 py-1 bg-brandCyan/10 border border-brandCyan/20 text-brandCyan text-xs font-semibold rounded-lg">
                            {{ $topic }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400">No specific topics recommended yet.</p>
            @endif
        </div>
    </div>

    <!-- Question Wise Detailed Analysis -->
    <div class="space-y-6">
        <h3 class="text-lg font-bold">Detailed Question Analysis</h3>

        @foreach($questions as $index => $q)
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 space-y-4">
            <div class="flex justify-between items-start border-b border-white/5 pb-3">
                <h4 class="font-bold text-brandPurple text-sm">Question {{ $index + 1 }}</h4>
                <span class="px-2.5 py-1 bg-white/5 border border-white/10 text-brandCyan font-bold rounded-lg text-xs">
                    Score: {{ $q->ai_score }}/100
                </span>
            </div>

            <div class="space-y-3">
                <div>
                    <h5 class="text-xs font-semibold text-gray-400">Interviewer:</h5>
                    <p class="text-sm text-white font-medium italic mt-0.5">"{{ $q->question_text }}"</p>
                </div>

                <div>
                    <h5 class="text-xs font-semibold text-gray-400">Your Answer:</h5>
                    <p class="text-sm text-gray-200 mt-0.5">{{ $q->user_answer }}</p>
                </div>
            </div>

            <!-- Analysis breakdown grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-2xl p-4">
                    <h5 class="text-xs font-bold text-emerald-400 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-regular fa-circle-check"></i> Positive Points
                    </h5>
                    <ul class="text-xs text-gray-300 space-y-1 list-disc pl-4">
                        @foreach(explode("\n", $q->ai_feedback_positive) as $item)
                            @if(trim($item)) <li>{{ $item }}</li> @endif
                        @endforeach
                    </ul>
                </div>

                <div class="bg-red-500/5 border border-red-500/10 rounded-2xl p-4">
                    <h5 class="text-xs font-bold text-red-400 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-regular fa-circle-xmark"></i> Missing Points
                    </h5>
                    <ul class="text-xs text-gray-300 space-y-1 list-disc pl-4">
                        @foreach(explode("\n", $q->ai_feedback_missing) as $item)
                            @if(trim($item)) <li>{{ $item }}</li> @endif
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Suggestions -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                <h5 class="text-xs font-bold text-brandCyan mb-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-lightbulb"></i> Improvement Tips</span>
                    <button onclick="speakReportSuggestions({{ $q->id }})" class="text-xs text-brandCyan hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-volume-high text-[10px]"></i> Listen Tips
                    </button>
                </h5>
                <ul class="text-xs text-gray-300 space-y-1 list-disc pl-4" id="sug-list-{{ $q->id }}">
                    @foreach(explode("\n", $q->ai_feedback_suggestions) as $item)
                        @if(trim($item)) <li>{{ $item }}</li> @endif
                    @endforeach
                </ul>
            </div>

            <!-- Grammar, voice & camera feedback -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-3 space-y-1">
                    <span class="font-bold text-gray-300"><i class="fa-solid fa-language text-brandPurple"></i> Grammar & Tone</span>
                    <p class="text-gray-400 leading-normal">{{ $q->grammar_feedback }}</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-3 space-y-1">
                    <span class="font-bold text-gray-300"><i class="fa-solid fa-volume-high text-brandCyan"></i> Voice Analysis</span>
                    <p class="text-gray-400 leading-normal">{{ $q->voice_analysis }}</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-3 space-y-1">
                    <span class="font-bold text-gray-300"><i class="fa-solid fa-camera text-pink-400"></i> Video Behavior</span>
                    <p class="text-gray-400 leading-normal">{{ $q->camera_analysis }}</p>
                </div>
            </div>

            <!-- AI Improved Answer Box -->
            <div class="bg-gradient-to-tr from-brandCyan/5 to-brandPurple/5 border border-white/10 rounded-2xl p-4">
                <h5 class="text-xs font-bold text-white mb-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-wand-magic-sparkles"></i> AI Improved Answer</span>
                    <button onclick="speakReportAnswer({{ $q->id }})" class="text-xs text-brandCyan hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-volume-high text-[10px]"></i> Listen Answer
                    </button>
                </h5>
                <p class="text-xs text-gray-300 leading-relaxed" id="ans-text-{{ $q->id }}">{{ $q->ai_improved_answer }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Bottom Actions -->
    <div class="flex gap-4">
        <a href="{{ route('interviews') }}" class="w-1/2 py-3 bg-white/10 hover:bg-white/15 rounded-2xl font-bold text-center text-gray-300 transition-colors">
            Back to Interviews
        </a>
        <a href="{{ route('dashboard') }}" class="w-1/2 py-3 bg-brandPurple hover:bg-brandPurple/90 rounded-2xl font-bold text-center text-white transition-colors">
            Go to Dashboard
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function speak(text) {
        if ('speechSynthesis' in window) {
            // Cancel any current speaking
            window.speechSynthesis.cancel();
            
            const utterance = new SpeechSynthesisUtterance(text);
            
            // Auto match voice language preference
            const prefLang = "{{ $session->language }}";
            if (prefLang.startsWith('hi')) {
                utterance.lang = 'hi-IN';
            } else if (prefLang.startsWith('gu')) {
                utterance.lang = 'gu-IN';
            } else {
                utterance.lang = 'en-US';
            }
            
            window.speechSynthesis.speak(utterance);
        }
    }

    function speakReportSuggestions(id) {
        const listItems = document.querySelectorAll(`#sug-list-${id} li`);
        let text = "Here are the improvement tips: ";
        listItems.forEach((li, index) => {
            text += li.innerText + ". ";
        });
        speak(text);
    }

    function speakReportAnswer(id) {
        const text = document.getElementById(`ans-text-${id}`).innerText;
        speak(text);
    }
</script>
@endsection
