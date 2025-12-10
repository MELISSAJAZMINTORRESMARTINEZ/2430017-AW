<?php
require "config.php";

$nombre = $_POST["nombre"];
$email = $_POST["email"];

// Usando prepared statements para evitar SQL injection
$stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email) VALUES (:nombre, :email)");
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':email', $email);
$stmt->execute();
?>