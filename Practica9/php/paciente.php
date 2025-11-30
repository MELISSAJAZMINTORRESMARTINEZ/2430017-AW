<?php
// Iniciar sesión para obtener datos del usuario
session_start();

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

    // validamos si llega una peticion get y si accion es lista
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'lista') {
        
        // Obtener rol e id del médico desde la sesión
        $rolUsuario = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
        $idMedicoSesion = isset($_SESSION['id_medico']) ? $_SESSION['id_medico'] : null;
        
        // Si es médico, mostrar solo los pacientes que tienen citas con él
        if ($rolUsuario === 'medico' && $idMedicoSesion) {
            // Consulta que obtiene solo los pacientes que tienen citas con este médico
            $sql = "SELECT DISTINCT
                        p.IdPaciente,
                        p.NombreCompleto,
                        p.CURP,
                        p.FechaNacimiento,
                        p.Sexo,
                        p.Telefono,
                        p.CorreoElectronico,
                        p.Direccion,
                        p.ContactoEmergencia,
                        p.TelefonoEmergencia,
                        p.Alergias,
                        p.AntecedentesMedicos,
                        p.FechaRegistro,
                        p.Estatus
                    FROM controlpacientes p
                    INNER JOIN controlagenda a ON p.IdPaciente = a.IdPaciente
                    WHERE a.IdMedico = :idMedico
                    ORDER BY p.IdPaciente DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idMedico', $idMedicoSesion, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Si es admin o secretaria, mostrar todos los pacientes
            $sql = "SELECT 
                        IdPaciente,
                        NombreCompleto,
                        CURP,
                        FechaNacimiento,
                        Sexo,
                        Telefono,
                        CorreoElectronico,
                        Direccion,
                        ContactoEmergencia,
                        TelefonoEmergencia,
                        Alergias,
                        AntecedentesMedicos,
                        FechaRegistro,
                        Estatus
                    FROM controlpacientes
                    ORDER BY IdPaciente DESC";
            
            $stmt = $pdo->query($sql);
        }

        // se obtienen todos los resultados como arreglo asociativo
        $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // indicamos que la respuesta sera json
        header('Content-Type: application/json');

        // convertimos los datos a json y los imprimimos
        echo json_encode($pacientes);

        // detenemos la ejecucion
        exit;
    }

    // aqui revisamos si quiere obtener un solo paciente por id
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener') {
        
        // Obtener rol e id del médico desde la sesión
        $rolUsuario = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
        $idMedicoSesion = isset($_SESSION['id_medico']) ? $_SESSION['id_medico'] : null;
        
        // consulta con parametro
        $sql = "SELECT * FROM controlpacientes WHERE IdPaciente = :id";
        
        // Si es médico, verificar que el paciente tenga citas con él
        if ($rolUsuario === 'medico' && $idMedicoSesion) {
            $sql = "SELECT p.* 
                    FROM controlpacientes p
                    INNER JOIN controlagenda a ON p.IdPaciente = a.IdPaciente
                    WHERE p.IdPaciente = :id AND a.IdMedico = :idMedico
                    LIMIT 1";
        }

        // preparamos la consulta
        $stmt = $pdo->prepare($sql);

        // vinculamos el parametro :id con el valor recibido por get
        $stmt->bindParam(':id', $_GET['id']);
        
        if ($rolUsuario === 'medico' && $idMedicoSesion) {
            $stmt->bindParam(':idMedico', $idMedicoSesion, PDO::PARAM_INT);
        }

        // ejecutamos la consulta
        $stmt->execute();
        
        // obtenemos un solo registro
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // indicamos que la salida sera json
        header('Content-Type: application/json');

        // imprimimos el json
        echo json_encode($paciente ?: ['error' => 'Paciente no encontrado o sin permisos']);

        // terminamos
        exit;
    }

    // aqui revisamos si es post y no existe el campo idpacienteEditar
    // eso significa que es un registro nuevo
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['idpacienteEditar'])) {
        
        // consulta insert para agregar paciente
        $sql = "INSERT INTO controlpacientes
                (IdPaciente, NombreCompleto, CURP, FechaNacimiento, Sexo, Telefono, CorreoElectronico, Direccion, ContactoEmergencia, TelefonoEmergencia, Alergias, AntecedentesMedicos, FechaRegistro, Estatus)
                VALUES 
                (:idpaciente, :nombrecompleto, :curp, :fechanacimiento, :sexo, :telefono, :correo, :direccion, :contactoemergencia, :telefonoemergencia, :alergias, :antecedentesmedicos, :fecharegistro, :estatus)";

        // preparamos la consulta
        $stmt = $pdo->prepare($sql);

        // vinculamos cada dato con lo que viene del formulario
        $stmt->bindParam(':idpaciente', $_POST['idpaciente']);
        $stmt->bindParam(':nombrecompleto', $_POST['nombrecompleto']);
        $stmt->bindParam(':curp', $_POST['curp']);
        $stmt->bindParam(':fechanacimiento', $_POST['fechanacimiento']);
        $stmt->bindParam(':sexo', $_POST['sexo']);
        $stmt->bindParam(':telefono', $_POST['telefono']);
        $stmt->bindParam(':correo', $_POST['correo']);
        $stmt->bindParam(':direccion', $_POST['direccion']);
        $stmt->bindParam(':contactoemergencia', $_POST['contactoemergencia']);
        $stmt->bindParam(':telefonoemergencia', $_POST['telefonoemergencia']);
        $stmt->bindParam(':alergias', $_POST['alergias']);
        $stmt->bindParam(':antecedentesmedicos', $_POST['antecedentesmedicos']);
        $stmt->bindParam(':fecharegistro', $_POST['fecharegistro']);
        $stmt->bindParam(':estatus', $_POST['estatus']);

        // insertamos el registro
        $stmt->execute();

        // mensaje si todo salio bien
        echo "OK - paciente guardado";
        exit;
    }

    // si es post y existe idpacienteEditar, entonces es un update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idpacienteEditar'])) {
        
        // Obtener rol e id del médico desde la sesión
        $rolUsuario = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
        $idMedicoSesion = isset($_SESSION['id_medico']) ? $_SESSION['id_medico'] : null;
        
        // Si es médico, verificar que el paciente tenga citas con él antes de editar
        if ($rolUsuario === 'medico' && $idMedicoSesion) {
            $sqlVerificar = "SELECT COUNT(*) as total 
                           FROM controlagenda 
                           WHERE IdPaciente = :idPaciente AND IdMedico = :idMedico";
            $stmtVerificar = $pdo->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':idPaciente', $_POST['idpacienteEditar']);
            $stmtVerificar->bindParam(':idMedico', $idMedicoSesion, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total'] == 0) {
                echo "Error: No tienes permisos para editar este paciente";
                exit;
            }
        }
        
        // consulta update
        $sql = "UPDATE controlpacientes SET
                NombreCompleto = :nombrecompleto,
                CURP = :curp,
                FechaNacimiento = :fechanacimiento,
                Sexo = :sexo,
                Telefono = :telefono,
                CorreoElectronico = :correo,
                Direccion = :direccion,
                ContactoEmergencia = :contactoemergencia,
                TelefonoEmergencia = :telefonoemergencia,
                Alergias = :alergias,
                AntecedentesMedicos = :antecedentesmedicos,
                FechaRegistro = :fecharegistro,
                Estatus = :estatus
                WHERE IdPaciente = :idpaciente";

        // preparamos la consulta
        $stmt = $pdo->prepare($sql);

        // vinculamos los parametros
        $stmt->bindParam(':idpaciente', $_POST['idpacienteEditar']);
        $stmt->bindParam(':nombrecompleto', $_POST['nombrecompleto']);
        $stmt->bindParam(':curp', $_POST['curp']);
        $stmt->bindParam(':fechanacimiento', $_POST['fechanacimiento']);
        $stmt->bindParam(':sexo', $_POST['sexo']);
        $stmt->bindParam(':telefono', $_POST['telefono']);
        $stmt->bindParam(':correo', $_POST['correo']);
        $stmt->bindParam(':direccion', $_POST['direccion']);
        $stmt->bindParam(':contactoemergencia', $_POST['contactoemergencia']);
        $stmt->bindParam(':telefonoemergencia', $_POST['telefonoemergencia']);
        $stmt->bindParam(':alergias', $_POST['alergias']);
        $stmt->bindParam(':antecedentesmedicos', $_POST['antecedentesmedicos']);
        $stmt->bindParam(':fecharegistro', $_POST['fecharegistro']);
        $stmt->bindParam(':estatus', $_POST['estatus']);

        // ejecutamos el update
        $stmt->execute();

        // mensaje si todo ok
        echo "OK - paciente actualizado";
        exit;
    }

    // aqui revisamos si se mando un get con accion eliminar
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        
        // Obtener rol e id del médico desde la sesión
        $rolUsuario = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';
        $idMedicoSesion = isset($_SESSION['id_medico']) ? $_SESSION['id_medico'] : null;
        
        // Si es médico, verificar que el paciente tenga citas con él antes de eliminar
        if ($rolUsuario === 'medico' && $idMedicoSesion) {
            $sqlVerificar = "SELECT COUNT(*) as total 
                           FROM controlagenda 
                           WHERE IdPaciente = :id AND IdMedico = :idMedico";
            $stmtVerificar = $pdo->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':id', $_GET['id']);
            $stmtVerificar->bindParam(':idMedico', $idMedicoSesion, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total'] == 0) {
                echo "Error: No tienes permisos para eliminar este paciente";
                exit;
            }
        }
        
        // consulta para borrar
        $sql = "DELETE FROM controlpacientes WHERE IdPaciente = :id";

        // preparamos la consulta
        $stmt = $pdo->prepare($sql);

        // vinculamos el id recibido
        $stmt->bindParam(':id', $_GET['id']);

        // ejecutamos el delete
        $stmt->execute();

        // mensaje de exito
        echo "OK - paciente eliminado";
        exit;
    }

// si ocurre un error en cualquier parte del try
} catch (PDOException $e) {

    // mostramos el error
    echo "Error: " . $e->getMessage();
}
?>