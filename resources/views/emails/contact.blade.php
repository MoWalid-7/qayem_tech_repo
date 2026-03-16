<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f7f9;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .content {
            padding: 40px;
        }

        .info-card {
            background: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 0 8px 8px 0;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
        }

        .info-value {
            color: #1e293b;
            font-size: 16px;
        }

        .message-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            color: #334155;
            white-space: pre-wrap;
        }

        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            background: #f1f5f9;
        }

        .brand {
            color: #3b82f6;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>QayemTech</h1>
            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">Contact Form Submission</p>
        </div>
        <div class="content">
            <h2 style="margin-top: 0; color: #0f172a; font-size: 20px;">New Message Received</h2>
            <p style="color: #64748b;">You have a new submission from the website contact form.</p>

            <div class="info-card">
                <div class="info-item">
                    <div class="info-label">Sender Name</div>
                    <div class="info-value">{{ $name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><a href="mailto:{{ $email }}" style="color: #3b82f6; text-decoration: none;">{{ $email }}</a></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Subject</div>
                    <div class="info-value">{{ $subject }}</div>
                </div>
            </div>

            <div class="info-label" style="margin-bottom: 8px;">Message Content</div>
            <div class="message-box">
                {{ $messageBody }}
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} <a href="{{ config('app.url') }}" class="brand">QayemTech</a>. All rights reserved.
            <br>
            Leading the way in AI-driven HR technology.
        </div>
    </div>
</body>

</html>