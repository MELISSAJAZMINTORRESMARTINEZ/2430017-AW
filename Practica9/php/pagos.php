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
                FROM gestorpagos p
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
        $sql = "SELECT * FROM gestorpagos WHERE IdPago = :id";

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
        
        $sql = "SELECT IdPaciente, NombreCompleto FROM controlpacientes WHERE IdPaciente = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($paciente ?: ['error' => 'Paciente no encontrado']);
        exit;
    }

    // validar si existe una cita
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'validarCita') {
        
        $sql = "SELECT a.IdCita, a.FechaCita, a.MotivoConsulta, p.NombreCompleto 
                FROM controlagenda a 
                LEFT JOIN controlpacientes p ON a.IdPaciente = p.IdPaciente
                WHERE a.IdCita = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($cita ?: ['error' => 'Cita no encontrada']);
        exit;
    }

    // registrar un nuevo pago (POST sin idPagoEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idPagoEditar'])) {
        
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
        $stmtValidar->bindParam(':id', $_POST['idPaciente']);
        $stmtValidar->execute();
        if (!$stmtValidar->fetch()) {
            echo "Error: El paciente no existe";
            exit;
        }

        // validar que exista la cita
        $sqlValidarCita = "SELECT IdCita FROM controlagenda WHERE IdCita = :id";
        $stmtValidar = $pdo->prepare($sqlValidarCita);
        $stmtValidar->bindParam(':id', $_POST['idCita']);
        $stmtValidar->execute();
        if (!$stmtValidar->fetch()) {
            echo "Error: La cita no existe";
            exit;
        }

        // consulta insert (sin IdPago, dejar que sea autoincremental)
        $sql = "INSERT INTO gestorpagos
                (IdCita, IdPaciente, Monto, MetodoPago, FechaPago, Referencia, EstatusPago)
                VALUES 
                (:idCita, :idPaciente, :monto, :metodoPago, :fechaPago, :referencia, :estatusPago)";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idCita', $_POST['idCita']);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmt->bindParam(':monto', $_POST['monto']);
        $stmt->bindParam(':metodoPago', $_POST['metodoPago']);
        $stmt->bindParam(':fechaPago', $_POST['fechaPago']);
        
        $referencia = !empty($_POST['referencia']) ? $_POST['referencia'] : null;
        $stmt->bindParam(':referencia', $referencia);
        
        $stmt->bindParam(':estatusPago', $_POST['estatusPago']);

        // ejecutar insert
        $stmt->execute();

        // mensaje final
        echo "OK - pago guardado";
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
        $stmtValidar->bindParam(':id', $_POST['idPaciente']);
        $stmtValidar->execute();
        if (!$stmtValidar->fetch()) {
            echo "Error: El paciente no existe";
            exit;
        }

        // validar que exista la cita
        $sqlValidarCita = "SELECT IdCita FROM controlagenda WHERE IdCita = :id";
        $stmtValidar = $pdo->prepare($sqlValidarCita);
        $stmtValidar->bindParam(':id', $_POST['idCita']);
        $stmtValidar->execute();
        if (!$stmtValidar->fetch()) {
            echo "Error: La cita no existe";
            exit;
        }

        // consulta update
        $sql = "UPDATE gestorpagos SET
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
        $stmt->bindParam(':idPago', $_POST['idPagoEditar']);
        $stmt->bindParam(':idCita', $_POST['idCita']);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente']);
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
        $sql = "DELETE FROM gestorpagos WHERE IdPago = :id";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular id
        $stmt->bindParam(':id', $_GET['id']);

        // ejecutar
        $stmt->execute();

        echo "OK - pago eliminado";
        exit;
    }

// captura errores de PDO
} catch (PDOException $e) {

    // imprime error
    echo "Error: " . $e->getMessage();
}
?>

