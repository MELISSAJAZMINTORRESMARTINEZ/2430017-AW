<?php
session_start();

// Datos de conexión a la base de datos
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "admin";
$pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener lista de reportes
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'lista') {
        $sql = "SELECT 
                    r.IdReporte,
                    r.TipoReporte,
                    r.IdPaciente,
                    r.IdMedico,
                    r.FechaGeneracion,
                    r.RutaArchivo,
                    r.Descripcion,
                    r.GeneradoPor,
                    p.NombreCompleto as NombrePaciente,
                    m.NombreCompleto as NombreMedico
                FROM reportes r
                LEFT JOIN controlpacientes p ON r.IdPaciente = p.IdPaciente
                LEFT JOIN controlmedicos m ON r.IdMedico = m.IdMedico
                ORDER BY r.FechaGeneracion DESC";
        
        $stmt = $pdo->query($sql);
        $reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($reportes);
        exit;
    }

   // Obtener datos para generar reporte de pagos
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'datosPagos') {
    $idPaciente = isset($_GET['idPaciente']) ? $_GET['idPaciente'] : null;
    $fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : null;
    $fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : null;
    
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
                pac.Telefono as TelefonoPaciente,
                pac.Correo as CorreoPaciente,
                a.FechaCita,
                a.MotivoConsulta,
                m.NombreCompleto as NombreMedico,
                e.NombreEspecialidad
            FROM gestorpagos p
            LEFT JOIN controlpacientes pac ON p.IdPaciente = pac.IdPaciente
            LEFT JOIN controlagenda a ON p.IdCita = a.IdCita
            LEFT JOIN controlmedicos m ON a.IdMedico = m.IdMedico
            LEFT JOIN especialidadesmedicas e ON m.IdEspecialidad = e.IdEspecialidad
            WHERE p.EstatusPago = 'Pagado'";
    
    if ($idPaciente) {
        $sql .= " AND p.IdPaciente = :idPaciente";
    }
    
    if ($fechaInicio) {
        $sql .= " AND p.FechaPago >= :fechaInicio";
    }
    
    if ($fechaFin) {
        $sql .= " AND p.FechaPago <= :fechaFin";
    }
    
    $sql .= " ORDER BY p.FechaPago DESC";
    
    $stmt = $pdo->prepare($sql);
    
    if ($idPaciente) {
        $stmt->bindParam(':idPaciente', $idPaciente);
    }
    if ($fechaInicio) {
        $stmt->bindParam(':fechaInicio', $fechaInicio);
    }
    if ($fechaFin) {
        $stmt->bindParam(':fechaFin', $fechaFin);
    }
    
    $stmt->execute();
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($datos);
    exit;
}

    // Guardar registro de reporte generado
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
        $sql = "INSERT INTO reportes 
                (TipoReporte, IdPaciente, IdMedico, FechaGeneracion, RutaArchivo, Descripcion, GeneradoPor)
                VALUES 
                (:tipoReporte, :idPaciente, :idMedico, :fechaGeneracion, :rutaArchivo, :descripcion, :generadoPor)";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':tipoReporte', $_POST['tipoReporte']);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmt->bindParam(':idMedico', $_POST['idMedico']);
        $stmt->bindParam(':fechaGeneracion', $_POST['fechaGeneracion']);
        $stmt->bindParam(':rutaArchivo', $_POST['rutaArchivo']);
        $stmt->bindParam(':descripcion', $_POST['descripcion']);
        $stmt->bindParam(':generadoPor', $_POST['generadoPor']);
        
        $stmt->execute();
        
        echo "OK - reporte registrado";
        exit;
    }

    // Eliminar reporte
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        $sql = "DELETE FROM reportes WHERE IdReporte = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        
        echo "OK - reporte eliminado";
        exit;
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>