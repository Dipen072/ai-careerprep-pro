@extends('layouts.auth')

@section('content')
<div class="glassmorphism rounded-3xl p-8 relative overflow-hidden transition-all duration-300">
    
    <!-- Progress Indicator -->
    <div class="mb-6">
        <div class="flex justify-between items-center text-xs text-gray-400 font-semibold mb-2">
            <span id="step-label">Step 1 of 3: Enter Email</span>
            <span id="step-percentage">33%</span>
        </div>
        <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden">
            <div id="progress-indicator" class="bg-gradient-to-r from-brandCyan to-brandPurple h-full transition-all duration-500" style="width: 33%;"></div>
        </div>
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-brandCyan/20 text-brandCyan border border-brandCyan/30 mb-3 animate-pulse">
            <i class="fa-solid fa-key text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold tracking-tight" id="main-heading">Forgot Password</h2>
        <p class="text-gray-400 text-sm mt-1" id="sub-heading">Reset your account password via email verification</p>
    </div>

    <!-- STEP 1: Enter Email -->
    <div id="step-1" class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Email Address</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fa-regular fa-envelope"></i>
                </span>
                <input type="email" id="email" required class="block w-full pl-10 pr-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors" placeholder="name@example.com">
            </div>
        </div>

        <button type="button" onclick="sendResetOtp()" class="w-full py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-xl font-semibold text-white shadow-lg transition-all flex justify-center items-center">
            Send Reset OTP <i class="fa-solid fa-paper-plane ml-2 text-sm"></i>
        </button>
    </div>

    <!-- STEP 2: Enter OTP -->
    <div id="step-2" class="hidden space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Enter Verification Code</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input type="text" id="otp-code" required maxlength="6" class="block w-full pl-10 pr-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors tracking-widest text-center text-lg" placeholder="••••••">
            </div>
            <p class="text-xs text-gray-400 mt-2 text-center">We've sent a 6-digit verification code to your email.</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <button type="button" onclick="goToStep(1)" class="py-3 bg-white/10 hover:bg-white/15 rounded-xl font-semibold text-gray-300 transition-colors flex justify-center items-center">
                <i class="fa-solid fa-arrow-left mr-2 text-sm"></i> Back
            </button>
            <button type="button" onclick="verifyResetOtp()" class="py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-xl font-semibold text-white shadow-lg transition-opacity flex justify-center items-center">
                Verify Code <i class="fa-solid fa-shield-check ml-2 text-sm"></i>
            </button>
        </div>
    </div>

    <!-- STEP 3: Reset Password -->
    <div id="step-3" class="hidden space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">New Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input type="password" id="new-password" required class="block w-full pl-10 pr-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors" placeholder="••••••••">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirm New Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input type="password" id="new-password-confirm" required class="block w-full pl-10 pr-3 py-2.5 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:border-brandCyan text-white transition-colors" placeholder="••••••••">
            </div>
        </div>

        <button type="button" onclick="resetPassword()" class="w-full py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-xl font-semibold text-white shadow-lg transition-all flex justify-center items-center">
            Reset Password <i class="fa-solid fa-circle-check ml-2 text-sm"></i>
        </button>
    </div>

    <!-- Footer -->
    <div class="mt-8 text-center text-sm">
        <a href="{{ route('login') }}" class="text-brandCyan hover:text-brandCyan/80 font-semibold underline decoration-brandCyan/30">Back to Sign In</a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentStep = 1;
    let savedEmail = '';
    let savedOtp = '';

    function goToStep(step) {
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        document.getElementById(`step-${step}`).classList.remove('hidden');
        currentStep = step;

        const progressIndicator = document.getElementById('progress-indicator');
        const stepLabel = document.getElementById('step-label');
        const stepPercentage = document.getElementById('step-percentage');
        const mainHeading = document.getElementById('main-heading');
        const subHeading = document.getElementById('sub-heading');

        if (step === 1) {
            progressIndicator.style.width = '33%';
            stepLabel.innerText = 'Step 1 of 3: Enter Email';
            stepPercentage.innerText = '33%';
            mainHeading.innerText = 'Forgot Password';
            subHeading.innerText = 'Reset your account password via email verification';
        } else if (step === 2) {
            progressIndicator.style.width = '66%';
            stepLabel.innerText = 'Step 2 of 3: Verification';
            stepPercentage.innerText = '66%';
            mainHeading.innerText = 'Verify Code';
            subHeading.innerText = 'Enter the 6-digit code sent to your inbox';
        } else if (step === 3) {
            progressIndicator.style.width = '100%';
            stepLabel.innerText = 'Step 3 of 3: Reset Password';
            stepPercentage.innerText = '100%';
            mainHeading.innerText = 'Set New Password';
            subHeading.innerText = 'Choose a secure new password for your account';
        }
    }

    function sendResetOtp() {
        const emailInput = document.getElementById('email');
        const email = emailInput.value.trim();

        if (!email) {
            alert('Please enter your email address first!');
            return;
        }

        fetch("{{ route('forgot-password.send') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify({ email: email })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                savedEmail = email;
                alert("Reset OTP sent to " + email + "! (For local testing/dev, check storage/logs/laravel.log or code is: " + data.otp + ")");
                goToStep(2);
            } else {
                alert(data.message || "Failed to send reset code.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("An error occurred while sending OTP.");
        });
    }

    function verifyResetOtp() {
        const otpInput = document.getElementById('otp-code');
        const otp = otpInput.value.trim();

        if (otp.length !== 6) {
            alert('Please enter a 6-digit OTP code!');
            return;
        }

        fetch("{{ route('forgot-password.verify') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify({ email: savedEmail, otp: otp })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                savedOtp = otp;
                alert("OTP verified successfully!");
                goToStep(3);
            } else {
                alert(data.message || "Invalid or expired verification code.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("An error occurred while verifying OTP.");
        });
    }

    function resetPassword() {
        const password = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('new-password-confirm').value;

        if (!password || password.length < 6) {
            alert('Password must be at least 6 characters long!');
            return;
        }

        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }

        fetch("{{ route('forgot-password.reset') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify({
                email: savedEmail,
                otp: savedOtp,
                password: password,
                password_confirmation: confirmPassword
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Password reset successfully! Please log in with your new password.");
                window.location.href = "{{ route('login') }}";
            } else {
                alert(data.message || "Failed to reset password.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("An error occurred while resetting password.");
        });
    }
</script>
@endsection
