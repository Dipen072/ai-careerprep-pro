@extends('layouts.app')

@section('page_title', 'AI Career Coach')

@section('content')
<div class="max-w-2xl mx-auto glassmorphism rounded-3xl p-8 border border-white/10 text-center space-y-8">
    
    <div>
        <div class="w-14 h-14 rounded-2xl bg-brandCyan/20 text-brandCyan border border-brandCyan/30 flex items-center justify-center text-2xl mx-auto mb-4">
            <i class="fa-solid fa-compass"></i>
        </div>
        <h2 class="text-2xl font-bold text-white">Customized Learning Roadmaps</h2>
        <p class="text-gray-400 text-sm mt-1">Select your desired career path to generate an AI-tailored study roadmap and identify skill gaps.</p>
    </div>

    <form action="{{ route('career-coach.roadmap') }}" method="POST" class="space-y-6 text-left">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-3">Choose Your Target Job Role:</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($roles as $index => $role)
                <label class="relative flex items-center p-4 bg-white/5 border border-white/10 hover:border-brandCyan/30 rounded-2xl cursor-pointer hover:bg-white/10 transition-colors" id="role-card-{{ $index }}">
                    <input type="radio" name="role" value="{{ $role }}" required class="form-radio text-brandCyan focus:ring-0 mr-3" onchange="highlightRole({{ $index }}, {{ count($roles) }})">
                    <div class="flex-1">
                        <span class="font-bold text-white text-sm block">{{ $role }}</span>
                        <span class="text-[10px] text-gray-400">5-Month Study Plan</span>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <button type="submit" class="w-full py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-2xl font-bold text-white shadow-lg transition-opacity flex justify-center items-center gap-2">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Generate AI Roadmap
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function highlightRole(activeIndex, totalCount) {
        for(let i = 0; i < totalCount; i++) {
            const card = document.getElementById(`role-card-${i}`);
            if (card) {
                card.classList.remove('bg-brandCyan/10', 'border-brandCyan');
                card.classList.add('bg-white/5', 'border-white/10');
            }
        }

        const activeCard = document.getElementById(`role-card-${activeIndex}`);
        if (activeCard) {
            activeCard.classList.remove('bg-white/5', 'border-white/10');
            activeCard.classList.add('bg-brandCyan/10', 'border-brandCyan');
        }
    }
</script>
@endsection
