<?php
// se inicia la sesion para poder usar las variables de sesion
session_start();

// aqui se incluye el archivo de configuracion donde esta la conexion pdo
require_once 'php/config/config.php';

// antes de cerrar la sesion se registra en la bitacora que el usuario cerro sesion
// esto solo se hace si existe un usuario logueado
if (isset($_SESSION['usuario_id'])) {
    try {
        // se prepara la consulta para insertar un registro en la bitacora
        $stmt = $pdo->prepare("
            INSERT INTO bitacoraacceso (IdUsuario, FechaAcceso, AccionRealizada, Modulo) 
            VALUES (:id, NOW(), 'Cierre de sesion', 'Logout')
        ");

        // se ejecuta la consulta enviando el id del usuario que cerro sesion
        $stmt->execute(['id' => $_SESSION['usuario_id']]);
    } catch (PDOException $e) {
        // si hubo un error en la base de datos se guarda en el log del servidor
        error_log("Error al registrar bitacora de logout " . $e->getMessage());
    }
}

// aqui se vacian todas las variables de sesion dejandolas en un arreglo vacio
$_SESSION = array();

// si existe una cookie de sesion se borra
if (isset($_COOKIE[session_name()])) {
    // se manda una cookie expirada para asegurar que se elimine
    setcookie(session_name(), '', time() - 3600, '/');
}

// ahora se destruye completamente la sesion en el servidor
session_destroy();

// despues de destruir la sesion se redirige al login
header("Location: index.php");
exit();
?>
