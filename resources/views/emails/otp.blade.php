<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#FAF6EC; padding:32px; color:#26261F;">
    <div style="max-width:440px; margin:0 auto; background:#fff; border:1px solid #DED4BC; border-radius:10px; padding:32px;">
        <h2 style="color:#1F4D2C; margin-top:0;">🌾 Smart Agri-Advisory Platform</h2>

        @if ($purpose === 'register')
            <p>Use the code below to verify your email and finish creating your account.</p>
        @else
            <p>Use the code below to reset your password.</p>
        @endif

        <div style="font-size:32px; font-weight:700; letter-spacing:8px; color:#1F4D2C; background:#F1EADA; padding:16px 0; text-align:center; border-radius:8px; margin:20px 0;">
            {{ $code }}
        </div>

        <p style="color:#5C5A4E; font-size:0.9rem;">This code expires in <strong>5 minutes</strong>. If you didn't request this, you can safely ignore this email.</p>
    </div>
</body>
</html>
