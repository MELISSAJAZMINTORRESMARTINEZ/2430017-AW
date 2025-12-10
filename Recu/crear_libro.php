<?php
require "config.php";

$nombre = $_POST["nombre"];
$autor = $_POST["autor"];
$categoria = $_POST["categoria"];
$paginas = $_POST["paginas"];   
$editorial = $_POST["editorial"];

$stmt = $pdo->prepare("INSERT INTO libros (nombre, autor, categoria, paginas, editorial) VALUES (:nombre, :autor, :categoria, :paginas, :editorial)");
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':autor', $autor);
$stmt->bindParam(':categoria', $categoria);
$stmt->bindParam(':paginas', $paginas);
$stmt->bindParam(':editorial', $editorial);
$stmt->execute();
?>