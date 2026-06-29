<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI CareerPrep Pro — Dashboard</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        darkBg: '#0a0e27',
                        cardBg: 'rgba(255, 255, 255, 0.03)',
                        glassBorder: 'rgba(255, 255, 255, 0.08)',
                        brandCyan: '#06b6d4',
                        brandPurple: '#a855f7',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0a0e27;
            font-family: 'Outfit', sans-serif;
            color: #f3f4f6;
            overflow-x: hidden;
        }
        .glow-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.25;
            z-index: 0;
            pointer-events: none;
        }
        .glassmorphism {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
    
    @yield('styles')
</head>
<body class="min-h-screen relative flex">

    <!-- Glowing Background Elements -->
    <div class="glow-circle w-[400px] h-[400px] bg-brandPurple top-[-100px] left-[10%]"></div>
    <div class="glow-circle w-[400px] h-[400px] bg-brandCyan bottom-[10%] right-[5%]"></div>

    <!-- Sidebar -->
    <aside class="w-64 border-r border-white/10 glassmorphism flex flex-col fixed h-screen z-20 transition-all duration-300" id="sidebar">
        <!-- Logo -->
        <div class="p-6 border-b border-white/10 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brandCyan to-brandPurple flex items-center justify-center text-white shadow-lg">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <span class="font-bold text-lg tracking-tight bg-gradient-to-r from-brandCyan to-brandPurple bg-clip-text text-transparent">CareerPrep Pro</span>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/5 text-brandCyan border-l-2 border-brandCyan' : 'text-gray-400' }}">
                <i class="fa-solid fa-chart-line w-5"></i>
                <span class="font-semibold text-sm">{{ __('messages.dashboard') }}</span>
            </a>

            <a href="{{ route('interviews') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-colors {{ request()->routeIs('interviews*') ? 'bg-white/5 text-brandCyan border-l-2 border-brandCyan' : 'text-gray-400' }}">
                <i class="fa-solid fa-user-tie w-5"></i>
                <span class="font-semibold text-sm">{{ __('messages.interviews') }}</span>
            </a>

            <a href="{{ route('resume') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-colors {{ request()->routeIs('resume*') ? 'bg-white/5 text-brandCyan border-l-2 border-brandCyan' : 'text-gray-400' }}">
                <i class="fa-regular fa-file-pdf w-5"></i>
                <span class="font-semibold text-sm">{{ __('messages.resume') }}</span>
            </a>

            <a href="{{ route('quiz') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-colors {{ request()->routeIs('quiz*') ? 'bg-white/5 text-brandCyan border-l-2 border-brandCyan' : 'text-gray-400' }}">
                <i class="fa-solid fa-circle-question w-5"></i>
                <span class="font-semibold text-sm">{{ __('messages.quiz') }}</span>
            </a>

            <a href="{{ route('coding') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-colors {{ request()->routeIs('coding*') ? 'bg-white/5 text-brandCyan border-l-2 border-brandCyan' : 'text-gray-400' }}">
                <i class="fa-solid fa-code w-5"></i>
                <span class="font-semibold text-sm">{{ __('messages.coding') }}</span>
            </a>

            <a href="{{ route('career-coach') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-colors {{ request()->routeIs('career-coach*') ? 'bg-white/5 text-brandCyan border-l-2 border-brandCyan' : 'text-gray-400' }}">
                <i class="fa-solid fa-compass w-5"></i>
                <span class="font-semibold text-sm">{{ __('messages.career_coach') }}</span>
            </a>
            
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-colors {{ request()->routeIs('admin*') ? 'bg-white/5 text-brandPurple border-l-2 border-brandPurple' : 'text-gray-400' }}">
                <i class="fa-solid fa-shield-halved w-5"></i>
                <span class="font-semibold text-sm">{{ __('messages.admin') }}</span>
            </a>
            @endif
        </nav>

        <!-- Bottom User Section -->
        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3 px-2 py-3">
                <div class="w-10 h-10 rounded-full bg-brandCyan/20 text-brandCyan flex items-center justify-center font-bold border border-brandCyan/30">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ ucfirst(Auth::user()->user_type) }}</p>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-red-400 hover:bg-red-500/10 rounded-xl transition-colors mt-2 text-sm font-semibold">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    {{ __('messages.logout') }}
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col ml-64 min-h-screen">
        
        <!-- Header / Top Bar -->
        <header class="h-20 border-b border-white/10 glassmorphism flex items-center justify-between px-8 z-10 sticky top-0">
            <!-- Breadcrumbs or Title -->
            <div>
                <h1 class="text-xl font-bold">@yield('page_title', 'Dashboard')</h1>
            </div>

            <!-- Stats & Controls -->
            <div class="flex items-center gap-6">
                <!-- Streak Badge -->
                <div class="flex items-center gap-2 px-3.5 py-1.5 bg-orange-500/15 border border-orange-500/30 text-orange-400 rounded-full text-sm font-bold shadow-md shadow-orange-950/20" title="Daily Streak">
                    <i class="fa-solid fa-fire text-base animate-pulse"></i>
                    <span>{{ Auth::user()->streak }} {{ __('messages.streak') }}</span>
                </div>

                <!-- XP Score Card -->
                <div class="flex items-center gap-2 px-3.5 py-1.5 bg-brandCyan/15 border border-brandCyan/30 text-brandCyan rounded-full text-sm font-bold shadow-md" title="XP Points">
                    <i class="fa-solid fa-circle-nodes text-base"></i>
                    <span>{{ Auth::user()->xp_points }} XP</span>
                </div>

                <!-- Language Selection Dropdown -->
                <div class="relative" id="lang-selector-container">
                    <button onclick="toggleLangDropdown()" class="flex items-center gap-2 px-3.5 py-1.5 bg-white/5 border border-white/10 hover:border-white/20 rounded-xl text-sm font-semibold transition-all">
                        <span id="active-lang-flag">
                            @if(App::getLocale() === 'gu') 🦁 @elseif(App::getLocale() === 'hi') 🇮🇳 @else 🇬🇧 @endif
                        </span>
                        <span class="hidden sm:inline">
                            @if(App::getLocale() === 'gu') Gujarati @elseif(App::getLocale() === 'hi') Hindi @else English @endif
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                    </button>
                    <!-- Dropdown List -->
                    <div id="lang-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-darkBg/95 border border-white/10 rounded-2xl shadow-2xl p-2 z-30 glassmorphism">
                        <form action="{{ route('profile.language') }}" method="POST" id="lang-form">
                            @csrf
                            <input type="hidden" name="language_preference" id="lang-pref-input">
                            
                            <button type="button" onclick="changeLang('en')" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-left hover:bg-white/5 text-sm font-medium transition-colors">
                                <span>🇬🇧</span> English Only
                            </button>
                            <button type="button" onclick="changeLang('hi')" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-left hover:bg-white/5 text-sm font-medium transition-colors">
                                <span>🇮🇳</span> Hindi (हिंदी)
                            </button>
                            <button type="button" onclick="changeLang('gu')" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-left hover:bg-white/5 text-sm font-medium transition-colors">
                                <span>🦁</span> Gujarati (ગુજરાતી)
                            </button>
                            <button type="button" onclick="changeLang('hi_en')" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-left hover:bg-white/5 text-sm font-medium transition-colors">
                                <span>💬</span> Hinglish (Hindi+EN)
                            </button>
                            <button type="button" onclick="changeLang('gu_en')" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-left hover:bg-white/5 text-sm font-medium transition-colors">
                                <span>🗣️</span> Gujlish (Guj+EN)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleLangDropdown() {
            const dropdown = document.getElementById('lang-dropdown');
            dropdown.classList.toggle('hidden');
        }

        function changeLang(lang) {
            document.getElementById('lang-pref-input').value = lang;
            document.getElementById('lang-form').submit();
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            const container = document.getElementById('lang-selector-container');
            const dropdown = document.getElementById('lang-dropdown');
            if (container && !container.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Custom Styled SweetAlert Mixin for CareerPrep Pro
        const CareerPrepSwal = Swal.mixin({
            background: '#0a0e27',
            color: '#f3f4f6',
            customClass: {
                popup: 'border border-white/10 rounded-3xl shadow-2xl glassmorphism text-center',
                title: 'text-white font-bold text-xl pt-2',
                htmlContainer: 'text-gray-300 text-sm py-4',
                confirmButton: 'rounded-xl px-6 py-2.5 bg-gradient-to-r from-brandCyan to-brandPurple text-white font-bold hover:opacity-90 transition-all focus:outline-none focus:ring-2 focus:ring-brandCyan/50 cursor-pointer my-2'
            },
            buttonsStyling: false
        });

        // Override native window.alert with CareerPrepSwal
        window.alert = function(message) {
            if (message === null || message === undefined) return;
            const hasNewLine = typeof message === 'string' && message.includes('\n');
            const lowerMsg = String(message).toLowerCase();
            
            let icon = 'info';
            let title = 'Notification';
            
            if (lowerMsg.includes('success') || lowerMsg.includes('verified') || lowerMsg.includes('successful')) {
                icon = 'success';
                title = 'Success';
            } else if (lowerMsg.includes('fail') || lowerMsg.includes('error') || lowerMsg.includes('invalid') || lowerMsg.includes('not match')) {
                icon = 'error';
                title = 'Error';
            } else if (lowerMsg.includes('warning') || lowerMsg.includes('please') || lowerMsg.includes('first') || lowerMsg.includes('must be') || lowerMsg.includes('select')) {
                icon = 'warning';
                title = 'Warning';
            }
            
            CareerPrepSwal.fire({
                title: title,
                icon: icon,
                [hasNewLine ? 'html' : 'text']: hasNewLine ? message.replace(/\n/g, '<br>') : message,
                confirmButtonText: 'OK'
            });
        };

        // Flash Session Alerts
        @if(session('success') || session('login_success') || session('logout_success'))
            CareerPrepSwal.fire({
                icon: 'success',
                title: 'Success',
                text: @json(session('success') ?? session('login_success') ?? session('logout_success')),
                confirmButtonText: 'OK'
            });
        @endif

        @if(session('error'))
            CareerPrepSwal.fire({
                icon: 'error',
                title: 'Error',
                text: @json(session('error')),
                confirmButtonText: 'OK'
            });
        @endif

        @if(session('warning'))
            CareerPrepSwal.fire({
                icon: 'warning',
                title: 'Warning',
                text: @json(session('warning')),
                confirmButtonText: 'OK'
            });
        @endif

        @if(session('info'))
            CareerPrepSwal.fire({
                icon: 'info',
                title: 'Information',
                text: @json(session('info')),
                confirmButtonText: 'OK'
            });
        @endif
    </script>

    @yield('scripts')
</body>
</html>
