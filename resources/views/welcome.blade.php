<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIRPO - Login & Register</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: "Poppins", sans-serif;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            width: 900px;
            max-width: 100%;
            min-height: 550px;
            transition: all 0.3s ease;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            transition: all 0.6s ease-in-out;
            overflow-y: auto;
            padding: 20px 0;
        }

        .login-container {
            left: 0;
            z-index: 2;
        }

        .register-container {
            left: 0;
            opacity: 0;
            z-index: 1;
        }

        .container.right-panel-active .login-container {
            transform: translateX(100%);
        }

        .container.right-panel-active .register-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        @keyframes show {
            0%, 49.99% {
                opacity: 0;
                z-index: 1;
            }
            50%, 100% {
                opacity: 1;
                z-index: 5;
            }
        }

        form {
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            min-height: 100%;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .logo-img {
            height: 80px;
            margin-bottom: 10px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .logo-slogan {
            font-size: 14px;
            color: #6b7280;
            font-style: italic;
        }

        h1 {
            font-weight: 700;
            letter-spacing: -1px;
            margin: 0 0 20px 0;
            color: #1e293b;
            font-size: 24px;
        }

        .form-group {
            position: relative;
            width: 100%;
            margin: 10px 0;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
        }

        input, select {
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px 15px;
            width: 100%;
            font-size: 14px;
            font-family: "Poppins", sans-serif;
            transition: all 0.3s ease;
            outline: none;
            color: #111827;
        }

        input::placeholder {
            color: #9ca3af;
        }

        input:focus, select:focus {
            border-color: #3b82f6;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        select option {
            background-color: #fff;
            color: #111827;
            padding: 8px 12px;
        }

        .validation-text {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
            opacity: 0.8;
        }

        .error-text {
            font-size: 11px;
            color: #ef4444;
            margin-top: 5px;
        }

        button {
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            margin: 15px 0;
            padding: 12px 40px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-family: "Poppins", sans-serif;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }

        .auth-link {
            margin-top: 15px;
            font-size: 13px;
        }

        .auth-link a {
            color: #3b82f6;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .auth-link a:hover {
            text-decoration: underline;
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        .overlay {
            background: linear-gradient(135deg, #fcfcfc 0%, #3b82f6 100%);
            color: #fff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
            overflow: hidden;
            z-index: 10;
        }

        .overlay-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            opacity: 0.3;
        }

        .overlay-content {
            position: relative;
            z-index: 10;
            color: #fff;
            text-align: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            backdrop-filter: blur(5px);
        }

        .overlay-left {
            transform: translateX(-20%);
        }

        .container.right-panel-active .overlay-left {
            transform: translateX(0);
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }

        .overlay h1 {
            font-size: 28px;
            color: #fff;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            font-weight: 700;
            position: relative;
            z-index: 15;
        }

        .overlay p {
            font-size: 14px;
            margin: 10px 0 20px;
            opacity: 0.95;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            line-height: 1.5;
            position: relative;
            z-index: 15;
        }

        .overlay-btn {
            background: transparent;
            border: 2px solid #fff;
            color: #fff;
            padding: 12px 35px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-transform: uppercase;
            margin-top: 15px;
            position: relative;
            z-index: 15;
        }

        .overlay-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        /* Custom scrollbar */
        .form-container::-webkit-scrollbar {
            width: 6px;
        }

        .form-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .form-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .form-container::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        @media (max-width: 768px) {
            .container {
                width: 100%;
                min-height: 600px;
                margin: 10px;
            }

            .form-container {
                width: 100%;
                padding: 15px 0;
            }

            form {
                padding: 0 30px;
            }

            .overlay-container {
                display: none;
            }

            .login-container {
                transform: translateX(0);
            }

            .container.right-panel-active .login-container {
                transform: translateX(100%);
            }

            .register-container {
                transform: translateX(100%);
            }

            .container.right-panel-active .register-container {
                transform: translateX(0);
            }
        }

        @media (max-width: 480px) {
            form {
                padding: 0 20px;
            }

            .logo-img {
                height: 60px;
            }

            .logo-text {
                font-size: 20px;
            }

            button {
                padding: 10px 30px;
                font-size: 13px;
            }
/* ovelay vidio */



        }
    </style>
</head>
<body>
    <div class="container" id="container">
        <!-- Register Form -->
        <div class="form-container register-container">
            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf
                <div class="logo-container">
                    <img src="{{ asset('logologin.png') }}" alt="Logo" class="logo-img" id="logoPlaceholder">

                </div>
                <h1>Daftar Akun</h1>

                <div class="form-group">
                    <label for="register-nik">NIK</label>
                    <input type="text" id="register-nik" name="nik" value="{{ old('nik') }}" placeholder="Masukkan Nomor Induk Karyawan (NIK)" required>
                    <div class="validation-text">Masukkan Nomor Induk Karyawan (NIK)</div>
                    @error('nik')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="register-name">Nama Lengkap</label>
                    <input type="text" id="register-name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                    @error('name')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="register-role">Role</label>
                    <select id="register-role" name="role" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="register-password">Password</label>
                    <input type="password" id="register-password" name="password" placeholder="Masukkan password" required>
                    @error('password')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="register-confirm-password">Konfirmasi Password</label>
                    <input type="password" id="register-confirm-password" name="password_confirmation" placeholder="Konfirmasi password" required>
                </div>

                <button type="submit">
                    DAFTAR
                </button>

                <div class="auth-link">
                    Sudah punya akun? <a href="#" id="loginLink">Masuk disini</a>
                </div>
            </form>
        </div>


        <!-- Login Form -->
        <div class="form-container login-container">
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                <div class="logo-container">
                    <img src="{{ asset('images/logologin.png') }}" alt="Logo" class="logo-img" id="logoPlaceholder">

                </div>
                <h1>Masuk</h1>

                <div class="form-group">
                    <label for="login-nik">NIK</label>
                    <input type="text" id="login-nik" name="nik" value="{{ old('nik') }}" placeholder="Masukkan NIK" required>
                    @error('nik')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" placeholder="Masukkan password" required>
                    @error('password')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit">
                    LOG IN
                </button>

                <div class="auth-link">
                    Belum punya akun? <a href="#" id="registerLink">Daftar disini</a>
                </div>
            </form>
        </div>

        <!-- Overlay -->
            <div class="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <video autoplay muted loop playsinline class="overlay-video">
                            <source src="{{ asset('overlay.mp4') }}" type="video/mp4">
                        </video>
                        <div class="overlay-content">
                            <h1>Selamat Datang!</h1>
                            <p>Untuk tetap terhubung dengan kami, silakan masuk dengan info pribadi Anda</p>
                            <button class="overlay-btn" id="login">
                                Masuk
                            </button>
                        </div>
                    </div>
                    <div class="overlay-panel overlay-right">
                        <video autoplay muted loop playsinline class="overlay-video">
                            <source src="{{ asset('overlay.mp4') }}" type="video/mp4">
                        </video>
                        <div class="overlay-content">

                            <h1>Halo, Kawan!</h1>
                            <p>Masukkan detail pribadi Anda dan mulai perjalanan dengan kami</p>
                            <button class="overlay-btn" id="register">
                                Daftar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script>
        const registerButton = document.getElementById("register");
        const loginButton = document.getElementById("login");
        const loginLinkButton = document.getElementById("loginLink");
        const registerLinkButton = document.getElementById("registerLink");
        const container = document.getElementById("container");

        registerButton.addEventListener("click", () => {
            container.classList.add("right-panel-active");
        });

        loginButton.addEventListener("click", () => {
            container.classList.remove("right-panel-active");
        });

        loginLinkButton.addEventListener("click", (e) => {
            e.preventDefault();
            container.classList.remove("right-panel-active");
        });

        registerLinkButton.addEventListener("click", (e) => {
            e.preventDefault();
            container.classList.add("right-panel-active");
        });

        // Replace placeholder logo with your actual logo
        document.addEventListener('DOMContentLoaded', function() {
            // Replace this with your actual logo path
            const logoPath = "{{ asset('logologin.png') }}";
            const logoPlaceholders = document.querySelectorAll('.logo-img');

            logoPlaceholders.forEach(logo => {
                logo.src = logoPath;
                logo.alt = 'BRK Syariah Logo';
            });
        });

        // Show register panel if there are validation errors
        @if($errors->any() && old('name'))
            container.classList.add("right-panel-active");
        @endif
    </script>
</body>
</html>
