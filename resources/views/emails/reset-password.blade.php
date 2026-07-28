<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .email-header {
            background: #0d6efd;
            color: #fff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header img {
            max-height: 60px;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 30px 25px;
        }
        .btn-reset {
            background: #0d6efd;
            color: #fff !important;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }
        .btn-reset:hover {
            background: #0b5ed7;
        }
        .email-footer {
            background: #f1f3f5;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <!-- Ganti src dengan path logo sekolah Anda -->
            <img src="{{ asset('assets/img/logo-school.png') }}" alt="Logo Sekolah">
            <h3 style="margin: 10px 0 0; font-weight:600;">Reset Password</h3>
        </div>
        <div class="email-body">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>
            <p>Anda menerima email ini karena kami menerima permintaan untuk mereset password akun Anda.</p>
            <p>Klik tombol di bawah ini untuk mengatur password baru:</p>
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $url }}" class="btn-reset">Reset Password</a>
            </div>
            <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
            <p>Link reset ini akan kadaluarsa dalam 60 menit.</p>
            <hr>
            <p style="color: #6c757d; font-size: 14px;">Jika tombol tidak berfungsi, salin dan tempel URL berikut ke browser Anda:</p>
            <p style="word-break: break-all; font-size: 13px; background: #f1f3f5; padding: 10px; border-radius: 5px;">{{ $url }}</p>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>