document.addEventListener("DOMContentLoaded", function () { // aqui espero a que la pagina cargue
    cargarPacientes(); // aqui apenas carga la pagina, muestro la lista de pacientes

    const form = document.querySelector("#formPaciente"); // aqui agarro el formulario donde escribo los datos
    form.addEventListener("submit", function (e) { // aqui reviso cuando le doy guardar
        e.preventDefault(); // aqui evito que la pagina se recargue sola
        guardarPaciente(new FormData(form)); // aqui mando los datos para guardarlos
    });

    // aqui dejo el formulario limpio cuando cierro el modal
    document.getElementById('modalPaciente').addEventListener('hidden.bs.modal', function () {
        document.querySelector("#formPaciente").reset(); // limpio todo el formulario
        document.getElementById('modalpacientelabel').innerHTML = '<i class="fa-solid fa-user-plus me-2"></i>Agregar Paciente'; // pongo el titulo normal
        
        // aqui quito el campo escondido si estaba editando antes
        const inputEditar = document.querySelector('input[name="idpacienteEditar"]');
        if (inputEditar) inputEditar.remove(); // lo borro
        
        document.getElementById('idpaciente').disabled = false; // aqui vuelvo a activar el campo id
    });
});


function cargarPacientes() { // aqui cargo todos los pacientes que estan guardados
    fetch("php/paciente.php?accion=lista") // pido la lista al archivo php
        .then(response => response.json()) // convierto lo que manda php a formato entendible
        .then(data => {
            const tbody = document.querySelector("#tablaPacientes tbody"); // aqui agarro el cuerpo de la tabla
            tbody.innerHTML = ""; // la dejo vacia para volver a llenarla

            if (data.length === 0) { // si no hay pacientes registrados
                tbody.innerHTML = '<tr><td colspan="15" class="text-center text-muted">no hay pacientes registrados</td></tr>';
                return;
            }

            data.forEach(p => { // aqui recorro cada paciente uno por uno
                // aqui formateo las fechas para quitar la hora
                const fechaNacimiento = p.FechaNacimiento ? p.FechaNacimiento.split(' ')[0] : '';
                const fechaRegistro = p.FechaRegistro ? p.FechaRegistro.split(' ')[0] : '';
                
                const fila = `
                <tr>
                    <td>${p.IdPaciente}</td>
                    <td>${p.NombreCompleto}</td>
                    <td>${p.CURP}</td>
                    <td>${fechaNacimiento}</td>
                    <td>${p.Sexo}</td>
                    <td>${p.Telefono}</td>
                    <td>${p.CorreoElectronico}</td>
                    <td>${p.Direccion}</td>
                    <td>${p.ContactoEmergencia}</td>
                    <td>${p.TelefonoEmergencia}</td>
                    <td>${p.Alergias || 'ninguna'}</td>
                    <td>${p.AntecedentesMedicos || 'ninguno'}</td>
                    <td>${fechaRegistro}</td>
                    <td>
                        <span class="badge ${p.Estatus === 'Activo' ? 'bg-success' : 'bg-secondary'}">
                            ${p.Estatus}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm me-1" onclick="editarPaciente(${p.IdPaciente})">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarPaciente(${p.IdPaciente}, '${p.NombreCompleto}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += fila; // aqui voy agregando cada paciente a la tabla
            });
        })
}


function guardarPaciente(formData) { // aqui guardo o actualizo un paciente
    fetch("php/paciente.php", {
        method: "POST", // lo envio como post
        body: formData // mando todo lo que escribi en el formulario
    })
        .then(response => response.text()) // convierto lo que responde php a texto
        .then(respuesta => {
            if (respuesta.includes("OK")) { // si todo salio bien
                Swal.fire({
                    icon: "success",
                    title: respuesta.includes("actualizado") ? "paciente actualizado" : "paciente guardado",
                    timer: 1800,
                    showConfirmButton: false
                });

                document.querySelector("#formPaciente").reset(); // limpio el formulario

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalPaciente")); // agarro el modal
                modal.hide(); // lo cierro

                cargarPacientes(); // vuelvo a cargar la tabla
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
                text: "no se pudo guardar el paciente"
            });
        });
}


function editarPaciente(id) { // aqui cuando quiero editar un paciente
    fetch(`php/paciente.php?accion=obtener&id=${id}`) // aqui pido los datos del paciente
        .then(response => response.json()) // paso lo que me mando php a json
        .then(paciente => {

            document.getElementById('modalpacientelabel').innerHTML = '<i class="fa-solid fa-user-edit me-2"></i>editar paciente'; // cambio titulo
            
            // aqui lleno todos los campos con los datos que ya tenia
            document.getElementById('idpaciente').value = paciente.IdPaciente;
            document.getElementById('idpaciente').disabled = true; // no dejo cambiar el id
            document.getElementById('nombrecompleto').value = paciente.NombreCompleto;
            document.getElementById('curp').value = paciente.CURP;
            document.getElementById('fechanacimiento').value = paciente.FechaNacimiento;
            document.getElementById('sexo').value = paciente.Sexo;
            document.getElementById('telefono').value = paciente.Telefono;
            document.getElementById('correo').value = paciente.CorreoElectronico;
            document.getElementById('direccion').value = paciente.Direccion;
            document.getElementById('contactoemergencia').value = paciente.ContactoEmergencia;
            document.getElementById('telefonoemergencia').value = paciente.TelefonoEmergencia;
            document.getElementById('alergias').value = paciente.Alergias;
            document.getElementById('antecedentesmedicos').value = paciente.AntecedentesMedicos;
            document.getElementById('fecharegistro').value = paciente.FechaRegistro;
            document.getElementById('estatus').value = paciente.Estatus;
            
            // aqui pongo un campo escondido para saber que estaba editando
            let inputEditar = document.querySelector('input[name="idpacienteEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idpacienteEditar';
                document.getElementById('formPaciente').appendChild(inputEditar);
            }
            inputEditar.value = paciente.IdPaciente; // aqui le pongo el id
            
            const modal = new bootstrap.Modal(document.getElementById('modalPaciente')); // muestro el modal
            modal.show();
        })
        .catch(error => {
            console.error("error al cargar paciente:", error);
            Swal.fire({
                icon: "error",
                title: "error",
                text: "no se pudo cargar la informacion del paciente"
            });
        });
}


function eliminarPaciente(id, nombre) { // aqui cuando quiero borrar un paciente
    Swal.fire({
        title: 'estas seguro?',
        text: `se eliminara al paciente: ${nombre}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'si, eliminar',
        cancelButtonText: 'cancelar'
    }).then((result) => {
        if (result.isConfirmed) { // si confirme que si quiero borrar
            fetch(`php/paciente.php?accion=eliminar&id=${id}`) // aqui lo borro
                .then(response => response.text())
                .then(respuesta => {
                    if (respuesta.includes("OK")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'eliminado',
                            text: 'el paciente ha sido eliminado correctamente',
                            timer: 1800,
                            showConfirmButton: false
                        });
                        cargarPacientes(); // cargo otra vez la tabla
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
                        text: 'no se pudo eliminar el paciente'
                    });
                });
        }
    });
}