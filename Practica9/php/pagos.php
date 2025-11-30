<?php
// se definen los datos para conectar a la base de datos
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "admin";
$pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554";

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
        
        // consulta sql para obtener pagos con información de paciente y cita
        $sql = "SELECT 
                    p.IdPago,
                    p.IdCita,
                    p.IdPaciente,
                    p.Monto,
                    p.MetodoPago,
                    p.FechaPago,
                    p.Referencia,
                    p.EstatusPago,
                    pac.NombreCompleto as NombrePaciente,
                    a.FechaCita,
                    a.MotivoConsulta
                FROM pagos p
                LEFT JOIN controlpacientes pac ON p.IdPaciente = pac.IdPaciente
                LEFT JOIN controlagenda a ON p.IdCita = a.IdCita
                ORDER BY p.FechaPago DESC";
        
        // ejecuta la consulta directamente
        $stmt = $pdo->query($sql);

        // obtiene todos los resultados
        $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // indica que se enviará JSON
        header('Content-Type: application/json');

        // imprime los datos en json
        echo json_encode($pagos);

        exit;
    }

    // obtener un solo pago por ID
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {
        
        // consulta con parámetro
        $sql = "SELECT * FROM pagos WHERE IdPago = :id";

        // prepara la consulta
        $stmt = $pdo->prepare($sql);

        // vincula parámetro
        $stmt->bindParam(':id', $_GET['id']);

        // ejecuta consulta
        $stmt->execute();
        
        // obtiene un registro
        $pago = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // indica formato JSON
        header('Content-Type: application/json');

        // imprime el JSON
        echo json_encode($pago);

        exit;
    }

    // validar si existe un paciente
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'validarPaciente') {
        
        $id = $_GET['id'];
        
        // Log para debug
        error_log("Validando paciente ID: " . $id);
        
        $sql = "SELECT IdPaciente, NombreCompleto FROM controlpacientes WHERE IdPaciente = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Log del resultado
        error_log("Resultado paciente: " . json_encode($paciente));
        
        header('Content-Type: application/json');
        echo json_encode($paciente ?: ['error' => 'Paciente no encontrado', 'id_buscado' => $id]);
        exit;
    }

    // validar si existe una cita
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'validarCita') {
        
        $id = $_GET['id'];
        
        // Log para debug
        error_log("Validando cita ID: " . $id);
        
        // Primero verificar si la cita existe en la tabla
        $sqlCheck = "SELECT COUNT(*) as total FROM controlagenda WHERE IdCita = :id";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtCheck->execute();
        $existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        error_log("Citas encontradas: " . $existe['total']);
        
        $sql = "SELECT a.IdCita, a.FechaCita, a.MotivoConsulta, a.IdPaciente, p.NombreCompleto 
                FROM controlagenda a 
                LEFT JOIN controlpacientes p ON a.IdPaciente = p.IdPaciente
                WHERE a.IdCita = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Log del resultado
        error_log("Resultado cita: " . json_encode($cita));
        
        header('Content-Type: application/json');
        echo json_encode($cita ?: ['error' => 'Cita no encontrada', 'id_buscado' => $id, 'total_encontradas' => $existe['total']]);
        exit;
    }

    // registrar un nuevo pago (POST sin idPagoEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idPagoEditar'])) {
        
        // Log de los datos recibidos
        error_log("POST recibido: " . json_encode($_POST));
        
        // validaciones
        if (empty($_POST['idPaciente']) || empty($_POST['idCita'])) {
            echo "Error: Paciente y Cita son obligatorios";
            exit;
        }

        if ($_POST['monto'] <= 0) {
            echo "Error: El monto debe ser mayor a 0";
            exit;
        }

        // validar que exista el paciente
        $sqlValidarPaciente = "SELECT IdPaciente FROM controlpacientes WHERE IdPaciente = :id";
        $stmtValidar = $pdo->prepare($sqlValidarPaciente);
        $stmtValidar->bindParam(':id', $_POST['idPaciente'], PDO::PARAM_INT);
        $stmtValidar->execute();
        $pacienteExiste = $stmtValidar->fetch();
        
        error_log("Paciente existe: " . ($pacienteExiste ? 'SI' : 'NO'));
        
        if (!$pacienteExiste) {
            echo "Error: El paciente con ID " . $_POST['idPaciente'] . " no existe";
            exit;
        }

        // validar que exista la cita
        $sqlValidarCita = "SELECT IdCita FROM controlagenda WHERE IdCita = :id";
        $stmtValidar = $pdo->prepare($sqlValidarCita);
        $stmtValidar->bindParam(':id', $_POST['idCita'], PDO::PARAM_INT);
        $stmtValidar->execute();
        $citaExiste = $stmtValidar->fetch();
        
        error_log("Cita existe: " . ($citaExiste ? 'SI' : 'NO'));
        
        if (!$citaExiste) {
            // Mostrar cuántas citas hay en total para debug
            $sqlCount = "SELECT COUNT(*) as total FROM controlagenda";
            $stmtCount = $pdo->query($sqlCount);
            $count = $stmtCount->fetch();
            
            echo "Error: La cita con ID " . $_POST['idCita'] . " no existe. Total de citas en BD: " . $count['total'];
            exit;
        }

        // consulta insert (sin IdPago, dejar que sea autoincremental)
        $sql = "INSERT INTO pagos
                (IdCita, IdPaciente, Monto, MetodoPago, FechaPago, Referencia, EstatusPago)
                VALUES 
                (:idCita, :idPaciente, :monto, :metodoPago, :fechaPago, :referencia, :estatusPago)";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idCita', $_POST['idCita'], PDO::PARAM_INT);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente'], PDO::PARAM_INT);
        $stmt->bindParam(':monto', $_POST['monto']);
        $stmt->bindParam(':metodoPago', $_POST['metodoPago']);
        $stmt->bindParam(':fechaPago', $_POST['fechaPago']);
        
        $referencia = !empty($_POST['referencia']) ? $_POST['referencia'] : null;
        $stmt->bindParam(':referencia', $referencia);
        
        $stmt->bindParam(':estatusPago', $_POST['estatusPago']);

        // ejecutar insert
        $stmt->execute();

        // mensaje final
        echo "OK - pago guardado con ID: " . $pdo->lastInsertId();
        exit;
    }

    // actualizar un pago (POST con idPagoEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idPagoEditar'])) {
        
        // validaciones
        if (empty($_POST['idPaciente']) || empty($_POST['idCita'])) {
            echo "Error: Paciente y Cita son obligatorios";
            exit;
        }

        if ($_POST['monto'] <= 0) {
            echo "Error: El monto debe ser mayor a 0";
            exit;
        }

        // validar que exista el paciente
        $sqlValidarPaciente = "SELECT IdPaciente FROM controlpacientes WHERE IdPaciente = :id";
        $stmtValidar = $pdo->prepare($sqlValidarPaciente);
        $stmtValidar->bindParam(':id', $_POST['idPaciente'], PDO::PARAM_INT);
        $stmtValidar->execute();
        if (!$stmtValidar->fetch()) {
            echo "Error: El paciente no existe";
            exit;
        }

        // validar que exista la cita
        $sqlValidarCita = "SELECT IdCita FROM controlagenda WHERE IdCita = :id";
        $stmtValidar = $pdo->prepare($sqlValidarCita);
        $stmtValidar->bindParam(':id', $_POST['idCita'], PDO::PARAM_INT);
        $stmtValidar->execute();
        if (!$stmtValidar->fetch()) {
            echo "Error: La cita no existe";
            exit;
        }

        // consulta update
        $sql = "UPDATE pagos SET
                IdCita = :idCita,
                IdPaciente = :idPaciente,
                Monto = :monto,
                MetodoPago = :metodoPago,
                FechaPago = :fechaPago,
                Referencia = :referencia,
                EstatusPago = :estatusPago
                WHERE IdPago = :idPago";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idPago', $_POST['idPagoEditar'], PDO::PARAM_INT);
        $stmt->bindParam(':idCita', $_POST['idCita'], PDO::PARAM_INT);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente'], PDO::PARAM_INT);
        $stmt->bindParam(':monto', $_POST['monto']);
        $stmt->bindParam(':metodoPago', $_POST['metodoPago']);
        $stmt->bindParam(':fechaPago', $_POST['fechaPago']);
        
        $referencia = !empty($_POST['referencia']) ? $_POST['referencia'] : null;
        $stmt->bindParam(':referencia', $referencia);
        
        $stmt->bindParam(':estatusPago', $_POST['estatusPago']);

        // ejecutar update
        $stmt->execute();

        echo "OK - pago actualizado";
        exit;
    }

    // eliminar pago
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        
        // consulta delete
        $sql = "DELETE FROM pagos WHERE IdPago = :id";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular id
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);

        // ejecutar
        $stmt->execute();

        echo "OK - pago eliminado";
        exit;
    }

// captura errores de PDO
} catch (PDOException $e) {

    // imprime error con más detalle
    error_log("Error PDO: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
}
?>