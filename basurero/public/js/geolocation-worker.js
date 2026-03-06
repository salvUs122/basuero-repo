// Worker para geolocalización en segundo plano - VERSIÓN POR DISTANCIA
let watchId = null;
let url = '';
let token = '';
let recorridoId = null;
let puntosPendientes = [];

// Configuración
const DISTANCIA_MINIMA = 10; // Metros mínimos para enviar un nuevo punto
const TIEMPO_MAXIMO = 30000; // 30 segundos - envío forzado aunque no haya movimiento
const MAX_PUNTOS_PENDIENTES = 10; // Máximo de puntos a acumular antes de envío forzado

let ultimaPosicion = null;
let ultimoEnvio = 0;
let intervaloForzado = null;

// Escuchar mensajes desde la aplicación principal
self.addEventListener('message', function(e) {
    const data = e.data;
    
    switch(data.tipo) {
        case 'INICIAR':
            iniciarSeguimiento(data);
            break;
        case 'DETENER':
            detenerSeguimiento();
            break;
        case 'ACTUALIZAR_CONFIG':
            actualizarConfig(data);
            break;
    }
});

function iniciarSeguimiento(data) {
    url = data.url;
    token = data.token;
    recorridoId = data.recorridoId;
    ultimoEnvio = Date.now();
    
    if (!navigator.geolocation) {
        self.postMessage({ tipo: 'ERROR', mensaje: 'Geolocalización no soportada' });
        return;
    }
    
    // Opciones de alta precisión
    const opciones = {
        enableHighAccuracy: true,
        maximumAge: 0,
        timeout: 10000
    };
    
    // Iniciar watchPosition para seguimiento continuo
    watchId = navigator.geolocation.watchPosition(
        posicionRecibida,
        errorRecibido,
        opciones
    );
    
    // Intervalo para envío forzado (evitar que pasen >30s sin enviar)
    intervaloForzado = setInterval(envioForzado, 5000);
    
    self.postMessage({ tipo: 'INICIADO' });
}

function detenerSeguimiento() {
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }
    
    if (intervaloForzado !== null) {
        clearInterval(intervaloForzado);
        intervaloForzado = null;
    }
    
    // Enviar puntos pendientes antes de detener
    if (puntosPendientes.length > 0) {
        enviarPuntosPendientes();
    }
    
    ultimaPosicion = null;
    self.postMessage({ tipo: 'DETENIDO' });
}

function actualizarConfig(data) {
    if (data.url) url = data.url;
    if (data.token) token = data.token;
    if (data.recorridoId) recorridoId = data.recorridoId;
}

/**
 * Calcula la distancia entre dos coordenadas (Haversine)
 * @returns {number} Distancia en metros
 */
function calcularDistancia(pos1, pos2) {
    const R = 6371000; // Radio de la Tierra en metros
    const lat1 = pos1.lat * Math.PI / 180;
    const lat2 = pos2.lat * Math.PI / 180;
    const deltaLat = (pos2.lat - pos1.lat) * Math.PI / 180;
    const deltaLng = (pos2.lng - pos1.lng) * Math.PI / 180;

    const a = Math.sin(deltaLat/2) * Math.sin(deltaLat/2) +
              Math.cos(lat1) * Math.cos(lat2) *
              Math.sin(deltaLng/2) * Math.sin(deltaLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    
    return R * c;
}

function posicionRecibida(posicion) {
    const puntoActual = {
        lat: posicion.coords.latitude,
        lng: posicion.coords.longitude,
        precision_m: posicion.coords.accuracy,
        velocidad_mps: posicion.coords.speed,
        rumbo_grados: posicion.coords.heading,
        fecha_gps: new Date().toISOString().slice(0, 19).replace('T', ' '),
        timestamp: Date.now()
    };
    
    // Si es la primera posición, enviar inmediatamente
    if (ultimaPosicion === null) {
        puntosPendientes.push(puntoActual);
        enviarPuntosPendientes();
        ultimaPosicion = {
            lat: puntoActual.lat,
            lng: puntoActual.lng
        };
        return;
    }
    
    // Calcular distancia desde la última posición enviada
    const distancia = calcularDistancia(ultimaPosicion, {
        lat: puntoActual.lat,
        lng: puntoActual.lng
    });
    
    // 🔴 NÚCLEO DE LA LÓGICA: Enviar si se movió >= DISTANCIA_MINIMA
    if (distancia >= DISTANCIA_MINIMA) {
        puntosPendientes.push(puntoActual);
        
        // Actualizar última posición enviada
        ultimaPosicion = {
            lat: puntoActual.lat,
            lng: puntoActual.lng
        };
        
        // Enviar inmediatamente si alcanzamos el máximo de pendientes
        if (puntosPendientes.length >= MAX_PUNTOS_PENDIENTES) {
            enviarPuntosPendientes();
        }
    }
    
    // Notificar a la app principal (para UI)
    self.postMessage({
        tipo: 'POSICION',
        punto: puntoActual,
        distancia: Math.round(distancia),
        pendientes: puntosPendientes.length
    });
}

function envioForzado() {
    const ahora = Date.now();
    
    // Si ha pasado más de TIEMPO_MAXIMO sin enviar, forzar envío
    if (puntosPendientes.length > 0 && (ahora - ultimoEnvio) > TIEMPO_MAXIMO) {
        enviarPuntosPendientes();
    }
}

function errorRecibido(error) {
    self.postMessage({
        tipo: 'ERROR',
        codigo: error.code,
        mensaje: error.message
    });
}

async function enviarPuntosPendientes() {
    if (puntosPendientes.length === 0) return;
    
    const puntosAEnviar = [...puntosPendientes];
    puntosPendientes = [];
    ultimoEnvio = Date.now();
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                puntos: puntosAEnviar,
                recorrido_id: recorridoId
            })
        });
        
        if (response.ok) {
            const resultado = await response.json();
            self.postMessage({
                tipo: 'ENVIADO',
                cantidad: puntosAEnviar.length,
                resultado: resultado
            });
        } else {
            // Si hay error, devolver puntos a la cola
            puntosPendientes = [...puntosAEnviar, ...puntosPendientes];
            self.postMessage({
                tipo: 'ERROR_ENVIO',
                cantidad: puntosAEnviar.length,
                status: response.status
            });
        }
    } catch (error) {
        // Si hay error de red, devolver puntos a la cola
        puntosPendientes = [...puntosAEnviar, ...puntosPendientes];
        self.postMessage({
            tipo: 'ERROR_ENVIO',
            cantidad: puntosAEnviar.length,
            error: error.message
        });
    }
}