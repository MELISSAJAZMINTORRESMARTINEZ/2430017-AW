<?php
// Parámetros de conexión
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "admin";
$pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554"; // vacío


try {
    // Conexión PDO
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar la consulta SQL
    $sql = "INSERT INTO usuarios
            (IdUsuario, Usuario, ContrasenaHash, Rol, IdMedico, Activo, UltimoAcceso)
            VALUES 
            (:idUsuario, :usuario, :contrasena, :rol, :idMedico, :activo, :ultimoAcceso)";

    $stmt = $pdo->prepare($sql);

    // Enlazar parámetros (usa $_POST con los mismos nombres de tus inputs)
    $stmt->bindParam(':idUsuario', $_POST['idUsuario']);
    $stmt->bindParam(':usuario', $_POST['usuario']);
    
    // 🔒 Encriptar la contraseña antes de guardar
    $hash = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $stmt->bindParam(':contrasena', $hash);
    
    $stmt->bindParam(':rol', $_POST['rol']);
    $stmt->bindParam(':idMedico', $_POST['idMedico']);
    $stmt->bindParam(':activo', $_POST['activo']);
    $stmt->bindParam(':ultimoAcceso', $_POST['ultimoAcceso']);

    // Ejecutar
    $stmt->execute();

    echo "<h3 style='color:green;'>✅ Usuario agregado correctamente.</h3>";

} catch (PDOException $e) {
    echo "<h3 style='color:red;'>❌ Error al guardar usuario: " . $e->getMessage() . "</h3>";
}
?>
