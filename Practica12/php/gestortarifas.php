<?php
// se definen los datos para conectar a la base de datos
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "root";
$pass = "";

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
        
        // consulta sql para obtener tarifas y su especialidad con LEFT JOIN
        $sql = "SELECT 
                    gt.IdTarifa,
                    gt.DescripcionServicio,
                    gt.CostoBase,
                    gt.EspecialidadId,
                    gt.Estatus,
                    e.NombreEspecialidad
                FROM gestortarifas gt
                LEFT JOIN especialidades e ON gt.EspecialidadId = e.IdEspecialidad
                ORDER BY gt.IdTarifa DESC";
        
        // ejecuta la consulta directamente porque no lleva parametros
        $stmt = $pdo->query($sql);

        // obtiene todos los resultados
        $tarifas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // indica que se enviará JSON
        header('Content-Type: application/json');

        // imprime los datos en json
        echo json_encode($tarifas);

        exit; // detiene la ejecución del script
    }

    // obtener una sola tarifa por ID
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {
        
        // consulta con parámetro
        $sql = "SELECT * FROM gestortarifas WHERE IdTarifa = :id";

        // prepara la consulta
        $stmt = $pdo->prepare($sql);

        // vincula parámetro
        $stmt->bindParam(':id', $_GET['id']);

        // ejecuta consulta
        $stmt->execute();
        
        // obtiene un registro
        $tarifa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // indica formato JSON
        header('Content-Type: application/json');

        // imprime el JSON
        echo json_encode($tarifa);

        exit;
    }

    // registrar una nueva tarifa (POST sin idTarifaEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idTarifaEditar'])) {
        
        // consulta insert
        $sql = "INSERT INTO gestortarifas
                (IdTarifa, DescripcionServicio, CostoBase, EspecialidadId, Estatus)
                VALUES 
                (:idTarifa, :descripcionServicio, :costoBase, :especialidadId, :estatus)";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idTarifa', $_POST['idTarifa']);
        $stmt->bindParam(':descripcionServicio', $_POST['descripcionServicio']);
        $stmt->bindParam(':costoBase', $_POST['costoBase']);
        $stmt->bindParam(':especialidadId', $_POST['especialidadId']);
        $stmt->bindParam(':estatus', $_POST['estatus']);

        // ejecutar insert
        $stmt->execute();

        // mensaje final
        echo "OK - tarifa guardada";
        exit;
    }

    // actualizar una tarifa (POST con idTarifaEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idTarifaEditar'])) {
        
        // consulta update
        $sql = "UPDATE gestortarifas SET
                DescripcionServicio = :descripcionServicio,
                CostoBase = :costoBase,
                EspecialidadId = :especialidadId,
                Estatus = :estatus
                WHERE IdTarifa = :idTarifa";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idTarifa', $_POST['idTarifaEditar']);
        $stmt->bindParam(':descripcionServicio', $_POST['descripcionServicio']);
        $stmt->bindParam(':costoBase', $_POST['costoBase']);
        $stmt->bindParam(':especialidadId', $_POST['especialidadId']);
        $stmt->bindParam(':estatus', $_POST['estatus']);

        // ejecutar update
        $stmt->execute();

        echo "OK - tarifa actualizada";
        exit;
    }

    // eliminar tarifa
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        
        // consulta delete
        $sql = "DELETE FROM gestortarifas WHERE IdTarifa = :id";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular id
        $stmt->bindParam(':id', $_GET['id']);

        // ejecutar
        $stmt->execute();

        echo "OK - tarifa eliminada";
        exit;
    }

// captura errores de PDO
} catch (PDOException $e) {

    // imprime error
    echo "Error: " . $e->getMessage();
}
?>