@extends('layouts.app')

@section('page_title', 'Configure Mock Interview')

@section('content')
<div class="max-w-2xl mx-auto glassmorphism rounded-3xl p-8 border border-white/10">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold">Interview Configurator</h2>
        <p class="text-gray-400 text-sm mt-1">Select your topics and parameters for AI questions generation</p>
    </div>

    <form action="{{ route('interviews.create') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Type Toggle -->
        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-3">Choose Interview Type</label>
            <div class="grid grid-cols-2 gap-4">
                <label class="relative flex flex-col items-center justify-center p-4 bg-white/5 border border-white/10 rounded-2xl cursor-pointer hover:bg-white/10 transition-colors" id="label-hr">
                    <input type="radio" name="type" value="HR" checked class="sr-only" onchange="toggleType(this)">
                    <div class="w-10 h-10 rounded-xl bg-brandCyan/20 text-brandCyan flex items-center justify-center mb-2 border border-brandCyan/30">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="font-semibold text-white">HR / Behavioral</span>
                    <span class="text-[10px] text-gray-400 text-center mt-1">Aptitude, career goals, soft skills</span>
                </label>

                <label class="relative flex flex-col items-center justify-center p-4 bg-white/5 border border-white/10 rounded-2xl cursor-pointer hover:bg-white/10 transition-colors" id="label-tech">
                    <input type="radio" name="type" value="Technical" class="sr-only" onchange="toggleType(this)">
                    <div class="w-10 h-10 rounded-xl bg-brandPurple/20 text-brandPurple flex items-center justify-center mb-2 border border-brandPurple/30">
                        <i class="fa-solid fa-laptop-code"></i>
                    </div>
                    <span class="font-semibold text-white">Technical Interview</span>
                    <span class="text-[10px] text-gray-400 text-center mt-1">Domain specific coding & systems</span>
                </label>
            </div>
        </div>

        <!-- Technology (Technical only) -->
        <div id="tech-select-container" class="hidden">
            <label class="block text-sm font-semibold text-gray-300 mb-1.5">Select Technologies (Choose one or more)</label>
            <div class="flex flex-wrap gap-2.5 p-1" id="tech-chips-selector">
                @foreach($availableTechs as $tech)
                @php
                    $isSkill = in_array($tech, $user->skills ?? []);
                @endphp
                <label class="px-4 py-2 rounded-xl cursor-pointer text-sm font-semibold transition-all duration-200 select-none flex items-center gap-2 {{ $isSkill ? 'bg-brandPurple/25 border border-brandPurple text-brandPurple shadow-md' : 'bg-white/5 border border-white/10 text-gray-300' }} hover:border-brandPurple/50 active-scale" id="tech-label-{{ Str::slug($tech) }}">
                    <input type="checkbox" name="technology[]" value="{{ $tech }}" class="sr-only" onchange="toggleTechChip(this, '{{ Str::slug($tech) }}')" @if($isSkill) checked @endif>
                    <span>{{ $tech }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Difficulty Level -->
        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-1.5">Select Experience Difficulty</label>
            <div class="relative">
                <select name="difficulty" class="block w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors">
                    <option value="Fresher" class="bg-darkBg" @if($user->user_type === 'fresher') selected @endif>Fresher</option>
                    <option value="Experienced" class="bg-darkBg" @if($user->user_type === 'experienced') selected @endif>Experienced</option>
                </select>
            </div>
        </div>

        <!-- Preferred Language -->
        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-1.5">AI Interview Communication Language</label>
            <div class="relative">
                <select name="language" class="block w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors">
                    <option value="en" class="bg-darkBg" @if($user->language_preference === 'en') selected @endif>English Only</option>
                    <option value="hi" class="bg-darkBg" @if($user->language_preference === 'hi') selected @endif>Hindi Only (हिंदी)</option>
                    <option value="gu" class="bg-darkBg" @if($user->language_preference === 'gu') selected @endif>Gujarati Only (ગુજરાતી)</option>
                    <option value="hi_en" class="bg-darkBg" @if($user->language_preference === 'hi_en') selected @endif>Hindi + English (Hinglish)</option>
                    <option value="gu_en" class="bg-darkBg" @if($user->language_preference === 'gu_en') selected @endif>Gujarati + English (Gujlish)</option>
                </select>
            </div>
        </div>

        <!-- CTAs -->
        <div class="flex gap-4 pt-4">
            <a href="{{ route('interviews') }}" class="w-1/3 py-3 bg-white/10 hover:bg-white/15 rounded-xl font-semibold text-center text-gray-300 transition-colors">
                Cancel
            </a>
            <button type="submit" class="w-2/3 py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-xl font-bold text-white shadow-lg transition-opacity flex justify-center items-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Initialize AI Interviewer
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function toggleType(input) {
        const hrLabel = document.getElementById('label-hr');
        const techLabel = document.getElementById('label-tech');
        const container = document.getElementById('tech-select-container');
        
        if (input.value === 'Technical') {
            techLabel.classList.add('bg-brandPurple/10', 'border-brandPurple');
            hrLabel.classList.remove('bg-brandCyan/10', 'border-brandCyan');
            container.classList.remove('hidden');
        } else {
            hrLabel.classList.add('bg-brandCyan/10', 'border-brandCyan');
            techLabel.classList.remove('bg-brandPurple/10', 'border-brandPurple');
            container.classList.add('hidden');
        }
    }

    function toggleTechChip(checkbox, slug) {
        const label = document.getElementById(`tech-label-${slug}`);
        if (checkbox.checked) {
            label.classList.remove('bg-white/5', 'border-white/10', 'text-gray-300');
            label.classList.add('bg-brandPurple/25', 'border-brandPurple', 'text-brandPurple', 'shadow-md');
        } else {
            label.classList.remove('bg-brandPurple/25', 'border-brandPurple', 'text-brandPurple', 'shadow-md');
            label.classList.add('bg-white/5', 'border-white/10', 'text-gray-300');
        }
    }

    // initialize on load
    document.addEventListener("DOMContentLoaded", function() {
        toggleType(document.querySelector('input[name="type"]:checked'));
    });
</script>

<style>
    .active-scale:active {
        transform: scale(0.96);
    }
</style>
