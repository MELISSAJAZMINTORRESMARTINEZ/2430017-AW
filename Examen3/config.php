<?php
// config.php - Configuración centralizada de la base de datos

$dbHost = 'localhost';
$dbName = 'banco_db';    
$dbUser = 'root';        
$dbPass = '';           

try {
    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'mensaje' => 'Error de conexión: ' . $e->getMessage()]));
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
