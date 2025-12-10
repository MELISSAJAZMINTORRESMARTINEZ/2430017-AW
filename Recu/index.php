<?php require "config.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="estilos.css">
    <meta charset="UTF-8">
    <title>Biblioteca</title>
</head>

<body>

    <h2>Agregar Libro</h2>

    <form id="formCrear">
        Nombre del Libro: <input type="text" name="nombre" id="nombre" required>
        Autor: <input type="text" name="autor" id="autor" required>
        Categeoria: <input type="text" name="categoria" id="categoria" required>
        Paginas : <input type="number" name="paginas" id="paginas" required>
        Editorial : <input type="text" name="editorial" id="editorial" required>
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