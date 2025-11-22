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

    // validamos si llega una peticion get y si accion es lista
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'lista') {
        
        // consulta sql para obtener todos los pacientes
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
        
        // se ejecuta la consulta directamente porque no lleva parametros
        $stmt = $pdo->query($sql);

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
        
        // consulta con parametro
        $sql = "SELECT * FROM controlpacientes WHERE IdPaciente = :id";

        // preparamos la consulta
        $stmt = $pdo->prepare($sql);

        // vinculamos el parametro :id con el valor recibido por get
        $stmt->bindParam(':id', $_GET['id']);

        // ejecutamos la consulta
        $stmt->execute();
        
        // obtenemos un solo registro
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // indicamos que la salida sera json
        header('Content-Type: application/json');

        // imprimimos el json
        echo json_encode($paciente);

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