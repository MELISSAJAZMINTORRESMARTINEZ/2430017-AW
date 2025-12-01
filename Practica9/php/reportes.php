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
        
        // consulta sql para obtener reportes
        $sql = "SELECT 
                    IdReporte,
                    TipoReporte,
                    IdPaciente,
                    IdMedico,
                    FechaGeneracion,
                    RutaArchivo,
                    Descripcion,
                    GeneradoPor
                FROM reportes
                ORDER BY IdReporte DESC";
        
        // ejecuta la consulta directamente porque no lleva parametros
        $stmt = $pdo->query($sql);

        // obtiene todos los resultados
        $reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // indica que se enviará JSON
        header('Content-Type: application/json');

        // imprime los datos en json
        echo json_encode($reportes);

        exit; // detiene la ejecución del script
    }

    // obtener un solo reporte por ID
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {
        
        // consulta con parámetro
        $sql = "SELECT * FROM reportes WHERE IdReporte = :id";

        // prepara la consulta
        $stmt = $pdo->prepare($sql);

        // vincula parámetro
        $stmt->bindParam(':id', $_GET['id']);

        // ejecuta consulta
        $stmt->execute();
        
        // obtiene un registro
        $reporte = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // indica formato JSON
        header('Content-Type: application/json');

        // imprime el JSON
        echo json_encode($reporte);

        exit;
    }

    // registrar un nuevo reporte (POST sin idReporteEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idReporteEditar'])) {
        
        // consulta insert
        $sql = "INSERT INTO reportes
                (IdReporte, TipoReporte, IdPaciente, IdMedico, FechaGeneracion, RutaArchivo, Descripcion, GeneradoPor)
                VALUES 
                (:idReporte, :tipoReporte, :idPaciente, :idMedico, :fechaGeneracion, :ruta, :descripcion, :generado)";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idReporte', $_POST['idReporte']);
        $stmt->bindParam(':tipoReporte', $_POST['tipoReporte']);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmt->bindParam(':idMedico', $_POST['idMedico']);
        $stmt->bindParam(':fechaGeneracion', $_POST['fechaGeneracion']);
        $stmt->bindParam(':ruta', $_POST['ruta']);
        $stmt->bindParam(':descripcion', $_POST['descripcion']);
        $stmt->bindParam(':generado', $_POST['generado']);

        // ejecutar insert
        $stmt->execute();

        // mensaje final
        echo "OK - reporte guardado";
        exit;
    }

    // actualizar un reporte (POST con idReporteEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idReporteEditar'])) {
        
        // consulta update
        $sql = "UPDATE reportes SET
                TipoReporte = :tipoReporte,
                IdPaciente = :idPaciente,
                IdMedico = :idMedico,
                FechaGeneracion = :fechaGeneracion,
                RutaArchivo = :ruta,
                Descripcion = :descripcion,
                GeneradoPor = :generado
                WHERE IdReporte = :idReporte";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idReporte', $_POST['idReporteEditar']);
        $stmt->bindParam(':tipoReporte', $_POST['tipoReporte']);
        $stmt->bindParam(':idPaciente', $_POST['idPaciente']);
        $stmt->bindParam(':idMedico', $_POST['idMedico']);
        $stmt->bindParam(':fechaGeneracion', $_POST['fechaGeneracion']);
        $stmt->bindParam(':ruta', $_POST['ruta']);
        $stmt->bindParam(':descripcion', $_POST['descripcion']);
        $stmt->bindParam(':generado', $_POST['generado']);

        // ejecutar update
        $stmt->execute();

        echo "OK - reporte actualizado";
        exit;
    }

    // eliminar reporte
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        
        // consulta delete
        $sql = "DELETE FROM reportes WHERE IdReporte = :id";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular id
        $stmt->bindParam(':id', $_GET['id']);

        // ejecutar
        $stmt->execute();

        echo "OK - reporte eliminado";
        exit;
    }

    // obtener datos de pagos para reporte
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'datosPagos') {
        
        // consulta para obtener todos los pagos con detalles
        $sql = "SELECT 
                    p.IdPago,
                    p.IdCita,
                    p.IdPaciente,
                    pac.NombreCompleto as NombrePaciente,
                    p.Monto,
                    p.MetodoPago,
                    p.FechaPago,
                    p.Referencia,
                    p.EstatusPago,
                    a.FechaCita,
                    a.MotivoConsulta,
                    m.NombreCompleto as NombreMedico
                FROM gestorpagos p
                LEFT JOIN controlpacientes pac ON p.IdPaciente = pac.IdPaciente
                LEFT JOIN controlagenda a ON p.IdCita = a.IdCita
                LEFT JOIN controlmedicos m ON a.IdMedico = m.IdMedico
                ORDER BY p.FechaPago DESC";
        
        $stmt = $pdo->query($sql);
        $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($pagos);
        exit;
    }

// captura errores de PDO
} catch (PDOException $e) {

    // imprime error
    echo "Error: " . $e->getMessage();
}
?>