<?php
// se definen los datos para conectar a la base de datos
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "admin";
$pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554"; 


// iniciamos un bloque try para capturar errores
try {

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // LISTA DE TARIFAS
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'lista') {

        $sql = "SELECT 
                    gt.IdTarifa,
                    gt.DescripcionServicio,
                    gt.CostoBase,
                    gt.EspecialidadId,
                    e.NombreEspecialidad
                FROM gestortarifas gt
                LEFT JOIN especialidades e ON gt.EspecialidadId = e.IdEspecialidad
                ORDER BY gt.IdTarifa DESC";

        $stmt = $pdo->query($sql);
        $tarifas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($tarifas);
        exit;
    }

    // OBTENER UNA TARIFA
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {

        $sql = "SELECT * FROM gestortarifas WHERE IdTarifa = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();

        $tarifa = $stmt->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($tarifa);
        exit;
    }

    // REGISTRAR NUEVA TARIFA
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idTarifaEditar'])) {

        $sql = "INSERT INTO gestortarifas
                (IdTarifa, DescripcionServicio, CostoBase, EspecialidadId)
                VALUES 
                (:idTarifa, :descripcionServicio, :costoBase, :especialidadId)";
        
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':idTarifa', $_POST['idTarifa']);
        $stmt->bindParam(':descripcionServicio', $_POST['descripcionServicio']);
        $stmt->bindParam(':costoBase', $_POST['costoBase']);
        $stmt->bindParam(':especialidadId', $_POST['especialidadId']);

        $stmt->execute();

        echo "OK - tarifa guardada";
        exit;
    }

    // ACTUALIZAR TARIFA
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idTarifaEditar'])) {

        $sql = "UPDATE gestortarifas SET
                DescripcionServicio = :descripcionServicio,
                CostoBase = :costoBase,
                EspecialidadId = :especialidadId
                WHERE IdTarifa = :idTarifa";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':idTarifa', $_POST['idTarifaEditar']);
        $stmt->bindParam(':descripcionServicio', $_POST['descripcionServicio']);
        $stmt->bindParam(':costoBase', $_POST['costoBase']);
        $stmt->bindParam(':especialidadId', $_POST['especialidadId']);

        $stmt->execute();

        echo "OK - tarifa actualizada";
        exit;
    }

    // ELIMINAR TARIFA
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {

        $sql = "DELETE FROM gestortarifas WHERE IdTarifa = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();

        echo "OK - tarifa eliminada";
        exit;
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
