@extends('layouts.app')

@section('page_title', 'Online Coding Lab')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- LEFT PANEL: Coding Challenges list -->
    <div class="lg:col-span-2 space-y-6">
        <div class="glassmorphism rounded-3xl p-6 border border-white/10">
            <h3 class="text-lg font-bold mb-4">Practice Challenges</h3>

            <div class="space-y-4">
                @foreach($problemsList as $prob)
                <div class="bg-white/5 border border-white/10 hover:border-brandCyan/40 rounded-2xl p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 transition-colors">
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <h4 class="font-bold text-white text-base">{{ $prob['title'] }}</h4>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold @if($prob['difficulty'] === 'Easy') bg-emerald-500/10 text-emerald-400 @else bg-amber-500/10 text-amber-400 @endif">
                                {{ $prob['difficulty'] }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($prob['company_tags'] as $tag)
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-gray-400 rounded text-[9px] font-semibold">
                                {{ $tag }}
                            </span>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('coding.show', $prob['id']) }}" class="py-2 px-4 bg-brandCyan/20 hover:bg-brandCyan/30 border border-brandCyan/30 text-brandCyan text-xs font-bold rounded-xl transition-colors shrink-0">
                        Solve Challenge <i class="fa-solid fa-code ml-1"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Submission History -->
    <div class="glassmorphism rounded-3xl p-6 border border-white/10 h-fit space-y-4">
        <h3 class="text-lg font-bold flex items-center gap-2">
            <i class="fa-solid fa-clock-history text-brandPurple"></i>
            Submissions
        </h3>
        
        <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
            @forelse($submissions as $sub)
            <div class="p-3 bg-white/5 border border-white/10 rounded-xl flex items-center justify-between text-xs">
                <div>
                    <span class="font-bold text-white uppercase">{{ $sub['problem_id'] }}</span>
                    <span class="text-gray-400 ml-1">({{ $sub['language'] }})</span>
                </div>
                <div class="text-right">
                    @if($sub['status'] === 'Accepted')
                        <span class="text-emerald-400 font-bold">Accepted</span>
                    @else
                        <span class="text-red-400 font-bold">Wrong Answer</span>
                    @endif
                    <p class="text-[9px] text-gray-500 mt-0.5">{{ $sub['created_at']->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400">No recent submissions found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
