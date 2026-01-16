<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\DB;

class OtpService
{
    /**
     * OTP expiry time in minutes
     */
    const OTP_EXPIRY_MINUTES = 10;

    /**
     * OTP length
     */
    const OTP_LENGTH = 6;

    /**
     * Generate and send OTP to customer
     */
    public function generateAndSend(Customer $customer): bool
    {
        try {
            DB::beginTransaction();

            // Invalidate all previous OTPs for this customer
            CustomerOtp::where('customer_id', $customer->id)
                ->where('is_used', false)
                ->update(['is_used' => true]);

            // Generate new OTP
            $otp = $this->generateOtp();

            // Store OTP
            CustomerOtp::create([
                'customer_id' => $customer->id,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
                'is_used' => false,
            ]);

            // Send OTP via email
            $customer->notify(new SendOtpNotification($otp));

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('OTP Generation Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify OTP
     */
    public function verify(Customer $customer, string $otp): bool
    {
        $otpRecord = CustomerOtp::where('customer_id', $customer->id)
            ->where('otp', $otp)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            return false;
        }

        // Mark OTP as used
        $otpRecord->markAsUsed();

        // Mark email as verified if not already
        if (is_null($customer->email_verified_at)) {
            $customer->update(['email_verified_at' => now()]);
        }

        return true;
    }

    /**
     * Generate a random OTP
     */
    protected function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Clean up expired OTPs (optional, can be run via scheduled task)
     */
    public function cleanupExpiredOtps(): int
    {
        return CustomerOtp::where('expires_at', '<', now())
            ->orWhere('is_used', true)
            ->where('created_at', '<', now()->subDays(7))
            ->delete();
    }
}