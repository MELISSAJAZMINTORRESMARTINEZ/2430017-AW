// Event listener para el formulario
document.getElementById("formCrear").addEventListener("submit", function(e) {
    e.preventDefault();
    crear();
});

function crear() {
    let nombre = document.getElementById("nombre").value;
    let email = document.getElementById("email").value;

    fetch("crear.php", {
        method: "POST",
        body: new URLSearchParams({ nombre, email })
    })
    .then(() => {
        cargarUsuarios();
        // Limpiar el formulario
        document.getElementById("formCrear").reset();
    });
}

function cargarUsuarios() {
    fetch("leer.php")
        .then(res => res.text())
        .then(html => document.getElementById("lista").innerHTML = html);
}

function editar(id) {
    let nombre = prompt("Nuevo nombre:");
    let email = prompt("Nuevo email:");

    if (nombre && email) {
        fetch("actualizar.php", {
            method: "POST",
            body: new URLSearchParams({ id, nombre, email })
        }).then(() => cargarUsuarios());
    }
}

function eliminarUsuario(id) {
    if (confirm("¿Eliminar usuario?")) {
        fetch("eliminar.php", {
            method: "POST",
            body: new URLSearchParams({ id })
        }).then(() => cargarUsuarios());
    }
}