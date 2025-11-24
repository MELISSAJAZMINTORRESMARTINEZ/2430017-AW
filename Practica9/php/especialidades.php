<?php
header("Content-Type: application/json; charset=utf-8"); // aqui establezco que la respuesta sera json

// configuracion
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "clinica3";
$pass = ""; // vacío
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8"; // aqui armo el dsn para la conexion
    $pdo = new PDO($dsn, $user, $pass); // aqui creo la instancia pdo
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // aqui activo los errores de excepcion

} catch (PDOException $e) {
    echo json_encode(["error" => "Error de conexion: " . $e->getMessage()]); // si falla la conexion muestro el error
    exit; // aqui detengo todo
}

// listar especialidades
if (isset($_GET["accion"]) && $_GET["accion"] == "lista") { // aqui reviso si pidieron la lista de especialidades
    try {
        $stmt = $pdo->query("SELECT * FROM especialidades ORDER BY IdEspecialidad ASC"); // aqui hago la consulta
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC); // aqui obtengo todos los datos en arreglo
        echo json_encode($datos); // aqui regreso los datos en json
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]); // si algo falla muestro el error
    }
    exit; // aqui termino
}

// post - crear, actualizar o eliminar
if ($_SERVER["REQUEST_METHOD"] === "POST") { // aqui verifico si la solicitud es post
    
    $accion = $_POST["accion"] ?? "agregar"; // aqui obtengo la accion o por defecto agregar
    
    // agregar especialidad
    if ($accion === "agregar") {
        if (!isset($_POST["IdEspecialidad"], $_POST["nombreEspecialidad"], $_POST["descripcion"])) {
            echo "ERROR: campos incompletos"; // aqui reviso que vengan todos los campos
            exit;
        }

        $sql = "INSERT INTO especialidades (IdEspecialidad, NombreEspecialidad, Descripcion)
                VALUES (:IdEspecialidad, :nombreEspecialidad, :descripcion)"; // aqui preparo el insert
        
        try {
            $stmt = $pdo->prepare($sql); // aqui preparo la sentencia
            $stmt->bindParam(':IdEspecialidad', $_POST['IdEspecialidad']); // aqui vinculo el id
            $stmt->bindParam(':nombreEspecialidad', $_POST['nombreEspecialidad']); // aqui vinculo el nombre
            $stmt->bindParam(':descripcion', $_POST['descripcion']); // aqui vinculo la descripcion
            $stmt->execute(); // aqui ejecuto el insert
            echo "OK"; // si todo sale bien mando ok
        } catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage(); // aqui muestro error si falla
        }
    }
    
    // editar especialidad
    elseif ($accion === "editar") {
        if (!isset($_POST["IdEspecialidad"], $_POST["nombreEspecialidad"], $_POST["descripcion"])) {
            echo "ERROR: campos incompletos"; // aqui reviso los campos
            exit;
        }

        $sql = "UPDATE especialidades 
                SET NombreEspecialidad = :nombreEspecialidad, 
                    Descripcion = :descripcion
                WHERE IdEspecialidad = :IdEspecialidad"; // aqui preparo el update
        
        try {
            $stmt = $pdo->prepare($sql); // aqui preparo la sentencia
            $stmt->bindParam(':IdEspecialidad', $_POST['IdEspecialidad']); // aqui vinculo el id
            $stmt->bindParam(':nombreEspecialidad', $_POST['nombreEspecialidad']); // aqui vinculo nombre
            $stmt->bindParam(':descripcion', $_POST['descripcion']); // aqui vinculo descripcion
            $stmt->execute(); // ejecuto el update
            echo "OK"; // aqui confirmo
        } catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage(); // aqui muestro error
        }
    }
    
    // eliminar especialidad
    elseif ($accion === "eliminar") {
        if (!isset($_POST["IdEspecialidad"])) {
            echo "ERROR: id no proporcionado"; // aqui valido que mandaron el id
            exit;
        }

        $sql = "DELETE FROM especialidades WHERE IdEspecialidad = :IdEspecialidad"; // aqui preparo el delete
        
        try {
            $stmt = $pdo->prepare($sql); // preparo sentencia
            $stmt->bindParam(':IdEspecialidad', $_POST['IdEspecialidad']); // vinculo id
            $stmt->execute(); // ejecuto el delete
            echo "OK"; // confirmo eliminacion
        } catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage(); // muestro error
        }
    }
    
    exit; // aqui termino el proceso post
}
?>