<x-dinamico-layout>
<x-slot name="header">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                👤 Gestión de Conductores
            </h2>
            <p class="text-sm text-gray-600 mt-1">Administra los conductores del sistema</p>
        </div>
    </div>
</x-slot>
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- 🔴 BOTÓN DE RESPALDO DENTRO DEL CONTENIDO -->
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.conductores.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 flex items-center shadow-lg hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>
                Nuevo Conductor
            </a>
        </div>
        <!-- Mensajes -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tarjetas de estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Conductores</p>
                        <p class="text-3xl font-bold mt-2">{{ $conductores->count() }}</p>
                    </div>
                    <div class="bg-blue-400 p-3 rounded-full">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Activos</p>
                        <p class="text-3xl font-bold mt-2">{{ $conductores->count() }}</p>
                    </div>
                    <div class="bg-green-400 p-3 rounded-full">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Con Licencia</p>
                        <p class="text-3xl font-bold mt-2">{{ $conductores->whereNotNull('licencia')->count() }}</p>
                    </div>
                    <div class="bg-purple-400 p-3 rounded-full">
                        <i class="fas fa-id-card text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de conductores -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Lista de Conductores</h3>
            </div>
            
            <div class="overflow-x-auto">
                @if($conductores->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Licencia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($conductores as $conductor)
                        <tr class="hover:bg-gray-50" id="conductor-{{ $conductor->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $conductor->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $conductor->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $conductor->telefono ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $conductor->licencia ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="verConductor({{ $conductor->id }})" 
                                            class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition-colors">
                                        <i class="fas fa-eye mr-1"></i> Ver
                                    </button>
                                    <a href="{{ route('admin.conductores.edit', $conductor->id) }}" 
                                       class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-lg transition-colors">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </a>
                                    <button onclick="eliminarConductor({{ $conductor->id }}, '{{ $conductor->name }}')" 
                                            class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg transition-colors">
                                        <i class="fas fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-12">
                    <div class="text-gray-300 mb-4">
                        <i class="fas fa-users text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700">No hay conductores registrados</h3>
                    <p class="mt-1 text-sm text-gray-500">Comienza agregando tu primer conductor.</p>
                    <a href="{{ route('admin.conductores.create') }}" 
                       class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Agregar primer conductor
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Conductor -->
<div id="modal-ver" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-user mr-2 text-blue-600"></i>
                Detalles del Conductor
            </h3>
            <button onclick="cerrarModalVer()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="modal-contenido" class="space-y-3">
            <!-- Contenido cargado por JS -->
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="cerrarModalVer()" 
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div id="modal-eliminar" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Eliminar Conductor</h3>
            <p class="text-sm text-gray-500 mb-4" id="mensaje-eliminar"></p>
            <div class="flex justify-center space-x-3">
                <button onclick="cerrarModalEliminar()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancelar
                </button>
                <button id="btn-confirmar-eliminar" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let conductorAEliminar = null;

function verConductor(id) {
    fetch(`/admin/conductores/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modal-contenido').innerHTML = `
                <div class="border-b pb-2"><span class="font-semibold">Nombre:</span> ${data.name}</div>
                <div class="border-b pb-2"><span class="font-semibold">Email:</span> ${data.email}</div>
                <div class="border-b pb-2"><span class="font-semibold">Teléfono:</span> ${data.telefono || 'N/A'}</div>
                <div class="border-b pb-2"><span class="font-semibold">Licencia:</span> ${data.licencia || 'N/A'}</div>
                <div class="border-b pb-2"><span class="font-semibold">Dirección:</span> ${data.direccion || 'N/A'}</div>
                <div><span class="font-semibold">Registrado:</span> ${data.created_at}</div>
            `;
            document.getElementById('modal-ver').classList.remove('hidden');
        });
}

function cerrarModalVer() {
    document.getElementById('modal-ver').classList.add('hidden');
}

function eliminarConductor(id, nombre) {
    conductorAEliminar = id;
    document.getElementById('mensaje-eliminar').textContent = 
        `¿Estás seguro de eliminar al conductor "${nombre}"?`;
    document.getElementById('modal-eliminar').classList.remove('hidden');
}

function cerrarModalEliminar() {
    document.getElementById('modal-eliminar').classList.add('hidden');
    conductorAEliminar = null;
}

document.getElementById('btn-confirmar-eliminar')?.addEventListener('click', function() {
    if (!conductorAEliminar) return;
    
    fetch(`/admin/conductores/${conductorAEliminar}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`conductor-${conductorAEliminar}`).remove();
            alert(data.message);
        } else {
            alert(data.message);
        }
        cerrarModalEliminar();
    })
    .catch(error => {
        alert('Error al eliminar conductor');
        cerrarModalEliminar();
    });
});

// Cerrar modal al hacer clic fuera
document.getElementById('modal-ver').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalVer();
});

document.getElementById('modal-eliminar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalEliminar();
});
</script>
</x-dinamico-layout>