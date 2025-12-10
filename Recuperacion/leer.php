<?php
require "config.php";

$stmt = $pdo->query("SELECT * FROM libros");

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Autor</th><th>Categoria</th><th>Paginas</th><th>Editorial</th><th>Acciones</th></tr>";

while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Escapar datos para prevenir XSS
    $id = htmlspecialchars($fila['id']);
    $nombre = htmlspecialchars($fila['nombre']);
    $autor = htmlspecialchars($fila['autor']);
    $categoria = htmlspecialchars($fila['categoria']);
    $paginas = htmlspecialchars($fila['paginas']);
    $editorial = htmlspecialchars($fila['editorial']);
    
    echo "<tr>
            <td>{$id}</td>
            <td>{$nombre}</td>
            <td>{$autor}</td>
            <td>{$categoria}</td>
            <td>{$paginas}</td>
            <td>{$editorial}</td>


            <td>
                <button onclick='eliminarLibro({$id})'>Eliminar</button>
            </td>
        </tr>";
}

echo "</table>";
?>