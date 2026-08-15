<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AppNotification;
use App\Models\User;

/**
 * Sends transactional email through Brevo's HTTP API
 * (https://api.brevo.com/v3/smtp/email) instead of SMTP. Used for the
 * registration and password-reset OTP codes.
 *
 * Required .env values:
 *   BREVO_API_KEY=xkeysib-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 *   BREVO_SENDER_EMAIL=you@yourdomain.com   (must be a *verified* sender in Brevo)
 *   BREVO_SENDER_NAME="Smart Agri-Advisory Platform"
 */
class BrevoMailService
{
    protected string $endpoint = 'https://api.brevo.com/v3/smtp/email';

    /**
     * Send a 6-digit OTP email for either 'register' or 'password_reset'.
     * Returns true on success, false on failure (and logs the API response).
     */
    public function sendOtp(string $toEmail, string $code, string $purpose): bool
    {
        $subject = $purpose === 'register'
            ? 'Verify your email — Smart Agri-Advisory Platform'
            : 'Reset your password — Smart Agri-Advisory Platform';

        $html = view('emails.otp', ['code' => $code, 'purpose' => $purpose])->render();

        return $this->send($toEmail, $subject, $html);
    }

    /**
     * Send a general-purpose transactional notification (account approval,
     * an officer/supplier "response" to a farmer's request, etc.) using the
     * shared emails.notification template.
     */
    public function sendNotification(
        string $toEmail,
        string $subject,
        string $heading,
        string $bodyHtml,
        ?string $ctaText = null,
        ?string $ctaUrl = null
    ): bool {
        $html = view('emails.notification', [
            'heading' => $heading,
            'bodyHtml' => $bodyHtml,
            'ctaText' => $ctaText,
            'ctaUrl' => $ctaUrl,
        ])->render();

        return $this->send($toEmail, $subject, $html);
    }

    /**
     * Notify a platform user (currently used for Extension Officer → farmer
     * messages) through BOTH channels at once: a transactional email, and a
     * row in app_notifications so it also shows up in the navbar
     * notification bell. Either channel failing doesn't block the other.
     */
    public function notifyUser(
        User $user,
        string $subject,
        string $heading,
        string $bodyHtml,
        ?string $ctaText = null,
        ?string $ctaUrl = null
    ): bool {
        AppNotification::create([
            'user_id' => $user->id,
            'title' => $heading,
            'body' => strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $bodyHtml)),
            'url' => $ctaUrl,
        ]);

        return $this->sendNotification($user->email, $subject, $heading, $bodyHtml, $ctaText, $ctaUrl);
    }

    protected function send(string $toEmail, string $subject, string $html): bool
    {        $apiKey = config('services.brevo.key');

        if (! $apiKey) {
            Log::warning('Brevo API key not configured — email not sent.', ['to' => $toEmail]);
            return false;
        }

        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($this->endpoint, [
            'sender' => [
                'name' => config('services.brevo.sender_name'),
                'email' => config('services.brevo.sender_email'),
            ],
            'to' => [
                ['email' => $toEmail],
            ],
            'subject' => $subject,
            'htmlContent' => $html,
        ]);

        if ($response->failed()) {
            Log::error('Brevo email send failed', [
                'to' => $toEmail,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        }

        return true;
    }
}
