<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi Email – SI-ASIH</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f0f4f8;
            color: #1a202c;
        }
        .wrapper {
            max-width: 580px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            padding: 40px 40px 32px;
            text-align: center;
        }
        .header .logo {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 1px;
        }
        .header .logo span {
            color: #a8d4ff;
        }
        .header p {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            margin-top: 6px;
        }
        .body {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 12px;
        }
        .description {
            font-size: 14px;
            color: #4a5568;
            line-height: 1.7;
            margin-bottom: 32px;
        }
        .code-container {
            background: linear-gradient(135deg, #f0f7ff, #e6f0ff);
            border: 2px dashed #0d6efd;
            border-radius: 12px;
            padding: 28px 20px;
            text-align: center;
            margin-bottom: 28px;
        }
        .code-label {
            font-size: 12px;
            font-weight: 600;
            color: #0d6efd;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 12px;
        }
        .code {
            font-size: 48px;
            font-weight: 800;
            color: #0a58ca;
            letter-spacing: 12px;
            font-family: 'Courier New', monospace;
        }
        .expire-note {
            font-size: 12px;
            color: #718096;
            margin-top: 12px;
        }
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 28px 0; }
        .warning {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 6px;
            padding: 14px 16px;
            font-size: 13px;
            color: #78350f;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .footer {
            background: #f7fafc;
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            font-size: 12px;
            color: #a0aec0;
            line-height: 1.6;
        }
        .footer a { color: #0d6efd; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="logo">SI<span>-</span>ASIH</div>
            <p>Sistem Informasi Aset dan Sumber Daya Desa Cibeusi</p>
        </div>
        <div class="body">
            <div class="greeting">Halo, {{ $name }}!</div>
            <p class="description">
                Terima kasih sudah mendaftar di <strong>SI-ASIH</strong>. Untuk mengaktifkan akun Anda,
                masukkan kode verifikasi di bawah ini pada halaman verifikasi.
            </p>

            <div class="code-container">
                <div class="code-label">Kode Verifikasi Anda</div>
                <div class="code">{{ $code }}</div>
                <div class="expire-note">Kode berlaku selama <strong>30 menit</strong></div>
            </div>

            <div class="warning">
                <strong>Penting:</strong> Jangan bagikan kode ini kepada siapapun.
                Tim SI-ASIH tidak akan pernah meminta kode verifikasi Anda.
                Jika Anda tidak mendaftar, abaikan email ini.
            </div>

            <hr class="divider">

            <p style="font-size: 13px; color: #718096; line-height: 1.6;">
                Kode ini dikirimkan karena ada permintaan registrasi menggunakan alamat email ini.
                Jika Anda tidak merasa mendaftar, Anda dapat mengabaikan email ini.
            </p>
        </div>
        <div class="footer">
            <p>
                &copy; {{ date('Y') }} <strong>SI-ASIH</strong> – BUMDes Cipta Asih, Desa Cibeusi<br>
                Email ini dikirim secara otomatis, mohon tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>
