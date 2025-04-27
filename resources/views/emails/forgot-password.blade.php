<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .container {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }

        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Reset Your Password</h2>
        <p>Hello,</p>
        <p>We received a request to reset your password. If you didn't make this request, you can safely ignore this
            email.</p>
        <p>To reset your password, please click the button below:</p>
        <a href="http://localhost:8000/api/reset-password-form/{{ $token }}" class="button">Reset Password</a>
        <p>This link will expire in 60 minutes.</p>
        <div class="footer">
            <p>If you didn't request a password reset, please ignore this email or contact support if you have concerns.
            </p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>