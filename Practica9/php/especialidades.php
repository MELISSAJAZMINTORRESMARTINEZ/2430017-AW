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

// LISTAR especialidades
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

// POST - Crear, Actualizar o Eliminar
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $accion = $_POST["accion"] ?? "agregar";
    
    // AGREGAR especialidad
    if ($accion === "agregar") {
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
            echo "ERROR: " . $e->getMessage();
        }
    }
    
    // EDITAR especialidad
    elseif ($accion === "editar") {
        if (!isset($_POST["IdEspecialidad"], $_POST["nombreEspecialidad"], $_POST["descripcion"])) {
            echo "ERROR: Campos incompletos";
            exit;
        }

        $sql = "UPDATE especialidades 
                SET NombreEspecialidad = :nombreEspecialidad, 
                    Descripcion = :descripcion
                WHERE IdEspecialidad = :IdEspecialidad";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':IdEspecialidad', $_POST['IdEspecialidad']);
            $stmt->bindParam(':nombreEspecialidad', $_POST['nombreEspecialidad']);
            $stmt->bindParam(':descripcion', $_POST['descripcion']);
            $stmt->execute();
            echo "OK";
        } catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage();
        }
    }
    
    // ELIMINAR especialidad
    elseif ($accion === "eliminar") {
        if (!isset($_POST["IdEspecialidad"])) {
            echo "ERROR: ID no proporcionado";
            exit;
        }

        $sql = "DELETE FROM especialidades WHERE IdEspecialidad = :IdEspecialidad";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':IdEspecialidad', $_POST['IdEspecialidad']);
            $stmt->execute();
            echo "OK";
        } catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage();
        }
    }
    
    exit;
}
?>