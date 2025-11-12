<?php
// Parámetros de conexión
$host = "localhost";
$port = "3306";
$dbname = "futbol";
$user = "root";
$pass = "";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $sexo = $_POST['sexo'];
    $numero_dorsal = $_POST['numero_dorsal'];
    $nombre_equipo = $_POST['nombre_equipo'];


    $sql = "INSERT INTO registro
            (nombre, apellido, correo, telefono, sexo, numero_dorsal, nombre_equipo)
            VALUES 
            (:nombre, :apellido, :correo, :telefono, :sexo, :numero_dorsal, :nombre_equipo)";

    $stmt = $pdo->prepare($sql);



    $stmt->bindParam(':nombre', $_POST['nombre']);
    $stmt->bindParam(':apellido', $_POST['apellido']);
    $stmt->bindParam(':correo', $_POST['correo']);
    $stmt->bindParam(':telefono', $_POST['telefono']);
    $stmt->bindParam(':sexo', $_POST['sexo']);
    $stmt->bindParam(':numero_dorsal', $_POST['numero_dorsal']);
    $stmt->bindParam(':nombre_equipo', $_POST['nombre_equipo']);


    $stmt->execute();

    echo "<h2>Se agrego, creo</h2>";
} catch (PDOException $e) {
    echo "<h2>nimdo, no se pudo " . $e->getMessage() . "</h2>";
}
