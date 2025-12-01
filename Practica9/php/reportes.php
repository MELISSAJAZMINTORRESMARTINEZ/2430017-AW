<?php
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "admin";
$pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    /* ============= LISTA ============= */
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['accion'] === 'lista') {

        $sql = "SELECT 
                IdReporte,
                TipoReporte,
                FechaGeneracion,
                RutaArchivo,
                Descripcion,
                GeneradoPor
                FROM reportes
                ORDER BY IdReporte DESC";

        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }


    /* ============= OBTENER UNO ============= */
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['accion'] === 'obtener') {

        $stmt = $pdo->prepare("SELECT * FROM reportes WHERE IdReporte = :id");
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();

        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        exit;
    }


    /* ============= INSERTAR ============= */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idReporteEditar'])) {

        $sql = "INSERT INTO reportes
                (IdReporte, TipoReporte, FechaGeneracion, RutaArchivo, Descripcion, GeneradoPor)
                VALUES
                (:idReporte, :tipoReporte, :fecha, :ruta, :descripcion, :generado)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':idReporte' => $_POST['idReporte'],
            ':tipoReporte' => $_POST['tipoReporte'],
            ':fecha' => $_POST['fechaGeneracion'],
            ':ruta' => $_POST['ruta'],
            ':descripcion' => $_POST['descripcion'],
            ':generado' => $_POST['generado']
        ]);

        echo "OK - reporte guardado";
        exit;
    }


    /* ============= EDITAR ============= */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idReporteEditar'])) {

        $sql = "UPDATE reportes SET
                TipoReporte = :tipo,
                FechaGeneracion = :fecha,
                RutaArchivo = :ruta,
                Descripcion = :descripcion,
                GeneradoPor = :generado
                WHERE IdReporte = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':id' => $_POST['idReporteEditar'],
            ':tipo' => $_POST['tipoReporte'],
            ':fecha' => $_POST['fechaGeneracion'],
            ':ruta' => $_POST['ruta'],
            ':descripcion' => $_POST['descripcion'],
            ':generado' => $_POST['generado']
        ]);

        echo "OK - reporte actualizado";
        exit;
    }


    /* ============= ELIMINAR ============= */
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['accion'] === 'eliminar') {

        $stmt = $pdo->prepare("DELETE FROM reportes WHERE IdReporte = :id");
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();

        echo "OK - reporte eliminado";
        exit;
    }


    /* ============= DATOS DE PAGOS ============= */
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['accion'] === 'datosPagos') {

        $sql = "SELECT 
                p.IdPago,
                p.Monto,
                p.MetodoPago,
                p.FechaPago,
                p.Referencia,
                p.EstatusPago
                FROM gestorpagos p
                ORDER BY p.FechaPago DESC";

        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }


} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
