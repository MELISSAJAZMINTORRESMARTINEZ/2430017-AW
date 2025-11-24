document.addEventListener("DOMContentLoaded", function () { 
    // espero a que la página termine de cargar antes de ejecutar algo
    
    cargarMedicos(); // cargo la tabla de médicos al iniciar

    const form = document.querySelector("#formMedicos"); // obtengo el formulario
    form.addEventListener("submit", function (e) { 
        e.preventDefault(); // evito que la página se recargue
        guardarMedico(new FormData(form)); // envío los datos al PHP
    });

    // cuando se cierra el modal, limpio todo
    document.getElementById('modalMedico').addEventListener('hidden.bs.modal', function () {
        document.querySelector("#formMedicos").reset(); // limpiar formulario
        document.getElementById('modalMedicoLabel').innerHTML = 
            '<i class="fa-solid fa-user-md me-2"></i>agregar medico'; 
        
        // si había un input oculto de edición, lo elimino
        const inputEditar = document.querySelector('input[name="idMedicoEditar"]');
        if (inputEditar) inputEditar.remove();
        
        document.getElementById('idMedico').disabled = false; // vuelvo a activar el campo ID
    });
});


// carga todos los médicos en la tabla
function cargarMedicos() {
    fetch("php/medicos.php?accion=lista") // pido la lista al PHP
        .then(response => response.json()) // convierto respuesta en JSON
        .then(data => {
            const tbody = document.querySelector("#tablaMedicos tbody"); 
            tbody.innerHTML = ""; // limpio la tabla

            if (data.length === 0) { 
                tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">no hay medicos registrados</td></tr>';
                return;
            }

            data.forEach(m => { // recorro cada médico
                const fila = `
                <tr>
                    <td>${m.IdMedico}</td>
                    <td>${m.NombreCompleto}</td>
                    <td>${m.CedulaProfesional}</td>
                    <td>${m.NombreEspecialidad ?? "sin especialidad"}</td>
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
                tbody.innerHTML += fila; // agrego fila
            });
        })
        .catch(err => {
            console.error("error cargando medicos:", err);
            Swal.fire({
                icon: "error",
                title: "error al cargar",
                text: "no se pudieron cargar los medicos"
            });
        });
}


// guardar o actualizar médico
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
                    title: respuesta.includes("actualizado") ? "medico actualizado" : "medico guardado",
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
                    title: "error",
                    text: respuesta
                });
            }
        })
        .catch(error => {
            console.error("error:", error);
            Swal.fire({
                icon: "error",
                title: "error en la peticion",
                text: "no se pudo guardar el medico"
            });
        });
}


// cargar datos para editar
function editarMedico(id) {
    fetch(`php/medicos.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(medico => {

            document.getElementById('modalMedicoLabel').innerHTML =
                '<i class="fa-solid fa-user-edit me-2"></i>editar medico';

            // lleno los campos
            document.getElementById('idMedico').value = medico.IdMedico;
            document.getElementById('idMedico').disabled = true;
            document.getElementById('nombreCompleto').value = medico.NombreCompleto;
            document.getElementById('cedulaProfesional').value = medico.CedulaProfesional;
            document.getElementById('especialidad').value = medico.EspecialidadId;
            document.getElementById('telefono').value = medico.Telefono;
            document.getElementById('correo').value = medico.CorreoElectronico;
            document.getElementById('horario').value = medico.HorarioAtencion;
            document.getElementById('fechaIngreso').value = medico.FechaIngreso;
            document.getElementById('estatus').value = medico.Estatus;

            // input oculto para indicar que es edición
            let inputEditar = document.querySelector('input[name="idMedicoEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idMedicoEditar';
                document.getElementById('formMedicos').appendChild(inputEditar);
            }
            inputEditar.value = medico.IdMedico;

            const modal = new bootstrap.Modal(document.getElementById('modalMedico'));
            modal.show();
        })
        .catch(error => {
            console.error("error al cargar medico:", error);
            Swal.fire({
                icon: "error",
                title: "error",
                text: "no se pudo cargar la informacion del medico"
            });
        });
}


// eliminar médico
function eliminarMedico(id, nombre) {

    Swal.fire({
        title: 'estas seguro?',
        text: `se eliminara al medico: ${nombre}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'si, eliminar',
        cancelButtonText: 'cancelar'
    }).then((result) => {
        
        if (result.isConfirmed) {

            fetch(`php/medicos.php?accion=eliminar&id=${id}`)
                .then(response => response.text())
                .then(respuesta => {
                    
                    if (respuesta.includes("OK")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'eliminado',
                            text: 'el medico ha sido eliminado correctamente',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        cargarMedicos();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'error',
                            text: respuesta
                        });
                    }
                })
                .catch(error => {
                    console.error("error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'error',
                        text: 'no se pudo eliminar el medico'
                    });
                });
        }
    });
}
