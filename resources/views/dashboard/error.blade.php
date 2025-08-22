<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Error - SIRPO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .error-container { margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center error-container">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4>Dashboard Error</h4>
                    </div>
                    <div class="card-body">
                        <p>Maaf, terjadi kesalahan saat memuat dashboard.</p>
                        @if(isset($error))
                            <div class="alert alert-warning">
                                <strong>Error:</strong> {{ $error }}
                            </div>
                        @endif
                        <div class="mt-3">
                            <a href="{{ route('pb.index') }}" class="btn btn-primary me-2">Lihat PB</a>
                            <a href="{{ route('profile.edit') }}" class="btn btn-secondary me-2">Profile</a>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
