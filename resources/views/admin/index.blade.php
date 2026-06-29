@extends('layouts.app')

@section('page_title', 'Admin Control Panel')

@section('content')
<div class="space-y-8">
    
    <!-- Aggregate Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glassmorphism rounded-2xl p-5 border border-white/10 flex flex-col justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">Total Users</span>
            <span class="text-3xl font-extrabold text-white mt-2">{{ $totalUsers }}</span>
        </div>
        <div class="glassmorphism rounded-2xl p-5 border border-white/10 flex flex-col justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">AI Interviews</span>
            <span class="text-3xl font-extrabold text-brandCyan mt-2">{{ $totalInterviews }}</span>
        </div>
        <div class="glassmorphism rounded-2xl p-5 border border-white/10 flex flex-col justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">Avg Interview Score</span>
            <span class="text-3xl font-extrabold text-brandPurple mt-2">{{ $avgInterviewScore }}/100</span>
        </div>
        <div class="glassmorphism rounded-2xl p-5 border border-white/10 flex flex-col justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">Total Quizzes</span>
            <span class="text-3xl font-extrabold text-emerald-400 mt-2">{{ $totalQuizzes }}</span>
        </div>
    </div>

    <!-- Main Admin Workspace Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- User Management Table -->
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 lg:col-span-2">
            <h3 class="text-lg font-bold mb-4">User Accounts Management</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-white/10 text-gray-400">
                            <th class="py-2.5 font-semibold">User</th>
                            <th class="py-2.5 font-semibold">Email</th>
                            <th class="py-2.5 font-semibold">Role</th>
                            <th class="py-2.5 font-semibold">Lang</th>
                            <th class="py-2.5 font-semibold text-right">XP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($users as $u)
                        <tr>
                            <td class="py-3 font-semibold text-white">{{ $u->name }}</td>
                            <td class="py-3 text-gray-400">{{ $u->email }}</td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold @if($u->role === 'admin') bg-brandPurple/10 text-brandPurple @else bg-white/5 text-gray-300 @endif">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-3 uppercase text-gray-400 font-semibold">{{ $u->language_preference }}</td>
                            <td class="py-3 text-right font-bold text-brandCyan">{{ $u->xp_points }} XP</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Language Distribution Widget -->
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 space-y-6">
            <div>
                <h3 class="text-lg font-bold">Languages Distribution</h3>
                <p class="text-xs text-gray-400 mt-0.5">User preferred system communication languages</p>
            </div>

            <div class="space-y-4">
                @foreach($languages as $lang => $count)
                <div class="space-y-1">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-semibold text-gray-300 uppercase">
                            @if($lang === 'en') 🇬🇧 English @elseif($lang === 'hi') 🇮🇳 Hindi @elseif($lang === 'gu') 🦁 Gujarati @else 🗣️ Bilingual @endif
                        </span>
                        <span class="text-gray-400">{{ $count }} users</span>
                    </div>
                    <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-brandCyan h-full rounded-full" style="width: {{ $totalUsers > 0 ? ($count / $totalUsers) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
