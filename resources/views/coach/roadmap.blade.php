@extends('layouts.app')

@section('page_title')
    AI Career Coach — Roadmap
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- LEFT PANEL: Vertical Timeline study plan -->
    <div class="lg:col-span-2 space-y-6">
        <div class="glassmorphism rounded-3xl p-6 md:p-8 border border-white/10">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                <i class="fa-solid fa-timeline text-brandCyan"></i>
                Study Roadmap for <span class="text-brandCyan">{{ $role }}</span>
            </h3>

            <!-- Vertical Timeline Structure -->
            <div class="relative border-l-2 border-white/10 ml-4 space-y-8 pb-4">
                @foreach($timeline as $node)
                <div class="relative pl-8 group">
                    <!-- Dot -->
                    <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-darkBg border-2 border-brandCyan group-hover:bg-brandCyan transition-colors"></div>
                    
                    <div class="space-y-1">
                        <span class="text-brandCyan font-bold text-xs uppercase tracking-wider">{{ $node['month'] }}</span>
                        <h4 class="text-base font-bold text-white">{{ $node['topic'] }}</h4>
                        <p class="text-xs text-gray-400 leading-relaxed max-w-xl">{{ $node['description'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Skill Gap Analysis & Courses recommendations -->
    <div class="space-y-6">
        <!-- Skill gap card -->
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 space-y-6">
            <div>
                <h3 class="text-lg font-bold">Skill Gap Analysis</h3>
                <p class="text-xs text-gray-400 mt-0.5">Estimated deficit to qualify for job roles</p>
            </div>

            <div class="space-y-4">
                @foreach($skillGap as $gap)
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-semibold text-gray-300">{{ $gap['skill'] }}</span>
                        <span class="text-brandPurple font-bold">{{ $gap['gap_percentage'] }}% Gap</span>
                    </div>
                    <div class="w-full bg-white/10 h-2 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-brandPurple to-pink-550 h-full rounded-full" style="width: {{ $gap['gap_percentage'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Courses Card -->
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 space-y-4">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-graduation-cap text-yellow-500"></i>
                Recommended Learning Path
            </h3>
            <div class="space-y-3">
                <div class="p-3 bg-white/5 border border-white/10 rounded-xl space-y-1">
                    <span class="text-xs font-bold text-white block">Laravel Core to Advanced</span>
                    <span class="text-[10px] text-gray-400 block">Available in Courses Panel (40 hours)</span>
                </div>
                <div class="p-3 bg-white/5 border border-white/10 rounded-xl space-y-1">
                    <span class="text-xs font-bold text-white block">DevOps on AWS for Beginners</span>
                    <span class="text-[10px] text-gray-400 block">Available in Courses Panel (25 hours)</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
