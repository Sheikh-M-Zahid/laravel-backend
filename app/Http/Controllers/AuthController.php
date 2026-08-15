<?php

namespace App\Http\Controllers;

use App\Models\OtpVerification;
use App\Services\BrevoMailService;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
        }

        $user = Auth::user();

        // The 3 founding Super Admin emails (config/super_admins.php) are
        // bootstrapped here, the first time they log in -- they always get
        // in, regardless of what role they registered under or whether
        // that role would normally need admin approval first.
        if ($user->isFoundingSuperAdmin() && (! $user->is_super_admin || $user->status !== 'active')) {
            $user->update(['is_super_admin' => true, 'is_admin' => true, 'status' => 'active', 'restricted_until' => null]);
        }

        // A time-boxed restriction ("suspend for N days") auto-lifts itself
        // once it's past due, instead of needing the admin to remember to
        // come back and reactivate the account.
        if ($user->status === 'suspended' && $user->restricted_until && $user->restricted_until->isPast()) {
            $user->update(['status' => 'active', 'restricted_until' => null]);
        }

        // Catch pending/suspended accounts here with a friendly message,
        // rather than letting them fall through to EnsureRole's raw 403.
        if ($user->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = match (true) {
                $user->status === 'pending' => "Your account is still pending admin approval. You'll be able to log in once an admin approves it.",
                $user->status === 'suspended' && $user->restricted_until => "Your account is restricted until {$user->restricted_until->format('d M Y, h:i A')}. Please contact an admin if you think this is a mistake.",
                default => 'Your account has been suspended. Please contact an admin for help.',
            };

            return back()->withErrors(['email' => $message]);
        }

        $request->session()->regenerate();

        return $this->redirectForRole($user);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Step 1 of registration: validate the form, stash it as a pending
     * payload (password already hashed), email a 6-digit OTP, and send the
     * person to /verify-email. No user row is created yet.
     */
    public function register(Request $request, BrevoMailService $brevo)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:farmer,extension_officer,supplier'],
            'business_name' => ['required_if:role,supplier', 'nullable', 'string'],
            'business_address' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'business_name' => $request->business_name,
            'business_address' => $request->business_address,
        ];

        $code = OtpVerification::issue($request->email, 'register', $payload);
        $sent = $brevo->sendOtp($request->email, $code, 'register');

        $request->session()->put('pending_registration_email', $request->email);

        $status = $sent
            ? "We've sent a 6-digit code to {$request->email}. It expires in 5 minutes."
            : "We generated your code but the email couldn't be sent right now. Check storage/logs/laravel.log, fix the mail setup, then use \"Resend code\" below.";

        return redirect()->route('verify-email', ['email' => $request->email])->with('status', $status);
    }

    public function showVerifyEmail(Request $request)
    {
        $email = $request->query('email') ?? $request->session()->get('pending_registration_email');
        if (! $email) {
            return redirect()->route('register')->withErrors([
                'email' => 'Your verification session expired or was lost (e.g. after navigating back). Please sign up again — it only takes a moment.',
            ]);
        }
        return view('auth.verify-email', ['email' => $email]);
    }

    /**
     * Step 2 of registration: check the OTP, and only now create the User
     * (+ Supplier row if applicable) from the stashed payload.
     */
    public function verifyEmail(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $record = OtpVerification::verify($data['email'], 'register', $data['otp']);
        if (! $record) {
            return back()->withErrors(['otp' => 'That code is incorrect or has expired. Request a new one below.'])
                ->withInput(['email' => $data['email']]);
        }

        $payload = $record->decodedPayload();

        // If this email previously belonged to a removed account, don't
        // silently auto-activate them again (even a farmer would normally
        // skip approval) -- force manual admin review so the admin can look
        // at why the old account was removed before deciding.
        $hadPriorRemoval = User::onlyTrashed()->where('removed_original_email', $payload['email'])->exists();
        $status = ($payload['role'] === 'farmer' && ! $hadPriorRemoval) ? 'active' : 'pending';

        $user = User::create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone' => $payload['phone'],
            'password' => $payload['password'], // already hashed in register()
            'role' => $payload['role'],
            'status' => $status,
        ]);

        if ($payload['role'] === 'supplier') {
            Supplier::create([
                'user_id' => $user->id,
                'business_name' => $payload['business_name'],
                'business_address' => $payload['business_address'],
                'verified' => false,
            ]);
        }

        $record->markConsumed();
        $request->session()->forget('pending_registration_email');

        if ($status === 'pending') {
            return redirect()->route('login')->with(
                'status', 'Email verified! An admin must approve your account before you can log in.'
            );
        }

        Auth::login($user);
        return $this->redirectForRole($user);
    }

    public function resendVerifyEmail(Request $request, BrevoMailService $brevo)
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        // Re-issue only if there's a still-pending registration for this email.
        $existing = OtpVerification::where('email', $data['email'])
            ->where('purpose', 'register')->whereNull('consumed_at')->latest()->first();

        if (! $existing) {
            return redirect()->route('register')->withErrors(['email' => 'That registration has expired. Please sign up again.']);
        }

        $code = OtpVerification::issue($data['email'], 'register', $existing->decodedPayload());
        $brevo->sendOtp($data['email'], $code, 'register');

        return back()->with('status', "A new code was sent to {$data['email']}.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Supports the "Join as <role>" flow on the roles hub: a logged-in
        // user who wants a second account (e.g. a farmer who is also a
        // supplier) logs out first, then lands straight on the register
        // form with that role pre-selected instead of just the login page.
        $joinRole = $request->input('join_as');
        if (in_array($joinRole, ['farmer', 'extension_officer', 'supplier'], true)) {
            return redirect()->route('register', ['role' => $joinRole]);
        }

        return redirect()->route('login');
    }

    // ---------------- Forgot / reset password (OTP-based) ----------------

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetOtp(Request $request, BrevoMailService $brevo)
    {
        $data = $request->validate(['email' => ['required', 'email', 'exists:users,email']]);

        $code = OtpVerification::issue($data['email'], 'password_reset');
        $brevo->sendOtp($data['email'], $code, 'password_reset');

        return redirect()->route('password.reset', ['email' => $data['email']])->with(
            'status', "We've sent a 6-digit reset code to {$data['email']}. It expires in 5 minutes."
        );
    }

    public function showResetPassword(Request $request)
    {
        $email = $request->query('email');
        if (! $email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'That reset link is missing your email. Please request a new code.',
            ]);
        }
        return view('auth.reset-password', ['email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = OtpVerification::verify($data['email'], 'password_reset', $data['otp']);
        if (! $record) {
            return back()->withErrors(['otp' => 'That code is incorrect or has expired. Request a new one.'])
                ->withInput(['email' => $data['email']]);
        }

        $user = User::where('email', $data['email'])->firstOrFail();
        $user->update(['password' => Hash::make($data['password'])]);
        $record->markConsumed();

        return redirect()->route('login')->with('status', 'Password reset. You can log in with your new password now.');
    }

    public function resendResetOtp(Request $request, BrevoMailService $brevo)
    {
        $data = $request->validate(['email' => ['required', 'email', 'exists:users,email']]);

        $code = OtpVerification::issue($data['email'], 'password_reset');
        $brevo->sendOtp($data['email'], $code, 'password_reset');

        return back()->with('status', "A new code was sent to {$data['email']}.");
    }

    protected function redirectForRole(User $user)
    {
        return match ($user->role) {
            'farmer' => redirect()->route('farmer.dashboard'),
            'extension_officer' => redirect()->route('officer.dashboard'),
            'supplier' => redirect()->route('supplier.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
        };
    }
}
