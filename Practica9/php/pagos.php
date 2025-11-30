<?php
session_start();

// datos para conectar a la base de datos
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "admin";
$pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554";

try {
    // conexion con pdo
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // LISTAR TODOS LOS PAGOS
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'lista') {
        
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
                    c.FechaCita,
                    m.NombreCompleto as NombreMedico
                FROM pagos p
                LEFT JOIN controlpacientes pac ON p.IdPaciente = pac.IdPaciente
                LEFT JOIN controlagenda c ON p.IdCita = c.IdCita
                LEFT JOIN controlmedicos m ON c.IdMedico = m.IdMedico
                ORDER BY p.FechaPago DESC";
        
        $stmt = $pdo->query($sql);
        $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($pagos);
        exit;
    }

    // OBTENER UN PAGO POR ID
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {
        
        $sql = "SELECT * FROM pagos WHERE IdPago = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        
        $pago = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($pago ?: ['error' => 'Pago no encontrado']);
        exit;
    }

    // VALIDAR QUE EXISTE EL PACIENTE
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

    // VALIDAR QUE EXISTE LA CITA Y PERTENECE AL PACIENTE
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'validarCita') {
        
        $sql = "SELECT 
                    c.IdCita,
                    c.IdPaciente,
                    c.FechaCita,
                    c.MotivoConsulta,
                    p.NombreCompleto as NombrePaciente,
                    m.NombreCompleto as NombreMedico
                FROM controlagenda c
                LEFT JOIN controlpacientes p ON c.IdPaciente = p.IdPaciente
                LEFT JOIN controlmedicos m ON c.IdMedico = m.IdMedico
                WHERE c.IdCita = :idCita";
        
        if (isset($_GET['idPaciente']) && !empty($_GET['idPaciente'])) {
            $sql .= " AND c.IdPaciente = :idPaciente";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idCita', $_GET['idCita']);
        
        if (isset($_GET['idPaciente']) && !empty($_GET['idPaciente'])) {
            $stmt->bindParam(':idPaciente', $_GET['idPaciente']);
        }
        
        $stmt->execute();
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($cita ?: ['error' => 'Cita no encontrada o no pertenece al paciente']);
        exit;
    }

    // VERIFICAR SI LA CITA YA TIENE UN PAGO
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'verificarPago') {
        
        $sql = "SELECT IdPago, Monto, EstatusPago FROM pagos WHERE IdCita = :idCita";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idCita', $_GET['idCita']);
        $stmt->execute();
        
        $pagoExistente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($pagoExistente ?: ['noPago' => true]);
        exit;
    }

    // INSERTAR NUEVO PAGO
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idPagoEditar'])) {
        
        // validaciones
        if (empty($_POST['idPaciente']) || empty($_POST['idCita'])) {
            echo "Error: Paciente y Cita son obligatorios";
            exit;
        }

        if (empty($_POST['monto']) || $_POST['monto'] <= 0) {
            echo "Error: El monto debe ser mayor a 0";
            exit;
        }

        // verificar que el paciente existe
        $sqlPaciente = "SELECT IdPaciente FROM controlpacientes WHERE IdPaciente = :id";
        $stmtPaciente = $pdo->prepare($sqlPaciente);
        $stmtPaciente->bindParam(':id', $_POST['idPaciente']);
        $stmtPaciente->execute();
        
        if (!$stmtPaciente->fetch()) {
            echo "Error: El paciente no existe";
            exit;
        }

        // verificar que la cita existe y pertenece al paciente
        $sqlCita = "SELECT IdCita FROM controlagenda WHERE IdCita = :idCita AND IdPaciente = :idPaciente";
        $stmtCita = $pdo->prepare($sqlCita);
        $stmtCita->bindParam(':idCita', $_POST['idCita']);
        $stmtCita->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmtCita->execute();
        
        if (!$stmtCita->fetch()) {
            echo "Error: La cita no existe o no pertenece al paciente seleccionado";
            exit;
        }

        // verificar que la cita no tenga ya un pago
        $sqlPagoExiste = "SELECT IdPago FROM pagos WHERE IdCita = :idCita";
        $stmtPagoExiste = $pdo->prepare($sqlPagoExiste);
        $stmtPagoExiste->bindParam(':idCita', $_POST['idCita']);
        $stmtPagoExiste->execute();
        
        if ($stmtPagoExiste->fetch()) {
            echo "Error: Esta cita ya tiene un pago registrado";
            exit;
        }

        // insertar pago
        $sql = "INSERT INTO pagos
                (IdCita, IdPaciente, Monto, MetodoPago, FechaPago, Referencia, EstatusPago)
                VALUES 
                (:idCita, :idPaciente, :monto, :metodoPago, :fechaPago, :referencia, :estatusPago)";

        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':idCita', $_POST['idCita']);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmt->bindParam(':monto', $_POST['monto']);
        $stmt->bindParam(':metodoPago', $_POST['metodoPago']);
        $stmt->bindParam(':fechaPago', $_POST['fechaPago']);
        $stmt->bindParam(':referencia', $_POST['referencia']);
        $stmt->bindParam(':estatusPago', $_POST['estatusPago']);

        $stmt->execute();

        echo "OK - pago guardado";
        exit;
    }

    // ACTUALIZAR PAGO
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idPagoEditar'])) {
        
        // validaciones
        if (empty($_POST['monto']) || $_POST['monto'] <= 0) {
            echo "Error: El monto debe ser mayor a 0";
            exit;
        }

        $sql = "UPDATE pagos SET
                Monto = :monto,
                MetodoPago = :metodoPago,
                FechaPago = :fechaPago,
                Referencia = :referencia,
                EstatusPago = :estatusPago
                WHERE IdPago = :idPago";

        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':idPago', $_POST['idPagoEditar']);
        $stmt->bindParam(':monto', $_POST['monto']);
        $stmt->bindParam(':metodoPago', $_POST['metodoPago']);
        $stmt->bindParam(':fechaPago', $_POST['fechaPago']);
        $stmt->bindParam(':referencia', $_POST['referencia']);
        $stmt->bindParam(':estatusPago', $_POST['estatusPago']);

        $stmt->execute();

        echo "OK - pago actualizado";
        exit;
    }

    // ELIMINAR PAGO
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        
        $sql = "DELETE FROM pagos WHERE IdPago = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();

        echo "OK - pago eliminado";
        exit;
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>