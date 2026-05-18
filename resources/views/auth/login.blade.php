<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');

        :root {
            --bg-main: #f5f8ff;
            --bg-card: rgba(255, 255, 255, 0.88);
            --accent-color: #7a5af8;
            --accent-second: #5b8dff;
            --border-color: #dbe4ff;
            --text-main: #1f2759;
            --text-dim: #6b739f;
        }

        body {
            font-family: 'Manrope', sans-serif;
            background:
                radial-gradient(circle at 14% 14%, rgba(143, 112, 255, 0.17) 0%, rgba(143, 112, 255, 0) 38%),
                radial-gradient(circle at 88% 16%, rgba(91, 141, 255, 0.2) 0%, rgba(91, 141, 255, 0) 42%),
                linear-gradient(150deg, #f8f9ff 0%, #eff4ff 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            margin: 0;
            overflow: hidden;
        }

        .bg-glow {
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 999px;
            filter: blur(85px);
            opacity: 0.9;
            z-index: -1;
        }

        .bg-glow.one {
            left: -80px;
            top: -90px;
            background: rgba(122, 90, 248, 0.25);
        }

        .bg-glow.two {
            right: -80px;
            bottom: -110px;
            background: rgba(91, 141, 255, 0.28);
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 38px;
            box-shadow: 0 22px 54px rgba(90, 111, 220, 0.18);
            backdrop-filter: blur(8px);
            animation: cardIn 0.45s ease-out;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-second) 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 22px;
            color: #ffffff;
            box-shadow: 0 14px 30px rgba(122, 90, 248, 0.3);
        }

        .form-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #7278a5;
            margin-bottom: 8px;
        }

        .input-group-text {
            background: #f8faff !important;
            border-color: #dbe4ff !important;
            color: #7a84b4 !important;
        }

        .form-control {
            background: #f8faff !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
            padding: 12px 16px;
            border-radius: 8px;
        }

        .form-control::placeholder {
            color: #9ba5cd;
        }

        .form-control:focus {
            border-color: var(--accent-color) !important;
            box-shadow: 0 0 0 4px rgba(122, 90, 248, 0.12) !important;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-second) 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            letter-spacing: 0.4px;
            width: 100%;
            margin-top: 10px;
            transition: 0.3s;
            box-shadow: 0 14px 24px rgba(122, 90, 248, 0.25);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #6848eb 0%, #4d7df2 100%);
            transform: translateY(-2px);
            box-shadow: 0 18px 26px rgba(91, 141, 255, 0.3);
        }

        .error-alert {
            background: rgba(255, 77, 109, 0.08);
            border: 1px solid rgba(255, 77, 109, 0.2);
            color: #e5486b;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .heading-gradient {
            background: linear-gradient(120deg, #6c4ef6 0%, #4f7eff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 576px) {
            .login-card {
                margin: 16px;
                padding: 30px 24px;
                border-radius: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="bg-glow one"></div>
    <div class="bg-glow two"></div>

    <div class="login-card text-center">
        <div class="brand-icon">
            <i class="fas fa-shield-alt"></i>
        </div>

        <h4 class="fw-bold mb-1 heading-gradient">Welcome Back</h4>
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
                    <span class="input-group-text border-end-0">
                        <i class="fas fa-user-alt small"></i>
                    </span>
                    <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="Masukkan username" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0">
                        <i class="fas fa-lock small"></i>
                    </span>
                    <input type="password" id="password" name="password" class="form-control border-start-0 border-end-0 ps-0" placeholder="••••••••" required>
                    <span class="input-group-text border-start-0" style="cursor: pointer;" onclick="togglePassword()">
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