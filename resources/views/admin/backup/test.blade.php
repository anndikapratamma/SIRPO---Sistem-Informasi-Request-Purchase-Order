<!DOCTYPE html>
<html>
<head>
    <title>Backup Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .success { color: green; background: #e8f5e8; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="success">
            <h1>✅ Backup Management - Berhasil Dimuat!</h1>
            <p>Controller dan view backup bekerja dengan baik.</p>
        </div>

        <h2>Informasi Debug:</h2>
        <ul>
            <li>Route: {{ request()->route()->getName() }}</li>
            <li>URL: {{ request()->fullUrl() }}</li>
            <li>Method: {{ request()->method() }}</li>
            <li>Timestamp: {{ now() }}</li>
        </ul>

        @if(Auth::check())
            <h3>User Login:</h3>
            <ul>
                <li>Nama: {{ Auth::user()->name }}</li>
                <li>Role: {{ Auth::user()->role }}</li>
                <li>Email: {{ Auth::user()->email }}</li>
            </ul>
        @else
            <p><strong>User belum login</strong></p>
        @endif

        <div style="margin-top: 20px;">
            <a href="{{ route('dashboard') }}" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
