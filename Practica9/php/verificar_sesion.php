<?php
session_start();

// Verificar si el usuario está logueado
/*if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}*/

// Obtener información del usuario de la sesión
//$nombreUsuario = $_SESSION['nombre_usuario'] ?? 'Usuario';
//$rolUsuario = $_SESSION['rol'] ?? 'invitado';

// Definir permisos por rol
$permisos = [
    'super admin' => [
        'usuarios', 'pacientes', 'agenda', 'medicos', 'reportes', 
        'expedientes', 'pagos', 'tarifas', 'bitacoras', 'especialidades'
    ],
    'medico' => [
        'pacientes', 'agenda', 'expedientes', 'reportes'
    ],
    'secretaria' => [
        'pacientes', 'agenda', 'pagos'
    ],
    'paciente' => [
        // Definir permisos para paciente si es necesario, por ahora vacío o básico
    ],
    'invitado' => []
];

// Función para verificar si el usuario tiene un permiso específico
function tienePermiso($permiso) {
    global $permisos, $rolUsuario;
    
    // Normalizar el rol a minúsculas
    $rol = strtolower($rolUsuario);
    
    // Verificar si el rol existe en el array de permisos
    if (!isset($permisos[$rol])) {
        return false;
    }
    
    // Verificar si el permiso está en la lista del rol
    return in_array($permiso, $permisos[$rol]);
}

// Función para verificar acceso a una página específica
function verificarAccesoAPagina($permisoRequerido) {
    if (!tienePermiso($permisoRequerido)) {
        header("Location: dash.php?error=sin_permiso");
        exit();
    }
}
?>