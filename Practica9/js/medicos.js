document.addEventListener("DOMContentLoaded", function () {
    cargarMedicos();

    const form = document.querySelector("#formMedicos");
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        guardarMedico(new FormData(form));
    });

    // Resetear formulario cuando se cierra el modal
    document.getElementById('modalMedico').addEventListener('hidden.bs.modal', function () {
        document.querySelector("#formMedicos").reset();
        document.getElementById('modalMedicoLabel').innerHTML = '<i class="fa-solid fa-user-md me-2"></i>Agregar Médico';
        
        // Remover campo oculto si existe
        const inputEditar = document.querySelector('input[name="idMedicoEditar"]');
        if (inputEditar) inputEditar.remove();
        
        // Habilitar campo ID
        document.getElementById('idMedico').disabled = false;
    });
});


function cargarMedicos() {
    fetch("php/medicos.php?accion=lista")
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector("#tablaMedicos tbody");
            tbody.innerHTML = "";

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">No hay médicos registrados</td></tr>';
                return;
            }

            data.forEach(m => {
                const fila = `
                <tr>
                    <td>${m.IdMedico}</td>
                    <td>${m.NombreCompleto}</td>
                    <td>${m.CedulaProfesional}</td>
                    <td>${m.NombreEspecialidad ?? "Sin especialidad"}</td>
                    <td>${m.Telefono}</td>
                    <td>${m.CorreoElectronico}</td>
                    <td>${m.HorarioAtencion}</td>
                    <td>${m.FechaIngreso}</td>
                    <td>
                        <span class="badge ${m.Estatus === 'Activo' ? 'bg-success' : 'bg-secondary'}">
                            ${m.Estatus}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm me-1" onclick="editarMedico(${m.IdMedico})">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarMedico(${m.IdMedico}, '${m.NombreCompleto}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += fila;
            });
        })
        .catch(err => {
            console.error("Error cargando médicos:", err);
            Swal.fire({
                icon: "error",
                title: "Error al cargar",
                text: "No se pudieron cargar los médicos"
            });
        });
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
                    title: respuesta.includes("actualizado") ? "Médico actualizado" : "Médico guardado",
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
            console.error("Error:", error);
            Swal.fire({
                icon: "error",
                title: "Error en la petición",
                text: "No se pudo guardar el médico"
            });
        });
}


function editarMedico(id) {
    fetch(`php/medicos.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(medico => {
            // Cambiar título del modal
            document.getElementById('modalMedicoLabel').innerHTML = '<i class="fa-solid fa-user-edit me-2"></i>Editar Médico';
            
            // Llenar el formulario
            document.getElementById('idMedico').value = medico.IdMedico;
            document.getElementById('idMedico').disabled = true; // No permitir cambiar el ID
            document.getElementById('nombreCompleto').value = medico.NombreCompleto;
            document.getElementById('cedulaProfesional').value = medico.CedulaProfesional;
            document.getElementById('especialidad').value = medico.EspecialidadId;
            document.getElementById('telefono').value = medico.Telefono;
            document.getElementById('correo').value = medico.CorreoElectronico;
            document.getElementById('horario').value = medico.HorarioAtencion;
            document.getElementById('fechaIngreso').value = medico.FechaIngreso;
            document.getElementById('estatus').value = medico.Estatus;
            
            // Agregar campo oculto para saber que es una edición
            let inputEditar = document.querySelector('input[name="idMedicoEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idMedicoEditar';
                document.getElementById('formMedicos').appendChild(inputEditar);
            }
            inputEditar.value = medico.IdMedico;
            
            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('modalMedico'));
            modal.show();
        })
        .catch(error => {
            console.error("Error al cargar médico:", error);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "No se pudo cargar la información del médico"
            });
        });
}


function eliminarMedico(id, nombre) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Se eliminará al médico: ${nombre}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`php/medicos.php?accion=eliminar&id=${id}`)
                .then(response => response.text())
                .then(respuesta => {
                    if (respuesta.includes("OK")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'El médico ha sido eliminado correctamente',
                            timer: 1800,
                            showConfirmButton: false
                        });
                        cargarMedicos();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: respuesta
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar el médico'
                    });
                });
        }
    });
}