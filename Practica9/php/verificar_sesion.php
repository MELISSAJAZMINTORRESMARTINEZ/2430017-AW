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

// SI EL USUARIO ES MÉDICO, OBTENER SU IdMedico DE LA BASE DE DATOS
if (strtolower($rolUsuario) === 'medico' && !isset($_SESSION['id_medico'])) {
    // Conectar a la base de datos
    $host = "localhost";
    $port = "3306";
    $dbname = "clinica";
    $user = "admin";
    $pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554";
    
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Obtener el IdMedico basado en el nombre de usuario
        // AJUSTA ESTA CONSULTA según cómo relacionas usuarios con médicos en tu BD
        $sql = "SELECT IdMedico FROM controlmedicos WHERE NombreCompleto = :nombre LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombreUsuario);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado) {
            $_SESSION['id_medico'] = $resultado['IdMedico'];
        }
        
    } catch (PDOException $e) {
        // Si hay error, no hacer nada (o registrar error)
    }
}

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
    'paciente' => array(
        'agenda',
    ),
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