<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteTitle }} Artist Account</title>
</head>
<body style="margin:0;background:#f5f3ef;color:#151821;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f3ef;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border:1px solid #e5e0d8;">
                    <tr>
                        <td style="background:#111111;padding:24px 30px;">
                            @if ($siteLogoUrl)
                                <img src="{{ $siteLogoUrl }}" alt="{{ $siteTitle }}" style="display:block;max-height:58px;width:auto;filter:invert(1);">
                            @else
                                <div style="color:#ffffff;font-size:12px;font-weight:800;letter-spacing:4px;text-transform:uppercase;">{{ $siteTitle }}</div>
                            @endif
                            <div style="margin-top:8px;color:#cfcfcf;font-size:13px;">Artist account approved</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 30px;">
                            <h1 style="margin:0 0 18px;color:#111111;font-family:Georgia,'Times New Roman',serif;font-size:30px;line-height:1.2;">Welcome to your artist dashboard</h1>
                            <p style="margin:0 0 18px;color:#4b5563;font-size:16px;line-height:1.7;">Dear {{ $artist->name }},</p>
                            <p style="margin:0 0 24px;color:#4b5563;font-size:16px;line-height:1.7;">Your artist profile has been approved. You can now log in and upload artworks for review.</p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid #e5e7eb;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:13px 16px;background:#f9fafb;color:#6b7280;font-size:13px;">Login email</td>
                                    <td align="right" style="padding:13px 16px;background:#f9fafb;color:#111827;font-size:13px;font-weight:700;">{{ $artist->email }}</td>
                                </tr>
                                @if ($temporaryPassword)
                                    <tr>
                                        <td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Temporary password</td>
                                        <td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $temporaryPassword }}</td>
                                    </tr>
                                @endif
                            </table>

                            <a href="{{ $loginUrl }}" style="display:inline-block;background:#111111;color:#ffffff;font-size:13px;font-weight:800;letter-spacing:1px;padding:14px 20px;text-decoration:none;text-transform:uppercase;">Log in to dashboard</a>

                            <p style="margin:24px 0 0;color:#4b5563;font-size:15px;line-height:1.7;">@if ($temporaryPassword) For security, please change this password after your first login. @else Use your existing account password to sign in. @endif For help, reply to {{ $supportEmail }}.</p>
                            <p style="margin:24px 0 0;color:#111111;font-size:15px;line-height:1.7;">Regards,<br><strong>{{ $siteTitle }}</strong></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
