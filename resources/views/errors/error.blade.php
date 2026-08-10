<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $status }} — {{ $title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-width: 320px;
            color: #10213c;
            font-family: Inter, Arial, sans-serif;
        }

        .page {
            position: relative;
            display: grid;
            min-height: 100vh;
            place-items: center;
            overflow: hidden;
            padding: 24px;
            background: linear-gradient(135deg, #021024 0%, #052659 48%, #5483b3 100%);
        }

        .orb {
            position: absolute;
            border-radius: 999px;
            filter: blur(4px);
            opacity: .22;
        }

        .orb-one {
            top: -190px;
            right: -110px;
            width: 440px;
            height: 440px;
            background: #c1e8ff;
        }

        .orb-two {
            bottom: -180px;
            left: -135px;
            width: 390px;
            height: 390px;
            background: #58d4f2;
        }

        .card {
            position: relative;
            width: min(100%, 720px);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 30px;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 28px 70px rgba(0, 13, 35, .38);
            text-align: center;
        }

        .accent {
            height: 8px;
            background: linear-gradient(90deg, #7da0ca, #105192, #052659);
        }

        .content {
            padding: 46px 38px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
            color: #052659;
            font-weight: 600;
            font-size: 14px;
        }

        .brand img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .code {
            margin: 0;
            color: #052659;
            font: 700 clamp(76px, 18vw, 148px)/.9 Poppins, sans-serif;
            letter-spacing: -7px;
        }

        h1 {
            margin: 20px 0 10px;
            color: #052659;
            font: 700 clamp(23px, 4vw, 32px)/1.25 Poppins, sans-serif;
        }

        p {
            max-width: 520px;
            margin: 0 auto;
            color: #5b6b82;
            font-size: 15px;
            line-height: 1.7;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-top: 30px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 20px;
            border: 1px solid transparent;
            border-radius: 14px;
            color: #fff;
            background: #052659;
            box-shadow: 0 8px 18px rgba(5, 38, 89, .2);
            font: 600 14px Inter, sans-serif;
            text-decoration: none;
            transition: transform .18s ease, background .18s ease;
            cursor: pointer;
        }

        .button:hover {
            background: #105192;
            transform: translateY(-2px);
        }

        .button-secondary {
            border-color: #d9e2ee;
            color: #31547d;
            background: #fff;
            box-shadow: none;
        }

        .button-secondary:hover {
            background: #f2f7fb;
        }

        .footer {
            margin-top: 28px;
            color: #92a0b3;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .page {
                padding: 16px;
            }

            .content {
                padding: 36px 22px 30px;
            }

            .code {
                letter-spacing: -4px;
                text-align: center;
            }

            .actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <main class="page">
        <div class="orb orb-one"></div>
        <div class="orb orb-two"></div>

        <section class="card" aria-labelledby="error-title">
            <div class="accent"></div>
            <div class="content">
                <div class="brand">
                    <img src="{{ asset('assets/img/logo_smkn_2.png') }}" alt="Logo sekolah">
                    <span>ICT Absensi</span>
                </div>
                <p class="code" aria-label="Error {{ $status }}">{{ $status }}</p>
                <h1 id="error-title">{{ $title }}</h1>
                <p>{{ $message }}</p>

                <div class="actions">
                    <a class="button" href="{{ url('/') }}">Kembali ke beranda</a>
                    @if ($showBack ?? true)
                    <button class="button button-secondary" type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.assign('{{ url('/') }}')">Kembali ke halaman sebelumnya</button>
                    @endif
                </div>

                <div class="footer">Jika masalah berlanjut, silakan hubungi administrator.</div>
            </div>
        </section>
    </main>
</body>

</html>