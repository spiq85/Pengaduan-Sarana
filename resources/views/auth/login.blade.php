<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #06070a;
            --bg-card: #0f111a;
            --accent-color: #3d5afe;
            --border-color: #1f222d;
        }

        body {
            background-color: var(--bg-main);
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            margin: 0;
            overflow: hidden;
        }

        /* Dekorasi Background Bulat Subtle */
        .bg-glow {
            position: absolute;
            width: 300px;
            height: 300px;
            background: var(--accent-color);
            filter: blur(150px);
            opacity: 0.1;
            z-index: -1;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .brand-icon {
            width: 50px;
            height: 50px;
            background: var(--accent-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
            box-shadow: 0 0 20px rgba(61, 90, 254, 0.3);
        }

        .form-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #8a8d98;
            margin-bottom: 8px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid var(--border-color) !important;
            color: #fff !important;
            padding: 12px 16px;
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: var(--accent-color) !important;
            box-shadow: 0 0 0 4px rgba(61, 90, 254, 0.1) !important;
        }

        .btn-login {
            background: var(--accent-color);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #2b46e0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(61, 90, 254, 0.4);
        }

        .error-alert {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            color: #ff6b6b;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>

<body>
    <div class="bg-glow"></div>

    <div class="login-card text-center">
        <div class="brand-icon">
            <i class="fas fa-shield-alt"></i>
        </div>

        <h4 class="fw-bold mb-1">Welcome Back</h4>
        <p class="text-secondary small mb-4">Silakan login untuk akses panel.</p>

        @error('username')
        <div class="error-alert">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ $message }}</span>
        </div>
        @enderror

        <form method="POST" action="/login" class="text-start">
            @csrf

            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 border-secondary border-opacity-25 text-secondary">
                        <i class="fas fa-user-alt small"></i>
                    </span>
                    <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="Masukkan username" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 border-secondary border-opacity-25 text-secondary">
                        <i class="fas fa-lock small"></i>
                    </span>
                    <input type="password" id="password" name="password" class="form-control border-start-0 border-end-0 ps-0" placeholder="••••••••" required>
                    <span class="input-group-text bg-transparent border-start-0 border-secondary border-opacity-25 text-secondary" style="cursor: pointer;" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn-login">
                SIGN IN <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <p class="mt-4 text-secondary small">
            &copy; {{ date('Y') }} SMK ASPIRASI. All rights reserved.
        </p>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>