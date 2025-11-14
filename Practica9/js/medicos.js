document.addEventListener("DOMContentLoaded", function () {
    cargarMedicos();

    const form = document.querySelector("#formMedicos");
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        guardarMedico(new FormData(form));
    });
});


function cargarMedicos() {
    fetch("php/medicos.php?accion=lista")
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector("#tablaPacientes tbody");
            tbody.innerHTML = "";

            data.forEach(m => {
                const fila = `
                <tr>
                    <td>${m.IdMedico}</td>
                    <td>${m.NombreCompleto}</td>
                    <td>${m.CedulaProfesional}</td>
                    <td>${m.NombreEspecialidad ?? "Sin dato"}</td>
                    <td>${m.Telefono}</td>
                    <td>${m.CorreoElectronico}</td>
                    <td>${m.HorarioAtencion}</td>
                    <td>${m.FechaIngreso}</td>
                    <td>${m.Estatus}</td>
                    <td>
                        <button class="btn btn-warning btn-sm me-1">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += fila;
            });
        })
        .catch(err => console.error("Error cargando médicos:", err));
}


function guardarMedico(formData) {
    fetch("php/medicos.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {
            if (respuesta.includes("OK")) {

                Swal.fire({
                    icon: "success",
                    title: "Médico guardado",
                    timer: 1800,
                    showConfirmButton: false
                });

                document.querySelector("#formMedicos").reset();

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalMedico"));
                modal.hide();

                cargarMedicos();

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

