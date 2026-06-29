@extends('layouts.app')

@section('page_title', 'Aptitude & Technical Quizzes')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- LEFT PANEL: Topics list -->
    <div class="lg:col-span-2 space-y-6">
        <div class="glassmorphism rounded-3xl p-6 border border-white/10">
            <h3 class="text-lg font-bold mb-4">Select Quiz Topic</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Topic 1 -->
                <div class="bg-white/5 border border-white/10 hover:border-brandCyan/40 rounded-2xl p-5 flex flex-col justify-between transition-colors">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-brandCyan/15 text-brandCyan flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fa-brands fa-laravel"></i>
                        </div>
                        <h4 class="font-bold text-white">Laravel Core Framework</h4>
                        <p class="text-xs text-gray-400 mt-1">ORM relations, routes, blade views, and Artisan CLI commands.</p>
                    </div>
                    
                    <form action="#" method="GET" onsubmit="startQuiz(this, 'Laravel'); return false;" class="mt-6 flex gap-2">
                        <select name="difficulty" class="bg-darkBg text-xs font-semibold rounded-lg border border-white/10 px-2 py-1.5 focus:outline-none focus:border-brandCyan">
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                        </select>
                        <button type="submit" class="flex-1 py-1.5 px-3 bg-brandCyan/20 hover:bg-brandCyan/30 text-brandCyan text-xs font-bold rounded-lg border border-brandCyan/30 transition-colors">
                            Start Quiz
                        </button>
                    </form>
                </div>

                <!-- Topic 2 -->
                <div class="bg-white/5 border border-white/10 hover:border-brandPurple/40 rounded-2xl p-5 flex flex-col justify-between transition-colors">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-brandPurple/15 text-brandPurple flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fa-brands fa-php"></i>
                        </div>
                        <h4 class="font-bold text-white">PHP Core OOP</h4>
                        <p class="text-xs text-gray-400 mt-1">Foundational syntaxes, arrays, functions, structures and class bounds.</p>
                    </div>
                    
                    <form action="#" method="GET" onsubmit="startQuiz(this, 'PHP'); return false;" class="mt-6 flex gap-2">
                        <select name="difficulty" class="bg-darkBg text-xs font-semibold rounded-lg border border-white/10 px-2 py-1.5 focus:outline-none focus:border-brandPurple">
                            <option value="Beginner">Beginner</option>
                        </select>
                        <button type="submit" class="flex-1 py-1.5 px-3 bg-brandPurple/20 hover:bg-brandPurple/30 text-brandPurple text-xs font-bold rounded-lg border border-brandPurple/30 transition-colors">
                            Start Quiz
                        </button>
                    </form>
                </div>

                <!-- Topic 3 -->
                <div class="bg-white/5 border border-white/10 hover:border-emerald-500/40 rounded-2xl p-5 flex flex-col justify-between transition-colors">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fa-solid fa-database"></i>
                        </div>
                        <h4 class="font-bold text-white">Relational Databases</h4>
                        <p class="text-xs text-gray-400 mt-1">SQL joins, tables queries, indexing, and NoSQL comparison basics.</p>
                    </div>
                    
                    <form action="#" method="GET" onsubmit="startQuiz(this, 'Databases'); return false;" class="mt-6 flex gap-2">
                        <select name="difficulty" class="bg-darkBg text-xs font-semibold rounded-lg border border-white/10 px-2 py-1.5 focus:outline-none focus:border-emerald-500">
                            <option value="Beginner">Beginner</option>
                        </select>
                        <button type="submit" class="flex-1 py-1.5 px-3 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 text-xs font-bold rounded-lg border border-emerald-500/30 transition-colors">
                            Start Quiz
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Past Attempts -->
        <div class="glassmorphism rounded-3xl p-6 border border-white/10">
            <h3 class="text-sm font-bold mb-3">Your Quiz History</h3>
            @if($userAttempts->count() > 0)
            <div class="space-y-2">
                @foreach($userAttempts as $attempt)
                <div class="flex justify-between items-center p-3 bg-white/5 border border-white/10 rounded-xl text-xs">
                    <div>
                        <span class="font-bold text-white">{{ $attempt->topic }}</span>
                        <span class="text-gray-400 ml-1">({{ $attempt->difficulty }})</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-gray-400">Score: {{ $attempt->score }} XP</span>
                        <span class="text-emerald-400 font-semibold">{{ $attempt->correct_answers }}/{{ $attempt->total_questions }} Correct</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-xs text-gray-400">No attempts logged yet. Select a topic above to begin!</p>
            @endif
        </div>
    </div>

    <!-- RIGHT PANEL: Leaderboard Widget -->
    <div class="glassmorphism rounded-3xl p-6 border border-white/10 h-fit space-y-6">
        <div>
            <h3 class="text-lg font-bold flex items-center gap-2">
                <i class="fa-solid fa-trophy text-yellow-500"></i>
                Global Leaderboard
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">Top scorers on CareerPrep Pro platform</p>
        </div>

        <div class="space-y-3">
            @foreach($leaderboard as $index => $user)
            <div class="flex items-center justify-between p-3.5 bg-white/5 border border-white/10 rounded-2xl">
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-extrabold @if($index === 0) bg-yellow-500 text-black @elseif($index === 1) bg-slate-300 text-black @else bg-white/10 text-white @endif">
                        {{ $index + 1 }}
                    </span>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-white">{{ $user->name }}</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest">{{ $user->user_type }}</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-brandCyan">{{ $user->xp_points }} XP</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function startQuiz(form, topic) {
        const difficulty = form.elements['difficulty'].value;
        window.location.href = `/quiz/take/${topic}/${difficulty}`;
    }
</script>
@endsection
