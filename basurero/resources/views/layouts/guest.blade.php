<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GeoFlota') }} &mdash; Acceso</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *{box-sizing:border-box;}
        body{
            margin:0;padding:0;
            min-height:100vh;
            font-family:'Figtree',system-ui,sans-serif;
            background:linear-gradient(135deg,#0f2057 0%,#1e40af 55%,#1d4ed8 100%);
            display:flex;align-items:center;justify-content:center;
            overflow:hidden;
        }

        /* ── Burbujas decorativas de fondo ── */
        .bubble{
            position:fixed;
            border-radius:50%;
            background:rgba(255,255,255,.06);
            pointer-events:none;
            animation:floatUp 18s ease-in-out infinite;
        }
        .bubble:nth-child(1){width:340px;height:340px;top:-80px;left:-100px;animation-delay:0s;}
        .bubble:nth-child(2){width:260px;height:260px;bottom:-60px;right:-80px;animation-delay:-5s;}
        .bubble:nth-child(3){width:180px;height:180px;top:40%;left:5%;animation-delay:-9s;}
        .bubble:nth-child(4){width:120px;height:120px;bottom:10%;left:55%;animation-delay:-13s;}
        .bubble:nth-child(5){width:80px;height:80px;top:15%;right:12%;animation-delay:-3s;}
        @keyframes floatUp{
            0%,100%{transform:translateY(0) scale(1);}
            50%{transform:translateY(-28px) scale(1.04);}
        }

        /* ── Card ── */
        .login-card{
            position:relative;z-index:10;
            width:100%;max-width:440px;
            background:#fff;
            border-radius:28px;
            box-shadow:0 32px 80px rgba(0,0,0,.35),0 8px 24px rgba(0,0,0,.2);
            padding:42px 44px 38px;
            animation:slideIn .6s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes slideIn{
            from{opacity:0;transform:translateY(36px) scale(.96);}
            to{opacity:1;transform:translateY(0) scale(1);}
        }

        /* ── Logo area ── */
        .logo-area{
            display:flex;flex-direction:column;align-items:center;
            margin-bottom:28px;
        }
        .logo-img-wrap{
            width:80px;height:80px;
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 8px 24px rgba(30,64,175,.35);
            border:3px solid #e0e7ff;
            margin-bottom:14px;
        }
        .logo-img-wrap img{width:100%;height:100%;object-fit:contain;}
        .app-title{
            font-size:1.6rem;font-weight:800;
            background:linear-gradient(90deg,#1e3a8a,#2563eb);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;
            letter-spacing:-.5px;
        }
        .app-subtitle{
            font-size:.78rem;color:#6b7280;margin-top:2px;
            text-align:center;
        }

        /* ── Divisor ── */
        .divider{
            height:1px;background:linear-gradient(90deg,transparent,#e5e7eb,transparent);
            margin:0 0 26px;
        }

        /* ── Labels e inputs ── */
        .field-label{
            display:block;
            font-size:.78rem;font-weight:600;
            color:#374151;margin-bottom:5px;
            letter-spacing:.3px;
        }
        .field-input{
            width:100%;
            padding:11px 14px 11px 42px;
            border:1.5px solid #d1d5db;
            border-radius:12px;
            font-size:.9rem;color:#111827;
            outline:none;
            transition:border-color .2s,box-shadow .2s;
            background:#f9fafb;
        }
        .field-input:focus{
            border-color:#2563eb;
            box-shadow:0 0 0 3px rgba(37,99,235,.15);
            background:#fff;
        }
        .field-wrap{
            position:relative;margin-bottom:18px;
        }
        .field-icon{
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            color:#9ca3af;font-size:.85rem;
            pointer-events:none;
        }

        /* ── Toggle password ── */
        .toggle-pass{
            position:absolute;right:14px;top:50%;transform:translateY(-50%);
            background:none;border:none;cursor:pointer;
            color:#9ca3af;font-size:.85rem;padding:0;
        }
        .toggle-pass:hover{color:#2563eb;}

        /* ── Btn principal ── */
        .btn-login{
            width:100%;
            padding:13px;
            background:linear-gradient(135deg,#1e40af,#2563eb);
            color:#fff;
            font-weight:700;font-size:.95rem;
            border:none;border-radius:14px;
            cursor:pointer;
            transition:transform .2s,box-shadow .2s,filter .2s;
            box-shadow:0 6px 20px rgba(37,99,235,.4);
            letter-spacing:.3px;
        }
        .btn-login:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 28px rgba(37,99,235,.5);
            filter:brightness(1.06);
        }
        .btn-login:active{transform:translateY(0);}

        /* ── checkbox recuérdame ── */
        .remember-row{
            display:flex;align-items:center;justify-content:space-between;
            margin-bottom:22px;
        }
        .remember-label{
            display:flex;align-items:center;gap:8px;
            font-size:.83rem;color:#4b5563;cursor:pointer;
        }
        .remember-label input[type=checkbox]{
            width:16px;height:16px;
            accent-color:#2563eb;
            cursor:pointer;
        }
        /* ── Error messages ── */
        .field-error{
            font-size:.75rem;color:#dc2626;
            margin-top:4px;display:block;
        }

        /* ── Footer ── */
        .card-footer{
            text-align:center;margin-top:24px;
            font-size:.72rem;color:#9ca3af;
        }

        @media(max-width:480px){
            .login-card{padding:32px 24px 28px;border-radius:20px;}
        }
    </style>
</head>
<body>
    <!-- Burbujas de fondo -->
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <div class="login-card">
        <!-- Logo + título -->
        <div class="logo-area">
            <div class="logo-img-wrap">
                <img src="{{ asset('Logo.png') }}" alt="Logo GeoFlota">
            </div>
            <span class="app-title">GeoFlota</span>
            <span class="app-subtitle">Sistema de gestión de flota vehicular</span>
        </div>

        <div class="divider"></div>

        {{ $slot }}

        <div class="card-footer">
            <i class="fas fa-shield-alt mr-1"></i>
            Acceso seguro &mdash; {{ config('app.name') }} &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
