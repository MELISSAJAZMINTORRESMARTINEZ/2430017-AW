<?php require "config.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="estilos.css">
    <meta charset="UTF-8">
    <title>CRUD Simple</title>
</head>
<body>

<h2>Agregar Usuario</h2>

<form id="formCrear">
    Nombre: <input type="text" name="nombre" id="nombre" required>
    Email: <input type="email" name="email" id="email" required>
    <button type="submit">Crear</button>
</form>

<hr>

<h2>Lista de Usuarios</h2>
<div id="lista"></div>

<script src="script.js"></script>
<script>
cargarUsuarios();
</script>

</body>
</html>