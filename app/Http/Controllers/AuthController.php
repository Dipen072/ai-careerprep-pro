<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function processRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'user_type' => 'required|in:fresher,experienced',
            'language_preference' => 'required|in:en,hi,gu,hi_en,gu_en',
            'skills' => 'nullable|array',
        ]);

        if (!$request->session()->get('otp_verified') || $request->session()->get('register_otp_mobile') !== $request->mobile) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your mobile OTP code first.'
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'role' => 'student', // default student, can login to dashboard
            'language_preference' => $request->language_preference,
            'skills' => $request->skills ?? [],
            'xp_points' => 100, // starting bonus
            'streak' => 1,
            'last_activity_at' => now(),
        ]);

        // Create starting badge
        $user->badges()->create([
            'badge_name' => 'Welcome aboard',
            'badge_icon' => '🌟',
            'description' => 'Signed up on AI CareerPrep Pro!',
        ]);

        Auth::login($user);

        return response()->json(['success' => true, 'redirect' => route('dashboard')]);
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function processLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            // Update activity & streak
            $user = Auth::user();
            $lastActivity = $user->last_activity_at;
            if ($lastActivity) {
                $diff = now()->diffInDays($lastActivity);
                if ($diff == 1) {
                    $user->increment('streak');
                } elseif ($diff > 1) {
                    $user->update(['streak' => 1]);
                }
            }
            $user->update(['last_activity_at' => now()]);

            return redirect()->intended(route('dashboard'))->with('login_success', 'Login successful! Welcome back to your Dashboard!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('logout_success', 'Logout successful!');
    }

    // Google mock login form view
    public function googleLoginMockForm()
    {
        return view('auth.google_mock');
    }

    // Google mock login submit handler
    public function googleLoginMockSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
        ]);

        // If a user with this email already exists, log them in
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Check if mobile is already in use by another user
            $existingMobile = User::where('mobile', $request->mobile)->first();
            if ($existingMobile) {
                return back()->withErrors([
                    'mobile' => 'This mobile number is already linked to another account.'
                ])->withInput();
            }

            // Create new dynamic mock user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => Hash::make('google-secret-pass-' . rand(1000, 9999)),
                'role' => 'student',
                'language_preference' => 'en',
                'user_type' => 'fresher',
                'skills' => ['PHP', 'Laravel', 'MySQL'],
                'xp_points' => 100,
                'streak' => 1,
                'last_activity_at' => now()
            ]);

            // Add starting badge
            $user->badges()->create([
                'badge_name' => 'Google Connected',
                'badge_icon' => '🌐',
                'description' => 'Successfully connected google account to CareerPrep Pro!',
            ]);
        }

        Auth::login($user);
        return redirect()->route('dashboard')->with('login_success', 'Login successful! Welcome back to your Dashboard!');
    }

    public function sendOtpSms(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|max:15',
        ]);

        // Check if mobile number is already taken
        $existing = User::where('mobile', $request->mobile)->first();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Mobile number already registered.'], 422);
        }

        $otp = rand(100000, 999999);
        $request->session()->put('register_otp', $otp);
        $request->session()->put('register_otp_mobile', $request->mobile);
        $request->session()->put('otp_verified', false); // reset verification flag

        $message = "Your CareerPrep Pro verification code is: {$otp}";
        
        $sent = $this->sendSmsViaGateway($request->mobile, $message, $otp);

        if ($sent) {
            // Also log to local log file for testing convenience
            Log::info("OTP SMS sent to {$request->mobile}: {$message}");
            return response()->json(['success' => true, 'otp' => $otp]); // Return OTP for convenience/UI display if needed, but it will also go via SMS
        }

        return response()->json(['success' => false, 'message' => 'Failed to send SMS OTP.'], 500);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
            'mobile' => 'required|string|max:15',
        ]);

        $sessionOtp = $request->session()->get('register_otp');
        $sessionMobile = $request->session()->get('register_otp_mobile');

        if ($sessionOtp && (string)$sessionOtp === (string)$request->otp && $sessionMobile === $request->mobile) {
            $request->session()->put('otp_verified', true);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
    }

    private function sendSmsViaGateway($mobile, $message, $otp)
    {
        $gateway = env('SMS_GATEWAY', 'mock'); // twilio, fast2sms, mock

        // Format mobile number for Twilio (requires country code like +91)
        $formattedMobile = $mobile;
        if (!str_starts_with($formattedMobile, '+')) {
            if (strlen($formattedMobile) === 10) {
                $formattedMobile = '+91' . $formattedMobile; // Default to India country code
            } else {
                $formattedMobile = '+' . $formattedMobile;
            }
        }

        if ($gateway === 'fast2sms') {
            $apiKey = env('FAST2SMS_API_KEY');
            if (!$apiKey) {
                Log::warning('Fast2SMS API key not set in .env');
                return false;
            }

            // Using Fast2SMS Quick SMS or OTP route
            $response = Http::withHeaders([
                'authorization' => $apiKey
            ])->post('https://www.fast2sms.com/dev/bulkV2', [
                'route' => 'otp',
                'variables_values' => (string)$otp,
                'numbers' => $mobile,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Fast2SMS Gateway Error: ' . $response->body());
            return false;
        }

        if ($gateway === 'twilio') {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $from = env('TWILIO_NUMBER');

            if (!$sid || !$token || !$from) {
                Log::warning('Twilio credentials not set in .env');
                return false;
            }

            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'To' => $formattedMobile,
                    'From' => $from,
                    'Body' => $message
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Twilio Gateway Error: ' . $response->body());
            return false;
        }

        // Mock mode (default)
        return true;
    }

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.forgot-password');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No account found with this email address.'], 404);
        }

        $otp = rand(100000, 999999);
        $request->session()->put('reset_otp', $otp);
        $request->session()->put('reset_otp_email', $request->email);
        $request->session()->put('reset_otp_verified', false);

        // Send Email using Mail facade
        $sent = false;
        try {
            Mail::html(
                "Your CareerPrep Pro password reset OTP code is: <strong>{$otp}</strong>",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Password Reset OTP - CareerPrep Pro');
                }
            );
            $sent = true;
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
        }

        if ($sent || env('MAIL_MAILER') === 'log' || env('MAIL_MAILER') === 'mock') {
            Log::info("Password reset OTP sent to {$request->email}: {$otp}");
            return response()->json(['success' => true, 'otp' => $otp]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to send reset email.'], 500);
    }

    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $sessionOtp = $request->session()->get('reset_otp');
        $sessionEmail = $request->session()->get('reset_otp_email');

        if ($sessionOtp && (string)$sessionOtp === (string)$request->otp && $sessionEmail === $request->email) {
            $request->session()->put('reset_otp_verified', true);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $sessionOtp = $request->session()->get('reset_otp');
        $sessionEmail = $request->session()->get('reset_otp_email');
        $verified = $request->session()->get('reset_otp_verified');

        if (!$verified || (string)$sessionOtp !== (string)$request->otp || $sessionEmail !== $request->email) {
            return response()->json(['success' => false, 'message' => 'Security check failed. Please verify OTP again.'], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Clear session keys
        $request->session()->forget(['reset_otp', 'reset_otp_email', 'reset_otp_verified']);

        return response()->json(['success' => true]);
    }
}
