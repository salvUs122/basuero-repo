<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} – Encargado</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    @stack('styles')

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
            0%,100%{box-shadow:0 0 0 0 rgba(16,185,129,.45)}
            50%{box-shadow:0 0 0 7px rgba(16,185,129,0)}
        }

        .anim-fade-down{animation:fadeInDown .45s ease both;}
        .anim-fade-up  {animation:fadeInUp  .45s ease both;}

        /* ── Navbar Encargado ── */
        .navbar-encargado{
            background:linear-gradient(135deg,#064e3b 0%,#059669 55%,#10b981 100%);
            box-shadow:0 4px 20px rgba(6,78,59,.45);
            position:sticky;top:0;z-index:50;
        }
        .nav-logo-enc{
            background:linear-gradient(90deg,#fff,#a7f3d0);
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
            font-weight:800;font-size:1.1rem;letter-spacing:.02em;
        }
        .nav-tagline-enc{font-size:.7rem;color:#6ee7b7;display:block;margin-top:-2px;}

        /* Contenedor del logo */
        .logo-wrap-enc{
            width:68px;height:68px;
            border-radius:14px;
            overflow:hidden;
            background:rgba(255,255,255,.15);
            display:flex;align-items:center;justify-content:center;
            border:1.5px solid rgba(255,255,255,.3);
            animation:pulseGlow 2.8s infinite;
            flex-shrink:0;
        }
        .logo-wrap-enc img{width:100%;height:100%;object-fit:contain;}

        .nav-link-enc{
            color:rgba(255,255,255,.82);
            padding:5px 12px;
            border-radius:8px;
            font-size:.83rem;font-weight:500;
            display:inline-flex;align-items:center;gap:6px;
            transition:all .25s ease;
            white-space:nowrap;
        }
        .nav-link-enc:hover,.nav-link-enc.active{
            background:rgba(255,255,255,.18);
            color:#fff;
            transform:translateY(-1px);
        }
        .user-pill-enc{
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
            background:#ecfdf5;color:#059669;
        }

        /* ── Page header ── */
        .page-header-enc{
            background:linear-gradient(135deg,#ecfdf5,#d1fae5);
            border-bottom:1px solid #a7f3d0;
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
            box-shadow:0 14px 34px rgba(0,0,0,.12);
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar{width:6px;}
        ::-webkit-scrollbar-track{background:#f1f5f9;}
        ::-webkit-scrollbar-thumb{background:#6ee7b7;border-radius:6px;}
        ::-webkit-scrollbar-thumb:hover{background:#10b981;}

        /* ── Toast Notifications ── */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .toast {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            max-width: 400px;
            min-width: 300px;
            pointer-events: auto;
            position: relative;
            overflow: hidden;
        }
        .toast.show { transform: translateX(0); opacity: 1; }
        .toast.hiding { transform: translateX(120%); opacity: 0; }
        .toast-success { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .toast-error { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
        .toast-warning { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        .toast-info { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
        .toast-icon { font-size: 1.5rem; margin-right: 12px; flex-shrink: 0; }
        .toast-content { flex: 1; }
        .toast-title { font-weight: 600; font-size: 0.95rem; margin-bottom: 2px; }
        .toast-message { font-size: 0.85rem; opacity: 0.9; }
        .toast-close { background: none; border: none; color: white; opacity: 0.7; cursor: pointer; padding: 4px; margin-left: 8px; transition: opacity 0.2s; }
        .toast-close:hover { opacity: 1; }
        .toast-progress { position: absolute; bottom: 0; left: 0; height: 3px; background: rgba(255, 255, 255, 0.4); border-radius: 0 0 12px 12px; animation: toast-progress 4s linear forwards; }
        @keyframes toast-progress { from { width: 100%; } to { width: 0%; } }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
<!-- Toast Container -->
<div id="toast-container" class="toast-container"></div>

<script>
function showToast(type, title, message, duration = 4000) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    const icons = { success: 'fas fa-check-circle', error: 'fas fa-exclamation-circle', warning: 'fas fa-exclamation-triangle', info: 'fas fa-info-circle' };
    toast.innerHTML = `
        <i class="${icons[type]} toast-icon"></i>
        <div class="toast-content"><div class="toast-title">${title}</div><div class="toast-message">${message}</div></div>
        <button class="toast-close" onclick="this.parentElement.classList.add('hiding'); setTimeout(() => this.parentElement.remove(), 400);"><i class="fas fa-times"></i></button>
        <div class="toast-progress" style="animation-duration: ${duration}ms"></div>
    `;
    container.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => { toast.classList.add('hiding'); setTimeout(() => toast.remove(), 400); }, duration);
}
</script>

<div class="min-h-screen flex flex-col">

    <!-- ══ NAVBAR ENCARGADO ══ -->
    <nav class="navbar-encargado anim-fade-down">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <a href="{{ route('encargado.dashboard') }}" class="flex items-center gap-3">
                    {{-- ═══════════════════════════════════════════════════════
                         LOGO: coloca tu imagen como public/logo.png
                         Se mostrará aquí automáticamente.
                         ═══════════════════════════════════════════════════════ --}}
                    <div class="logo-wrap-enc">
                        @if(file_exists(public_path('logo.png')))
                            <img src="{{ asset('logo.png') }}" alt="Logo">
                        @else
                            <i class="fas fa-clipboard-list text-white text-lg"></i>
                        @endif
                    </div>
                    <div>
                        <span class="nav-logo-enc">GeoFlota</span>
                        <span class="nav-tagline-enc">Panel de Encargado</span>
                    </div>
                </a>

                <!-- Links de navegación (desktop) -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('camiones.index') }}" class="nav-link-enc {{ request()->routeIs('camiones.*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i> Camiones
                    </a>
                    <a href="{{ route('admin.conductores.index') }}" class="nav-link-enc {{ request()->routeIs('admin.conductores.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Conductores
                    </a>
                    <a href="{{ route('rutas.index') }}" class="nav-link-enc {{ request()->routeIs('rutas.*') ? 'active' : '' }}">
                        <i class="fas fa-route"></i> Rutas
                    </a>
                    <a href="{{ route('monitoreo.index') }}" class="nav-link-enc {{ request()->routeIs('monitoreo.*') ? 'active' : '' }}">
                        <i class="fas fa-satellite-dish"></i> Monitoreo
                    </a>
                    <a href="{{ route('recorridos.index') }}" class="nav-link-enc {{ request()->routeIs('recorridos.*') ? 'active' : '' }}">
                        <i class="fas fa-history"></i> Recorridos
                    </a>
                </div>

                <!-- Usuario -->
                <div class="flex items-center gap-3">
                    <span class="user-pill-enc hidden md:inline-flex">
                        <i class="fas fa-user-check text-xs text-green-200"></i>
                        {{ Auth::user()->name }}
                    </span>
                    <div class="relative" x-data="{ open:false }">
                        <button @click="open=!open"
                            class="w-9 h-9 rounded-full bg-white/20 hover:bg-white/30 transition-colors border border-white/30 flex items-center justify-center">
                            <i class="fas fa-chevron-down text-white text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open=false" class="user-dropdown" style="display:none">
                            <a href="{{ route('profile.edit') }}"><i class="fas fa-user text-green-500"></i> Mi Perfil</a>
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
    @if(isset($header))
        <header class="page-header-enc anim-fade-down" style="animation-delay:.1s">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- ══ CONTENIDO ══ -->
    <main class="flex-1 anim-fade-up" style="animation-delay:.15s">
        {{ $slot }}
    </main>

</div>

<!-- Scripts -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@stack('scripts')
</body>
</html>