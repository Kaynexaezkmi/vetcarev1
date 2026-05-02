<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found - {{ config('app.name', 'VetCare') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logovet.jpg') }}">
    <style>
        body {
            align-items: center;
            background: #f8fafc;
            color: #0f172a;
            display: flex;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            justify-content: center;
            margin: 0;
            min-height: 100vh;
            padding: 24px;
        }

        .not-found-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
            max-width: 420px;
            padding: 28px;
            text-align: center;
            width: 100%;
        }

        .not-found-logo {
            border-radius: 16px;
            height: 76px;
            margin-bottom: 18px;
            object-fit: contain;
            width: 76px;
        }

        .not-found-code {
            color: #f97316;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            font-size: 24px;
            line-height: 1.2;
            margin: 8px 0;
        }

        p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 22px;
        }

        a {
            background: #ff5a00;
            border-radius: 8px;
            color: #fff;
            display: inline-flex;
            font-size: 14px;
            font-weight: 700;
            padding: 10px 16px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <main class="not-found-card">
        <img src="{{ asset('images/logovet.jpg') }}" alt="{{ config('app.name', 'VetCare') }}" class="not-found-logo">
        <div class="not-found-code">404</div>
        <h1>Page not found</h1>
        <p>The page you opened does not exist or may have been moved.</p>
        <a href="{{ auth()->check() ? route('dashboard') : route('home') }}">Go back home</a>
    </main>
</body>
</html>
