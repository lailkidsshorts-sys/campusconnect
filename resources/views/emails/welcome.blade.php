<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to CampusConnect</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #F8FAFC; margin: 0; padding: 24px; color: #0F172A;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #FFFFFF; border-radius: 12px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #1E3A8A; padding: 24px; text-align: center; color: #FFFFFF;">
                            <h2 style="margin: 0; font-size: 24px; letter-spacing: 0.5px;">CampusConnect</h2>
                            <p style="margin: 6px 0 0; font-size: 13px; color: #93C5FD;">Smart Campus Complaint & Feedback Management</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding: 32px 28px;">
                            <h3 style="color: #1E3A8A; margin-top: 0;">Welcome, {{ $name }}!</h3>
                            <p style="font-size: 15px; line-height: 1.6; color: #334155;">
                                Your student account has been successfully created on <strong>CampusConnect</strong>. You can now submit complaints, track department resolution timelines, and rate service quality across university facilities.
                            </p>
                            <table width="100%" style="background-color: #F1F5F9; border-radius: 8px; padding: 14px; margin: 20px 0; font-size: 14px;">
                                <tr>
                                    <td style="color: #64748B; width: 120px;"><strong>Roll Number:</strong></td>
                                    <td style="color: #0F172A; font-family: monospace;">{{ $roll_no }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748B;"><strong>Registered Email:</strong></td>
                                    <td style="color: #0F172A;">{{ $email }}</td>
                                </tr>
                            </table>
                            <div style="text-align: center; margin-top: 28px;">
                                <a href="{{ $login_url }}" style="background-color: #1E3A8A; color: #FFFFFF; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 15px; display: inline-block;">
                                    Access Student Portal
                                </a>
                            </div>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F8FAFC; padding: 16px; text-align: center; border-top: 1px solid #E2E8F0; font-size: 12px; color: #94A3B8;">
                            &copy; {{ date('Y') }} CampusConnect Automated Notification System. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
