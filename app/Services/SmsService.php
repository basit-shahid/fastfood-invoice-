<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send OTP to the given phone number.
     *
     * @param string $phone
     * @param string $otp
     * @return bool
     */
    public function sendOtp($phone, $otp)
    {
        $driver = config('services.sms.driver', 'log');
        $message = "Your OTP for login is: $otp";

        if ($driver === 'termux') {
            return $this->sendViaTermux($phone, $message);
        }

        // Default to log driver
        Log::info("SMS to $phone: $message");
        return true;
    }

    /**
     * Send SMS using Termux API.
     *
     * @param string $phone
     * @param string $message
     * @return bool
     */
    protected function sendViaTermux($phone, $message)
    {
        // Sanitize inputs
        $phone = escapeshellarg($phone);
        $message = escapeshellarg($message);

        // Execute Termux command
        $command = "termux-sms-send -n $phone $message 2>&1";
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::error("Termux SMS failed: " . implode("\n", $output));
            return false;
        }

        return true;
    }
}
