@extends('layouts.app')

@section('page_title', 'Quiz Results')

@section('content')
<div class="max-w-2xl mx-auto space-y-8">
    
    <!-- Summary Header Card -->
    <div class="glassmorphism rounded-3xl p-6 border border-white/10 flex items-center justify-between">
        <div class="space-y-1">
            <span class="px-2.5 py-0.5 bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-[10px] font-bold rounded">
                Quiz Completed
            </span>
            <h2 class="text-xl font-bold">Your Performance</h2>
            <p class="text-xs text-gray-400">
                Topic: <span class="text-brandCyan font-semibold">{{ $attempt->topic }}</span> · 
                Difficulty: {{ $attempt->difficulty }}
            </p>
        </div>

        <div class="flex items-center gap-4">
            <!-- Score Card -->
            <div class="text-center bg-white/5 border border-white/10 rounded-2xl px-4 py-3">
                <span class="text-[10px] text-gray-400 font-semibold block uppercase">XP Earned</span>
                <span class="text-xl font-extrabold text-brandCyan">+{{ $xpEarned }} XP</span>
            </div>

            <!-- Accuracy Card -->
            <div class="text-center bg-white/5 border border-white/10 rounded-2xl px-4 py-3">
                <span class="text-[10px] text-gray-400 font-semibold block uppercase">Accuracy</span>
                <span class="text-xl font-extrabold text-emerald-400">
                    {{ $attempt->correct_answers }}/{{ $attempt->total_questions }}
                </span>
            </div>
        </div>
    </div>

    <!-- Questions Explanations List -->
    <div class="space-y-6">
        <h3 class="text-lg font-bold">Review & Explanations</h3>

        @foreach($results as $index => $res)
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 space-y-4">
            <div class="flex justify-between items-start">
                <h4 class="font-bold text-sm text-gray-300">Question {{ $index + 1 }}</h4>
                @if($res['is_correct'])
                    <span class="px-2 py-0.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold rounded flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> Correct (+10 XP)
                    </span>
                @else
                    <span class="px-2 py-0.5 bg-red-500/10 border border-red-500/20 text-red-400 text-[10px] font-bold rounded flex items-center gap-1">
                        <i class="fa-solid fa-circle-xmark"></i> Incorrect (-2.5 XP)
                    </span>
                @endif
            </div>

            <p class="text-white text-sm font-semibold">{{ $res['question'] }}</p>

            <!-- Options grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 text-xs">
                @foreach($res['options'] as $key => $option)
                <div class="p-3 rounded-xl border @if($key === $res['correct']) bg-emerald-500/10 border-emerald-500/30 text-emerald-300 @elseif($key === $res['selected'] && !$res['is_correct']) bg-red-500/10 border-red-500/30 text-red-300 @else bg-white/5 border-white/10 text-gray-400 @endif">
                    <span class="font-bold">{{ $key }}.</span> {{ $option }}
                </div>
                @endforeach
            </div>

            <!-- Explanation -->
            <div class="bg-brandCyan/5 border border-brandCyan/10 rounded-2xl p-4 text-xs leading-normal">
                <span class="font-bold text-brandCyan block mb-1">Explanation:</span>
                <p class="text-gray-300">{{ $res['explanation'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Bottom Action -->
    <a href="{{ route('quiz') }}" class="w-full py-3 bg-brandPurple hover:bg-brandPurple/90 rounded-2xl font-bold text-center text-white transition-colors block">
        Back to Quizzes
    </a>
</div>
@endsection
