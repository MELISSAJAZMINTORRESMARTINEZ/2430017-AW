<?php
require "config.php";

$id = $_POST["id"];
$nombre = $_POST["nombre"];
$email = $_POST["email"];

// Usando prepared statements para evitar SQL injection
$stmt = $pdo->prepare("UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':email', $email);
$stmt->execute();
?>