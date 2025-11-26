<?php
// aqui defino los datos para conectarme a la base de datos
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "admin";
$pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554"; 

// inicio un bloque try por si algo explota
try {

    // aqui armo la cadena de conexion usando pdo
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";

    // aqui creo la conexion con pdo
    $pdo = new PDO($dsn, $user, $pass);

    // aqui le digo a pdo que si algo sale mal me avise con error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // valido si llego una peticion GET y si piden la lista
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'lista') {
        
        // aqui armo la consulta para obtener todas las citas junto a nombres de paciente y medico
        $sql = "SELECT 
                    a.IdCita,
                    a.IdPaciente,
                    a.IdMedico,
                    a.FechaCita,
                    a.MotivoConsulta,
                    a.EstadoCita,
                    a.Observaciones,
                    a.FechaRegistro,
                    p.NombreCompleto as NombrePaciente,
                    m.NombreCompleto as NombreMedico
                FROM controlagenda a
                LEFT JOIN controlpacientes p ON a.IdPaciente = p.IdPaciente
                LEFT JOIN controlmedicos m ON a.IdMedico = m.IdMedico
                ORDER BY a.FechaCita DESC";
        
        // ejecuto la consulta directo
        $stmt = $pdo->query($sql);

        // aqui saco todos los resultados
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // digo que voy a regresar json
        header('Content-Type: application/json');

        // imprimo el json
        echo json_encode($citas);

        exit;
    }

    // aqui valido si piden una sola cita por ID
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {
        
        // consulta con parametro
        $sql = "SELECT * FROM controlagenda WHERE IdCita = :id";

        // preparo la consulta
        $stmt = $pdo->prepare($sql);

        // vinculo el id que paso por GET
        $stmt->bindParam(':id', $_GET['id']);

        // la ejecuto
        $stmt->execute();
        
        // obtengo el registro
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // aviso que mando json
        header('Content-Type: application/json');

        // mando el json
        echo json_encode($cita);

        exit;
    }

    // aqui valido si existe un paciente
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'validarPaciente') {
        
        // consulta para buscar paciente
        $sql = "SELECT IdPaciente, NombreCompleto FROM controlpacientes WHERE IdPaciente = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        
        // saco lo que encontro
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($paciente ?: ['error' => 'Paciente no encontrado']);
        exit;
    }

    // aqui valido si existe un medico
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'validarMedico') {
        
        $sql = "SELECT IdMedico, NombreCompleto FROM controlmedicos WHERE IdMedico = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        
        $medico = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($medico ?: ['error' => 'Medico no encontrado']);
        exit;
    }

    // aqui registro una nueva cita (POST sin idCitaEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idCitaEditar'])) {
        
        // valido que paciente y medico no vengan vacios
        if (empty($_POST['idPaciente']) || empty($_POST['idMedico'])) {
            echo "Error: Paciente y Medico son obligatorios";
            exit;
        }

        // valido que no metan una fecha pasada
        if (strtotime($_POST['fechaCita']) < strtotime(date('Y-m-d'))) {
            echo "Error: No se pueden agendar citas en fechas pasadas";
            exit;
        }

        // aqui va el insert
        $sql = "INSERT INTO controlagenda
                (IdPaciente, IdMedico, FechaCita, MotivoConsulta, EstadoCita, 
                Observaciones, FechaRegistro)
                VALUES 
                (:idPaciente, :idMedico, :fechaCita, :motivoConsulta, :estatus,
                :observaciones, :fechaRegistro)";

        // preparo
        $stmt = $pdo->prepare($sql);

        // vinculo todo
        $stmt->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmt->bindParam(':idMedico', $_POST['idMedico']);
        $stmt->bindParam(':fechaCita', $_POST['fechaCita']);
        $stmt->bindParam(':motivoConsulta', $_POST['motivoConsulta']);
        $stmt->bindParam(':estatus', $_POST['estatus']);
        $stmt->bindParam(':observaciones', $_POST['observaciones']);
        $stmt->bindParam(':fechaRegistro', $_POST['fechaRegistro']);

        // ejecuto
        $stmt->execute();

        echo "OK - cita guardada";
        exit;
    }

    // aqui actualizo una cita (POST con idCitaEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idCitaEditar'])) {
        
        // validaciones basicas
        if (empty($_POST['idPaciente']) || empty($_POST['idMedico'])) {
            echo "Error: Paciente y Medico son obligatorios";
            exit;
        }

        if (strtotime($_POST['fechaCita']) < strtotime(date('Y-m-d'))) {
            echo "Error: No se pueden agendar citas en fechas pasadas";
            exit;
        }

        // aqui armo el update
        $sql = "UPDATE controlagenda SET
                IdPaciente = :idPaciente,
                IdMedico = :idMedico,
                FechaCita = :fechaCita,
                MotivoConsulta = :motivoConsulta,
                EstadoCita = :estatus,
                Observaciones = :observaciones,
                FechaRegistro = :fechaRegistro
                WHERE IdCita = :idCita";

        // preparo
        $stmt = $pdo->prepare($sql);

        // vinculo todo
        $stmt->bindParam(':idCita', $_POST['idCitaEditar']);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmt->bindParam(':idMedico', $_POST['idMedico']);
        $stmt->bindParam(':fechaCita', $_POST['fechaCita']);
        $stmt->bindParam(':motivoConsulta', $_POST['motivoConsulta']);
        $stmt->bindParam(':estatus', $_POST['estatus']);
        $stmt->bindParam(':observaciones', $_POST['observaciones']);
        $stmt->bindParam(':fechaRegistro', $_POST['fechaRegistro']);

        // ejecuto
        $stmt->execute();

        echo "OK - cita actualizada";
        exit;
    }

    // aqui borro una cita
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        
        // consulta para borrar
        $sql = "DELETE FROM controlagenda WHERE IdCita = :id";

        // preparo
        $stmt = $pdo->prepare($sql);

        // vinculo id
        $stmt->bindParam(':id', $_GET['id']);

        // ejecuto
        $stmt->execute();

        echo "OK - cita eliminada";
        exit;
    }

// aqui agarro cualquier error del try
} catch (PDOException $e) {

    // imprimo el error
    echo "Error: " . $e->getMessage();
}
?>
