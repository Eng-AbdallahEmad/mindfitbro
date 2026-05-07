<!DOCTYPE html>
<html lang="en" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Reset Your Password</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Arial, sans-serif;
            background-color: #f0f4ff;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f0f4ff;font-family:'Inter','Segoe UI',Tahoma,Arial,sans-serif;direction:ltr;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0f4ff;min-height:100vh;">
    <tr>
        <td align="center" style="padding:40px 16px;">

            {{-- ═══════════════ CARD ═══════════════ --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:580px;width:100%;">

                {{-- ── HEADER ── --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#1a3fa0 0%,#174DAD 50%,#1e5dc4 100%);border-radius:24px 24px 0 0;padding:40px 40px 0 40px;text-align:center;overflow:hidden;position:relative;">

                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="padding-bottom:28px;">
                                    {{-- Logo --}}
                                    <img src="{{ $message->embed(public_path('assets/logo/mindfitbro.png')) }}"
                                         alt="MindFitBro"
                                         width="180"
                                         style="display:block;margin:0 auto;max-width:180px;height:auto;">
                                </td>
                            </tr>
                            <tr>
                                <td style="background:rgba(255,255,255,0.08);border-radius:20px 20px 0 0;padding:32px 32px 36px;">

                                    {{-- Lock icon circle --}}
                                    <table cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 20px;">
                                        <tr>
                                            <td style="background:#D4ED57;border-radius:50%;width:68px;height:68px;text-align:center;vertical-align:middle;">
                                                <span style="font-size:32px;line-height:68px;display:block;">🔐</span>
                                            </td>
                                        </tr>
                                    </table>

                                    <h1 style="color:#ffffff;font-size:26px;font-weight:900;margin:0 0 10px;line-height:1.3;">
                                        Reset Your Password
                                    </h1>
                                    <p style="color:rgba(255,255,255,0.7);font-size:14px;margin:0;line-height:1.7;">
                                        Hi <strong style="color:#D4ED57;">{{ $name }}</strong>, we received a request to reset your password.
                                    </p>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- ── BODY ── --}}
                <tr>
                    <td style="background:#ffffff;padding:40px;">

                        {{-- Message --}}
                        <p style="color:#374151;font-size:15px;line-height:1.8;margin:0 0 28px;text-align:left;">
                            Click the button below to set a new password for your account.
                            This link is valid for <strong style="color:#174DAD;">{{ $expireMinutes }} minutes</strong> only.
                        </p>

                        {{-- CTA Button --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $url }}"
                                       style="display:inline-block;background:#D4ED57;color:#1c1c1c;font-size:16px;font-weight:900;text-decoration:none;padding:16px 48px;border-radius:50px;font-family:'Inter','Segoe UI',Tahoma,Arial,sans-serif;letter-spacing:0.3px;">
                                        Reset Password →
                                    </a>
                                </td>
                            </tr>
                        </table>

                        {{-- Divider --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
                            <tr>
                                <td style="border-top:2px dashed #e5e7eb;font-size:0;line-height:0;">&nbsp;</td>
                            </tr>
                        </table>

                        {{-- Alt link --}}
                        <p style="color:#6b7280;font-size:13px;line-height:1.7;margin:0 0 8px;text-align:left;">
                            If the button doesn't work, copy and paste this link into your browser:
                        </p>
                        <p style="background:#f3f4f6;border-radius:10px;padding:12px 16px;margin:0 0 28px;word-break:break-all;text-align:left;">
                            <a href="{{ $url }}" style="color:#174DAD;font-size:12px;text-decoration:none;font-family:monospace;">{{ $url }}</a>
                        </p>

                        {{-- Warning Box --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="background:#fffbeb;border:1px solid #fde68a;border-radius:14px;padding:16px 20px;">
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td style="vertical-align:top;padding-right:12px;width:28px;">
                                                <span style="font-size:18px;">⚠️</span>
                                            </td>
                                            <td style="vertical-align:top;">
                                                <p style="color:#92400e;font-size:13px;font-weight:700;margin:0 0 4px;">Security Notice</p>
                                                <p style="color:#b45309;font-size:12px;line-height:1.7;margin:0;">
                                                    If you did not request a password reset, please ignore this email — your account is safe and no changes have been made.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                {{-- ── FOOTER ── --}}
                <tr>
                    <td style="background:#1c1c2e;border-radius:0 0 24px 24px;padding:28px 40px;text-align:center;">

                        {{-- Stats row --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
                            <tr>
                                <td align="center" style="padding:0 8px;">
                                    <table cellpadding="0" cellspacing="0" border="0" style="background:rgba(255,255,255,0.06);border-radius:12px;padding:12px 20px;display:inline-table;">
                                        <tr>
                                            <td style="text-align:center;">
                                                <p style="color:#D4ED57;font-size:18px;font-weight:900;margin:0;">🔒</p>
                                                <p style="color:rgba(255,255,255,0.5);font-size:11px;margin:4px 0 0;">SSL 256-bit</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td align="center" style="padding:0 8px;">
                                    <table cellpadding="0" cellspacing="0" border="0" style="background:rgba(255,255,255,0.06);border-radius:12px;padding:12px 20px;display:inline-table;">
                                        <tr>
                                            <td style="text-align:center;">
                                                <p style="color:#D4ED57;font-size:18px;font-weight:900;margin:0;">⏱️</p>
                                                <p style="color:rgba(255,255,255,0.5);font-size:11px;margin:4px 0 0;">{{ $expireMinutes }} min only</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td align="center" style="padding:0 8px;">
                                    <table cellpadding="0" cellspacing="0" border="0" style="background:rgba(255,255,255,0.06);border-radius:12px;padding:12px 20px;display:inline-table;">
                                        <tr>
                                            <td style="text-align:center;">
                                                <p style="color:#D4ED57;font-size:18px;font-weight:900;margin:0;">🎯</p>
                                                <p style="color:rgba(255,255,255,0.5);font-size:11px;margin:4px 0 0;">One-time link</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <p style="color:rgba(255,255,255,0.3);font-size:12px;margin:0 0 6px;">
                            © {{ date('Y') }} MindFitBro — All Rights Reserved
                        </p>
                        <p style="color:rgba(255,255,255,0.2);font-size:11px;margin:0;">
                            "Not a program, it's a lifestyle"
                        </p>

                    </td>
                </tr>

            </table>
            {{-- ═══════════════ END CARD ═══════════════ --}}

        </td>
    </tr>
</table>

</body>
</html>
