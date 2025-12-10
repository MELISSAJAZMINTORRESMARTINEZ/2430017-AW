<?php
require "config.php";

$titulo = $_POST["titulo"];
$autor = $_POST["autor"];
$año = $_POST["año"];
$genero = $_POST["genero"];
$disponible= $_POST["disponible"];



// Usando prepared statements para evitar SQL injection
$stmt = $pdo->prepare("INSERT INTO libros (titulo, autor, año, genero, disponible) VALUES (:titulo, :autor, :año, :genero, :disponible)");
$stmt->bindParam(':titulo', $titulo);
$stmt->bindParam(':autor', $autor);
$stmt->bindParam(':año', $año);
$stmt->bindParam(':genero', $genero);
$stmt->bindParam(':disponible', $disponible);

$stmt->execute();
?>