<?php
require "config.php";

$stmt = $pdo->query("SELECT * FROM usuarios");

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Acciones</th></tr>";

while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Escapar datos para prevenir XSS
    $id = htmlspecialchars($fila['id']);
    $nombre = htmlspecialchars($fila['nombre']);
    $email = htmlspecialchars($fila['email']);
    
    echo "<tr>
            <td>{$id}</td>
            <td>{$nombre}</td>
            <td>{$email}</td>
            <td>
                <button onclick='editar({$id})'>Editar</button>
                <button onclick='eliminarUsuario({$id})'>Eliminar</button>
            </td>
        </tr>";
}

echo "</table>";
?>