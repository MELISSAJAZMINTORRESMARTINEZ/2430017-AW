<?php
require "config.php";

$id = $_POST["id"];

$stmt = $pdo->prepare("DELETE FROM libros WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
?>