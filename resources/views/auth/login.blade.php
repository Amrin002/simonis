<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Monitoring Siswa</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #4a5568;
            --secondary-color: #718096;
            --background-color: #f7fafc;
            --card-background: #ffffff;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-color: #e2e8f0;
            --error-color: #e53e3e;
            --success-color: #48bb78;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
        }

        .login-card {
            background: var(--card-background);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            background: var(--primary-color);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .login-header .icon-container {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            backdrop-filter: blur(10px);
        }

        .login-header h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label i {
            font-size: 16px;
            color: var(--secondary-color);
        }

        .form-control {
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 85, 104, 0.1);
            background: white;
            outline: none;
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .form-control.is-invalid {
            border-color: var(--error-color);
        }

        .invalid-feedback {
            color: var(--error-color);
            font-size: 13px;
            margin-top: 5px;
        }

        .form-check {
            margin-bottom: 24px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            border: 1.5px solid var(--border-color);
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
            margin-left: 8px;
            font-size: 14px;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            background: #2d3748;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(74, 85, 104, 0.2);
        }

        .btn-login i {
            font-size: 18px;
        }

        .alert {
            border: none;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }

        .alert-danger {
            background: #fed7d7;
            color: #742a2a;
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-footer {
            padding: 20px 30px;
            background: #f8f9fa;
            text-align: center;
            border-top: 1px solid var(--border-color);
        }

        .login-footer p {
            margin: 0;
            font-size: 12px;
            color: var(--text-secondary);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-body {
                padding: 30px 20px;
            }

            .login-header {
                padding: 25px 20px;
            }

            .login-header h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="icon-container">
                    <i class="bi bi-mortarboard" style="font-size: 32px;"></i>
                </div>
                <h2>Sistem Monitoring Siswa</h2>
                <p>Silakan masuk untuk melanjutkan</p>
            </div>

            <!-- Body -->
            <div class="login-body">
                <!-- Alert untuk Session Status -->
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Alert untuk Error -->
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @if($errors->has('email'))
                            <i class="bi bi-exclamation-circle"></i>
                            Email atau password salah. Akun tidak terdaftar di sistem.
                        @else
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $errors->first() }}
                        @endif
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="email">
                            <i class="bi bi-envelope"></i>
                            Email
                        </label>
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@sekolah.com"
                            required
                            autocomplete="email"
                            autofocus
                        >
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">
                            <i class="bi bi-lock"></i>
                            Password
                        </label>
                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                            autocomplete="current-password"
                        >
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="form-check">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="remember"
                            name="remember"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="remember">
                            Ingat saya
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Masuk</span>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="login-footer">
                <p>&copy; {{ date('Y') }} Sistem Monitoring Siswa. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Simple form enhancement - optional
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });

            // Clear invalid state when user types
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });
        });
    </script>
</body>
</html>
