<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    <style>
        *{font-family:'Figtree',sans-serif;}

        /* ── Animaciones ── */
        @keyframes fadeInDown{
            from{opacity:0;transform:translateY(-20px)}
            to{opacity:1;transform:translateY(0)}
        }
        @keyframes fadeInUp{
            from{opacity:0;transform:translateY(22px)}
            to{opacity:1;transform:translateY(0)}
        }
        @keyframes fadeInRight{
            from{opacity:0;transform:translateX(-20px)}
            to{opacity:1;transform:translateX(0)}
        }
        @keyframes countUp{
            from{opacity:0;transform:translateY(14px) scale(.9)}
            to{opacity:1;transform:translateY(0) scale(1)}
        }
        @keyframes pulseGlow{
            0%,100%{box-shadow:0 0 0 0 rgba(59,130,246,.4)}
            50%{box-shadow:0 0 0 8px rgba(59,130,246,0)}
        }
        @keyframes shimmer{
            0%{background-position:-600px 0}
            100%{background-position:600px 0}
        }
        @keyframes rotate360{
            from{transform:rotate(0deg)}
            to{transform:rotate(360deg)}
        }
        @keyframes ripple{
            0%{transform:scale(1);opacity:.8}
            100%{transform:scale(2.5);opacity:0}
        }

        .anim-d0{animation:fadeInDown .5s ease both;}
        .anim-d1{animation:fadeInDown .5s ease .08s both;}
        .anim-u0{animation:fadeInUp  .5s ease both;}
        .anim-u1{animation:fadeInUp  .5s ease .1s  both;}
        .anim-u2{animation:fadeInUp  .5s ease .2s  both;}
        .anim-u3{animation:fadeInUp  .5s ease .3s  both;}
        .anim-u4{animation:fadeInUp  .5s ease .4s  both;}
        .anim-stat{animation:countUp .6s cubic-bezier(.22,1,.36,1) .35s both;}

        /* ── Gradients ── */
        .gradient-blue   {background:linear-gradient(135deg,#2563eb 0%,#1e40af 100%);}
        .gradient-green  {background:linear-gradient(135deg,#059669 0%,#047857 100%);}
        .gradient-purple {background:linear-gradient(135deg,#7c3aed 0%,#5b21b6 100%);}
        .gradient-orange {background:linear-gradient(135deg,#d97706 0%,#b45309 100%);}
        .gradient-rose   {background:linear-gradient(135deg,#e11d48 0%,#9f1239 100%);}
        .gradient-cyan   {background:linear-gradient(135deg,#0891b2 0%,#0e7490 100%);}

        /* ── Cards ── */
        .stat-card{
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 8px 28px rgba(0,0,0,.12);
            transition:transform .3s cubic-bezier(.22,1,.36,1),box-shadow .3s ease;
            position:relative;
        }
        .stat-card:hover{
            transform:translateY(-6px) scale(1.01);
            box-shadow:0 18px 40px rgba(0,0,0,.18);
        }
        .stat-card::before{
            content:'';
            position:absolute;
            inset:0;
            background:linear-gradient(rgba(255,255,255,.07),transparent);
            pointer-events:none;
        }
        .stat-icon{
            width:56px;height:56px;
            display:flex;align-items:center;justify-content:center;
            border-radius:999px;
            background:rgba(255,255,255,.2);
            font-size:1.5rem;
            color:#fff;
            transition:transform .3s ease;
        }
        .stat-card:hover .stat-icon{transform:rotate(10deg) scale(1.1);}

        /* progreso */
        .progress-track{
            width:100%;height:5px;
            background:rgba(255,255,255,.25);
            border-radius:999px;
            overflow:hidden;
            margin-top:12px;
        }
        .progress-bar{
            height:100%;background:rgba(255,255,255,.85);
            border-radius:999px;
            transition:width 1.2s cubic-bezier(.22,1,.36,1);
        }

        /* ── Hover-lift tarjetas de acceso ── */
        .card-shadow{box-shadow:0 4px 18px rgba(0,0,0,.07);}
        .hover-lift{
            transition:transform .3s ease,box-shadow .3s ease;
        }
        .hover-lift:hover{
            transform:translateY(-5px);
            box-shadow:0 14px 32px rgba(0,0,0,.13);
        }

        /* ── Tabla ── */
        .table-row-hover:hover{background:#eff6ff;}

        /* ── Badge animado ── */
        .badge-live{
            display:inline-flex;align-items:center;gap:6px;
            padding:3px 10px;
            border-radius:999px;
            font-size:.7rem;font-weight:700;
        }
        .dot-pulse{
            width:8px;height:8px;
            border-radius:50%;
            background:currentColor;
            animation:ripple 1.4s ease infinite;
        }

        /* ── Navbar (propio del dashboard) ── */
        .dash-nav{
            background:linear-gradient(135deg,#0f2557 0%,#1e40af 55%,#1d4ed8 100%);
            box-shadow:0 4px 20px rgba(15,37,87,.4);
            position:sticky;top:0;z-index:50;
        }
        .nav-logo-text{
            background:linear-gradient(90deg,#fff,#bfdbfe);
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
            font-weight:800;font-size:1.1rem;
        }
        .logo-wrap{
            width:38px;height:38px;
            border-radius:10px;
            background:rgba(255,255,255,.15);
            display:flex;align-items:center;justify-content:center;
            border:1.5px solid rgba(255,255,255,.3);
            animation:pulseGlow 2.8s infinite;
            overflow:hidden;
        }
        .logo-wrap img{width:100%;height:100%;object-fit:contain;}

        /* ── Scrollbar ── */
        ::-webkit-scrollbar{width:6px;}
        ::-webkit-scrollbar-track{background:#f1f5f9;}
        ::-webkit-scrollbar-thumb{background:#93c5fd;border-radius:6px;}
        ::-webkit-scrollbar-thumb:hover{background:#3b82f6;}
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- ══ NAVBAR ══ -->
    <nav class="dash-nav anim-d0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    {{-- ══ LOGO: pon public/logo.png y aparecerá aquí ══ --}}
                    <div class="logo-wrap">
                        @if(file_exists(public_path('logo.png')))
                            <img src="{{ asset('logo.png') }}" alt="Logo">
                        @else
                            <i class="fas fa-truck text-white text-lg"></i>
                        @endif
                    </div>
                    <div>
                        <span class="nav-logo-text">GeoFlota</span>
                        <span style="font-size:.68rem;color:#93c5fd;display:block;margin-top:-2px;">Panel Administrativo</span>
                    </div>
                </a>

                <!-- Links -->
                <div class="hidden md:flex items-center gap-1">
                    @foreach([
                        ['route'=>'monitoreo.index',         'icon'=>'satellite-dish', 'label'=>'Monitoreo'],
                        ['route'=>'camiones.index',          'icon'=>'truck',          'label'=>'Camiones'],
                        ['route'=>'rutas.index',             'icon'=>'route',          'label'=>'Rutas'],
                        ['route'=>'admin.conductores.index', 'icon'=>'users',          'label'=>'Conductores'],
                        ['route'=>'recorridos.index',        'icon'=>'history',        'label'=>'Recorridos'],
                        ['route'=>'admin.encargados.index',  'icon'=>'users-cog',      'label'=>'Encargados'],
                    ] as $item)
                    <a href="{{ route($item['route']) }}"
                       style="color:rgba(255,255,255,.82);padding:5px 11px;border-radius:8px;font-size:.82rem;font-weight:500;display:inline-flex;align-items:center;gap:5px;transition:all .25s ease;white-space:nowrap;"
                       onmouseover="this.style.background='rgba(255,255,255,.18)';this.style.color='#fff';"
                       onmouseout="this.style.background='';this.style.color='rgba(255,255,255,.82)';">
                        <i class="fas fa-{{ $item['icon'] }}"></i> {{ $item['label'] }}
                    </a>
                    @endforeach
                </div>

                <!-- Usuario + logout -->
                <div class="flex items-center gap-2">
                    <span style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:#fff;padding:4px 12px;border-radius:999px;font-size:.78rem;font-weight:600;" class="hidden md:inline-flex items-center gap-2">
                        <i class="fas fa-shield-alt" style="color:#93c5fd;font-size:.7rem;"></i>
                        {{ Auth::user()->name ?? 'Admin' }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;display:flex;align-items:center;justify-content:center;transition:background .2s;"
                            onmouseover="this.style.background='rgba(239,68,68,.6)'"
                            onmouseout="this.style.background='rgba(255,255,255,.15)'">
                            <i class="fas fa-sign-out-alt text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- ══ ENCABEZADO ══ -->
            <div class="mb-8 anim-d1">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                            <span style="width:40px;height:40px;background:linear-gradient(135deg,#2563eb,#1e40af);border-radius:12px;display:inline-flex;align-items:center;justify-content:center;">
                                <i class="fas fa-truck-moving text-white text-lg"></i>
                            </span>
                            Panel de Control Administrativo
                        </h1>
                        <p class="text-gray-500 mt-1 text-sm">Sistema de monitoreo de flota de camiones recolectores</p>
                    </div>
                    <div style="background:linear-gradient(135deg,#eff6ff,#dbeafe);padding:10px 18px;border-radius:12px;border:1px solid #bfdbfe;" class="flex items-center gap-2">
                        <i class="fas fa-clock text-blue-400"></i>
                        <span class="text-sm font-semibold text-blue-800">{{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- ══ TARJETAS DE ESTADÍSTICAS ══ -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <!-- Camiones Activos -->
                <div class="stat-card gradient-blue anim-u1">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-xs font-semibold uppercase tracking-widest">Camiones Activos</p>
                                <p class="text-4xl font-black mt-2 anim-stat">{{ $camionesActivos }}</p>
                                <p class="text-blue-200 text-xs mt-1 font-medium">de {{ $totalCamiones }} totales</p>
                            </div>
                            <div class="stat-icon"><i class="fas fa-truck"></i></div>
                        </div>
                        <div class="progress-track" style="margin-top:16px">
                            <div class="progress-bar" style="width:{{ $totalCamiones > 0 ? ($camionesActivos/$totalCamiones)*100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Recorridos Activos -->
                <div class="stat-card gradient-green anim-u2">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-green-100 text-xs font-semibold uppercase tracking-widest">Recorridos Activos</p>
                                <p class="text-4xl font-black mt-2 anim-stat">{{ $recorridosActivosCount }}</p>
                                <p class="text-green-200 text-xs mt-1 font-medium flex items-center gap-1">
                                    <span class="dot-pulse" style="background:#86efac;width:7px;height:7px;"></span>
                                    {{ $recorridosHoy }} recorridos hoy
                                </p>
                            </div>
                            <div class="stat-icon"><i class="fas fa-route"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Conductores -->
                <div class="stat-card gradient-purple anim-u3">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-purple-100 text-xs font-semibold uppercase tracking-widest">Conductores</p>
                                <p class="text-4xl font-black mt-2 anim-stat">{{ $totalConductores }}</p>
                                <p class="text-purple-200 text-xs mt-1 font-medium">
                                    <i class="fas fa-user-check mr-1"></i> Registrados
                                </p>
                            </div>
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Alertas Activas -->
                <div class="stat-card gradient-orange anim-u4">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-orange-100 text-xs font-semibold uppercase tracking-widest">Alertas Activas</p>
                                <p class="text-4xl font-black mt-2 anim-stat">{{ $alertasActivas }}</p>
                                <p class="text-orange-200 text-xs mt-1 font-medium">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Requieren atención
                                </p>
                            </div>
                            <div class="stat-icon"><i class="fas fa-bell"></i></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ══ TABLA RECORRIDOS EN TIEMPO REAL ══ -->
            <div class="bg-white rounded-2xl card-shadow overflow-hidden mb-8 anim-u2">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center"
                     style="background:linear-gradient(135deg,#eff6ff,#fff);">
                    <div>
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            <span class="dot-pulse" style="background:#3b82f6;width:9px;height:9px;"></span>
                            Recorridos en Tiempo Real
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Monitoreo activo de la flota</p>
                    </div>
                    <a href="{{ route('monitoreo.index') }}"
                       class="text-blue-600 hover:text-blue-800 text-xs font-semibold flex items-center gap-1 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                        <i class="fas fa-external-link-alt"></i> Ver monitoreo completo
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    @if($recorridosEnCurso->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Camión</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conductor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Actualización</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recorridosEnCurso as $recorrido)
                                @php
                                    $estado = $estados[$recorrido->id] ?? ['label' => 'DESCONOCIDO', 'color' => '#6b7280'];
                                    $punto = $ultimos[$recorrido->id] ?? null;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-truck text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $recorrido->camion->placa ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500">{{ $recorrido->camion->codigo ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $recorrido->ruta->nombre ?? 'Sin ruta' }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ $recorrido->ruta_id ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <i class="fas fa-user-circle text-gray-400 mr-1"></i>
                                            {{ $recorrido->conductor->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">ID: {{ $recorrido->conductor_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($punto)
                                            <div class="text-sm font-medium text-gray-900">
                                                <i class="fas fa-clock text-gray-400 mr-1"></i>
                                                {{ $punto->fecha_gps->diffForHumans() }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $punto->fecha_gps->format('H:i:s') }}
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500 italic">Sin datos</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                              style="background-color: {{ $estado['color'] }}20; color: {{ $estado['color'] }};">
                                            <i class="fas fa-circle mr-1" style="color: {{ $estado['color'] }}"></i>
                                            {{ $estado['label'] }}
                                        </span>
                                        @if($punto && $punto->velocidad_mps)
                                            <div class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-tachometer-alt mr-1"></i>
                                                {{ round($punto->velocidad_mps * 3.6, 1) }} km/h
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('monitoreo.index') }}?recorrido={{ $recorrido->id }}" 
                                           class="text-blue-600 hover:text-blue-900 mr-4 inline-flex items-center">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-12">
                        <div class="text-gray-300 text-5xl mb-4">
                            <i class="fas fa-road"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700">No hay recorridos activos</h3>
                        <p class="mt-1 text-sm text-gray-500">Todos los camiones están fuera de servicio o en mantenimiento.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- ══ ACCESOS RÁPIDOS ══ -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">

                @php $quickLinks = [
                    ['route'=>'monitoreo.index',         'icon'=>'map-marked-alt',  'label'=>'Monitoreo en Vivo',    'sub'=>'Seguimiento GPS',        'bg'=>'bg-blue-100',   'text'=>'text-blue-600',   'hover'=>'hover:border-blue-300'],
                    ['route'=>'camiones.index',          'icon'=>'truck-loading',   'label'=>'Gestión de Camiones',  'sub'=>'Asignar rutas',          'bg'=>'bg-green-100',  'text'=>'text-green-600',  'hover'=>'hover:border-green-300'],
                    ['route'=>'admin.conductores.index', 'icon'=>'users',           'label'=>'Conductores',          'sub'=>'Administrar personal',   'bg'=>'bg-purple-100', 'text'=>'text-purple-600', 'hover'=>'hover:border-purple-300'],
                    ['route'=>'rutas.index',             'icon'=>'route',           'label'=>'Rutas',                'sub'=>'Configurar trayectos',   'bg'=>'bg-indigo-100', 'text'=>'text-indigo-600', 'hover'=>'hover:border-indigo-300'],
                    ['route'=>'recorridos.index',        'icon'=>'chart-bar',       'label'=>'Reportes',             'sub'=>'Historial y análisis',   'bg'=>'bg-orange-100', 'text'=>'text-orange-600', 'hover'=>'hover:border-orange-300'],
                    ['route'=>'admin.encargados.index',  'icon'=>'users-cog',       'label'=>'Gestionar Encargados', 'sub'=>'Crear y administrar',    'bg'=>'bg-rose-100',   'text'=>'text-rose-600',   'hover'=>'hover:border-rose-300'],
                ]; @endphp

                @foreach ($quickLinks as $i => $link)
                <a href="{{ route($link['route']) }}"
                   class="bg-white rounded-2xl card-shadow hover-lift border border-transparent {{ $link['hover'] }} p-5 flex items-center gap-4 anim-u{{ min($i+1,4) }}"
                   style="animation-delay:{{ $i * 0.06 }}s">
                    <div class="{{ $link['bg'] }} p-4 rounded-xl flex items-center justify-center" style="min-width:52px;min-height:52px;">
                        <i class="fas fa-{{ $link['icon'] }} {{ $link['text'] }} text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm">{{ $link['label'] }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $link['sub'] }}</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 ml-auto text-xs"></i>
                </a>
                @endforeach

            </div>

            <!-- ══ FOOTER ══ -->
            <div class="pt-5 border-t border-gray-200 flex justify-between items-center text-xs text-gray-400">
                <span><i class="fas fa-copyright mr-1"></i> GeoFlota {{ date('Y') }}</span>
                <span class="inline-flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-400 inline-block" style="animation:ripple 2s ease infinite;"></span>
                    Sistema operativo
                </span>
            </div>
        </div>
    </div>

    <script>
    // Animar barras de progreso al cargar
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.progress-bar').forEach(bar => {
            const w = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => { bar.style.width = w; }, 300);
        });
    });
    </script>
</body>
</html>