<?php
// se definen los datos para conectar a la base de datos
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "clinica2";
$pass = "Clini123!";

// iniciamos un bloque try para capturar errores
try {

    // armamos la cadena de conexion usando pdo
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";

    // creamos la conexion usando pdo
    $pdo = new PDO($dsn, $user, $pass);

    // configuramos pdo para lanzar errores si algo sale mal
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // validamos si llega una peticion GET y si accion es "lista"
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'lista') {
        
        // consulta sql para obtener medicos y su especialidad con LEFT JOIN
        $sql = "SELECT 
                    cm.IdMedico,
                    cm.NombreCompleto,
                    cm.CedulaProfesional,
                    cm.EspecialidadId,
                    cm.Telefono,
                    cm.CorreoElectronico,
                    cm.HorarioAtencion,
                    cm.FechaIngreso,
                    cm.Estatus,
                    e.NombreEspecialidad
                FROM controlmedicos cm
                LEFT JOIN especialidades e ON cm.EspecialidadId = e.IdEspecialidad
                ORDER BY cm.IdMedico DESC";
        
        // ejecuta la consulta directamente porque no lleva parametros
        $stmt = $pdo->query($sql);

        // obtiene todos los resultados
        $medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // indica que se enviará JSON
        header('Content-Type: application/json');

        // imprime los datos en json
        echo json_encode($medicos);

        exit; // detiene la ejecución del script
    }

    // obtener un solo médico por ID
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {
        
        // consulta con parámetro
        $sql = "SELECT * FROM controlmedicos WHERE IdMedico = :id";

        // prepara la consulta
        $stmt = $pdo->prepare($sql);

        // vincula parámetro
        $stmt->bindParam(':id', $_GET['id']);

        // ejecuta consulta
        $stmt->execute();
        
        // obtiene un registro
        $medico = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // indica formato JSON
        header('Content-Type: application/json');

        // imprime el JSON
        echo json_encode($medico);

        exit;
    }

    // registrar un nuevo médico (POST sin idMedicoEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idMedicoEditar'])) {
        
        // consulta insert
        $sql = "INSERT INTO controlmedicos
                (IdMedico, NombreCompleto, CedulaProfesional, EspecialidadId, Telefono, CorreoElectronico, HorarioAtencion, FechaIngreso, Estatus)
                VALUES 
                (:idMedico, :nombreCompleto, :cedulaProfesional, :especialidad, :telefono, :correo, :horario, :fechaIngreso, :estatus)";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idMedico', $_POST['idMedico']);
        $stmt->bindParam(':nombreCompleto', $_POST['nombreCompleto']);
        $stmt->bindParam(':cedulaProfesional', $_POST['cedulaProfesional']);
        $stmt->bindParam(':especialidad', $_POST['especialidad']);
        $stmt->bindParam(':telefono', $_POST['telefono']);
        $stmt->bindParam(':correo', $_POST['correo']);
        $stmt->bindParam(':horario', $_POST['horario']);
        $stmt->bindParam(':fechaIngreso', $_POST['fechaIngreso']);
        $stmt->bindParam(':estatus', $_POST['estatus']);

        // ejecutar insert
        $stmt->execute();

        // mensaje final
        echo "OK - medico guardado";
        exit;
    }

    // actualizar un médico (POST con idMedicoEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idMedicoEditar'])) {
        
        // consulta update
        $sql = "UPDATE controlmedicos SET
                NombreCompleto = :nombreCompleto,
                CedulaProfesional = :cedulaProfesional,
                EspecialidadId = :especialidad,
                Telefono = :telefono,
                CorreoElectronico = :correo,
                HorarioAtencion = :horario,
                FechaIngreso = :fechaIngreso,
                Estatus = :estatus
                WHERE IdMedico = :idMedico";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idMedico', $_POST['idMedicoEditar']);
        $stmt->bindParam(':nombreCompleto', $_POST['nombreCompleto']);
        $stmt->bindParam(':cedulaProfesional', $_POST['cedulaProfesional']);
        $stmt->bindParam(':especialidad', $_POST['especialidad']);
        $stmt->bindParam(':telefono', $_POST['telefono']);
        $stmt->bindParam(':correo', $_POST['correo']);
        $stmt->bindParam(':horario', $_POST['horario']);
        $stmt->bindParam(':fechaIngreso', $_POST['fechaIngreso']);
        $stmt->bindParam(':estatus', $_POST['estatus']);

        // ejecutar update
        $stmt->execute();

        echo "OK - medico actualizado";
        exit;
    }

    // eliminar médico
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        
        // consulta delete
        $sql = "DELETE FROM controlmedicos WHERE IdMedico = :id";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular id
        $stmt->bindParam(':id', $_GET['id']);

        // ejecutar
        $stmt->execute();

        echo "OK - medico eliminado";
        exit;
    }

// captura errores de PDO
} catch (PDOException $e) {

    // imprime error
    echo "Error: " . $e->getMessage();
}
?>
