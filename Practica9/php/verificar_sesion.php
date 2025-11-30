<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// MODO DESARROLLO - Eliminar en producción
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = 1;
    $_SESSION['nombre_usuario'] = 'Administrador';
    $_SESSION['rol'] = 'super admin';
}

// Obtener información del usuario
$nombreUsuario = isset($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : 'Usuario';
$rolUsuario = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'invitado';

// Permisos por rol - Usando array() en lugar de []
$permisos = array(
    'super admin' => array(
        'usuarios', 'pacientes', 'agenda', 'medicos', 'reportes', 
        'expedientes', 'pagos', 'tarifas', 'bitacoras', 'especialidades'
    ),
    'medico' => array(
        'pacientes', 'agenda', 'expedientes', 'reportes'
    ),
    'secretaria' => array(
        'pacientes', 'agenda', 'pagos'
    ),
    'paciente' => array(),
    'invitado' => array()
);

// Función para verificar permisos
function tienePermiso($permiso) {
    global $permisos, $rolUsuario;
    
    $rol = strtolower($rolUsuario);
    
    if (!isset($permisos[$rol])) {
        return false;
    }
    
    return in_array($permiso, $permisos[$rol]);
}

// Función para verificar acceso a una página
function verificarAccesoAPagina($permisoRequerido) {
    if (!tienePermiso($permisoRequerido)) {
        header("Location: /2430017-AW/Practica9/dash.php?error=sin_permiso");
        exit();
    }
}
?>