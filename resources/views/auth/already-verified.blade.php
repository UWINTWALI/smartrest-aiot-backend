<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Already Verified</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .info-icon {
            width: 80px;
            height: 80px;
            background: #4299e1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .info-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }

        h1 {
            color: #2d3748;
            font-size: 28px;
            margin-bottom: 20px;
        }

        p {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .user-info {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: left;
        }

        .user-info p {
            margin-bottom: 10px;
            color: #4a5568;
        }

        .user-info strong {
            color: #2d3748;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #4299e1;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #3182ce;
            transform: translateY(-2px);
        }

        .permissions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .permission-tag {
            background: #e2e8f0;
            color: #4a5568;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #c6f6d5;
            color: #2f855a;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="info-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1>Email Already Verified</h1>
        <div class="status-badge">✓ Verified Account</div>
        <p>Your email has already been verified. You can access all features of your account.</p>

        <div class="user-info">
            <p><strong>Name:</strong> {{ $data->first_name }} {{ $data->last_name }}</p>
            <p><strong>Email:</strong> {{ $data->email }}</p>
            <p><strong>Role:</strong> {{ ucfirst($data->role) }}</p>
            @if($data->phone)
                <p><strong>Phone:</strong> {{ $data->phone }}</p>
            @endif
        </div>

        <div class="permissions">
            @foreach($data->permissions as $permission)
                <span class="permission-tag">{{ $permission }}</span>
            @endforeach
        </div>

        <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/login" class="btn">Proceed to Login</a>
    </div>
</body>

</html>