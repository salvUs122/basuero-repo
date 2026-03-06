<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mi Recorrido
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                @if (session('success'))
                    <p style="color:green;">{{ session('success') }}</p>
                @endif

                @if (session('error'))
                    <p style="color:red;">{{ session('error') }}</p>
                @endif

                @if ($recorridoActivo)
                    <p><b>Recorrido en curso ✅</b></p>
                    <p>Ruta: {{ $recorridoActivo->ruta->nombre }}</p>
                    <p>Camión: {{ $recorridoActivo->camion->placa }}</p>
                    <p>Inicio: {{ $recorridoActivo->fecha_inicio }}</p>

                    <form method="POST" action="{{ route('conductor.recorrido.finalizar') }}">
                        @csrf
                        <button type="submit" style="margin-top: 10px;">Finalizar recorrido</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('conductor.recorrido.iniciar') }}">
                        @csrf

                        <div>
                            <label>Camión</label>
                            <select name="camion_id" required>
                                @foreach($camiones as $camion)
                                    <option value="{{ $camion->id }}">
                                        {{ $camion->placa }} ({{ $camion->codigo }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="margin-top:10px;">
                            <label>Ruta</label>
                            <select name="ruta_id" required>
                                @foreach($rutas as $ruta)
                                    <option value="{{ $ruta->id }}">{{ $ruta->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" style="margin-top: 10px;">Iniciar recorrido</button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
