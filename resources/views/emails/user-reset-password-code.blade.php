<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode Reset Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 32px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #1461D2;
        }
        .code-box {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
        .code {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 4px;
            color: #1e293b;
        }
        .footer {
            font-size: 12px;
            color: #64748b;
            text-align: center;
            margin-top: 32px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">BLUD Pariwisata</div>
        </div>
        <p>Halo,</p>
        <p>Kami menerima permintaan untuk mereset password akun BLUD Pariwisata Anda. Gunakan kode di bawah ini untuk melanjutkan proses reset password:</p>
        
        <div class="code-box">
            <div class="code">{{ $code }}</div>
        </div>
        
        <p>Kode ini berlaku selama <strong>15 menit</strong>. Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini.</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} BLUD Pariwisata. All rights reserved.
        </div>
    </div>
</body>
</html>
