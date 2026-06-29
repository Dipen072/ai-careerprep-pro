@extends('layouts.auth')

@section('content')
<div class="glassmorphism rounded-3xl p-8 relative overflow-hidden transition-all duration-300">
    
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-brandCyan/20 text-brandCyan border border-brandCyan/30 mb-3">
            <i class="fa-solid fa-right-to-bracket text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold tracking-tight">Welcome Back</h2>
        <p class="text-gray-400 text-sm mt-1">Log in to resume your interview practice</p>
    </div>

    <!-- Error Summary -->
    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-200 text-sm rounded-xl p-3 mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Email Address</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fa-regular fa-envelope"></i>
                </span>
                <input type="email" name="email" value="{{ old('email') }}" required class="block w-full pl-10 pr-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors" placeholder="name@example.com">
            </div>
        </div>

        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label class="block text-sm font-medium text-gray-300">Password</label>
                <a href="{{ route('forgot-password') }}" class="text-xs text-brandCyan hover:underline">Forgot password?</a>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input type="password" name="password" required class="block w-full pl-10 pr-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors" placeholder="••••••••">
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-brandCyan focus:ring-brandCyan bg-white/5 border-white/10">
                <label for="remember" class="ml-2 block text-sm text-gray-300">Remember me</label>
            </div>
        </div>

        <button type="submit" class="w-full py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-xl font-semibold text-white shadow-lg transition-opacity flex justify-center items-center">
            Sign In <i class="fa-solid fa-arrow-right-to-bracket ml-2 text-sm"></i>
        </button>
    </form>

    <!-- Divider -->
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-white/10"></div></div>
        <div class="relative flex justify-center text-xs uppercase"><span class="bg-darkBg px-2 text-gray-400 font-semibold">Or continue with</span></div>
    </div>

    <!-- Social Signin -->
    <div class="space-y-4">
        <a href="{{ route('auth.google.mock') }}" class="w-full flex items-center justify-center px-4 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-xl text-sm font-semibold transition-all duration-200">
            <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24">
                <path fill="#EA4335" d="M12 5.04c1.66 0 3.2.57 4.38 1.69l3.27-3.27C17.67 1.54 14.98 1 12 1 7.35 1 3.37 3.65 1.42 7.5l3.86 3C6.23 7.63 8.89 5.04 12 5.04z"/>
                <path fill="#4285F4" d="M23.49 12.27c0-.81-.07-1.59-.2-2.36H12v4.51h6.46c-.29 1.48-1.14 2.73-2.4 3.58l3.76 2.91c2.2-2.03 3.67-5.02 3.67-8.64z"/>
                <path fill="#FBBC05" d="M5.28 14.78c-.26-.78-.41-1.6-.41-2.46s.15-1.68.41-2.46L1.42 6.86C.51 8.7.01 10.79.01 13c0 2.21.5 4.3 1.41 6.14l3.86-3.36z"/>
                <path fill="#34A853" d="M12 22.99c3.24 0 5.97-1.07 7.96-2.91l-3.76-2.91c-1.05.7-2.4 1.13-4.2 1.13-3.11 0-5.77-2.59-6.72-5.46L1.42 16.2c1.95 3.85 5.93 6.5 10.58 6.5z"/>
            </svg>
            Sign in with Google
        </a>

        <p class="text-center text-sm text-gray-400">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-brandCyan hover:text-brandCyan/80 font-semibold underline decoration-brandCyan/30">Register here</a>
        </p>
    </div>
</div>
@endsection
