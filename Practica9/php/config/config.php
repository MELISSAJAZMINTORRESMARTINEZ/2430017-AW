<?php
// Configuración de la base de datos
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "admin";
$pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554";
$charset = "utf8mb4";

// Crear cadena de conexión DSN
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

// Opciones de PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];

try {
    // Crear instancia PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // En producción, no mostrar detalles del error
    error_log("Error de conexión a BD: " . $e->getMessage());
    die("Error al conectar con la base de datos. Por favor contacte al administrador.");
}
?>