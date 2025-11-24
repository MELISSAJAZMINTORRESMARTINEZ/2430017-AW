<?php
// se definen los datos para conectar a la base de datos
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "clinicausuario";
$pass = "12";

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
        
        // consulta sql para obtener todos los expedientes
        $sql = "SELECT 
                    IdExpediente,
                    IdPaciente,
                    IdMedico,
                    FechaConsulta,
                    Sintomas,
                    Diagnostico,
                    Tratamiento,
                    RecetaMedica,
                    NotasAdicionales,
                    ProximaCita
                FROM expedienteclinico
                ORDER BY IdExpediente DESC";
        
        // ejecuta la consulta directamente porque no lleva parametros
        $stmt = $pdo->query($sql);

        // obtiene todos los resultados
        $expedientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // indica que se enviará JSON
        header('Content-Type: application/json');

        // imprime los datos en json
        echo json_encode($expedientes);

        exit; // detiene la ejecución del script
    }

    // obtener un solo expediente por ID
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {
        
        // consulta con parámetro
        $sql = "SELECT * FROM expedienteclinico WHERE IdExpediente = :id";

        // prepara la consulta
        $stmt = $pdo->prepare($sql);

        // vincula parámetro
        $stmt->bindParam(':id', $_GET['id']);

        // ejecuta consulta
        $stmt->execute();
        
        // obtiene un registro
        $expediente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // indica formato JSON
        header('Content-Type: application/json');

        // imprime el JSON
        echo json_encode($expediente);

        exit;
    }

    // registrar un nuevo expediente (POST sin idExpedienteEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idExpedienteEditar'])) {
        
        // validación: la próxima cita no puede ser en el pasado
        if (!empty($_POST['proximaCita'])) {
            $hoy = date('Y-m-d');
            if ($_POST['proximaCita'] < $hoy) {
                echo "Error - la proxima cita no puede ser en el pasado";
                exit;
            }
        }
        
        // validación: la próxima cita debe ser posterior a la fecha de consulta
        if (!empty($_POST['fechaConsulta']) && !empty($_POST['proximaCita'])) {
            if ($_POST['proximaCita'] < $_POST['fechaConsulta']) {
                echo "Error - la proxima cita debe ser posterior a la fecha de consulta";
                exit;
            }
        }
        
        // validar que paciente y médico existan
        $sqlCheck = "SELECT COUNT(*) FROM controlpacientes WHERE IdPaciente = :idPaciente";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmtCheck->execute();
        if ($stmtCheck->fetchColumn() == 0) {
            echo "Error - el paciente no existe";
            exit;
        }
        
        $sqlCheck = "SELECT COUNT(*) FROM controlmedicos WHERE IdMedico = :idMedico";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':idMedico', $_POST['idMedico']);
        $stmtCheck->execute();
        if ($stmtCheck->fetchColumn() == 0) {
            echo "Error - el medico no existe";
            exit;
        }
        
        // consulta insert
        $sql = "INSERT INTO expedienteclinico
                (IdExpediente, IdPaciente, IdMedico, FechaConsulta, Sintomas, Diagnostico, Tratamiento, RecetaMedica, NotasAdicionales, ProximaCita)
                VALUES 
                (:idExpediente, :idPaciente, :idMedico, :fechaConsulta, :sintomas, :diagnostico, :tratamiento, :recetaMedica, :notasAdicionales, :proximaCita)";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idExpediente', $_POST['idExpediente']);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmt->bindParam(':idMedico', $_POST['idMedico']);
        $stmt->bindParam(':fechaConsulta', $_POST['fechaConsulta']);
        $stmt->bindParam(':sintomas', $_POST['sintomas']);
        $stmt->bindParam(':diagnostico', $_POST['diagnostico']);
        $stmt->bindParam(':tratamiento', $_POST['tratamiento']);
        $stmt->bindParam(':recetaMedica', $_POST['recetaMedica']);
        $stmt->bindParam(':notasAdicionales', $_POST['notasAdicionales']);
        $stmt->bindParam(':proximaCita', $_POST['proximaCita']);

        // ejecutar insert
        $stmt->execute();

        // mensaje final
        echo "OK - expediente guardado";
        exit;
    }

    // actualizar un expediente (POST con idExpedienteEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idExpedienteEditar'])) {
        
        // validación: la próxima cita no puede ser en el pasado
        if (!empty($_POST['proximaCita'])) {
            $hoy = date('Y-m-d');
            if ($_POST['proximaCita'] < $hoy) {
                echo "Error - la proxima cita no puede ser en el pasado";
                exit;
            }
        }
        
        // validación: la próxima cita debe ser posterior a la fecha de consulta
        if (!empty($_POST['fechaConsulta']) && !empty($_POST['proximaCita'])) {
            if ($_POST['proximaCita'] < $_POST['fechaConsulta']) {
                echo "Error - la proxima cita debe ser posterior a la fecha de consulta";
                exit;
            }
        }
        
        // consulta update
        $sql = "UPDATE expedienteclinico SET
                IdPaciente = :idPaciente,
                IdMedico = :idMedico,
                FechaConsulta = :fechaConsulta,
                Sintomas = :sintomas,
                Diagnostico = :diagnostico,
                Tratamiento = :tratamiento,
                RecetaMedica = :recetaMedica,
                NotasAdicionales = :notasAdicionales,
                ProximaCita = :proximaCita
                WHERE IdExpediente = :idExpediente";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idExpediente', $_POST['idExpedienteEditar']);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmt->bindParam(':idMedico', $_POST['idMedico']);
        $stmt->bindParam(':fechaConsulta', $_POST['fechaConsulta']);
        $stmt->bindParam(':sintomas', $_POST['sintomas']);
        $stmt->bindParam(':diagnostico', $_POST['diagnostico']);
        $stmt->bindParam(':tratamiento', $_POST['tratamiento']);
        $stmt->bindParam(':recetaMedica', $_POST['recetaMedica']);
        $stmt->bindParam(':notasAdicionales', $_POST['notasAdicionales']);
        $stmt->bindParam(':proximaCita', $_POST['proximaCita']);

        // ejecutar update
        $stmt->execute();

        echo "OK - expediente actualizado";
        exit;
    }

    // eliminar expediente
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        
        // consulta delete
        $sql = "DELETE FROM expedienteclinico WHERE IdExpediente = :id";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular id
        $stmt->bindParam(':id', $_GET['id']);

        // ejecutar
        $stmt->execute();

        echo "OK - expediente eliminado";
        exit;
    }

// captura errores de PDO
} catch (PDOException $e) {

    // imprime error
    echo "Error: " . $e->getMessage();
}
?>