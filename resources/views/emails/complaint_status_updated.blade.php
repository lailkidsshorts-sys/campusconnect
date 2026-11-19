<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Complaint Status Update</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #F8FAFC; margin: 0; padding: 24px; color: #0F172A;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #FFFFFF; border-radius: 12px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <tr>
                        <td style="background-color: #1E3A8A; padding: 22px; text-align: center; color: #FFFFFF;">
                            <h2 style="margin: 0; font-size: 22px;">CampusConnect</h2>
                            <p style="margin: 4px 0 0; font-size: 13px; color: #93C5FD;">Department Status Update</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 28px 24px;">
                            <p style="font-size: 15px; margin-top: 0; color: #334155;">Hello <strong>{{ $student_name }}</strong>,</p>
                            <p style="font-size: 14px; line-height: 1.5; color: #334155;">
                                An administrative action or status update has been recorded on your complaint by <strong>{{ $department }}</strong>.
                            </p>
                            <div style="background-color: #EFF6FF; border-left: 4px solid #2563EB; padding: 14px; margin: 16px 0; border-radius: 4px;">
                                <div style="font-size: 13px; color: #1E3A8A; font-weight: bold;">New Status: {{ $status }}</div>
                                <div style="font-size: 14px; color: #0F172A; margin-top: 6px;">
                                    <strong>Official Remark:</strong> "{{ $remark }}"
                                </div>
                            </div>
                            <table width="100%" cellpadding="6" cellspacing="0" style="font-size: 13px; color: #64748B;">
                                <tr>
                                    <td><strong>Reference No:</strong> {{ $reference_no }}</td>
                                    <td align="right"><strong>Updated:</strong> {{ $updated_at }}</td>
                                </tr>
                            </table>
                            <div style="text-align: center; margin-top: 24px;">
                                <a href="{{ $view_url }}" style="background-color: #1E3A8A; color: #FFFFFF; padding: 11px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block;">
                                    View Full Status & Timeline
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F8FAFC; padding: 14px; text-align: center; border-top: 1px solid #E2E8F0; font-size: 12px; color: #94A3B8;">
                            &copy; {{ date('Y') }} CampusConnect Notification System
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
