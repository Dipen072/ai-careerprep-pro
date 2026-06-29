@extends('layouts.app')

@section('page_title')
    Quiz: {{ $topic }} ({{ $difficulty }})
@endsection

@section('content')
<div class="max-w-2xl mx-auto glassmorphism rounded-3xl p-6 md:p-8 border border-white/10 space-y-6">
    
    <!-- Quiz Header -->
    <div class="flex justify-between items-center border-b border-white/5 pb-4">
        <div>
            <h2 class="text-xl font-bold">{{ $topic }} Quiz</h2>
            <span class="px-2 py-0.5 bg-brandCyan/10 border border-brandCyan/20 text-brandCyan text-[10px] font-bold rounded uppercase">
                {{ $difficulty }} Level
            </span>
        </div>
        <div class="text-right">
            <span class="text-sm font-bold text-amber-400 flex items-center gap-1.5 justify-end">
                <i class="fa-regular fa-clock"></i> <span id="quiz-timer">00:00</span>
            </span>
            <span class="text-[10px] text-red-400 font-semibold mt-0.5 block">
                ⚠️ Negative Marking: -0.25
            </span>
        </div>
    </div>

    <!-- Questions Form -->
    <form action="{{ route('quiz.submit') }}" method="POST" id="quiz-form" class="space-y-8">
        @csrf
        <input type="hidden" name="topic" value="{{ $topic }}">
        <input type="hidden" name="difficulty" value="{{ $difficulty }}">
        <input type="hidden" name="time_spent" id="time-spent-input" value="0">

        @foreach($questions as $index => $q)
        <div class="space-y-4" id="question-card-{{ $index }}">
            <h3 class="text-base font-bold text-white flex items-start gap-2.5">
                <span class="w-6 h-6 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-xs text-brandCyan shrink-0 mt-0.5">
                    {{ $index + 1 }}
                </span>
                <span>{{ $q['question'] }}</span>
            </h3>

            <div class="grid grid-cols-1 gap-3 pl-8">
                @foreach($q['options'] as $key => $option)
                <label class="flex items-center p-3.5 bg-white/5 border border-white/10 hover:border-brandCyan/30 rounded-2xl cursor-pointer hover:bg-white/10 transition-all text-xs text-gray-200" id="option-label-{{ $q['id'] }}-{{ $key }}">
                    <input type="radio" name="answers[{{ $q['id'] }}]" value="{{ $key }}" required class="form-radio text-brandCyan focus:ring-0 mr-3" onchange="highlightOption({{ $q['id'] }}, '{{ $key }}')">
                    <span class="font-bold mr-1">{{ $key }}.</span> {{ $option }}
                </label>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="pt-4 border-t border-white/5 flex gap-4">
            <button type="button" onclick="cancelQuiz()" class="w-1/3 py-3 bg-white/10 hover:bg-white/15 rounded-xl font-semibold text-center text-gray-300 transition-colors">
                Give Up
            </button>
            <button type="submit" class="w-2/3 py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-xl font-bold text-white shadow-lg transition-opacity">
                Submit Answers
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let secondsSpent = 0;
    const timerInterval = setInterval(() => {
        secondsSpent++;
        const mins = String(Math.floor(secondsSpent / 60)).padStart(2, '0');
        const secs = String(secondsSpent % 60).padStart(2, '0');
        document.getElementById('quiz-timer').innerText = `${mins}:${secs}`;
        document.getElementById('time-spent-input').value = secondsSpent;
    }, 1000);

    function highlightOption(questionId, key) {
        // Remove active background from all sibling option labels for this question
        const options = ['A', 'B', 'C', 'D'];
        options.forEach(opt => {
            const label = document.getElementById(`option-label-${questionId}-${opt}`);
            if (label) {
                label.classList.remove('bg-brandCyan/10', 'border-brandCyan');
                label.classList.add('bg-white/5', 'border-white/10');
            }
        });

        // Add highlight to selected option label
        const activeLabel = document.getElementById(`option-label-${questionId}-${key}`);
        if (activeLabel) {
            activeLabel.classList.remove('bg-white/5', 'border-white/10');
            activeLabel.classList.add('bg-brandCyan/10', 'border-brandCyan');
        }
    }

    function cancelQuiz() {
        if (confirm("Are you sure you want to exit? Your progress will not be saved.")) {
            clearInterval(timerInterval);
            window.location.href = '/quiz';
        }
    }

    document.getElementById('quiz-form').addEventListener('submit', function() {
        clearInterval(timerInterval);
    });
</script>
@endsection
