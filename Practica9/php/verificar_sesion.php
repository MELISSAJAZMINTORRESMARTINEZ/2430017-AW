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

// Conectar a la base de datos para obtener IDs según el rol
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "admin";
$pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SI EL USUARIO ES MÉDICO, OBTENER SU IdMedico
    if (strtolower($rolUsuario) === 'medico' && !isset($_SESSION['id_medico'])) {
        // Primero intentar obtener desde la tabla usuarios
        $sql = "SELECT IdMedico FROM usuarios WHERE IdUsuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['usuario_id']);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado && $resultado['IdMedico']) {
            $_SESSION['id_medico'] = $resultado['IdMedico'];
        }
    }
    
    // SI EL USUARIO ES PACIENTE, OBTENER SU IdPaciente
    if (strtolower($rolUsuario) === 'paciente' && !isset($_SESSION['id_paciente'])) {
        // Opción 1: Si tienes IdPaciente en la tabla usuarios
        $sql = "SELECT IdPaciente FROM usuarios WHERE IdUsuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['usuario_id']);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado && isset($resultado['IdPaciente'])) {
            $_SESSION['id_paciente'] = $resultado['IdPaciente'];
        } else {
            // Opción 2: Si relacionas por nombre o correo
            // AJUSTA SEGÚN TU ESTRUCTURA DE BASE DE DATOS
            $sql = "SELECT IdPaciente FROM controlpacientes 
                    WHERE NombreCompleto = :nombre 
                    OR CorreoElectronico = (SELECT Correo FROM usuarios WHERE IdUsuario = :id)
                    LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre', $nombreUsuario);
            $stmt->bindParam(':id', $_SESSION['usuario_id']);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultado) {
                $_SESSION['id_paciente'] = $resultado['IdPaciente'];
            }
        }
    }
    
} catch (PDOException $e) {
    // Si hay error, registrar en log (opcional)
    error_log("Error en verificar_sesion.php: " . $e->getMessage());
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
        'agenda', 'expedientes'
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
        header("Location: dash.php?error=sin_permiso");
        exit();
    }
}
?>