<?php
session_start();
require_once 'config/config.php';

// Registrar en bitácora antes de cerrar sesión
if (isset($_SESSION['usuario_id'])) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO bitacoraacceso (IdUsuario, FechaAcceso, AccionRealizada, Modulo) 
            VALUES (:id, NOW(), 'Cierre de sesión', 'Logout')
        ");
        $stmt->execute(['id' => $_SESSION['usuario_id']]);
    } catch (PDOException $e) {
        error_log("Error al registrar bitácora de logout: " . $e->getMessage());
    }
}

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: index.php");
exit();
?>