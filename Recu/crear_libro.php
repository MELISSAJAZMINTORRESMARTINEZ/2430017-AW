<?php
require "config.php";

// Verificar que se recibieron los datos
if (!isset($_POST["nombre"]) || !isset($_POST["autor"]) || !isset($_POST["categoria"]) || 
    !isset($_POST["paginas"]) || !isset($_POST["editorial"])) {
    http_response_code(400);
    die("Faltan datos requeridos");
}

$nombre = $_POST["nombre"];
$autor = $_POST["autor"];
$categoria = $_POST["categoria"];
$paginas = $_POST["paginas"];   
$editorial = $_POST["editorial"];

try {
    $stmt = $pdo->prepare("INSERT INTO libros (nombre, autor, categoria, paginas, editorial) VALUES (:nombre, :autor, :categoria, :paginas, :editorial)");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':paginas', $paginas, PDO::PARAM_INT);
    $stmt->bindParam(':editorial', $editorial);
    $stmt->execute();
    
    http_response_code(200);
    echo "Libro creado exitosamente";
} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error al crear libro: " . $e->getMessage());
    die("Error al crear el libro");
}
?>