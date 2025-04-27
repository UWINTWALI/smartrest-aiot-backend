<!DOCTYPE html>
<html>

<head>
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <h2>Email Verification</h2>
    <p>Hello {{ $user->first_name }} {{ $user->last_name }},</p>
    <p>Thank you for registering. Please click the button below to verify your email address:</p>

    <a href="{{ $verificationUrl }}" class="button">Verify Email Address</a>

    <p>If you did not create an account, no further action is required.</p>

    <div class="footer">
        <p>This email was sent to {{ $user->email }}.</p>
        <p>If you have any questions, please contact our support team.</p>
    </div>
</body>

</html>