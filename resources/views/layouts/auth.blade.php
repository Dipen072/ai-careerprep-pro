<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI CareerPrep Pro — Join Now</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CDN for high-fidelity styling -->
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
            opacity: 0.35;
            z-index: 0;
            pointer-events: none;
        }
        .glassmorphism {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex items-center justify-center relative py-12 px-4 sm:px-6 lg:px-8">
    
    <!-- Glowing aesthetic background elements -->
    <div class="glow-circle w-[400px] h-[400px] bg-brandPurple top-[-100px] left-[-100px]"></div>
    <div class="glow-circle w-[500px] h-[500px] bg-brandCyan bottom-[-150px] right-[-150px]"></div>
    
    <div class="w-full max-w-lg z-10">
        @yield('content')
    </div>

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
