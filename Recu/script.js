// Event listener para el formulario
document.getElementById("formCrear").addEventListener("submit", function(e) {
    e.preventDefault();
    crear();
});

function crear() {
    let nombre = document.getElementById("nombre").value;
    let autor = document.getElementById("autor").value;
    let categoria = document.getElementById("categoria").value;
    let paginas = document.getElementById("paginas").value;
    let editorial = document.getElementById("editorial").value;

    fetch("crear_libro.php", {
        method: "POST",
        body: new URLSearchParams({ nombre, autor   , categoria, paginas, editorial  })
    })
    .then(() => {
        cargarLibros();
        document.getElementById("formCrear").reset();
    });
}

function cargarLibros() {
    fetch("leer.php")
        .then(res => res.text())
        .then(html => document.getElementById("lista").innerHTML = html);
}


function eliminarUsuario(id) {
    if (confirm("¿Eliminar usuario?")) {
        fetch("eliminar.php", {
            method: "POST",
            body: new URLSearchParams({ id })
        }).then(() => cargarUsuarios());
    }
}