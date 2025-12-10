<?php
$host = "localhost";
$dbname = "crud_bd";
$user = "admin";
$password = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>