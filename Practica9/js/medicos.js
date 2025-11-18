document.addEventListener("DOMContentLoaded", function () { // aqui espero a que la pagina cargue
    cargarMedicos(); // aqui apenas carga la pagina, muestro la lista de medicos

    const form = document.querySelector("#formMedicos"); // aqui agarro el formulario donde escribo los datos
    form.addEventListener("submit", function (e) { // aqui reviso cuando le doy guardar
        e.preventDefault(); // aqui evito que la pagina se recargue sola
        guardarMedico(new FormData(form)); // aqui mando los datos para guardarlos
    });

    // aqui dejo el formulario limpio cuando cierro el modal
    document.getElementById('modalMedico').addEventListener('hidden.bs.modal', function () {
        document.querySelector("#formMedicos").reset(); // limpio todo el formulario
        document.getElementById('modalMedicoLabel').innerHTML = '<i class="fa-solid fa-user-md me-2"></i>agregar medico'; // pongo el titulo normal
        
        // aqui quito el campo escondido si estaba editando antes
        const inputEditar = document.querySelector('input[name="idMedicoEditar"]');
        if (inputEditar) inputEditar.remove(); // lo borro
        
        document.getElementById('idMedico').disabled = false; // aqui vuelvo a activar el campo id
    });
});


function cargarMedicos() { // aqui cargo todos los medicos que estan guardados
    fetch("php/medicos.php?accion=lista") // pido la lista al archivo php
        .then(response => response.json()) // convierto lo que manda php a formato entendible
        .then(data => {
            const tbody = document.querySelector("#tablaMedicos tbody"); // aqui agarro el cuerpo de la tabla
            tbody.innerHTML = ""; // la dejo vacia para volver a llenarla

            if (data.length === 0) { // si no hay medicos registrados
                tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">no hay medicos registrados</td></tr>';
                return;
            }

            data.forEach(m => { // aqui recorro cada medico uno por uno
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
                tbody.innerHTML += fila; // aqui voy agregando cada medico a la tabla
            });
        })
        .catch(err => {
            console.error("error cargando medicos:", err); // si algo falla 
            Swal.fire({
                icon: "error",
                title: "error al cargar",
                text: "no se pudieron cargar los medicos"
            });
        });
}


function guardarMedico(formData) { // aqui guardo o actualizo un medico
    fetch("php/medicos.php", {
        method: "POST", // lo envio como post
        body: formData // mando todo lo que escribi en el formulario
    })
        .then(response => response.text()) // convierto lo que responde php a texto
        .then(respuesta => {
            if (respuesta.includes("OK")) { // si todo salio bien
                Swal.fire({
                    icon: "success",
                    title: respuesta.includes("actualizado") ? "medico actualizado" : "medico guardado",
                    timer: 1800,
                    showConfirmButton: false
                });

                document.querySelector("#formMedicos").reset(); // limpio el formulario

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalMedico")); // agarro el modal
                modal.hide(); // lo cierro

                cargarMedicos(); // vuelvo a cargar la tabla
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


function editarMedico(id) { // aqui cuando quiero editar un medico
    fetch(`php/medicos.php?accion=obtener&id=${id}`) // aqui pido los datos de el medico
        .then(response => response.json()) // paso lo que me mando php a json
        .then(medico => {

            document.getElementById('modalMedicoLabel').innerHTML = '<i class="fa-solid fa-user-edit me-2"></i>editar medico'; // cambio titulo
            
            // aqui lleno todos los campos con los datos que ya tenia
            document.getElementById('idMedico').value = medico.IdMedico;
            document.getElementById('idMedico').disabled = true; // no dejo cambiar el id
            document.getElementById('nombreCompleto').value = medico.NombreCompleto;
            document.getElementById('cedulaProfesional').value = medico.CedulaProfesional;
            document.getElementById('especialidad').value = medico.EspecialidadId;
            document.getElementById('telefono').value = medico.Telefono;
            document.getElementById('correo').value = medico.CorreoElectronico;
            document.getElementById('horario').value = medico.HorarioAtencion;
            document.getElementById('fechaIngreso').value = medico.FechaIngreso;
            document.getElementById('estatus').value = medico.Estatus;
            
            // aqui pongo un campo escondido para saber que estaba editando
            let inputEditar = document.querySelector('input[name="idMedicoEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idMedicoEditar';
                document.getElementById('formMedicos').appendChild(inputEditar);
            }
            inputEditar.value = medico.IdMedico; // aqui le pongo el id
            
            const modal = new bootstrap.Modal(document.getElementById('modalMedico')); // muestro el modal
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


function eliminarMedico(id, nombre) { // aqui cuando quiero borrar un medico
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
        if (result.isConfirmed) { // si confirme que si quiero borrar
            fetch(`php/medicos.php?accion=eliminar&id=${id}`) // aqui lo borro
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
                        cargarMedicos(); // cargo otra vez la tabla
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
