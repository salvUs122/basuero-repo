<x-dinamico-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ✏️ Editar Camión
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-red-800 font-medium">Por favor corrige los siguientes errores:</p>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex items-center">
                        <div class="bg-blue-600 p-2 rounded-lg mr-3">
                            <i class="fas fa-truck text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Editar Camión</h3>
                            <p class="text-sm text-gray-600">Modifica la información del camión</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('camiones.update', $camion) }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Placa -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Placa del Camión *
                            </label>
                            <input type="text" name="placa" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   placeholder="Ej: ABC-1234"
                                   value="{{ old('placa', $camion->placa) }}">
                            <p class="mt-1 text-sm text-gray-500">Placa oficial del vehículo</p>
                        </div>

                        <!-- Código -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Código Interno *
                            </label>
                            <input type="text" name="codigo" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   placeholder="Ej: CAM-001"
                                   value="{{ old('codigo', $camion->codigo) }}">
                            <p class="mt-1 text-sm text-gray-500">Código único para identificación interna</p>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Estado del Camión *
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Activo -->
                                <label class="relative flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="estado" value="activo" 
                                           {{ old('estado', $camion->estado) == 'activo' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900">Activo</span>
                                        <span class="block text-sm text-gray-500">En servicio</span>
                                    </div>
                                    <div class="absolute top-4 right-4">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    </div>
                                </label>
                                
                                <!-- Inactivo -->
                                <label class="relative flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="estado" value="inactivo" 
                                           {{ old('estado', $camion->estado) == 'inactivo' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900">Inactivo</span>
                                        <span class="block text-sm text-gray-500">Fuera de servicio</span>
                                    </div>
                                    <div class="absolute top-4 right-4">
                                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                    </div>
                                </label>
                                
                                <!-- Mantenimiento -->
                                <label class="relative flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="estado" value="mantenimiento" 
                                           {{ old('estado', $camion->estado) == 'mantenimiento' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900">Mantenimiento</span>
                                        <span class="block text-sm text-gray-500">En reparación</span>
                                    </div>
                                    <div class="absolute top-4 right-4">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="pt-6 border-t border-gray-200 flex justify-end space-x-3">
                            <a href="{{ route('camiones.index') }}" 
                               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-medium shadow-lg hover:shadow-xl">
                                <i class="fas fa-save mr-2"></i>
                                Actualizar Camión
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-dinamico-layout>