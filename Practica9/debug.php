<?php
// Mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<!DOCTYPE html><html><head><title>Diagnóstico</title></head><body>";
echo "<h1>Diagnóstico del Sistema</h1>";

// 1. Verificar versión de PHP
echo "<h2>1. Información de PHP</h2>";
echo "Versión de PHP: " . phpversion() . "<br>";
echo "Sistema Operativo: " . PHP_OS . "<br><br>";

// 2. Verificar archivos
echo "<h2>2. Verificación de Archivos</h2>";
$archivos = [
    'php/verificar_sesion.php',
    'css/dashboard.css',
    'images/New Patients.png',
    'images/otrogatito (2).png'
];

foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        echo "✓ <span style='color:green'>EXISTE:</span> $archivo<br>";
    } else {
        echo "✗ <span style='color:red'>NO EXISTE:</span> $archivo<br>";
    }
}
echo "<br>";

// 3. Verificar permisos
echo "<h2>3. Permisos de Archivos</h2>";
if (file_exists('php/verificar_sesion.php')) {
    $perms = fileperms('php/verificar_sesion.php');
    echo "Permisos de verificar_sesion.php: " . decoct($perms & 0777) . "<br>";
} else {
    echo "<span style='color:red'>No se puede verificar - archivo no existe</span><br>";
}
echo "<br>";

// 4. Probar inclusión del archivo
echo "<h2>4. Prueba de Inclusión</h2>";
try {
    if (file_exists('php/verificar_sesion.php')) {
        echo "Intentando incluir verificar_sesion.php...<br>";
        include 'php/verificar_sesion.php';
        echo "✓ <span style='color:green'>Archivo incluido correctamente</span><br>";
        
        // Verificar variables
        echo "<br><h3>Variables definidas:</h3>";
        echo "nombreUsuario: " . (isset($nombreUsuario) ? $nombreUsuario : '<span style="color:red">NO DEFINIDA</span>') . "<br>";
        echo "rolUsuario: " . (isset($rolUsuario) ? $rolUsuario : '<span style="color:red">NO DEFINIDA</span>') . "<br>";
        
        // Verificar función
        echo "<br><h3>Función tienePermiso:</h3>";
        if (function_exists('tienePermiso')) {
            echo "✓ <span style='color:green'>Función existe</span><br>";
        } else {
            echo "✗ <span style='color:red'>Función NO existe</span><br>";
        }
    } else {
        echo "✗ <span style='color:red'>Archivo no encontrado</span><br>";
    }
} catch (Exception $e) {
    echo "✗ <span style='color:red'>ERROR: " . $e->getMessage() . "</span><br>";
}
echo "<br>";

// 5. Verificar sesiones
echo "<h2>5. Verificación de Sesiones</h2>";
try {
    session_start();
    echo "✓ <span style='color:green'>Sesión iniciada correctamente</span><br>";
    echo "ID de sesión: " . session_id() . "<br>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
} catch (Exception $e) {
    echo "✗ <span style='color:red'>ERROR al iniciar sesión: " . $e->getMessage() . "</span><br>";
}
echo "<br>";

// 6. Verificar directorio actual
echo "<h2>6. Información del Directorio</h2>";
echo "Directorio actual: " . getcwd() . "<br>";
echo "Script ejecutándose: " . _FILE_ . "<br><br>";

// 7. Listar archivos en el directorio
echo "<h2>7. Archivos en el directorio raíz</h2>";
$files = scandir('.');
echo "<ul>";
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "<li>$file</li>";
    }
}
echo "</ul>";

// 8. Listar archivos en php/
echo "<h2>8. Archivos en php/</h2>";
if (is_dir('php')) {
    $files = scandir('php');
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<span style='color:red'>El directorio php/ no existe</span>";
}

echo "</body></html>";
?>