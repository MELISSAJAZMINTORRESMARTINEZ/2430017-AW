<?php
// Parámetros de conexión
$host = "localhost";
$port = "3306";
$dbname = "futbol";
$user = "root";
$pass = "";
$sexo = "femenino";

try {
    // Conexión 
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $sql = "INSERT INTO registro
            (nombre, apellido, correo, telefono, sexo, numero_dorsal, nombre_equipo)
            VALUES 
            (:nombre, :apellido, :correo, :telefono, :sexo, :numero_dorsal, :nombre_equipo)";

    $stmt = $pdo->prepare($sql);


    if($sexo === "femenino"){
        echo "no se puede agregar a alguien mujer";

    }


    $stmt->bindParam(':nombre', $_POST['nombre']);
    $stmt->bindParam(':apellido', $_POST['apellido']);
    $stmt->bindParam(':correo', $_POST['correo']);
    $stmt->bindParam(':telefono', $_POST['telefono']);
    $stmt->bindParam(':sexo', $_POST['sexo']);
    $stmt->bindParam(':numero_dorsal', $_POST['numero_dorsal']);
    $stmt->bindParam(':nombre_equipo', $_POST['nombre_equipo']);


    $stmt->execute();

    echo "<h3 style='color:green;'>Se agrego, creo</h3>";

} catch (PDOException $e) {
    echo "<h3 style='color:red;'>nimdo, no se pudo" . $e->getMessage() . "</h3>";
}
?>
