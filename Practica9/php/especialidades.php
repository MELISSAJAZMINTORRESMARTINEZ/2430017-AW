<?php
header("Content-Type: application/json; charset=utf-8");

// Configuración
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "root";
$pass = "";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
    exit;
}


if (isset($_GET["accion"]) && $_GET["accion"] == "lista") {

    try {
        $stmt = $pdo->query("SELECT * FROM especialidades ORDER BY IdEspecialidad ASC");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($datos);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_POST["IdEspecialidad"], $_POST["nombreEspecialidad"], $_POST["descripcion"])) {
        echo "ERROR: Campos incompletos";
        exit;
    }

    $sql = "INSERT INTO especialidades (IdEspecialidad, NombreEspecialidad, Descripcion)
            VALUES (:IdEspecialidad, :nombreEspecialidad, :descripcion)";
    
    try {
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':IdEspecialidad', $_POST['IdEspecialidad']);
        $stmt->bindParam(':nombreEspecialidad', $_POST['nombreEspecialidad']);
        $stmt->bindParam(':descripcion', $_POST['descripcion']);

        $stmt->execute();

        echo "OK"; 

    } catch (PDOException $e) {

        // Envía el error como texto normal
        echo "ERROR: " . $e->getMessage();
    }
    exit;
}

?>
