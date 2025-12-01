<?php
// se inicia la sesion para poder usar variables de sesion
session_start();

// se incluye el archivo de configuracion donde esta la conexion pdo
require_once 'config/config.php';

// se indica que lo que se va a responder es json
header('Content-Type: application/json');

// se obtiene el contenido enviado por el cliente en formato json
$input = json_decode(file_get_contents('php://input'), true);

// si no llegaron los datos correo y contrasena se devuelve un error
if (!$input || !isset($input['correo']) || !isset($input['contrasena'])) {
    echo json_encode([
        'success' => false,
        'error' => 'datos incompletos'
    ]);
    exit();
}

// se limpian los datos que vienen del cliente
$correo = trim($input['correo']);
$contrasena = trim($input['contrasena']);

try {
    // se prepara la consulta para buscar al usuario en la tabla usuarios por el correo
    // si esta activo y si tiene un medico asociado tambien se jala su nombre completo
    $stmt = $pdo->prepare("
        SELECT u.IdUsuario, u.Usuario, u.ContrasenaHash, u.Rol, u.Activo, u.IdMedico,
               COALESCE(m.NombreCompleto, u.Usuario) as NombreCompleto
        FROM usuarios u
        LEFT JOIN controlmedicos m ON u.IdMedico = m.IdMedico
        WHERE u.Correo = :correo AND u.Activo = 1
    ");
    
    // se ejecuta la consulta con el correo enviado por el usuario
    $stmt->execute(['correo' => $correo]);

    // se obtiene el usuario si existe
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // si no existe o esta inactivo se devuelve mensaje de error
    if (!$usuario) {
        echo json_encode([
            'success' => false,
            'error' => 'usuario no encontrado o inactivo'
        ]);
        exit();
    }
    
    // aqui se verifica la contrasena
    // primero se intenta con password_verify por si esta hasheada
    if (password_verify($contrasena, $usuario['ContrasenaHash'])) {
        $contrasenaValida = true;
    } 
    // si no coincide y si la contrasena es texto plano se compara directo
    elseif ($contrasena === $usuario['ContrasenaHash']) {
        $contrasenaValida = true;
    } else {
        $contrasenaValida = false;
    }
    
    // si la contrasena no es valida se dibuja error
    if (!$contrasenaValida) {
        echo json_encode([
            'success' => false,
            'error' => 'contrasena incorrecta'
        ]);
        exit();
    }
    
    // si la contrasena es correcta se crean las variables de sesion
    $_SESSION['usuario_id'] = $usuario['IdUsuario'];
    $_SESSION['nombre_usuario'] = $usuario['NombreCompleto'];
    $_SESSION['rol'] = strtolower($usuario['Rol']);
    $_SESSION['correo'] = $correo;
    $_SESSION['id_medico'] = $usuario['IdMedico'];
    
    // se actualiza el ultimo acceso del usuario
    $stmt = $pdo->prepare("UPDATE usuarios SET UltimoAcceso = NOW() WHERE IdUsuario = :id");
    $stmt->execute(['id' => $usuario['IdUsuario']]);
    
    // se registra en la bitacora que el inicio de sesion fue exitoso
    $stmt = $pdo->prepare("
        INSERT INTO bitacoraacceso (IdUsuario, FechaAcceso, AccionRealizada, Modulo) 
        VALUES (:id, NOW(), 'inicio de sesion exitoso', 'login')
    ");
    $stmt->execute(['id' => $usuario['IdUsuario']]);
    
    // se responde al cliente que el login fue correcto
    echo json_encode([
        'success' => true,
        'mensaje' => 'inicio de sesion exitoso',
        'usuario' => [
            'nombre' => $usuario['NombreCompleto'],
            'rol' => $usuario['Rol']
        ]
    ]);
    
} catch (PDOException $e) {
    // si hubo un error en la base de datos se devuelve un mensaje generico
    echo json_encode([
        'success' => false,
        'error' => 'error en el servidor ' . $e->getMessage()
    ]);
}
?>
