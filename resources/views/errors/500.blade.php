<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error 500 - Terjadi Kesalahan Internal | SIMTAN PTPN IV</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-ptpn4.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #fafafa;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow: hidden;
            transition: background 0.3s ease;
        }
        html.dark body {
            background: #060818;
            color: #f1f5f9;
        }
        .container {
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            padding: 48px 32px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        html.dark .glass-card {
            background: rgba(14, 23, 41, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .logo-box {
            width: 80px;
            height: 80px;
            background: #ffffff;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.03);
            margin-bottom: 32px;
            transition: transform 0.3s ease;
        }
        html.dark .logo-box {
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .logo-box:hover {
            transform: scale(1.05);
        }
        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .error-code {
            font-size: 96px;
            font-weight: 900;
            font-style: italic;
            line-height: 1;
            letter-spacing: -0.05em;
            background: linear-gradient(135deg, #059669 0%, #0d9488 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        .error-title {
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #0f172a;
            margin-bottom: 12px;
        }
        html.dark .error-title {
            color: #ffffff;
        }
        .error-desc {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 36px;
        }
        html.dark .error-desc {
            color: #94a3b8;
        }
        .btn-back {
            display: inline-block;
            width: 100%;
            max-width: 240px;
            padding: 14px 28px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            text-decoration: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #059669 0%, #0d9488 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 10px 20px -5px rgba(13, 148, 136, 0.4) !important;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -5px rgba(13, 148, 136, 0.5) !important;
            filter: brightness(1.05);
        }
        .btn-back:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <div class="container">
        <div class="glass-card">
            <!-- Logo Box -->
            <div class="logo-box">
                <img src="{{ asset('assets/images/logo-ptpn4.png') }}" alt="PTPN IV Logo" class="logo-img">
            </div>

            <!-- Error Code -->
            <div class="error-code">500</div>

            <!-- Error Title -->
            <h2 class="error-title">Terjadi Kesalahan Internal</h2>

            <!-- Error Description -->
            <p class="error-desc">Tim teknis kami sedang menanganinya.</p>

            <!-- Back Button -->
            <a href="{{ url('/') }}" class="btn-back">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>
