<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ART Story</title>
</head>
<body style="margin:0;background:#f5f3ef;color:#151821;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f3ef;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border:1px solid #e5e0d8;">
                    <tr>
                        <td style="background:#111111;padding:24px 30px;">
                            @if (! empty($siteLogoUrl))
                                <img src="{{ $siteLogoUrl }}" alt="{{ $siteTitle ?? 'ART Story' }}" style="display:block;max-height:58px;width:auto;filter:invert(1);">
                            @else
                                <div style="color:#ffffff;font-size:12px;font-weight:800;letter-spacing:4px;text-transform:uppercase;">{{ $siteTitle ?? 'ART Story' }}</div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 30px;font-size:16px;line-height:1.7;color:#4b5563;">
                            {!! $htmlBody !!}
                        </td>
                    </tr>
                </table>
                <p style="margin:16px 0 0;color:#8a8f99;font-size:12px;">This email was sent by {{ $siteTitle ?? 'ART Story' }}.</p>
            </td>
        </tr>
    </table>
</body>
</html>
