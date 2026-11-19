<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Complaint Assigned</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #F8FAFC; margin: 0; padding: 24px; color: #0F172A;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #FFFFFF; border-radius: 12px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <tr>
                        <td style="background-color: #0F172A; padding: 22px; text-align: center; color: #FFFFFF;">
                            <h2 style="margin: 0; font-size: 22px;">CampusConnect Administration</h2>
                            <p style="margin: 4px 0 0; font-size: 13px; color: #94A3B8;">New Complaint Routed to Your Department</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 28px 24px;">
                            <p style="font-size: 15px; margin-top: 0; color: #334155;">Attention: <strong>{{ $department }}</strong>,</p>
                            <p style="font-size: 14px; line-height: 1.5; color: #334155;">
                                A new complaint has been submitted by student <strong>{{ $student_name }}</strong> and assigned to your department queue.
                            </p>
                            <table width="100%" cellpadding="8" cellspacing="0" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; margin: 18px 0; font-size: 13.5px;">
                                <tr style="border-bottom: 1px solid #E2E8F0;">
                                    <td style="color: #64748B; width: 140px;"><strong>Reference No:</strong></td>
                                    <td style="color: #1E3A8A; font-family: monospace; font-weight: bold;">{{ $reference_no }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #E2E8F0;">
                                    <td style="color: #64748B;"><strong>Subject:</strong></td>
                                    <td style="color: #0F172A;">{{ $subject }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #E2E8F0;">
                                    <td style="color: #64748B;"><strong>Category:</strong></td>
                                    <td style="color: #0F172A;">{{ $category }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #E2E8F0;">
                                    <td style="color: #64748B;"><strong>Priority:</strong></td>
                                    <td style="color: #0F172A;">{{ $priority }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748B;"><strong>Assigned:</strong></td>
                                    <td style="color: #0F172A;">{{ $submitted_at }}</td>
                                </tr>
                            </table>
                            <div style="text-align: center; margin-top: 24px;">
                                <a href="{{ $view_url }}" style="background-color: #1E3A8A; color: #FFFFFF; padding: 11px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block;">
                                    Open Complaint & Take Action
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F8FAFC; padding: 14px; text-align: center; border-top: 1px solid #E2E8F0; font-size: 12px; color: #94A3B8;">
                            &copy; {{ date('Y') }} CampusConnect Automated Routing System
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
