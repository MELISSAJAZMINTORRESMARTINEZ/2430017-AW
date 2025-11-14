<?php
// Parámetros de conexión
$host = "localhost";
$port = "3306";
$dbname = "clinica";
$user = "root";
$pass = "";

try {
    // Conexión PDO
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar la consulta SQL
    $sql = "INSERT INTO controlmedicos
(IdMedico, NombreCompleto, CedulaProfesional, EspecialidadId, Telefono, CorreoElectronico, HorarioAtencion, FechaIngreso, Estatus)
VALUES 
(:idMedico, :nombreCompleto, :cedulaProfesional, :especialidad, :telefono, :correo, :horario, :fechaIngreso, :estatus)";


  $stmt = $pdo->prepare($sql);

$stmt->bindParam(':idMedico', $_POST['idMedico']);
$stmt->bindParam(':nombreCompleto', $_POST['nombreCompleto']);
$stmt->bindParam(':cedulaProfesional', $_POST['cedulaProfesional']);
$stmt->bindParam(':especialidad', $_POST['especialidad']);
$stmt->bindParam(':telefono', $_POST['telefono']);
$stmt->bindParam(':correo', $_POST['correo']);
$stmt->bindParam(':horario', $_POST['horario']);
$stmt->bindParam(':fechaIngreso', $_POST['fechaIngreso']);
$stmt->bindParam(':estatus', $_POST['estatus']);

$stmt->execute();


    echo "<h3 style='color:green;'>Medico agreagdo correctamente.</h3>";

} catch (PDOException $e) {
    echo "<h3 style='color:red;' Error al guardar medico: " . $e->getMessage() . "</h3>";
}
?>
