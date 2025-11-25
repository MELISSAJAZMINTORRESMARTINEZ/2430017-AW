<?php
// se definen los datos para conectar a la base de datos
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "admin";
$pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554"; // vacio

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
        
        // consulta sql para obtener citas con nombres de paciente y médico
        $sql = "SELECT 
                    a.IdCita,
                    a.IdPaciente,
                    a.IdMedico,
                    a.FechaCita,
                    a.MotivoConsulta,
                    a.EstadoCita,
                    a.Observaciones,
                    a.FechaRegistro,
                    a.Activo,
                    p.NombreCompleto as NombrePaciente,
                    m.NombreCompleto as NombreMedico
                FROM controlagenda a
                LEFT JOIN controlpacientes p ON a.IdPaciente = p.IdPaciente
                LEFT JOIN controlmedicos m ON a.IdMedico = m.IdMedico
                ORDER BY a.FechaCita DESC";
        
        // ejecuta la consulta directamente
        $stmt = $pdo->query($sql);

        // obtiene todos los resultados
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // indica que se enviará JSON
        header('Content-Type: application/json');

        // imprime los datos en json
        echo json_encode($citas);

        exit;
    }

    // obtener una sola cita por ID
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {
        
        // consulta con parámetro
        $sql = "SELECT * FROM controlagenda WHERE IdCita = :id";

        // prepara la consulta
        $stmt = $pdo->prepare($sql);

        // vincula parámetro
        $stmt->bindParam(':id', $_GET['id']);

        // ejecuta consulta
        $stmt->execute();
        
        // obtiene un registro
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // indica formato JSON
        header('Content-Type: application/json');

        // imprime el JSON
        echo json_encode($cita);

        exit;
    }

    // validar si existe un paciente
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'validarPaciente') {
        
        $sql = "SELECT IdPaciente, NombreCompleto FROM controlpacientes WHERE IdPaciente = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($paciente ?: ['error' => 'Paciente no encontrado']);
        exit;
    }

    // validar si existe un médico
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'validarMedico') {
        
        $sql = "SELECT IdMedico, NombreCompleto FROM controlmedicos WHERE IdMedico = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        
        $medico = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($medico ?: ['error' => 'Médico no encontrado']);
        exit;
    }

    // registrar una nueva cita (POST sin idCitaEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idCitaEditar'])) {
        
        // validaciones del lado del servidor
        if (empty($_POST['idPaciente']) || empty($_POST['idMedico'])) {
            echo "Error: Paciente y Médico son obligatorios";
            exit;
        }

        // validar que la fecha de cita no sea pasada
        if (strtotime($_POST['fechaCita']) < strtotime(date('Y-m-d'))) {
            echo "Error: No se pueden agendar citas en fechas pasadas";
            exit;
        }

        // consulta insert
        $sql = "INSERT INTO controlagenda
                (IdCita, IdPaciente, IdMedico, FechaCita, MotivoConsulta, EstadoCita, 
                Observaciones, FechaRegistro, Activo)
                VALUES 
                (:idCita, :idPaciente, :idMedico, :fechaCita, :motivoConsulta, :estatus,
                :observaciones, :fechaRegistro, :activo)";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idCita', $_POST['idCita']);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmt->bindParam(':idMedico', $_POST['idMedico']);
        $stmt->bindParam(':fechaCita', $_POST['fechaCita']);
        $stmt->bindParam(':motivoConsulta', $_POST['motivoConsulta']);
        $stmt->bindParam(':estatus', $_POST['estatus']);
        $stmt->bindParam(':observaciones', $_POST['observaciones']);
        $stmt->bindParam(':fechaRegistro', $_POST['fechaRegistro']);
        $stmt->bindParam(':activo', $_POST['activo']);

        // ejecutar insert
        $stmt->execute();

        // mensaje final
        echo "OK - cita guardada";
        exit;
    }

    // actualizar una cita (POST con idCitaEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idCitaEditar'])) {
        
        // validaciones
        if (empty($_POST['idPaciente']) || empty($_POST['idMedico'])) {
            echo "Error: Paciente y Médico son obligatorios";
            exit;
        }

        if (strtotime($_POST['fechaCita']) < strtotime(date('Y-m-d'))) {
            echo "Error: No se pueden agendar citas en fechas pasadas";
            exit;
        }

        // consulta update
        $sql = "UPDATE controlagenda SET
                IdPaciente = :idPaciente,
                IdMedico = :idMedico,
                FechaCita = :fechaCita,
                MotivoConsulta = :motivoConsulta,
                EstadoCita = :estatus,
                Observaciones = :observaciones,
                FechaRegistro = :fechaRegistro,
                Activo = :activo
                WHERE IdCita = :idCita";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idCita', $_POST['idCitaEditar']);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmt->bindParam(':idMedico', $_POST['idMedico']);
        $stmt->bindParam(':fechaCita', $_POST['fechaCita']);
        $stmt->bindParam(':motivoConsulta', $_POST['motivoConsulta']);
        $stmt->bindParam(':estatus', $_POST['estatus']);
        $stmt->bindParam(':observaciones', $_POST['observaciones']);
        $stmt->bindParam(':fechaRegistro', $_POST['fechaRegistro']);
        $stmt->bindParam(':activo', $_POST['activo']);

        // ejecutar update
        $stmt->execute();

        echo "OK - cita actualizada";
        exit;
    }

    // eliminar cita
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        
        // consulta delete
        $sql = "DELETE FROM controlagenda WHERE IdCita = :id";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular id
        $stmt->bindParam(':id', $_GET['id']);

        // ejecutar
        $stmt->execute();

        echo "OK - cita eliminada";
        exit;
    }

// captura errores de PDO
} catch (PDOException $e) {

    // imprime error
    echo "Error: " . $e->getMessage();
}
?>