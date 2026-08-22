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
            border-left: 4px solid #4f46e5;
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

        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            background: #f1f5f9;
        }

        .brand {
            color: #4f46e5;
            font-weight: bold;
            text-decoration: none;
        }

        .btn-login {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Evalo</h1>
            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">Your Account Credentials</p>
        </div>
        <div class="content">
            <h2 style="margin-top: 0; color: #0f172a; font-size: 20px;">Welcome to Evalo, {{ $name }}!</h2>
            <p style="color: #64748b;">Your subscription has been processed successfully. Below are the administrative credentials created for your workspace:</p>

            <div class="info-card">
                <div class="info-item">
                    <div class="info-label">Login Email</div>
                    <div class="info-value">{{ $email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Temporary Password</div>
                    <div class="info-value" style="font-family: monospace; font-size: 18px; font-weight: bold; color: #4f46e5;">{{ $password }}</div>
                </div>
            </div>

            <p style="color: #64748b; font-size: 14px;">We highly recommend updating your password immediately after logging in for the first time.</p>

            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="btn-login">Go to Login Workspace</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} <a href="{{ config('app.url') }}" class="brand">Evalo</a>. All rights reserved.
            <br>
            Leading the way in AI-driven HR technology.
        </div>
    </div>
</body>

</html>
