<?php require "config.php"; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca</title>
</head>

<body>

    <h2>Agregar Libro</h2>

    <form id="formCrear">
        Nombre del Libro: <input type="text" name="nombre" id="nombre" required><br>
        Autor: <input type="text" name="autor" id="autor" required><br>
        Categoría: <input type="text" name="categoria" id="categoria" required><br>
        Páginas: <input type="number" name="paginas" id="paginas" required min="1"><br>
        Editorial: <input type="text" name="editorial" id="editorial" required><br>
        <button type="submit">Crear</button>
    </form>

    <hr>

    <h2>Lista de Libros</h2>
    <div id="lista">Cargando...</div>

    <script src="script.js"></script>
    <script>
        cargarLibros();
    </script>

</body>

</html>