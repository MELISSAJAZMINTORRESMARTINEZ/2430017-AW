<?php
session_start();
require_once 'config/config.php';

header('Content-Type: application/json');

// Obtener datos JSON del request
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['correo']) || !isset($input['contrasena'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Datos incompletos'
    ]);
    exit();
}

$correo = trim($input['correo']);
$contrasena = trim($input['contrasena']);

try {
    // Buscar usuario por correo en la tabla usuarios
    $stmt = $pdo->prepare("
        SELECT u.IdUsuario, u.Usuario, u.ContrasenaHash, u.Rol, u.Activo, u.IdMedico,
               COALESCE(m.NombreCompleto, u.Usuario) as NombreCompleto
        FROM usuarios u
        LEFT JOIN controlmedicos m ON u.IdMedico = m.IdMedico
        WHERE u.Usuario = :correo AND u.Activo = 1
    ");
    
    $stmt->execute(['correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        echo json_encode([
            'success' => false,
            'error' => 'Usuario no encontrado o inactivo'
        ]);
        exit();
    }
    
    // Verificar contraseña
    // Si la contraseña está hasheada con password_hash
    if (password_verify($contrasena, $usuario['ContrasenaHash'])) {
        $contrasenaValida = true;
    } 
    // Si la contraseña está en texto plano (solo para desarrollo)
    elseif ($contrasena === $usuario['ContrasenaHash']) {
        $contrasenaValida = true;
    } else {
        $contrasenaValida = false;
    }
    
    if (!$contrasenaValida) {
        echo json_encode([
            'success' => false,
            'error' => 'Contraseña incorrecta'
        ]);
        exit();
    }
    
    // Login exitoso - Crear sesión
    $_SESSION['usuario_id'] = $usuario['IdUsuario'];
    $_SESSION['nombre_usuario'] = $usuario['NombreCompleto'];
    $_SESSION['rol'] = strtolower($usuario['Rol']);
    $_SESSION['correo'] = $correo;
    $_SESSION['id_medico'] = $usuario['IdMedico'];
    
    // Actualizar último acceso
    $stmt = $pdo->prepare("UPDATE usuarios SET UltimoAcceso = NOW() WHERE IdUsuario = :id");
    $stmt->execute(['id' => $usuario['IdUsuario']]);
    
    // Registrar en bitácora
    $stmt = $pdo->prepare("
        INSERT INTO bitacoraacceso (IdUsuario, FechaAcceso, AccionRealizada, Modulo) 
        VALUES (:id, NOW(), 'Inicio de sesión exitoso', 'Login')
    ");
    $stmt->execute(['id' => $usuario['IdUsuario']]);
    
    echo json_encode([
        'success' => true,
        'mensaje' => 'Inicio de sesión exitoso',
        'usuario' => [
            'nombre' => $usuario['NombreCompleto'],
            'rol' => $usuario['Rol']
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error en el servidor: ' . $e->getMessage()
    ]);
}
?>