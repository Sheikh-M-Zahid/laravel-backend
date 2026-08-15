<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#FAF6EC; padding:32px; color:#26261F;">
    <div style="max-width:480px; margin:0 auto; background:#fff; border:1px solid #DED4BC; border-radius:10px; padding:32px;">
        <h2 style="color:#1F4D2C; margin-top:0;">🌾 Smart Agri-Advisory Platform</h2>

        <h3 style="color:#26261F; margin-bottom:6px;">{{ $heading }}</h3>
        <div style="color:#5C5A4E; font-size:0.95rem; line-height:1.5;">
            {!! $bodyHtml !!}
        </div>

        @if ($ctaText && $ctaUrl)
            <a href="{{ $ctaUrl }}"
               style="display:inline-block; margin-top:20px; background:#1F4D2C; color:#fff; text-decoration:none; padding:11px 22px; border-radius:8px; font-weight:600;">
                {{ $ctaText }}
            </a>
        @endif

        <p style="color:#9a9686; font-size:0.78rem; margin-top:28px;">
            You're receiving this because of activity on your Smart Agri-Advisory Platform account.
        </p>
    </div>
</body>
</html>
