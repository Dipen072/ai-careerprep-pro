@extends('layouts.app')

@section('page_title', 'AI Mock Interviews')

@section('content')
<div class="space-y-6">
    <!-- Header banner -->
    <div class="flex justify-between items-center glassmorphism rounded-3xl p-6 border border-white/10">
        <div>
            <h2 class="text-2xl font-bold">Mock Interviews</h2>
            <p class="text-sm text-gray-400">Practice your technical and behavioral skills with real-time feedback</p>
        </div>
        <a href="{{ route('interviews.setup') }}" class="px-5 py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-xl font-bold text-white shadow-lg flex items-center gap-2 transition-all">
            <i class="fa-solid fa-plus text-sm"></i> Start New Interview
        </a>
    </div>

    <!-- Past interviews list -->
    <div class="glassmorphism rounded-3xl p-6 border border-white/10">
        <h3 class="text-lg font-bold mb-4">Interview History</h3>

        @if($sessions->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/10 text-gray-400 text-sm">
                        <th class="py-3 font-semibold">Date</th>
                        <th class="py-3 font-semibold">Type</th>
                        <th class="py-3 font-semibold">Topic</th>
                        <th class="py-3 font-semibold">Difficulty</th>
                        <th class="py-3 font-semibold">Language</th>
                        <th class="py-3 font-semibold">Score</th>
                        <th class="py-3 font-semibold">Status</th>
                        <th class="py-3 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm">
                    @foreach($sessions as $session)
                    <tr>
                        <td class="py-4 font-medium">{{ $session->created_at->format('M d, Y H:i') }}</td>
                        <td class="py-4">{{ $session->type }}</td>
                        <td class="py-4">
                            @if($session->type === 'Technical')
                                <span class="px-2.5 py-1 bg-brandCyan/10 border border-brandCyan/20 text-brandCyan rounded-lg text-xs font-semibold">
                                    {{ $session->technology }}
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-white/5 border border-white/10 text-gray-300 rounded-lg text-xs">
                                    HR Interview
                                </span>
                            @endif
                        </td>
                        <td class="py-4">{{ ucfirst($session->difficulty) }}</td>
                        <td class="py-4">
                            <span class="uppercase font-semibold text-xs text-gray-400">{{ $session->language }}</span>
                        </td>
                        <td class="py-4 font-bold text-base text-brandCyan">
                            @if($session->status === 'completed')
                                {{ $session->score }}/100
                            @else
                                --
                            @endif
                        </td>
                        <td class="py-4">
                            @if($session->status === 'completed')
                                <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 font-semibold">
                                    <i class="fa-solid fa-circle text-[8px]"></i> Completed
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs text-amber-400 font-semibold">
                                    <i class="fa-solid fa-circle text-[8px]"></i> In Progress
                                </span>
                            @endif
                        </td>
                        <td class="py-4">
                            @if($session->status === 'completed')
                                <a href="{{ route('interviews.report', $session->id) }}" class="text-brandCyan hover:text-brandCyan/80 font-semibold flex items-center gap-1">
                                    View Report <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            @else
                                <a href="{{ route('interviews.arena', $session->id) }}" class="text-brandPurple hover:text-brandPurple/80 font-semibold flex items-center gap-1">
                                    Resume <i class="fa-solid fa-circle-play text-xs"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-12 text-center text-gray-400">
            <span class="text-5xl">🤖</span>
            <p class="mt-4">You have not completed any AI interviews yet.</p>
            <p class="text-sm mt-1 text-gray-500">Launch a mock interview session to evaluate your skills.</p>
        </div>
        @endif
    </div>
</div>
@endsection
