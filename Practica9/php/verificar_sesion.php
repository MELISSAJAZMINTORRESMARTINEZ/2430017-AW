<?php
// si la sesion no esta iniciada se inicia
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// modo desarrollo esto solo es para pruebas y se debe quitar en produccion
// si no existe un usuario logueado se crean datos falsos para poder avanzar
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = 1;
    $_SESSION['nombre_usuario'] = 'Administrador';
    $_SESSION['rol'] = 'super admin';
}

// aqui se obtiene el nombre del usuario si existe
// si no existe pues se pone un valor por defecto
$nombreUsuario = isset($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : 'Usuario';

// lo mismo para el rol del usuario
$rolUsuario = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'invitado';

// si el rol es medico pero aun no tenemos su id medico entonces lo buscamos en la base de datos
if (strtolower($rolUsuario) === 'medico' && !isset($_SESSION['id_medico'])) {
    
    // aqui vienen los datos de conexion a la base de datos
    $host = "localhost";
    $port = "3306";
    $dbname = "clinica";
    $user = "admin";
    $pass = "ca99bc649c71b2383154550b34e52d0bb17fe7183054c554";
    
    try {
        // se arma el dsn que es como la ruta completa para conectar
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
        
        // se crea el objeto pdo para conectarse
        $pdo = new PDO($dsn, $user, $pass);
        
        // se activa el modo de errores para que muestre excepciones
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // consulta para obtener el id del medico segun su nombre
        // esto depende de como tengas tus tablas y relaciones
        $sql = "SELECT IdMedico FROM controlmedicos WHERE NombreCompleto = :nombre LIMIT 1";
        
        // se prepara la consulta
        $stmt = $pdo->prepare($sql);
        
        // se enlaza el parametro nombre para que no haya inyeccion
        $stmt->bindParam(':nombre', $nombreUsuario);
        
        // se ejecuta la consulta
        $stmt->execute();
        
        // se obtiene la fila resultante si existe
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // si encontro un medico con ese nombre entonces guarda el id en la sesion
        if ($resultado) {
            $_SESSION['id_medico'] = $resultado['IdMedico'];
        }
        
    } catch (PDOException $e) {
        // si pasa un error en la conexion o consulta simplemente no se hace nada
        // en produccion podrias guardar esto en un log
    }
}

// aqui se define una lista de permisos segun cada rol
// cada rol tiene una lista de secciones o funcionalidades permitidas
$permisos = array(
    'super admin' => array(
        'usuarios', 'pacientes', 'agenda', 'medicos', 'reportes',
        'expedientes', 'pagos', 'tarifas', 'bitacoras', 'especialidades'
    ),
    'medico' => array(
        'pacientes', 'agenda', 'expedientes', 'reportes'
    ),
    'secretaria' => array(
        'pacientes', 'agenda', 'pagos'
    ),
    'paciente' => array(
        'agenda'
    ),
    'invitado' => array()
);

// funcion para revisar si el usuario tiene el permiso solicitado
function tienePermiso($permiso) {
    // importamos las variables globales
    global $permisos, $rolUsuario;
    
    // pasamos el rol a minusculas para que coincida con los keys
    $rol = strtolower($rolUsuario);
    
    // si el rol no existe en la lista de permisos pues no tiene permisos
    if (!isset($permisos[$rol])) {
        return false;
    }
    
    // si el permiso esta dentro de la lista del rol entonces si tiene permiso
    return in_array($permiso, $permisos[$rol]);
}

// funcion para proteger paginas especificamente
// si no tiene permiso lo manda a otra pagina con un mensaje
function verificarAccesoAPagina($permisoRequerido) {
    if (!tienePermiso($permisoRequerido)) {
        header("Location: /2430017-AW/Practica9/dash.php?error=sin_permiso");
        exit();
    }
}
?>
