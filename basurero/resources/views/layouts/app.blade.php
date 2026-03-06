<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} – Admin</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css"/>

    <style>
        *{font-family:'Figtree',sans-serif;}

        /* ── Animaciones ── */
        @keyframes fadeInDown{
            from{opacity:0;transform:translateY(-18px)}
            to{opacity:1;transform:translateY(0)}
        }
        @keyframes fadeInUp{
            from{opacity:0;transform:translateY(18px)}
            to{opacity:1;transform:translateY(0)}
        }
        @keyframes popIn{
            0%{opacity:0;transform:scale(.92)}
            70%{transform:scale(1.03)}
            100%{opacity:1;transform:scale(1)}
        }
        @keyframes pulseGlow{
            0%,100%{box-shadow:0 0 0 0 rgba(59,130,246,.45)}
            50%{box-shadow:0 0 0 7px rgba(59,130,246,0)}
        }
        @keyframes countUp{
            from{opacity:0;transform:translateY(12px)}
            to{opacity:1;transform:translateY(0)}
        }

        .anim-fade-down{animation:fadeInDown .45s ease both;}
        .anim-fade-up  {animation:fadeInUp  .45s ease both;}
        .anim-pop      {animation:popIn     .4s  ease both;}

        /* ── Navbar ── */
        .navbar-admin{
            background:linear-gradient(135deg,#0f2557 0%,#1e40af 55%,#1d4ed8 100%);
            box-shadow:0 4px 20px rgba(15,37,87,.45);
            position:sticky;top:0;z-index:50;
        }
        .nav-logo-text{
            background:linear-gradient(90deg,#fff,#bfdbfe);
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
            font-weight:800;font-size:1.1rem;letter-spacing:.02em;
        }
        .nav-tagline{font-size:.7rem;color:#93c5fd;display:block;margin-top:-2px;}

        /* Contenedor del logo */
        .logo-wrap{
            width:68px;height:68px;
            border-radius:14px;
            overflow:hidden;
            background:rgba(255,255,255,.15);
            display:flex;align-items:center;justify-content:center;
            border:1.5px solid rgba(255,255,255,.3);
            animation:pulseGlow 2.8s infinite;
            flex-shrink:0;
        }
        .logo-wrap img{width:100%;height:100%;object-fit:contain;}

        .nav-link-app{
            color:rgba(255,255,255,.8);
            padding:5px 12px;
            border-radius:8px;
            font-size:.83rem;font-weight:500;
            display:inline-flex;align-items:center;gap:6px;
            transition:all .25s ease;
            white-space:nowrap;
        }
        .nav-link-app:hover,.nav-link-app.active{
            background:rgba(255,255,255,.18);
            color:#fff;
            transform:translateY(-1px);
        }
        .user-pill{
            background:rgba(255,255,255,.15);
            border:1px solid rgba(255,255,255,.25);
            color:#fff;
            padding:4px 12px;
            border-radius:999px;
            font-size:.78rem;font-weight:600;
            display:inline-flex;align-items:center;gap:6px;
        }

        /* ── Dropdown usuario ── */
        .user-dropdown{
            position:absolute;right:0;top:calc(100% + 8px);
            background:#fff;
            border-radius:12px;
            box-shadow:0 8px 28px rgba(0,0,0,.14);
            min-width:176px;
            border:1px solid #e2e8f0;
            overflow:hidden;
            animation:fadeInDown .2s ease;
        }
        .user-dropdown a,.user-dropdown button{
            display:flex;align-items:center;gap:8px;
            width:100%;padding:10px 16px;
            text-align:left;font-size:.85rem;color:#374151;
            transition:background .2s;
        }
        .user-dropdown a:hover,.user-dropdown button:hover{
            background:#eff6ff;color:#1d4ed8;
        }

        /* ── Page header ── */
        .page-header{
            background:linear-gradient(135deg,#eff6ff,#dbeafe);
            border-bottom:1px solid #bfdbfe;
        }

        /* ── Cards globales ── */
        .card-global{
            background:#fff;
            border-radius:16px;
            box-shadow:0 4px 18px rgba(0,0,0,.06);
            transition:transform .3s ease,box-shadow .3s ease;
        }
        .card-global:hover{
            transform:translateY(-5px);
            box-shadow:0 14px 34px rgba(0,0,0,.13);
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar{width:6px;}
        ::-webkit-scrollbar-track{background:#f1f5f9;}
        ::-webkit-scrollbar-thumb{background:#93c5fd;border-radius:6px;}
        ::-webkit-scrollbar-thumb:hover{background:#3b82f6;}
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
<div class="min-h-screen flex flex-col">

    <!-- ══ NAVBAR ADMIN ══ -->
    <nav class="navbar-admin anim-fade-down">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    {{-- ═══════════════════════════════════════════════════════
                         LOGO: coloca tu imagen como public/logo.png
                         Se mostrará aquí automáticamente.
                         ═══════════════════════════════════════════════════════ --}}
                    <div class="logo-wrap">
                        @if(file_exists(public_path('logo.png')))
                            <img src="{{ asset('logo.png') }}" alt="Logo">
                        @else
                            <i class="fas fa-truck text-white text-lg"></i>
                        @endif
                    </div>
                    <div>
                        <span class="nav-logo-text">GeoFlota</span>
                        <span class="nav-tagline">Panel Administrativo</span>
                    </div>
                </a>

                <!-- Links de navegación (desktop) -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('monitoreo.index') }}" class="nav-link-app {{ request()->routeIs('monitoreo.*') ? 'active' : '' }}">
                        <i class="fas fa-satellite-dish"></i> Monitoreo
                    </a>
                    <a href="{{ route('camiones.index') }}" class="nav-link-app {{ request()->routeIs('camiones.*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i> Camiones
                    </a>
                    <a href="{{ route('rutas.index') }}" class="nav-link-app {{ request()->routeIs('rutas.*') ? 'active' : '' }}">
                        <i class="fas fa-route"></i> Rutas
                    </a>
                    <a href="{{ route('admin.conductores.index') }}" class="nav-link-app {{ request()->routeIs('admin.conductores.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Conductores
                    </a>
                    <a href="{{ route('recorridos.index') }}" class="nav-link-app {{ request()->routeIs('recorridos.*') ? 'active' : '' }}">
                        <i class="fas fa-history"></i> Recorridos
                    </a>
                    <a href="{{ route('admin.encargados.index') }}" class="nav-link-app {{ request()->routeIs('admin.encargados.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i> Encargados
                    </a>
                </div>

                <!-- Usuario -->
                <div class="flex items-center gap-3">
                    <span class="user-pill hidden md:inline-flex">
                        <i class="fas fa-shield-alt text-xs text-blue-200"></i>
                        {{ Auth::user()->name }}
                    </span>
                    <div class="relative" x-data="{ open:false }">
                        <button @click="open=!open"
                            class="w-9 h-9 rounded-full bg-white/20 hover:bg-white/30 transition-colors border border-white/30 flex items-center justify-center">
                            <i class="fas fa-chevron-down text-white text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open=false" class="user-dropdown" style="display:none">
                            <a href="{{ route('profile.edit') }}"><i class="fas fa-user text-blue-500"></i> Mi Perfil</a>
                            <hr class="border-gray-100 mx-3">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"><i class="fas fa-sign-out-alt text-red-400"></i> Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <!-- ══ PAGE HEADER ══ -->
    @isset($header)
        <header class="page-header anim-fade-down" style="animation-delay:.1s">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- ══ CONTENIDO ══ -->
    <main class="flex-1 anim-fade-up" style="animation-delay:.15s">
        {{ $slot }}
    </main>

</div>

<!-- Scripts -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@stack('scripts')
</body>
</html>