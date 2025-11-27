<?php
session_start();

$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "admin";
$pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // recibir datos del login
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // buscar usuario por correo
        $sql = "SELECT u.*, m.NombreCompleto as NombreMedico 
                FROM usuarios u 
                LEFT JOIN controlmedicos m ON u.IdMedico = m.IdMedico
                WHERE u.Usuario = :email AND u.Activo = 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['ContrasenaHash'])) {
            // actualizar último acceso
            $sqlUpdate = "UPDATE usuarios SET UltimoAcceso = NOW() WHERE IdUsuario = :id";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':id', $usuario['IdUsuario']);
            $stmtUpdate->execute();

            // guardar datos en sesión
            $_SESSION['usuario_id'] = $usuario['IdUsuario'];
            $_SESSION['usuario_nombre'] = $usuario['Usuario'];
            $_SESSION['usuario_rol'] = $usuario['Rol'];
            $_SESSION['id_medico'] = $usuario['IdMedico'];

            // responder con éxito
            echo json_encode([
                'success' => true,
                'rol' => $usuario['Rol'],
                'nombre' => $usuario['Usuario'],
                'idMedico' => $usuario['IdMedico']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Correo o contraseña incorrectos'
            ]);
        }
        exit;
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error en el servidor: ' . $e->getMessage()
    ]);
}
?>