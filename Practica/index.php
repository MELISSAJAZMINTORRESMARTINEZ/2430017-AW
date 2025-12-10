<?php require "config.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="estilos.css">
    <meta charset="UTF-8">
    <title>CRUD Biblioteca</title>
</head>
<body>

<h2>Agregar libro</h2>

<form id="formLibros">
    Titulo: <input type="text" name="titulo" id="nombre" required>
    Autor: <input type="text" name="autor" id="autor" required>
    año: <input type="date" name="año" id="año" required>
    Genero: <input type="text" name="genero" id="genero" required>
    <select name="disponible" id="disponible">
        <option value="disponible">Disponible</option>
        <option value="prestado">Prestado</option>
    </select>

    <button type="submit">Crear</button>
</form>

<hr>

<h2>Lista de Libros</h2>
<div id="lista"></div>

<script src="script.js"></script>
<script>
cargarLibros();
</script>

</body>
</html>