<?php
require "config.php";

$id = $_POST["id"];
$titulo = $_POST["titulo"];
$autor = $_POST["autor"];
$año = $_POST["año"];
$genero = $_POST["genero"];
$disponible= $_POST["disponible"];


// Usando prepared statements para evitar SQL injection
$stmt = $pdo->prepare("UPDATE libros SET titulo = :titulo, autor = :autor, año = :año, genero = :genero, disponible = :disponible WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->bindParam(':titulo', $titulo);
$stmt->bindParam(':autor', $autor);
$stmt->bindParam(':año', $año);
$stmt->bindParam(':genero', $genero);
$stmt->bindParam(':disponible', $disponible);

$stmt->execute();
?>