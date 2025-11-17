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

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'lista') {
        
        $sql = "SELECT 
                    cm.IdMedico,
                    cm.NombreCompleto,
                    cm.CedulaProfesional,
                    cm.EspecialidadId,
                    cm.Telefono,
                    cm.CorreoElectronico,
                    cm.HorarioAtencion,
                    cm.FechaIngreso,
                    cm.Estatus,
                    e.NombreEspecialidad
                FROM controlmedicos cm
                LEFT JOIN especialidades e ON cm.EspecialidadId = e.IdEspecialidad
                ORDER BY cm.IdMedico DESC";
        
        $stmt = $pdo->query($sql);
        $medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($medicos);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {
        
        $sql = "SELECT * FROM controlmedicos WHERE IdMedico = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        
        $medico = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($medico);
        exit;
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idMedicoEditar'])) {
        
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

        echo "OK - Médico guardado correctamente";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idMedicoEditar'])) {
        
        $sql = "UPDATE controlmedicos SET
                NombreCompleto = :nombreCompleto,
                CedulaProfesional = :cedulaProfesional,
                EspecialidadId = :especialidad,
                Telefono = :telefono,
                CorreoElectronico = :correo,
                HorarioAtencion = :horario,
                FechaIngreso = :fechaIngreso,
                Estatus = :estatus
                WHERE IdMedico = :idMedico";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':idMedico', $_POST['idMedicoEditar']);
        $stmt->bindParam(':nombreCompleto', $_POST['nombreCompleto']);
        $stmt->bindParam(':cedulaProfesional', $_POST['cedulaProfesional']);
        $stmt->bindParam(':especialidad', $_POST['especialidad']);
        $stmt->bindParam(':telefono', $_POST['telefono']);
        $stmt->bindParam(':correo', $_POST['correo']);
        $stmt->bindParam(':horario', $_POST['horario']);
        $stmt->bindParam(':fechaIngreso', $_POST['fechaIngreso']);
        $stmt->bindParam(':estatus', $_POST['estatus']);

        $stmt->execute();

        echo "OK - Médico actualizado correctamente";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        
        $sql = "DELETE FROM controlmedicos WHERE IdMedico = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();

        echo "OK - Médico eliminado correctamente";
        exit;
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>