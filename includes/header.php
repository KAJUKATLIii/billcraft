<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Billcraft - Premium Business Management System">
    <meta name="author" content="KAJUKATLIii">

    <title>Administrator | Billcraft</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
        integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    
    <!-- Modern Icons & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ══════════════════════════════════════
           DESIGN TOKENS
        ══════════════════════════════════════ */
        :root {
            --primary:      hsl(248, 85%, 65%);
            --primary-h:    248;
            --primary-raw:  248, 85%, 65%;
            --primary-glow: hsla(248, 85%, 65%, 0.3);
            --accent:       hsl(280, 80%, 68%);
            --success:      hsl(142, 71%, 45%);
            --warning:      hsl(38, 92%, 50%);
            --danger:       hsl(0, 84%, 60%);
            --info:         hsl(199, 89%, 48%);

            --bg:           hsl(220, 33%, 97%);
            --bg-raw:       220, 33%, 97%;
            --bg2:          hsl(220, 28%, 94%);
            --surface:      hsla(0, 0%, 100%, 0.8);
            --surface-solid:hsl(0, 0%, 100%);
            --border:       hsla(220, 13%, 88%, 0.9);
            --text:         hsl(224, 50%, 8%);
            --text-muted:   hsl(220, 9%, 46%);

            --sidebar-bg:      hsl(224, 50%, 7%);
            --sidebar-surface: hsla(224, 40%, 12%, 0.7);
            --sidebar-text:    hsla(210, 40%, 98%, 0.55);
            --sidebar-hover:   hsla(210, 40%, 98%, 0.06);
            --sidebar-active:  hsl(248, 85%, 65%);
            --sidebar-width:   256px;
            --topbar-h:        64px;

            --radius-sm:    8px;
            --radius:       12px;
            --radius-lg:    16px;
            --radius-xl:    20px;

            --shadow-sm:    0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow:       0 4px 12px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.04);
            --shadow-md:    0 10px 30px rgba(0,0,0,0.1), 0 4px 8px rgba(0,0,0,0.06);
            --shadow-lg:    0 20px 50px rgba(0,0,0,0.12), 0 8px 16px rgba(0,0,0,0.06);
        }

        .dark {
            --bg:           hsl(224, 45%, 5%);
            --bg-raw:       224, 45%, 5%;
            --bg2:          hsl(224, 40%, 8%);
            --surface:      hsla(224, 35%, 12%, 0.85);
            --surface-solid:hsl(224, 35%, 12%);
            --border:       hsla(215, 28%, 20%, 0.9);
            --text:         hsl(210, 40%, 96%);
            --text-muted:   hsl(215, 20%, 58%);

            --shadow-sm:    0 1px 3px rgba(0,0,0,0.3);
            --shadow:       0 4px 12px rgba(0,0,0,0.4);
            --shadow-md:    0 10px 30px rgba(0,0,0,0.5);
            --shadow-lg:    0 20px 50px rgba(0,0,0,0.6);
        }

        /* ══════════════════════════════════════
           BASE RESET
        ══════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; }

        html, body { height: 100%; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background: url('images/background.png') no-repeat center center fixed !important;
            background-size: cover !important;
            color: var(--text) !important;
            overflow-x: hidden;
            transition: background 0.35s ease, color 0.35s ease;
        }

        /* Background overlay & gradient blob */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background: 
                linear-gradient(hsla(var(--bg-raw), 0.2), hsla(var(--bg-raw), 0.2)),
                radial-gradient(ellipse 60% 50% at 10% 40%, hsla(var(--primary-raw), 0.07), transparent),
                radial-gradient(ellipse 50% 40% at 90% 20%, hsla(280, 80%, 65%, 0.06), transparent);
        }

        #wrapper {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 1;
            background: transparent !important;
        }

        /* ══════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════ */
        .sidebar {
            position: fixed !important;
            top: 0;
            left: 0;
            width: var(--sidebar-width) !important;
            height: 100vh;
            background: var(--sidebar-bg) !important;
            border-right: 1px solid hsla(215, 28%, 18%, 0.8) !important;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        /* Sidebar inner glow */
        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 1px;
            height: 100%;
            background: linear-gradient(to bottom,
                transparent,
                hsla(248, 85%, 65%, 0.4) 30%,
                hsla(280, 80%, 68%, 0.3) 70%,
                transparent);
        }

        /* Sidebar brand area */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 20px 16px;
            border-bottom: 1px solid hsla(215, 28%, 18%, 0.6);
            text-decoration: none !important;
            height: var(--topbar-h);
            flex-shrink: 0;
        }

        .sidebar-logo {
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px hsla(248, 85%, 65%, 0.4);
            flex-shrink: 0;
        }
        .sidebar-logo img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        .sidebar-brand-name {
            font-size: 17px;
            font-weight: 800;
            color: white !important;
            letter-spacing: -0.03em;
        }
        .sidebar-brand-name span {
            color: hsl(248, 85%, 75%);
        }

        /* Sidebar nav */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 12px 12px;
        }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: hsla(215, 28%, 30%, 0.5); border-radius: 4px; }

        .sidebar .nav-section-label {
            padding: 16px 12px 6px;
            font-size: 10.5px;
            font-weight: 700;
            color: hsla(210, 40%, 98%, 0.28);
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .sidebar ul { list-style: none; padding: 0; margin: 0; }
        .sidebar ul li { margin-bottom: 2px; }

        .sidebar ul li a {
            display: flex !important;
            align-items: center;
            gap: 12px;
            padding: 10px 14px !important;
            border-radius: var(--radius-sm) !important;
            color: var(--sidebar-text) !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
            text-decoration: none !important;
            background: transparent !important;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar ul li a .nav-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            background: hsla(210, 40%, 98%, 0.04);
            transition: all 0.2s ease;
        }

        .sidebar ul li a:hover {
            color: white !important;
            background: var(--sidebar-hover) !important;
        }
        .sidebar ul li a:hover .nav-icon {
            background: hsla(248, 85%, 65%, 0.15);
            color: hsl(248, 85%, 75%);
        }

        .sidebar ul li a.active {
            color: white !important;
            background: linear-gradient(135deg, hsla(248, 85%, 65%, 0.25), hsla(280, 80%, 68%, 0.15)) !important;
            border: 1px solid hsla(248, 85%, 65%, 0.25) !important;
        }
        .sidebar ul li a.active .nav-icon {
            background: hsl(248, 85%, 65%);
            color: white;
            box-shadow: 0 4px 12px hsla(248, 85%, 65%, 0.4);
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid hsla(215, 28%, 18%, 0.6);
            flex-shrink: 0;
        }
        .sidebar-footer a {
            display: flex !important;
            align-items: center;
            gap: 10px;
            color: var(--sidebar-text) !important;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
        }
        .sidebar-footer a:hover {
            color: hsl(0, 84%, 70%) !important;
            background: hsla(0, 84%, 60%, 0.08) !important;
        }

        /* ══════════════════════════════════════
           TOPBAR
        ══════════════════════════════════════ */
        .navbar-top {
            position: fixed !important;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-h);
            z-index: 1040;
            background: var(--surface) !important;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid var(--border) !important;
            box-shadow: var(--shadow-sm) !important;
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-page-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Theme Toggle */
        .topbar-btn {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-muted);
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .topbar-btn:hover {
            background: var(--bg2);
            color: var(--primary);
            border-color: hsla(var(--primary-raw), 0.3);
        }

        /* User Dropdown */
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            border-radius: var(--radius);
            cursor: pointer;
            border: 1px solid var(--border);
            background: transparent;
            transition: all 0.2s ease;
            text-decoration: none !important;
        }
        .topbar-user:hover {
            background: var(--bg2);
        }
        .topbar-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 13px;
            font-weight: 700;
        }
        .topbar-user-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
        }

        .dropdown-menu {
            border: 1px solid var(--border) !important;
            background: var(--surface-solid) !important;
            box-shadow: var(--shadow-md) !important;
            border-radius: var(--radius) !important;
            padding: 6px !important;
            min-width: 180px;
        }
        .dropdown-menu li a {
            border-radius: var(--radius-sm) !important;
            padding: 10px 14px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: var(--text) !important;
            transition: background 0.15s ease !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dropdown-menu li a:hover {
            background: var(--bg2) !important;
            color: var(--primary) !important;
        }
        .dropdown-menu li.divider {
            background: var(--border);
            margin: 4px 0;
        }

        /* ══════════════════════════════════════
           PAGE WRAPPER
        ══════════════════════════════════════ */
        #page-wrapper {
            margin-left: var(--sidebar-width) !important;
            padding: calc(var(--topbar-h) + 28px) 28px 28px !important;
            min-height: 100vh;
            background: transparent !important;
            flex: 1;
        }

        /* ══════════════════════════════════════
           TYPOGRAPHY
        ══════════════════════════════════════ */
        .page-header {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text) !important;
            border: none !important;
            margin: 0 0 1.75rem 0 !important;
            padding: 0 !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        /* ══════════════════════════════════════
           CARDS
        ══════════════════════════════════════ */
        .card-modern {
            background: var(--surface) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border) !important;
            border-radius: var(--radius-lg) !important;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .card-modern:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md), 0 0 0 1px hsla(var(--primary-raw), 0.15);
            border-color: hsla(var(--primary-raw), 0.3) !important;
        }

        .card-modern::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .card-modern:hover::after { opacity: 1; }

        .card-body { position: relative; z-index: 1; }

        .card-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .card-value {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: var(--text);
            line-height: 1;
            margin-bottom: 16px;
        }

        .card-icon {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            font-size: 2rem;
            opacity: 0.12;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .card-modern:hover .card-icon {
            opacity: 0.2;
            transform: scale(1.1) rotate(-5deg);
        }

        /* ══════════════════════════════════════
           TABLES
        ══════════════════════════════════════ */
        .table-responsive {
            border-radius: var(--radius-lg) !important;
            border: 1px solid var(--border) !important;
            background: var(--surface-solid) !important;
            box-shadow: var(--shadow-sm) !important;
            overflow: hidden !important;
        }

        .table { margin-bottom: 0 !important; }

        .table thead th {
            background: var(--bg2) !important;
            color: var(--text-muted) !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.07em !important;
            padding: 14px 16px !important;
            border-bottom: 1px solid var(--border) !important;
            border-top: none !important;
        }

        .table tbody td {
            padding: 14px 16px !important;
            vertical-align: middle !important;
            color: var(--text) !important;
            border-top: 1px solid var(--border) !important;
            font-size: 13.5px !important;
        }

        .table tbody tr:hover td {
            background: var(--bg2) !important;
        }

        /* ══════════════════════════════════════
           BUTTONS
        ══════════════════════════════════════ */
        .btn {
            border-radius: var(--radius) !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            padding: 8px 18px !important;
            transition: all 0.2s ease !important;
            border: none !important;
            letter-spacing: 0.01em;
        }

        .btn-primary {
            background: var(--primary) !important;
            color: white !important;
            box-shadow: 0 4px 12px hsla(var(--primary-raw), 0.3) !important;
        }
        .btn-primary:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px hsla(var(--primary-raw), 0.4) !important;
        }

        .btn-success {
            background: var(--success) !important;
            color: white !important;
        }
        .btn-danger {
            background: var(--danger) !important;
            color: white !important;
        }
        .btn-warning {
            background: var(--warning) !important;
            color: white !important;
        }
        .btn-info {
            background: var(--info) !important;
            color: white !important;
        }

        .btn-sm { padding: 5px 12px !important; font-size: 12px !important; }

        /* ══════════════════════════════════════
           FORMS
        ══════════════════════════════════════ */
        .form-control {
            border-radius: var(--radius) !important;
            border: 1.5px solid var(--border) !important;
            background: var(--surface-solid) !important;
            color: var(--text) !important;
            padding: 10px 14px !important;
            height: auto !important;
            font-size: 14px !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            transition: all 0.2s ease !important;
            box-shadow: none !important;
        }
        .form-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px hsla(var(--primary-raw), 0.12) !important;
            background: var(--surface-solid) !important;
        }
        .form-control::placeholder { color: var(--text-muted) !important; opacity: 0.7; }

        /* ══════════════════════════════════════
           PANELS (legacy compat)
        ══════════════════════════════════════ */
        .panel {
            background: var(--surface-solid) !important;
            border: 1px solid var(--border) !important;
            border-radius: var(--radius-lg) !important;
            box-shadow: var(--shadow-sm) !important;
        }
        .panel-heading {
            background: var(--bg2) !important;
            border-bottom: 1px solid var(--border) !important;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important;
            color: var(--text) !important;
            font-weight: 700;
            padding: 14px 20px !important;
        }
        .panel-body { color: var(--text) !important; }
        .well {
            background: var(--bg2) !important;
            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;
        }

        /* ══════════════════════════════════════
           ALERTS
        ══════════════════════════════════════ */
        .alert {
            border-radius: var(--radius) !important;
            border: none !important;
            font-weight: 600;
            padding: 12px 18px !important;
            font-size: 14px;
        }
        .alert-success { background: hsla(142, 71%, 45%, 0.12) !important; color: hsl(142, 60%, 30%) !important; }
        .alert-danger  { background: hsla(0, 84%, 60%, 0.1) !important; color: hsl(0, 65%, 40%) !important; }
        .dark .alert-success { color: hsl(142, 71%, 60%) !important; }
        .dark .alert-danger  { color: hsl(0, 84%, 70%) !important; }

        /* ══════════════════════════════════════
           BADGES & LABELS
        ══════════════════════════════════════ */
        .badge, .label {
            border-radius: 6px !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            padding: 3px 8px !important;
        }

        /* ══════════════════════════════════════
           SCROLLBAR
        ══════════════════════════════════════ */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary); }

        /* ══════════════════════════════════════
           ANIMATIONS
        ══════════════════════════════════════ */
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        /* ══════════════════════════════════════
           DARK/LIGHT TOGGLE ICONS
        ══════════════════════════════════════ */
        .dark .icon-sun { display: none; }
        .dark .icon-moon { display: inline; }
        .icon-moon { display: none; }
        .icon-sun { display: inline; }

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }
            .sidebar.sidebar-open {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(0,0,0,0.3);
            }
            .navbar-top {
                left: 0 !important;
            }
            #page-wrapper {
                margin-left: 0 !important;
                padding: calc(var(--topbar-h) + 20px) 16px 16px !important;
            }
        }

        /* ══════════════════════════════════════
           CUSTOM MODAL & TOASTS
        ══════════════════════════════════════ */
        .modal-glass {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 10000;
            background: hsla(var(--bg-raw), 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .modal-glass.active { display: flex; opacity: 1; }
        
        .modal-content-glass {
            background: var(--surface);
            backdrop-filter: blur(25px) saturate(200%);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            width: 100%;
            max-width: 400px;
            padding: 32px;
            box-shadow: var(--shadow-lg);
            transform: scale(0.95);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-align: center;
        }
        .modal-glass.active .modal-content-glass { transform: scale(1); }

        .modal-icon-container {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: hsla(0, 84%, 60%, 0.1);
            color: var(--danger);
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            margin: 0 auto 20px;
        }

        .modal-title-glass { font-size: 18px; font-weight: 800; color: var(--text); margin-bottom: 8px; }
        .modal-text-glass { font-size: 14px; color: var(--text-muted); margin-bottom: 24px; line-height: 1.5; }
        
        .modal-footer-glass { display: flex; gap: 12px; }
        .modal-btn {
            flex: 1;
            padding: 12px !important;
            border-radius: var(--radius) !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid var(--border);
        }
        .modal-btn-cancel { background: var(--bg2); color: var(--text); }
        .modal-btn-confirm { background: var(--danger) !important; color: white !important; border: none; }
        .modal-btn:hover { transform: translateY(-2px); filter: brightness(1.05); }

        /* Toast notifications */
        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 11000;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .toast-glass {
            background: var(--surface);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            padding: 12px 20px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            display: flex; align-items: center; gap: 12px;
            color: var(--text);
            font-size: 13.5px; font-weight: 600;
            min-width: 280px;
            animation: slideInRight 0.3s ease forwards;
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .toast-success i { color: var(--success); }
        .toast-error i { color: var(--danger); }
    </style>
    <script>
        // Anti-flash theme init
        (function() {
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <script src="assets/js/jquery.min.js" type="text/javascript"></script>

</head>

<body>
    <!-- Global Components -->
    <div id="confirm-modal" class="modal-glass">
        <div class="modal-content-glass">
            <div class="modal-icon-container"><i class="fa fa-trash-can"></i></div>
            <div class="modal-title-glass">Confirm Deletion</div>
            <div class="modal-text-glass" id="modal-message">Are you sure you want to delete this record? This action cannot be undone.</div>
            <div class="modal-footer-glass">
                <button class="modal-btn modal-btn-cancel" onclick="closeModal()">Cancel</button>
                <button class="modal-btn modal-btn-confirm" id="confirm-delete-btn">Delete Now</button>
            </div>
        </div>
    </div>
    <div id="toast-container" class="toast-container"></div>

    <script>
        let deleteUrl = '';
        let targetElement = null;

        function showToast(message, type = 'success') {
            const toast = $(`
                <div class="toast-glass toast-${type}">
                    <i class="fa ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i>
                    <span>${message}</span>
                </div>
            `);
            $('#toast-container').append(toast);
            setTimeout(() => {
                toast.fadeOut(300, function() { $(this).remove(); });
            }, 3500);
        }

        function openConfirmModal(url, message, el) {
            deleteUrl = url;
            targetElement = el;
            $('#modal-message').text(message || 'Are you sure you want to delete this record?');
            $('#confirm-modal').addClass('active');
        }

        function closeModal() {
            $('#confirm-modal').removeClass('active');
        }

        $(document).ready(function() {
            // Global click handler for AJAX delete
            $(document).on('click', '.ajax-delete', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const msg = $(this).data('confirm') || 'Delete this record?';
                
                // Target cards or table rows depending on the page
                const el = $(this).closest('.card-modern, .vendor-card, .cust-card, tr, .prod-card');
                
                if (url && (url.includes('delete_') || url.includes('id='))) {
                    openConfirmModal(url, msg, el);
                } else {
                    console.warn('AJAX Delete: Missing valid URL', url);
                }
            });

            $('#confirm-delete-btn').on('click', function() {
                if (!deleteUrl) return;
                
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');

                $.ajax({
                    url: deleteUrl + '&ajax=1',
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message || 'Deleted successfully');
                            if (targetElement) {
                                targetElement.fadeOut(400, function() { $(this).remove(); });
                            }
                        } else {
                            showToast(response.message || 'Error occurred', 'error');
                        }
                    },
                    error: function() {
                        showToast('Server error occurred while deleting', 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false).text('Delete Now');
                        closeModal();
                    }
                });
            });

            // Close modal on outside click
            $('#confirm-modal').on('click', function(e) {
                if (e.target === this) closeModal();
            });
        });
    </script>

    <div id="wrapper">

        <!-- ═══════ SIDEBAR ═══════ -->
        <div class="sidebar" id="sidebar" role="navigation">

            <a class="sidebar-brand" href="index.php">
                <div class="sidebar-logo">
                    <img src="logo.png" alt="Billcraft Logo">
                </div>
                <span class="sidebar-brand-name">BILL<span>CRAFT</span></span>
            </a>

            <div class="sidebar-nav">
                <ul>
                    <li class="nav-section-label">Main Menu</li>
                    <li>
                        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fa fa-chart-line"></i></span>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="vendors.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'vendors.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fa fa-truck-field"></i></span>
                            Vendors
                        </a>
                    </li>
                    <li>
                        <a href="customer.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'customer.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fa fa-users"></i></span>
                            Customers
                        </a>
                    </li>
                    <li>
                        <a href="product.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'product.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fa fa-boxes-stacked"></i></span>
                            Products
                        </a>
                    </li>
                    <li>
                        <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fa fa-receipt"></i></span>
                            Orders
                        </a>
                    </li>
                    <li>
                        <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
                            Reports
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-footer">
                <a href="settings.php">
                    <span class="nav-icon" style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:hsla(210,40%,98%,0.04);margin-right:2px;">
                        <i class="fa fa-gear"></i>
                    </span>
                    Settings
                </a>
                <a href="logout.php" style="color: hsla(0,84%,70%,0.7) !important; margin-top:2px;">
                    <span class="nav-icon" style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:hsla(0,84%,60%,0.06);margin-right:2px;">
                        <i class="fa fa-sign-out-alt"></i>
                    </span>
                    Logout
                </a>
            </div>
        </div>

        <!-- ═══════ TOPBAR ═══════ -->
        <nav class="navbar-top" role="navigation">
            <div class="topbar-left">
                <button class="topbar-btn" id="sidebar-toggle" onclick="toggleSidebar()" style="display:none;" title="Toggle sidebar">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="topbar-page-title">
                    <i class="fa fa-gauge-high" style="color: var(--primary); font-size: 14px;"></i>
                    <?php
                        $pageNames = [
                            'index.php'    => 'Dashboard',
                            'vendors.php'  => 'Vendors',
                            'customer.php' => 'Customers',
                            'product.php'  => 'Products',
                            'orders.php'   => 'Orders',
                            'reports.php'  => 'Reports',
                            'settings.php' => 'Settings',
                            'sell.php'     => 'New Sale',
                        ];
                        $currentPage = basename($_SERVER['PHP_SELF']);
                        echo $pageNames[$currentPage] ?? 'Billcraft';
                    ?>
                </div>
            </div>

            <div class="topbar-right">
                <button class="topbar-btn" onclick="toggleTheme()" title="Toggle theme" id="theme-toggle">
                    <i class="fa fa-sun icon-sun"></i>
                    <i class="fa fa-moon icon-moon"></i>
                </button>

                <div class="dropdown">
                    <a href="#" class="topbar-user dropdown-toggle" data-toggle="dropdown">
                        <div class="topbar-user-avatar">
                            <i class="fa fa-user" style="font-size:12px;"></i>
                        </div>
                        <span class="topbar-user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                        <i class="fa fa-chevron-down" style="font-size:10px; color: var(--text-muted);"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right animate-fade-in">
                        <li><a href="settings.php"><i class="fa fa-cog fa-fw"></i> Settings</a></li>
                        <li class="divider"></li>
                        <li><a href="logout.php" style="color: hsl(0,84%,60%) !important;"><i class="fa fa-sign-out-alt fa-fw"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- The End of the Header -->