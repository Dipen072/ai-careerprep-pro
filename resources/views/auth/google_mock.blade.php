@extends('layouts.auth')

@section('content')
<div class="glassmorphism rounded-3xl p-8 relative overflow-hidden transition-all duration-300">
    <!-- Google Logo representation -->
    <div class="text-center mb-6">
        <svg class="h-10 w-10 mx-auto mb-3" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.85z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.85c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        <h2 class="text-xl font-bold tracking-tight text-white">Sign in with Google</h2>
        <p class="text-gray-400 text-xs mt-1">to continue to <span class="text-brandCyan font-semibold">CareerPrep Pro</span></p>
    </div>

    <!-- Error Summary -->
    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-200 text-xs rounded-xl p-3 mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Choice: Choose existing accounts -->
    <div class="space-y-3 mb-6">
        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Choose Seeded Demo Account</span>
        
        <!-- Account 1 -->
        <button type="button" onclick="selectSeeded('dipen@careerprep.com', 'Dipen Patel', '9988776655')" class="w-full text-left p-3.5 bg-white/5 border border-white/10 hover:border-brandCyan/40 rounded-2xl flex items-center justify-between transition-colors group">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-brandCyan/20 text-brandCyan flex items-center justify-center font-bold text-xs">D</div>
                <div>
                    <span class="font-bold text-white text-xs block group-hover:text-brandCyan transition-colors">Dipen Patel (Student)</span>
                    <span class="text-[10px] text-gray-400">dipen@careerprep.com</span>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-[10px] text-gray-500"></i>
        </button>

        <!-- Account 2 -->
        <button type="button" onclick="selectSeeded('admin@careerprep.com', 'Admin Dipen', '9876543210')" class="w-full text-left p-3.5 bg-white/5 border border-white/10 hover:border-brandPurple/40 rounded-2xl flex items-center justify-between transition-colors group">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-brandPurple/20 text-brandPurple flex items-center justify-center font-bold text-xs">A</div>
                <div>
                    <span class="font-bold text-white text-xs block group-hover:text-brandPurple transition-colors">Admin Dipen (Admin)</span>
                    <span class="text-[10px] text-gray-400">admin@careerprep.com</span>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-[10px] text-gray-500"></i>
        </button>
    </div>

    <!-- Divider -->
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-white/10"></div></div>
        <div class="relative flex justify-center text-[10px] uppercase"><span class="bg-darkBg px-2 text-gray-400 font-bold">Or enter details dynamically</span></div>
    </div>

    <!-- Dynamic Form -->
    <form action="{{ route('auth.google.mock.post') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-gray-300 mb-1">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="block w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-xs text-white" placeholder="your_email@gmail.com">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-300 mb-1">Full Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="block w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-xs text-white" placeholder="Your Name">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-300 mb-1">Mobile Number</label>
            <input type="tel" id="mobile" name="mobile" value="{{ old('mobile') }}" required class="block w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-xs text-white" placeholder="10-digit mobile number">
        </div>

        <button type="submit" class="w-full py-2.5 bg-brandCyan hover:bg-brandCyan/90 font-bold text-white rounded-xl text-xs transition-colors flex justify-center items-center gap-1.5 mt-2">
            Continue to CareerPrep Pro <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </button>
    </form>

    <div class="text-center mt-6">
        <a href="{{ route('login') }}" class="text-[10px] text-gray-400 hover:text-white underline">Back to Login Screen</a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectSeeded(email, name, mobile) {
        document.getElementById('email').value = email;
        document.getElementById('name').value = name;
        document.getElementById('mobile').value = mobile;
        
        // Auto submit
        document.querySelector('form').submit();
    }
</script>
@endsection
