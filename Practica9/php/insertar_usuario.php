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
        
        // consulta sql para obtener usuarios
        $sql = "SELECT 
                    IdUsuario,
                    Usuario,
                    ContrasenaHash,
                    Rol,
                    IdMedico,
                    Activo,
                    UltimoAcceso
                FROM usuarios
                ORDER BY IdUsuario DESC";
        
        // ejecuta la consulta directamente porque no lleva parametros
        $stmt = $pdo->query($sql);

        // obtiene todos los resultados
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // indica que se enviará JSON
        header('Content-Type: application/json');

        // imprime los datos en json
        echo json_encode($usuarios);

        exit; // detiene la ejecución del script
    }

    // obtener un solo usuario por ID
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {
        
        // consulta con parámetro
        $sql = "SELECT 
                    IdUsuario,
                    Usuario,
                    Rol,
                    IdMedico,
                    Activo,
                    UltimoAcceso
                FROM usuarios 
                WHERE IdUsuario = :id";

        // prepara la consulta
        $stmt = $pdo->prepare($sql);

        // vincula parámetro
        $stmt->bindParam(':id', $_GET['id']);

        // ejecuta consulta
        $stmt->execute();
        
        // obtiene un registro
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // indica formato JSON
        header('Content-Type: application/json');

        // imprime el JSON
        echo json_encode($usuario);

        exit;
    }

    // registrar un nuevo usuario (POST sin idUsuarioEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idUsuarioEditar'])) {
        
        // validar que el usuario no exista
        $sqlCheck = "SELECT COUNT(*) FROM usuarios WHERE Usuario = :usuario";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':usuario', $_POST['usuario']);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) {
            echo "ERROR - el nombre de usuario ya existe";
            exit;
        }
        
        // validar que el IdUsuario no exista
        $sqlCheckId = "SELECT COUNT(*) FROM usuarios WHERE IdUsuario = :idUsuario";
        $stmtCheckId = $pdo->prepare($sqlCheckId);
        $stmtCheckId->bindParam(':idUsuario', $_POST['idUsuario']);
        $stmtCheckId->execute();
        
        if ($stmtCheckId->fetchColumn() > 0) {
            echo "ERROR - el ID de usuario ya existe";
            exit;
        }
        
        // consulta insert
        $sql = "INSERT INTO usuarios
                (IdUsuario, Usuario, ContrasenaHash, Rol, IdMedico, Activo, UltimoAcceso)
                VALUES 
                (:idUsuario, :usuario, :contrasena, :rol, :idMedico, :activo, :ultimoAcceso)";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // encriptar la contraseña
        $hash = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
        
        // manejar valores nulos
        $idMedico = empty($_POST['idMedico']) ? null : $_POST['idMedico'];
        $ultimoAcceso = empty($_POST['ultimoAcceso']) ? null : $_POST['ultimoAcceso'];

        // vincular parámetros
        $stmt->bindParam(':idUsuario', $_POST['idUsuario']);
        $stmt->bindParam(':usuario', $_POST['usuario']);
        $stmt->bindParam(':contrasena', $hash);
        $stmt->bindParam(':rol', $_POST['rol']);
        $stmt->bindParam(':idMedico', $idMedico);
        $stmt->bindParam(':activo', $_POST['activo']);
        $stmt->bindParam(':ultimoAcceso', $ultimoAcceso);

        // ejecutar insert
        $stmt->execute();

        // mensaje final
        echo "OK - usuario guardado";
        exit;
    }

    // actualizar un usuario (POST con idUsuarioEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idUsuarioEditar'])) {
        
        // validar que el usuario no esté duplicado (excepto el actual)
        $sqlCheck = "SELECT COUNT(*) FROM usuarios WHERE Usuario = :usuario AND IdUsuario != :idUsuario";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':usuario', $_POST['usuario']);
        $stmtCheck->bindParam(':idUsuario', $_POST['idUsuarioEditar']);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) {
            echo "ERROR - el nombre de usuario ya existe";
            exit;
        }
        
        // manejar valores nulos
        $idMedico = empty($_POST['idMedico']) ? null : $_POST['idMedico'];
        $ultimoAcceso = empty($_POST['ultimoAcceso']) ? null : $_POST['ultimoAcceso'];
        
        // si se envía una nueva contraseña, la actualizamos
        if (!empty($_POST['contrasena'])) {
            $hash = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
            
            $sql = "UPDATE usuarios SET
                    Usuario = :usuario,
                    ContrasenaHash = :contrasena,
                    Rol = :rol,
                    IdMedico = :idMedico,
                    Activo = :activo,
                    UltimoAcceso = :ultimoAcceso
                    WHERE IdUsuario = :idUsuario";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':contrasena', $hash);
        } else {
            // actualizar sin cambiar la contraseña
            $sql = "UPDATE usuarios SET
                    Usuario = :usuario,
                    Rol = :rol,
                    IdMedico = :idMedico,
                    Activo = :activo,
                    UltimoAcceso = :ultimoAcceso
                    WHERE IdUsuario = :idUsuario";
            
            $stmt = $pdo->prepare($sql);
        }

        // vincular parámetros comunes
        $stmt->bindParam(':idUsuario', $_POST['idUsuarioEditar']);
        $stmt->bindParam(':usuario', $_POST['usuario']);
        $stmt->bindParam(':rol', $_POST['rol']);
        $stmt->bindParam(':idMedico', $idMedico);
        $stmt->bindParam(':activo', $_POST['activo']);
        $stmt->bindParam(':ultimoAcceso', $ultimoAcceso);

        // ejecutar update
        $stmt->execute();

        echo "OK - usuario actualizado";
        exit;
    }

    // eliminar usuario
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        
        // consulta delete
        $sql = "DELETE FROM usuarios WHERE IdUsuario = :id";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular id
        $stmt->bindParam(':id', $_GET['id']);

        // ejecutar
        $stmt->execute();

        echo "OK - usuario eliminado";
        exit;
    }

// captura errores de PDO
} catch (PDOException $e) {

    // imprime error
    echo "Error: " . $e->getMessage();
}
?>