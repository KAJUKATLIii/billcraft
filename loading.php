<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BILLCRAFT | Loading...</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: 260 85% 65%;
            --background: 220 33% 98%;
            --foreground: 224 71% 4%;
            --card: 0 0% 100%;
        }
        
        .dark {
            --background: 224 71% 4%;
            --foreground: 213 31% 91%;
            --card: 222.2 84% 4.9%;
            --border: 217 33% 17%;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: hsl(var(--background));
            color: hsl(var(--foreground));
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            transition: background-color 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .loader-card {
            text-align: center;
            padding: 60px;
            background: hsl(var(--card));
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            max-width: 440px;
            width: 90%;
            border: 1px solid hsla(var(--foreground), 0.05);
            animation: card-reveal 1s cubic-bezier(0.19, 1, 0.22, 1);
            position: relative;
            z-index: 10;
        }

        @keyframes card-reveal {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .logo-container {
            margin-bottom: 32px;
            position: relative;
        }

        .logo-img {
            width: 140px;
            height: 140px;
            animation: pulse-logo 2.5s ease-in-out infinite;
            background: white;
            border-radius: 32px;
            padding: 16px;
            box-shadow: 0 10px 30px hsla(var(--foreground), 0.1);
            object-fit: contain;
        }

        @keyframes pulse-logo {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 0px hsla(var(--primary), 0)); }
            50% { transform: scale(1.08); filter: drop-shadow(0 10px 30px hsla(var(--primary), 0.2)); }
        }

        .brand-name {
            font-size: 32px;
            font-weight: 900;
            letter-spacing: -0.04em;
            margin: 0 0 8px 0;
            color: hsl(var(--foreground));
            text-transform: uppercase;
        }

        .loader-track {
            width: 100%;
            height: 8px;
            background: hsla(var(--foreground), 0.05);
            border-radius: 100px;
            overflow: hidden;
            margin-top: 40px;
            position: relative;
        }

        .loader-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, hsl(var(--primary)), #7c3aed);
            border-radius: 100px;
            transition: width 0.4s ease-out;
            position: relative;
        }

        .loader-fill::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: glow 1.5s infinite;
        }

        @keyframes glow {
            from { transform: translateX(-100%); }
            to { transform: translateX(100%); }
        }

        .status-text {
            font-size: 13px;
            color: hsl(var(--primary));
            font-weight: 700;
            margin-top: 16px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .abstract-bg {
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, hsl(var(--primary) / 0.1) 0%, transparent 70%);
            z-index: 1;
            animation: rotate-bg 20s linear infinite;
        }

        @keyframes rotate-bg {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body>
    <div class="abstract-bg" style="top: 0; left: 0;"></div>
    <div class="abstract-bg" style="bottom: 0; right: 0; animation-delay: -10s;"></div>

    <div class="loader-card">
        <div class="logo-container">
            <img src="logo.png" class="logo-img" alt="Logo">
        </div>
        <div class="brand-name">BILLCRAFT</div>
        <p style="opacity: 0.5; font-size: 14px; margin: 0;">Business Management Cloud</p>
        
        <div class="loader-track">
            <div class="loader-fill" id="progress"></div>
        </div>
        <div class="status-text" id="status">Syncing local state...</div>
    </div>

    <script>
        const progress = document.getElementById('progress');
        const status = document.getElementById('status');
        const messages = [
            'Waking up servers...',
            'Connecting database...',
            'Streaming assets...',
            'Checking authentication...',
            'Ready to flow'
        ];

        let width = 0;
        let msgIndex = 0;

        const interval = setInterval(() => {
            if (width >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 400);
            } else {
                width += Math.random() * 20;
                if (width > 100) width = 100;
                progress.style.width = width + '%';
                
                const nextMsgIndex = Math.floor((width / 100) * messages.length);
                if (nextMsgIndex < messages.length && nextMsgIndex !== msgIndex) {
                    msgIndex = nextMsgIndex;
                    status.style.opacity = 0;
                    setTimeout(() => {
                        status.textContent = messages[msgIndex];
                        status.style.opacity = 1;
                    }, 200);
                }
            }
        }, 150);
    </script>
</body>
</html>
