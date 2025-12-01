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
        
        // consulta sql para obtener bitácoras con información del usuario
        $sql = "SELECT 
                    b.IdBitacora,
                    b.IdUsuario,
                    b.FechaAcceso,
                    b.AccionRealizada,
                    b.Modulo,
                    u.Usuario as NombreUsuario,
                    u.Rol
                FROM bitacoraacceso b
                LEFT JOIN usuarios u ON b.IdUsuario = u.IdUsuario
                ORDER BY b.FechaAcceso DESC";
        
        // ejecuta la consulta directamente
        $stmt = $pdo->query($sql);

        // obtiene todos los resultados
        $bitacoras = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // indica que se enviará JSON
        header('Content-Type: application/json');

        // imprime los datos en json
        echo json_encode($bitacoras);

        exit;
    }

    // obtener una sola bitácora por ID
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {
        
        // consulta con parámetro
        $sql = "SELECT * FROM bitacoraacceso WHERE IdBitacora = :id";

        // prepara la consulta
        $stmt = $pdo->prepare($sql);

        // vincula parámetro
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);

        // ejecuta consulta
        $stmt->execute();
        
        // obtiene un registro
        $bitacora = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // indica formato JSON
        header('Content-Type: application/json');

        // imprime el JSON
        echo json_encode($bitacora);

        exit;
    }

    // validar si existe un usuario
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'validarUsuario') {
        
        $id = $_GET['id'];
        
        $sql = "SELECT IdUsuario, Usuario, Rol FROM usuarios WHERE IdUsuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($usuario ?: ['error' => 'Usuario no encontrado']);
        exit;
    }

    // registrar una nueva bitácora (POST sin idBitacoraEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idBitacoraEditar'])) {
        
        // validaciones
        if (empty($_POST['idUsuario'])) {
            echo "Error: El ID de usuario es obligatorio";
            exit;
        }

        // validar que exista el usuario
        $sqlValidarUsuario = "SELECT IdUsuario FROM usuarios WHERE IdUsuario = :id";
        $stmtValidar = $pdo->prepare($sqlValidarUsuario);
        $stmtValidar->bindParam(':id', $_POST['idUsuario'], PDO::PARAM_INT);
        $stmtValidar->execute();
        
        if (!$stmtValidar->fetch()) {
            echo "Error: El usuario con ID " . $_POST['idUsuario'] . " no existe";
            exit;
        }

        // consulta insert (IdBitacora autoincremental)
        $sql = "INSERT INTO bitacoraacceso
                (IdUsuario, FechaAcceso, AccionRealizada, Modulo)
                VALUES 
                (:idUsuario, :fechaAcceso, :accionRealizada, :modulo)";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idUsuario', $_POST['idUsuario'], PDO::PARAM_INT);
        $stmt->bindParam(':fechaAcceso', $_POST['fechaAcceso']);
        $stmt->bindParam(':accionRealizada', $_POST['accionRealizada']);
        $stmt->bindParam(':modulo', $_POST['modulo']);

        // ejecutar insert
        $stmt->execute();

        // mensaje final
        echo "OK - bitácora guardada con ID: " . $pdo->lastInsertId();
        exit;
    }

    // actualizar una bitácora (POST con idBitacoraEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idBitacoraEditar'])) {
        
        // validaciones
        if (empty($_POST['idUsuario'])) {
            echo "Error: El ID de usuario es obligatorio";
            exit;
        }

        // validar que exista el usuario
        $sqlValidarUsuario = "SELECT IdUsuario FROM usuarios WHERE IdUsuario = :id";
        $stmtValidar = $pdo->prepare($sqlValidarUsuario);
        $stmtValidar->bindParam(':id', $_POST['idUsuario'], PDO::PARAM_INT);
        $stmtValidar->execute();
        
        if (!$stmtValidar->fetch()) {
            echo "Error: El usuario no existe";
            exit;
        }

        // consulta update
        $sql = "UPDATE bitacoraacceso SET
                IdUsuario = :idUsuario,
                FechaAcceso = :fechaAcceso,
                AccionRealizada = :accionRealizada,
                Modulo = :modulo
                WHERE IdBitacora = :idBitacora";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idBitacora', $_POST['idBitacoraEditar'], PDO::PARAM_INT);
        $stmt->bindParam(':idUsuario', $_POST['idUsuario'], PDO::PARAM_INT);
        $stmt->bindParam(':fechaAcceso', $_POST['fechaAcceso']);
        $stmt->bindParam(':accionRealizada', $_POST['accionRealizada']);
        $stmt->bindParam(':modulo', $_POST['modulo']);

        // ejecutar update
        $stmt->execute();

        echo "OK - bitácora actualizada";
        exit;
    }

    // eliminar bitácora
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        
        // consulta delete
        $sql = "DELETE FROM bitacoraacceso WHERE IdBitacora = :id";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular id
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);

        // ejecutar
        $stmt->execute();

        echo "OK - bitácora eliminada";
        exit;
    }

// captura errores de PDO
} catch (PDOException $e) {

    // imprime error
    error_log("Error PDO Bitácora: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
}
?>