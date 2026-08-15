<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OtpVerification extends Model
{
    protected $fillable = ['email', 'otp_hash', 'purpose', 'payload', 'expires_at', 'consumed_at'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'consumed_at' => 'datetime'];
    }

    /**
     * Generate a fresh 6-digit OTP for the given email/purpose, invalidating
     * any earlier unconsumed codes, and return the plain-text code (the only
     * time it exists in plain text -- it's stored hashed).
     */
    public static function issue(string $email, string $purpose, ?array $payload = null): string
    {
        static::where('email', $email)->where('purpose', $purpose)->whereNull('consumed_at')->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        static::create([
            'email' => $email,
            'otp_hash' => Hash::make($code),
            'purpose' => $purpose,
            'payload' => $payload ? json_encode($payload) : null,
            'expires_at' => now()->addMinutes(5),
        ]);

        return $code;
    }

    /**
     * Verify a submitted code. Returns the matching (still-valid) record on
     * success, or null on failure (wrong code, expired, or already used).
     */
    public static function verify(string $email, string $purpose, string $code): ?self
    {
        $record = static::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        if (! $record || ! Hash::check($code, $record->otp_hash)) {
            return null;
        }

        return $record;
    }

    public function markConsumed(): void
    {
        $this->update(['consumed_at' => now()]);
    }

    public function decodedPayload(): array
    {
        return $this->payload ? json_decode($this->payload, true) : [];
    }
}
