<?php
session_start(); 
include "utils.php";

if(isset($_POST['register'])){
    Register($_POST['username'],$_POST['password'],$_POST['email']);
}
if(isset($_POST['login'])) {
    $user = Login($_POST['username'], $_POST['password']);
    if($user){
        $_SESSION['success'] = "Logged In";
        $_SESSION['user_logged_in'] = "True";
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        redirect('loading.php');
    } else {
        $_SESSION['failure'] = "Wrong Credentials";
    };
}

$register = false;
if (isset($_GET['register'])){
    $register = true;    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $register ? 'Register' : 'Sign In'; ?> | Billcraft</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary-h: 248;
            --primary: hsl(248, 85%, 65%);
            --primary-glow: hsla(248, 85%, 65%, 0.35);
            --primary-subtle: hsla(248, 85%, 65%, 0.12);
            --accent: hsl(280, 80%, 68%);
            --success: hsl(142, 71%, 45%);

            --bg: hsl(220, 33%, 97%);
            --surface: hsla(0, 0%, 100%, 0.75);
            --surface-solid: hsl(0, 0%, 100%);
            --border: hsla(220, 13%, 85%, 0.8);
            --text: hsl(224, 50%, 8%);
            --text-muted: hsl(220, 9%, 48%);
            --text-placeholder: hsl(220, 9%, 65%);
            --input-bg: hsla(220, 20%, 96%, 0.6);
            --shadow: 0 20px 60px -10px rgba(0,0,0,0.12), 0 8px 20px -5px rgba(0,0,0,0.08);
            --radius: 16px;
        }

        .dark {
            --bg: hsl(224, 40%, 5%);
            --surface: hsla(224, 35%, 10%, 0.85);
            --surface-solid: hsl(224, 35%, 10%);
            --border: hsla(215, 28%, 25%, 0.8);
            --text: hsl(210, 40%, 96%);
            --text-muted: hsl(215, 20%, 60%);
            --text-placeholder: hsl(215, 15%, 45%);
            --input-bg: hsla(224, 30%, 14%, 0.6);
            --shadow: 0 20px 60px -10px rgba(0,0,0,0.5), 0 8px 20px -5px rgba(0,0,0,0.3);
        }

        html, body {
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background 0.4s ease;
        }

        body {
            min-height: 100vh;
            background: url('images/background.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.2); /* Subtle overlay to ensure text contrast */
            z-index: -1;
        }

        /* Animated Gradient Background */
        .bg-gradient {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        .bg-gradient::before {
            content: '';
            position: absolute;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, hsla(248, 85%, 65%, 0.18), transparent 70%);
            top: -200px;
            left: -200px;
            animation: float1 12s ease-in-out infinite;
        }

        .bg-gradient::after {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, hsla(280, 80%, 65%, 0.14), transparent 70%);
            bottom: -150px;
            right: -150px;
            animation: float2 15s ease-in-out infinite;
        }

        .bg-orb {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, hsla(142, 71%, 45%, 0.08), transparent 70%);
            bottom: 20%;
            left: 20%;
            animation: float3 18s ease-in-out infinite;
        }

        @keyframes float1 {
            0%, 100% { transform: translate(0,0) scale(1); }
            33% { transform: translate(60px, 30px) scale(1.05); }
            66% { transform: translate(-40px, 50px) scale(0.95); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-50px, -40px) scale(1.08); }
        }
        @keyframes float3 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(40px, -60px); }
        }

        /* ─── Card ─── */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            padding: 20px;
        }

        .login-card {
            background: var(--surface);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: slideUp 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Top accent line */
        .card-accent-line {
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ─── Logo Section ─── */
        .logo-section {
            text-align: center;
            padding: 44px 40px 20px;
        }

        .logo-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 88px;
            height: 88px;
            background: white;
            border-radius: 22px;
            box-shadow: 0 8px 32px var(--primary-glow), 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .logo-container:hover {
            transform: translateY(-4px) rotate(3deg);
        }
        .logo-container img {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }

        .brand-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.04em;
            line-height: 1;
            margin-bottom: 6px;
        }

        .brand-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* ─── Form ─── */
        .form-section {
            padding: 28px 40px 40px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-placeholder);
            font-size: 15px;
            pointer-events: none;
            transition: color 0.2s ease;
            z-index: 2;
        }

        .form-input {
            width: 100%;
            height: 52px;
            padding: 0 16px 0 48px;
            background: var(--input-bg) !important;
            border: 1.5px solid var(--border) !important;
            border-radius: 14px !important;
            color: var(--text) !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 15px !important;
            font-weight: 500 !important;
            outline: none !important;
            box-shadow: none !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            appearance: none;
            -webkit-appearance: none;
            display: block;
        }

        .form-input::placeholder {
            color: var(--text-placeholder) !important;
            font-weight: 400;
        }

        .form-input:focus {
            border-color: var(--primary) !important;
            background: var(--surface-solid) !important;
            box-shadow: 0 0 0 4px var(--primary-subtle) !important;
        }

        .form-input:focus ~ .input-icon {
            color: var(--primary);
        }

        /* Password toggle */
        .toggle-pw {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-placeholder);
            font-size: 15px;
            transition: color 0.2s ease;
            background: none;
            border: none;
            padding: 0;
            z-index: 2;
        }
        .toggle-pw:hover { color: var(--primary); }

        /* ─── Submit Button ─── */
        .btn-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            height: 54px;
            margin-top: 8px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border: none;
            border-radius: 14px;
            color: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.01em;
            cursor: pointer;
            box-shadow: 0 8px 24px var(--primary-glow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.15));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px var(--primary-glow);
        }
        .btn-submit:hover::after { opacity: 1; }
        .btn-submit:active { transform: translateY(0px); }

        /* ─── Divider ─── */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .divider span {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ─── Footer link ─── */
        .switch-link {
            text-align: center;
            padding-top: 20px;
            font-size: 14px;
            color: var(--text-muted);
        }
        .switch-link a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }
        .switch-link a:hover { opacity: 0.75; text-decoration: underline; }

        /* ─── Flash messages ─── */
        .alert { border-radius: 12px !important; margin-bottom: 16px; font-size: 14px; font-weight: 600; }

        /* ─── Theme Toggle ─── */
        .theme-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--surface);
            backdrop-filter: blur(12px);
            color: var(--text-muted);
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            z-index: 100;
        }
        .theme-btn:hover { color: var(--primary); transform: scale(1.05); }

        .dark .icon-sun { display: none; }
        .dark .icon-moon { display: inline; }
        .icon-moon { display: none; }
        .icon-sun { display: inline; }
    </style>
    <script>
        // Anti-flash theme init
        (function() {
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body>
    <div class="bg-gradient"><div class="bg-orb"></div></div>

    <button class="theme-btn" id="themeToggle" onclick="toggleTheme()">
        <i class="fa fa-sun icon-sun"></i>
        <i class="fa fa-moon icon-moon"></i>
    </button>

    <div class="login-wrapper">
        <?php include 'includes/flash_messages.php'; ?>

        <div class="login-card">
            <div class="card-accent-line"></div>

            <div class="logo-section">
                <div class="logo-container">
                    <img src="logo.png" alt="Billcraft Logo">
                </div>
                <div class="brand-title">BILLCRAFT</div>
                <div class="brand-subtitle"><?php echo $register ? 'Create your account' : 'Welcome back'; ?></div>
            </div>

            <form class="form-section" method="POST" action="<?php echo $register ? 'login.php?register=true' : 'login.php'; ?>">
                <?php if($register): ?>

                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <input type="text" name="username" class="form-input" placeholder="Choose a username" required autocomplete="username">
                            <i class="fa fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <input type="email" name="email" class="form-input" placeholder="you@example.com" required autocomplete="email">
                            <i class="fa fa-envelope input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="reg-password" class="form-input" placeholder="Create a strong password" required autocomplete="new-password">
                            <i class="fa fa-lock input-icon"></i>
                            <button type="button" class="toggle-pw" onclick="togglePw('reg-password', this)">
                                <i class="fa fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="register" value="register" class="btn-submit">
                        <i class="fa fa-user-plus"></i> Create Account
                    </button>

                    <div class="switch-link">
                        Already have an account? <a href="login.php">Sign In</a>
                    </div>

                <?php else: ?>

                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <input type="text" name="username" class="form-input" placeholder="Enter your username" required autocomplete="username">
                            <i class="fa fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="login-password" class="form-input" placeholder="Enter your password" required autocomplete="current-password">
                            <i class="fa fa-lock input-icon"></i>
                            <button type="button" class="toggle-pw" onclick="togglePw('login-password', this)">
                                <i class="fa fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="login" value="login" class="btn-submit">
                        Sign In <i class="fa fa-arrow-right" style="font-size:13px;"></i>
                    </button>

                    <div class="switch-link">
                        Don't have an account? <a href="login.php?register=true">Create one</a>
                    </div>

                <?php endif; ?>
            </form>
        </div>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        function togglePw(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fa fa-eye';
            } else {
                field.type = 'password';
                icon.className = 'fa fa-eye-slash';
            }
        }
    </script>
</body>
</html>