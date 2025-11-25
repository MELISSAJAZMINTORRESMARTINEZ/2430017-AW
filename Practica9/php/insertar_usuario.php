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
        
        // consulta sql para obtener usuarios con nombre de médico si aplica
        $sql = "SELECT 
                    u.IdUsuario,
                    u.Usuario,
                    u.ContrasenaHash,
                    u.Rol,
                    u.IdMedico,
                    u.Activo,
                    u.UltimoAcceso,
                    m.NombreCompleto as NombreMedico
                FROM usuarios u
                LEFT JOIN controlmedicos m ON u.IdMedico = m.IdMedico
                ORDER BY u.IdUsuario DESC";
        
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
        $sql = "SELECT * FROM usuarios WHERE IdUsuario = :id";

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
        
        // validar que el usuario no exista ya
        $sqlCheck = "SELECT COUNT(*) FROM usuarios WHERE Usuario = :usuario";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':usuario', $_POST['usuario']);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) {
            echo "Error: El nombre de usuario ya existe";
            exit;
        }

        // encriptar la contraseña
        $hash = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

        // consulta insert
        $sql = "INSERT INTO usuarios
                (IdUsuario, Usuario, ContrasenaHash, Rol, IdMedico, Activo, UltimoAcceso)
                VALUES 
                (:idUsuario, :usuario, :contrasena, :rol, :idMedico, :activo, :ultimoAcceso)";

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idUsuario', $_POST['idUsuario']);
        $stmt->bindParam(':usuario', $_POST['usuario']);
        $stmt->bindParam(':contrasena', $hash);
        $stmt->bindParam(':rol', $_POST['rol']);
        
        // IdMedico puede ser NULL
        $idMedico = !empty($_POST['idMedico']) ? $_POST['idMedico'] : null;
        $stmt->bindParam(':idMedico', $idMedico);
        
        $stmt->bindParam(':activo', $_POST['activo']);
        
        // UltimoAcceso puede ser NULL
        $ultimoAcceso = !empty($_POST['ultimoAcceso']) ? $_POST['ultimoAcceso'] : null;
        $stmt->bindParam(':ultimoAcceso', $ultimoAcceso);

        // ejecutar insert
        $stmt->execute();

        // mensaje final
        echo "OK - usuario guardado";
        exit;
    }

    // actualizar un usuario (POST con idUsuarioEditar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idUsuarioEditar'])) {
        
        // si se envió una nueva contraseña, actualizarla
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
        } else {
            // si no se envió contraseña, no actualizarla
            $sql = "UPDATE usuarios SET
                    Usuario = :usuario,
                    Rol = :rol,
                    IdMedico = :idMedico,
                    Activo = :activo,
                    UltimoAcceso = :ultimoAcceso
                    WHERE IdUsuario = :idUsuario";
        }

        // preparar consulta
        $stmt = $pdo->prepare($sql);

        // vincular parámetros
        $stmt->bindParam(':idUsuario', $_POST['idUsuarioEditar']);
        $stmt->bindParam(':usuario', $_POST['usuario']);
        
        if (!empty($_POST['contrasena'])) {
            $stmt->bindParam(':contrasena', $hash);
        }
        
        $stmt->bindParam(':rol', $_POST['rol']);
        
        $idMedico = !empty($_POST['idMedico']) ? $_POST['idMedico'] : null;
        $stmt->bindParam(':idMedico', $idMedico);
        
        $stmt->bindParam(':activo', $_POST['activo']);
        
        $ultimoAcceso = !empty($_POST['ultimoAcceso']) ? $_POST['ultimoAcceso'] : null;
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