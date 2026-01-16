<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerAuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show registration form
     */
    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    /**
     * Handle customer registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $customer = Customer::create($validator->validated());

        return redirect()->route('customer.login')
            ->with('success', 'Registration successful! Please login with your email.');
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    /**
     * Send OTP to customer email
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:customers,email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return back()->withErrors(['email' => 'Email not found.'])->withInput();
        }

        if ($this->otpService->generateAndSend($customer)) {
            session(['customer_email' => $customer->email]);
            
            return redirect()->route('customer.verify.otp')
                ->with('success', 'OTP sent to your email successfully!');
        }

        return back()->withErrors(['email' => 'Failed to send OTP. Please try again.'])->withInput();
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtpForm()
    {
        if (!session('customer_email')) {
            return redirect()->route('customer.login')
                ->withErrors(['error' => 'Please enter your email first.']);
        }

        return view('customer.auth.verify-otp');
    }

    /**
     * Verify OTP and login customer
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $email = session('customer_email');
        if (!$email) {
            return redirect()->route('customer.login')
                ->withErrors(['error' => 'Session expired. Please try again.']);
        }

        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            return back()->withErrors(['otp' => 'Invalid session.']);
        }

        if ($this->otpService->verify($customer, $request->otp)) {
            Auth::guard('customer')->login($customer, $request->boolean('remember'));
            session()->forget('customer_email');

            $request->session()->regenerate();

            return redirect()->intended(route('customer.dashboard'))
                ->with('success', 'Login successful!');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $email = session('customer_email');
        
        if (!$email) {
            return redirect()->route('customer.login')
                ->withErrors(['error' => 'Session expired. Please try again.']);
        }

        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            return back()->withErrors(['error' => 'Customer not found.']);
        }

        if ($this->otpService->generateAndSend($customer)) {
            return back()->with('success', 'OTP resent successfully!');
        }

        return back()->withErrors(['error' => 'Failed to resend OTP.']);
    }

    /**
     * Logout customer
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login')
            ->with('success', 'Logged out successfully!');
    }
}