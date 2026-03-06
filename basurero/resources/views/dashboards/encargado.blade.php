<x-encargado-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-gray-800 leading-tight flex items-center gap-2">
                    <span style="width:36px;height:36px;background:linear-gradient(135deg,#059669,#047857);border-radius:10px;display:inline-flex;align-items:center;justify-content:center;">
                        <i class="fas fa-clipboard-list text-white text-sm"></i>
                    </span>
                    Panel de Encargado
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Bienvenido, <strong>{{ Auth::user()->name }}</strong></p>
            </div>
            <div style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);padding:9px 16px;border-radius:12px;border:1px solid #a7f3d0;" class="flex items-center gap-2">
                <i class="fas fa-clock text-green-500 text-sm"></i>
                <span class="text-sm font-semibold text-green-800">{{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </x-slot>

    {{-- Estilos propios de este dashboard --}}
    @push('styles')
    <style>
        @keyframes fadeInUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        @keyframes countUp{from{opacity:0;transform:translateY(12px) scale(.88)}to{opacity:1;transform:translateY(0) scale(1)}}
        @keyframes pulseGlow{0%,100%{box-shadow:0 0 0 0 rgba(16,185,129,.4)}50%{box-shadow:0 0 0 8px rgba(16,185,129,0)}}
        @keyframes ripple{0%{transform:scale(1);opacity:.9}100%{transform:scale(2.6);opacity:0}}

        .anim-u1{animation:fadeInUp .5s ease .08s both;}
        .anim-u2{animation:fadeInUp .5s ease .16s both;}
        .anim-u3{animation:fadeInUp .5s ease .24s both;}
        .anim-u4{animation:fadeInUp .5s ease .32s both;}
        .anim-stat{animation:countUp .65s cubic-bezier(.22,1,.36,1) .4s both;}

        /* Tarjetas estadísticas */
        .enc-stat-card{
            border-radius:20px;overflow:hidden;
            box-shadow:0 8px 26px rgba(0,0,0,.1);
            transition:transform .3s cubic-bezier(.22,1,.36,1),box-shadow .3s ease;
            position:relative;
        }
        .enc-stat-card:hover{transform:translateY(-6px) scale(1.01);box-shadow:0 18px 40px rgba(0,0,0,.17);}
        .enc-stat-card::before{content:'';position:absolute;inset:0;background:linear-gradient(rgba(255,255,255,.08),transparent);pointer-events:none;}

        .enc-icon{
            width:54px;height:54px;border-radius:999px;
            background:rgba(255,255,255,.2);
            display:flex;align-items:center;justify-content:center;
            font-size:1.4rem;color:#fff;
            transition:transform .3s ease;
        }
        .enc-stat-card:hover .enc-icon{transform:rotate(12deg) scale(1.12);}

        /* Tarjetas de acceso */
        .enc-access-card{
            background:#fff;border-radius:18px;
            box-shadow:0 4px 16px rgba(0,0,0,.07);
            transition:transform .3s ease,box-shadow .3s ease,border-color .2s;
            border:1.5px solid transparent;
            display:flex;align-items:center;gap:16px;
            padding:18px 20px;
        }
        .enc-access-card:hover{
            transform:translateY(-5px);
            box-shadow:0 14px 32px rgba(0,0,0,.12);
        }
        .dot-live{
            width:8px;height:8px;border-radius:50%;
            background:currentColor;
            animation:ripple 1.5s ease infinite;
        }
    </style>
    @endpush

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- ══ ESTADÍSTICAS ══ -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <!-- Camiones totales -->
                <div class="enc-stat-card anim-u1" style="background:linear-gradient(135deg,#2563eb,#1e40af);">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-xs font-semibold uppercase tracking-widest">Camiones</p>
                                <p class="text-4xl font-black mt-2 anim-stat">{{ $totalCamiones }}</p>
                                <p class="text-blue-200 text-xs mt-1">Total de la flota</p>
                            </div>
                            <div class="enc-icon"><i class="fas fa-truck"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Camiones activos -->
                <div class="enc-stat-card anim-u2" style="background:linear-gradient(135deg,#059669,#047857);">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-green-100 text-xs font-semibold uppercase tracking-widest">Activos</p>
                                <p class="text-4xl font-black mt-2 anim-stat">{{ $camionesActivos }}</p>
                                <p class="text-green-200 text-xs mt-1 flex items-center gap-1">
                                    <span class="dot-live" style="background:#86efac;color:#86efac;width:7px;height:7px;"></span>
                                    En servicio
                                </p>
                            </div>
                            <div class="enc-icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Rutas activas -->
                <div class="enc-stat-card anim-u3" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-purple-100 text-xs font-semibold uppercase tracking-widest">Rutas</p>
                                <p class="text-4xl font-black mt-2 anim-stat">{{ $totalRutas }}</p>
                                <p class="text-purple-200 text-xs mt-1">Configuradas</p>
                            </div>
                            <div class="enc-icon"><i class="fas fa-route"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Recorridos hoy -->
                <div class="enc-stat-card anim-u4" style="background:linear-gradient(135deg,#d97706,#b45309);">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-orange-100 text-xs font-semibold uppercase tracking-widest">Recorridos Hoy</p>
                                <p class="text-4xl font-black mt-2 anim-stat">{{ $recorridosHoy }}</p>
                                <p class="text-orange-200 text-xs mt-1">
                                    <i class="fas fa-calendar-day mr-1"></i> Hoy
                                </p>
                            </div>
                            <div class="enc-icon"><i class="fas fa-clock"></i></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ══ ACCESOS RÁPIDOS ══ -->
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4 anim-u2">Acceso Rápido</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

                @php $encLinks = [
                    ['route'=>'camiones.index',          'icon'=>'truck',          'label'=>'Camiones',          'sub'=>'Gestionar flota',       'bg'=>'bg-blue-100',   'text'=>'text-blue-600',   'hb'=>'hover:border-blue-300'],
                    ['route'=>'rutas.index',             'icon'=>'route',          'label'=>'Rutas',             'sub'=>'Administrar rutas',     'bg'=>'bg-green-100',  'text'=>'text-green-600',  'hb'=>'hover:border-green-300'],
                    ['route'=>'monitoreo.index',         'icon'=>'satellite-dish', 'label'=>'Monitoreo',         'sub'=>'Seguimiento GPS',       'bg'=>'bg-purple-100', 'text'=>'text-purple-600', 'hb'=>'hover:border-purple-300'],
                    ['route'=>'recorridos.index',        'icon'=>'history',        'label'=>'Recorridos',        'sub'=>'Historial',             'bg'=>'bg-orange-100', 'text'=>'text-orange-600', 'hb'=>'hover:border-orange-300'],
                    ['route'=>'admin.conductores.index', 'icon'=>'users',          'label'=>'Conductores',       'sub'=>'Ver todos los conductores', 'bg'=>'bg-teal-100',   'text'=>'text-teal-600',   'hb'=>'hover:border-teal-300'],
                   
                ]; @endphp

                @foreach($encLinks as $i => $link)
                <a href="{{ route($link['route']) }}"
                   class="enc-access-card {{ $link['hb'] }} anim-u{{ $i+1 }}"
                   style="animation-delay:{{ ($i * 0.07) + 0.4 }}s">
                    <div class="{{ $link['bg'] }} p-4 rounded-xl flex items-center justify-center" style="min-width:50px;min-height:50px;">
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

            <!-- Footer -->
            <div class="pt-5 border-t border-gray-200 flex justify-between items-center text-xs text-gray-400">
                <span><i class="fas fa-copyright mr-1"></i> GeoFlota {{ date('Y') }}</span>
                <span class="inline-flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-400 inline-block" style="animation:ripple 2s ease infinite;"></span>
                    Sistema operativo
                </span>
            </div>

        </div>
    </div>
</x-encargado-layout>