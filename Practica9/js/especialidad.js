
document.addEventListener("DOMContentLoaded", function () {
    cargarEspecialidades();

    const form = document.querySelector("form");
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        guardarEspecialidad(new FormData(form));
    });
});


function cargarEspecialidades() {
    fetch("php/especialidades.php?accion=lista")
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector("#tablaPacientes tbody");
            tbody.innerHTML = "";

            data.forEach(esp => {
                const fila = `
                    <tr>
                        <td>${esp.IdEspecialidad}</td>
                        <td>${esp.NombreEspecialidad}</td>
                        <td>${esp.Descripcion}</td>
                    </tr>
                `;
                tbody.innerHTML += fila;
            });
        })
        .catch(err => {
            console.error("Error cargando especialidades:", err);
        });
}


function guardarEspecialidad(formData) {
    fetch("php/especialidades.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {

            if (respuesta.includes("OK")) {

                Swal.fire({
                    icon: "success",
                    title: "Especialidad guardada",
                    text: "Se agregó correctamente",
                    timer: 1800,
                    showConfirmButton: false
                });

                document.querySelector("form").reset();

                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById("modalEspecialidad"));
                modal.hide();

                cargarEspecialidades();

            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: respuesta
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: "error",
                title: "Error en la petición",
                text: error
            });
        });
}
