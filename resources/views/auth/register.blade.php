@extends('layouts.auth')

@section('content')
<div class="glassmorphism rounded-3xl p-8 relative overflow-hidden transition-all duration-300">
    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex justify-between mb-2">
            <span class="text-sm font-semibold text-brandCyan" id="step-label">Step 1 of 3: Basic Info</span>
            <span class="text-sm font-medium text-gray-400" id="step-percentage">33%</span>
        </div>
        <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
            <div id="progress-indicator" class="bg-gradient-to-r from-brandCyan to-brandPurple h-full w-1/3 transition-all duration-300"></div>
        </div>
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-brandPurple/20 text-brandPurple border border-brandPurple/30 mb-3">
            <i class="fa-solid fa-graduation-cap text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold tracking-tight">Create Your Account</h2>
        <p class="text-gray-400 text-sm mt-1">Start your AI-powered career journey</p>
    </div>

    <!-- Form -->
    <form id="registerForm" method="POST" class="space-y-6">
        @csrf

        <!-- STEP 1: Basic Info -->
        <div id="step-1" class="space-y-5 transition-all duration-300">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Full Name</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fa-regular fa-user"></i>
                    </span>
                    <input type="text" name="name" required class="block w-full pl-10 pr-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors" placeholder="Dipen Patel">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fa-regular fa-envelope"></i>
                    </span>
                    <input type="email" name="email" required class="block w-full pl-10 pr-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors" placeholder="name@example.com">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Mobile Number</label>
                <div class="relative flex">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fa-solid fa-phone"></i>
                    </span>
                    <input type="tel" name="mobile" required class="block w-full pl-10 pr-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors" placeholder="9876543210">
                    <button type="button" onclick="sendOtp()" class="ml-2 px-3 py-2 bg-brandCyan/20 hover:bg-brandCyan/30 border border-brandCyan/30 rounded-xl text-brandCyan text-sm font-semibold transition-colors shrink-0">
                        Send OTP
                    </button>
                </div>
            </div>

            <!-- OTP input box (hidden by default, revealed on Send OTP click) -->
            <div id="otp-container" class="hidden">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Verify OTP</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fa-solid fa-key"></i>
                    </span>
                    <input type="text" id="otp-input" class="block w-full pl-10 pr-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors" placeholder="Enter 6-digit OTP">
                    <span id="otp-status" class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-semibold text-emerald-400 hidden">
                        <i class="fa-solid fa-circle-check mr-1"></i> Verified
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
                    <input type="password" name="password" required class="block w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors" placeholder="••••••">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="block w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors" placeholder="••••••">
                </div>
            </div>

            <button type="button" onclick="nextStep(2)" class="w-full py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-xl font-semibold text-white shadow-lg transition-opacity flex justify-center items-center">
                Next Step <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
            </button>
        </div>

        <!-- STEP 2: Skills & Experience -->
        <div id="step-2" class="hidden space-y-6 transition-all duration-300">
            <!-- Experience Level Card Selectors -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-3">I am a:</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex flex-col items-center justify-center p-4 bg-white/5 border border-white/10 rounded-2xl cursor-pointer hover:bg-white/10 transition-colors" id="label-fresher">
                        <input type="radio" name="user_type" value="fresher" checked class="sr-only" onchange="toggleExperienceCard()">
                        <div class="w-10 h-10 rounded-xl bg-brandCyan/20 text-brandCyan flex items-center justify-center mb-2 border border-brandCyan/30">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <span class="font-semibold text-white">Student / Fresher</span>
                        <span class="text-xs text-gray-400 text-center mt-1">Starting my career</span>
                    </label>

                    <label class="relative flex flex-col items-center justify-center p-4 bg-white/5 border border-white/10 rounded-2xl cursor-pointer hover:bg-white/10 transition-colors" id="label-experienced">
                        <input type="radio" name="user_type" value="experienced" class="sr-only" onchange="toggleExperienceCard()">
                        <div class="w-10 h-10 rounded-xl bg-brandPurple/20 text-brandPurple flex items-center justify-center mb-2 border border-brandPurple/30">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <span class="font-semibold text-white">Professional</span>
                        <span class="text-xs text-gray-400 text-center mt-1">Experienced developer</span>
                    </label>
                </div>
            </div>

            <!-- Skills Multiselect Chips -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Select Your Tech Stack (Choose at least 2)</label>
                <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto p-1">
                    @foreach(['PHP', 'Laravel', 'Java', 'Spring Boot', 'Python', 'Django', 'JavaScript', 'React', 'Vue.js', 'MySQL', 'MongoDB', 'AWS', 'Docker', 'Testing', 'Cyber Security'] as $skill)
                    <div onclick="toggleSkill(this, '{{ $skill }}')" class="px-3.5 py-1.5 bg-white/5 border border-white/10 hover:border-brandCyan/50 rounded-full cursor-pointer text-sm font-medium text-gray-300 transition-colors select-none">
                        {{ $skill }}
                    </div>
                    @endforeach
                </div>
                <!-- Hidden inputs for skills -->
                <div id="skills-hidden-container"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button type="button" onclick="prevStep(1)" class="py-3 bg-white/10 hover:bg-white/15 rounded-xl font-semibold text-gray-300 transition-colors flex justify-center items-center">
                    <i class="fa-solid fa-arrow-left mr-2 text-sm"></i> Back
                </button>
                <button type="button" onclick="nextStep(3)" class="py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-xl font-semibold text-white shadow-lg transition-opacity flex justify-center items-center">
                    Next Step <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                </button>
            </div>
        </div>

        <!-- STEP 3: Language Preference -->
        <div id="step-3" class="hidden space-y-6 transition-all duration-300">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-3 text-center">Select Preferred Language for AI Coaching</label>
                
                <div class="grid grid-cols-1 gap-3">
                    <!-- English Card -->
                    <label class="flex items-center justify-between p-4 bg-white/5 border border-white/10 rounded-2xl cursor-pointer hover:bg-white/10 transition-colors" id="lang-en">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🇬🇧</span>
                            <div class="text-left">
                                <p class="font-semibold text-white">English Only</p>
                                <p class="text-xs text-gray-400">AI communicates in standard English</p>
                            </div>
                        </div>
                        <input type="radio" name="language_preference" value="en" checked class="form-radio text-brandCyan focus:ring-0">
                    </label>

                    <!-- Hindi Card -->
                    <label class="flex items-center justify-between p-4 bg-white/5 border border-white/10 rounded-2xl cursor-pointer hover:bg-white/10 transition-colors" id="lang-hi">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🇮🇳</span>
                            <div class="text-left">
                                <p class="font-semibold text-white">Hindi Only (हिंदी)</p>
                                <p class="text-xs text-gray-400">AI communicates in pure Hindi</p>
                            </div>
                        </div>
                        <input type="radio" name="language_preference" value="hi" class="form-radio text-brandCyan focus:ring-0">
                    </label>

                    <!-- Gujarati Card -->
                    <label class="flex items-center justify-between p-4 bg-white/5 border border-white/10 rounded-2xl cursor-pointer hover:bg-white/10 transition-colors" id="lang-gu">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🦁</span>
                            <div class="text-left">
                                <p class="font-semibold text-white">Gujarati Only (ગુજરાતી)</p>
                                <p class="text-xs text-gray-400">AI communicates in pure Gujarati</p>
                            </div>
                        </div>
                        <input type="radio" name="language_preference" value="gu" class="form-radio text-brandCyan focus:ring-0">
                    </label>

                    <!-- Hindi + English Mixed Card -->
                    <label class="flex items-center justify-between p-4 bg-white/5 border border-white/10 rounded-2xl cursor-pointer hover:bg-white/10 transition-colors" id="lang-hi_en">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">💬</span>
                            <div class="text-left">
                                <p class="font-semibold text-white">Hindi + English (Hinglish)</p>
                                <p class="text-xs text-gray-400">AI asks questions bilingual or mixed style</p>
                            </div>
                        </div>
                        <input type="radio" name="language_preference" value="hi_en" class="form-radio text-brandCyan focus:ring-0">
                    </label>

                    <!-- Gujarati + English Mixed Card -->
                    <label class="flex items-center justify-between p-4 bg-white/5 border border-white/10 rounded-2xl cursor-pointer hover:bg-white/10 transition-colors" id="lang-gu_en">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🗣️</span>
                            <div class="text-left">
                                <p class="font-semibold text-white">Gujarati + English (Gujlish)</p>
                                <p class="text-xs text-gray-400">AI asks questions mixing Gujarati & English</p>
                            </div>
                        </div>
                        <input type="radio" name="language_preference" value="gu_en" class="form-radio text-brandCyan focus:ring-0">
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button type="button" onclick="prevStep(2)" class="py-3 bg-white/10 hover:bg-white/15 rounded-xl font-semibold text-gray-300 transition-colors flex justify-center items-center">
                    <i class="fa-solid fa-arrow-left mr-2 text-sm"></i> Back
                </button>
                <button type="submit" class="py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-90 rounded-xl font-bold text-white shadow-lg transition-opacity flex justify-center items-center">
                    <i class="fa-solid fa-circle-check mr-2 text-sm"></i> Create Account
                </button>
            </div>
        </div>
    </form>

    <!-- Divider -->
    <div class="relative my-6" id="or-divider">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-white/10"></div></div>
        <div class="relative flex justify-center text-xs uppercase"><span class="bg-darkBg px-2 text-gray-400 font-semibold">Or register with</span></div>
    </div>

    <!-- Social Signup / Sign in Redirect -->
    <div class="space-y-4" id="social-container">
        <a href="{{ route('auth.google.mock') }}" class="w-full flex items-center justify-center px-4 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-xl text-sm font-semibold transition-all duration-200">
            <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24">
                <path fill="#EA4335" d="M12 5.04c1.66 0 3.2.57 4.38 1.69l3.27-3.27C17.67 1.54 14.98 1 12 1 7.35 1 3.37 3.65 1.42 7.5l3.86 3C6.23 7.63 8.89 5.04 12 5.04z"/>
                <path fill="#4285F4" d="M23.49 12.27c0-.81-.07-1.59-.2-2.36H12v4.51h6.46c-.29 1.48-1.14 2.73-2.4 3.58l3.76 2.91c2.2-2.03 3.67-5.02 3.67-8.64z"/>
                <path fill="#FBBC05" d="M5.28 14.78c-.26-.78-.41-1.6-.41-2.46s.15-1.68.41-2.46L1.42 6.86C.51 8.7.01 10.79.01 13c0 2.21.5 4.3 1.41 6.14l3.86-3.36z"/>
                <path fill="#34A853" d="M12 22.99c3.24 0 5.97-1.07 7.96-2.91l-3.76-2.91c-1.05.7-2.4 1.13-4.2 1.13-3.11 0-5.77-2.59-6.72-5.46L1.42 16.2c1.95 3.85 5.93 6.5 10.58 6.5z"/>
            </svg>
            Continue with Google
        </a>

        <p class="text-center text-sm text-gray-400">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-brandCyan hover:text-brandCyan/80 font-semibold underline decoration-brandCyan/30">Log in</a>
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Selection state trackers
    let currentStep = 1;
    const selectedSkills = new Set();
    let isOtpVerified = false;
    let currentOtp = null;

    function sendOtp() {
        const mobile = document.querySelector('input[name="mobile"]').value;
        if (!mobile) {
            alert("Please enter your mobile number first!");
            return;
        }

        const otpContainer = document.getElementById('otp-container');
        const otpInput = document.getElementById('otp-input');
        const otpStatus = document.getElementById('otp-status');
        
        otpContainer.classList.remove('hidden');
        otpInput.value = '';
        otpInput.removeAttribute('disabled');
        otpInput.classList.remove('border-emerald-500', 'bg-emerald-500/10');
        otpStatus.classList.add('hidden');
        isOtpVerified = false;

        // AJAX POST to send-otp
        fetch("{{ route('register.send-otp') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify({ mobile: mobile })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Verification OTP sent to " + mobile + "! Check your phone or terminal. (For local testing/dev, code is logged to storage/logs/laravel.log or shown as: " + data.otp + ")");
            } else {
                alert(data.message || "Failed to send OTP. Please try again.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("An error occurred while sending OTP.");
        });
        
        // Listen to verify input
        otpInput.addEventListener('input', function() {
            if (otpInput.value.length === 6) {
                // AJAX POST to verify-otp
                fetch("{{ route('register.verify-otp') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: JSON.stringify({ otp: otpInput.value, mobile: mobile })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        otpStatus.classList.remove('hidden');
                        otpInput.setAttribute('disabled', 'true');
                        otpInput.classList.add('border-emerald-500', 'bg-emerald-500/10');
                        isOtpVerified = true;
                    }
                })
                .catch(err => {
                    console.error(err);
                });
            }
        });
    }

    function toggleSkill(element, skill) {
        if (selectedSkills.has(skill)) {
            selectedSkills.delete(skill);
            element.classList.remove('bg-brandCyan/20', 'border-brandCyan', 'text-brandCyan');
            element.classList.add('bg-white/5', 'border-white/10', 'text-gray-300');
        } else {
            selectedSkills.add(skill);
            element.classList.remove('bg-white/5', 'border-white/10', 'text-gray-300');
            element.classList.add('bg-brandCyan/20', 'border-brandCyan', 'text-brandCyan');
        }
        
        // Re-compile inputs
        const container = document.getElementById('skills-hidden-container');
        container.innerHTML = '';
        selectedSkills.forEach(s => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'skills[]';
            input.value = s;
            container.appendChild(input);
        });
    }

    function toggleExperienceCard() {
        const fresher = document.getElementById('label-fresher');
        const experienced = document.getElementById('label-experienced');
        const fresherInput = document.querySelector('input[value="fresher"]');
        
        if (fresherInput.checked) {
            fresher.classList.add('bg-brandCyan/10', 'border-brandCyan');
            experienced.classList.remove('bg-brandPurple/10', 'border-brandPurple');
        } else {
            experienced.classList.add('bg-brandPurple/10', 'border-brandPurple');
            fresher.classList.remove('bg-brandCyan/10', 'border-brandCyan');
        }
    }

    function nextStep(step) {
        // Validate inputs basic
        if (currentStep === 1) {
            const name = document.querySelector('input[name="name"]').value;
            const email = document.querySelector('input[name="email"]').value;
            const mobile = document.querySelector('input[name="mobile"]').value;
            const password = document.querySelector('input[name="password"]').value;
            const confirmation = document.querySelector('input[name="password_confirmation"]').value;
            
            if (!name || !email || !mobile || !password || !confirmation) {
                alert("Please fill all step 1 details!");
                return;
            }
            if (password !== confirmation) {
                alert("Passwords do not match!");
                return;
            }
            if (!isOtpVerified) {
                alert("Please verify your mobile number with the OTP first!");
                return;
            }
        }
        if (currentStep === 2 && step === 3) {
            if (selectedSkills.size < 2) {
                alert("Please select at least 2 skills to customize your roadmap!");
                return;
            }
        }

        // Hide current
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        
        // Show next
        document.getElementById(`step-${step}`).classList.remove('hidden');
        currentStep = step;

        // Progress bar updates
        const progressIndicator = document.getElementById('progress-indicator');
        const stepLabel = document.getElementById('step-label');
        const stepPercentage = document.getElementById('step-percentage');
        
        if (step === 1) {
            progressIndicator.style.width = '33%';
            stepLabel.innerText = 'Step 1 of 3: Basic Info';
            stepPercentage.innerText = '33%';
        } else if (step === 2) {
            progressIndicator.style.width = '66%';
            stepLabel.innerText = 'Step 2 of 3: Customize Stack';
            stepPercentage.innerText = '66%';
            toggleExperienceCard(); // style refresh
        } else if (step === 3) {
            progressIndicator.style.width = '100%';
            stepLabel.innerText = 'Step 3 of 3: Interface Language';
            stepPercentage.innerText = '100%';
            // Hide dividers on final step
            document.getElementById('or-divider').classList.add('hidden');
            document.getElementById('social-container').classList.add('hidden');
        }
    }

    function prevStep(step) {
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        document.getElementById(`step-${step}`).classList.remove('hidden');
        currentStep = step;

        const progressIndicator = document.getElementById('progress-indicator');
        const stepLabel = document.getElementById('step-label');
        const stepPercentage = document.getElementById('step-percentage');
        
        if (step === 1) {
            progressIndicator.style.width = '33%';
            stepLabel.innerText = 'Step 1 of 3: Basic Info';
            stepPercentage.innerText = '33%';
            document.getElementById('or-divider').classList.remove('hidden');
            document.getElementById('social-container').classList.remove('hidden');
        } else if (step === 2) {
            progressIndicator.style.width = '66%';
            stepLabel.innerText = 'Step 2 of 3: Customize Stack';
            stepPercentage.innerText = '66%';
        }
    }

    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch("{{ route('register.post') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Registration successful! Welcome to CareerPrep Pro!");
                window.location.href = data.redirect;
            } else {
                let errorMsg = data.message || "Please check validation rules.";
                if (data.errors) {
                    const errorDetails = Object.values(data.errors).flat().join("\n");
                    errorMsg += "\n\n" + errorDetails;
                }
                alert("Registration Failed: " + errorMsg);
            }
        })
        .catch(err => {
            console.error(err);
            alert("Errors occurred processing registration.");
        });
    });
</script>
@endsection
