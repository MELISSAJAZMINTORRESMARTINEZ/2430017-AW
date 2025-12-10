<?php
require "config.php";

if (!isset($_POST["id"])) {
    http_response_code(400);
    die("ID no proporcionado");
}

$id = $_POST["id"];

try {
    $stmt = $pdo->prepare("DELETE FROM libros WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo "Libro eliminado exitosamente";
    } else {
        http_response_code(404);
        echo "Libro no encontrado";
    }
} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error al eliminar libro: " . $e->getMessage());
    die("Error al eliminar el libro");
}
?>