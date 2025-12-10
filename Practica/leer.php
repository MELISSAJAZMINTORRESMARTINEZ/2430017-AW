<?php
require "config.php";

$stmt = $pdo->query("SELECT * FROM libros");

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Titulo</th><th>Autor</th><th>Año</th><th>Genero</th><th>Disponibildad</th><th>Acciones</th></tr>";

while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Escapar datos para prevenir XSS
    $id = htmlspecialchars($fila['id']);
    $titulo = htmlspecialchars($fila['titulo']);
    $autor = htmlspecialchars($fila['autor']);
    $año = htmlspecialchars($fila['año']);
    $genero = htmlspecialchars($fila['genero']);
    $disponible = htmlspecialchars($fila['disponible']);

    
    echo "<tr>
            <td>{$id}</td>
            <td>{$titulo}</td>
            <td>{$autor}</td>
            <td>{$año}</td>
            <td>{$genero}</td>
            <td>{$disponible}</td>
            <td>
                <button onclick='editar({$id})'>Editar</button>
                <button onclick='eliminarUsuario({$id})'>Eliminar</button>
            </td>
        </tr>";
}

echo "</table>";
?>