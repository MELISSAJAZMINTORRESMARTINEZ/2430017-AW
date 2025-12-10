// Event listener para el formulario
document.getElementById("formLibros").addEventListener("submit", function(e) {
    e.preventDefault();
    crear();
});

function crear() {
    let titulo = document.getElementById("titulo").value;
    let autor = document.getElementById("autor").value;
    let año = document.getElementById("año").value;
    let genero = document.getElementById("genero").value;
    let disponible = document.getElementById("disponible").value;

    fetch("crear.php", {
        method: "POST",
        body: new URLSearchParams({ titulo, autor, año, genero, disponible})
    })
    .then(() => {
        cargarLibros();
        // Limpiar el formulario
        document.getElementById("formLibros").reset();
    });
}

function cargarLibros() {
    fetch("leer.php")
        .then(res => res.text())
        .then(html => document.getElementById("lista").innerHTML = html);
}

function editar(id) {
    let titulo = prompt("Nuevo titulo:");
    let autor = prompt("Nuevo Autor:");
    let año = prompt("Nuevo Año:");
    let genero = prompt("Nuevo Genero:");
    let disponible = prompt("Nueva Disponibilidad:");


    if (titulo && autor && año && genero && disponible) {
        fetch("actualizar.php", {
            method: "POST",
            body: new URLSearchParams({ id, titulo, autor, año, genero, disponible })
        }).then(() => cargarLibros());
    }
}

function eliminarUsuario(id) {
    if (confirm("¿Eliminar usuario?")) {
        fetch("eliminar.php", {
            method: "POST",
            body: new URLSearchParams({ id })
        }).then(() => cargarLibros());
    }
}