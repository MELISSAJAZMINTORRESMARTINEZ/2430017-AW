document.addEventListener("DOMContentLoaded", function () { 
    // espero a que la página termine de cargar antes de ejecutar algo
    
    cargarExpedientes(); // cargo la tabla de expedientes al iniciar

    const form = document.querySelector("#modalExpediente form"); // obtengo el formulario
    form.addEventListener("submit", function (e) { 
        e.preventDefault(); // evito que la página se recargue
        
        // validaciones antes de guardar
        if (!validarFormulario()) {
            return;
        }
        
        guardarExpediente(new FormData(form)); // envío los datos al PHP
    });

    // cuando se cierra el modal, limpio todo
    document.getElementById('modalExpediente').addEventListener('hidden.bs.modal', function () {
        document.querySelector("#modalExpediente form").reset(); // limpiar formulario
        document.getElementById('modalExpedienteLabel').innerHTML = 
            '<i class="fa-solid fa-user-plus me-2"></i>Agregar Expediente'; 
        
        // si había un input oculto de edición, lo elimino
        const inputEditar = document.querySelector('input[name="idExpedienteEditar"]');
        if (inputEditar) inputEditar.remove();
        
        document.getElementById('idExpediente').disabled = false; // vuelvo a activar el campo ID
    });

    // establecer fecha mínima en los campos de fecha (hoy)
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('proximaCita').setAttribute('min', hoy);
});


// validar formulario antes de enviar
function validarFormulario() {
    const fechaConsulta = document.getElementById('fechaConsulta').value;
    const proximaCita = document.getElementById('proximaCita').value;
    const hoy = new Date().toISOString().split('T')[0];
    
    // validar que la próxima cita no sea en el pasado
    if (proximaCita && proximaCita < hoy) {
        Swal.fire({
            icon: "error",
            title: "fecha invalida",
            text: "la proxima cita no puede ser en el pasado"
        });
        return false;
    }
    
    // validar que la próxima cita sea posterior a la fecha de consulta
    if (fechaConsulta && proximaCita && proximaCita < fechaConsulta) {
        Swal.fire({
            icon: "error",
            title: "fecha invalida",
            text: "la proxima cita debe ser posterior a la fecha de consulta"
        });
        return false;
    }
    
    // validar campos requeridos
    const idPaciente = document.getElementById('idPaciente').value;
    const idMedico = document.getElementById('idMedico').value;
    
    if (!idPaciente || !idMedico) {
        Swal.fire({
            icon: "error",
            title: "campos requeridos",
            text: "el id del paciente y medico son obligatorios"
        });
        return false;
    }
    
    return true;
}


// carga todos los expedientes en la tabla
function cargarExpedientes() {
    fetch("php/expediente.php?accion=lista") // pido la lista al PHP
        .then(response => response.json()) // convierto respuesta en JSON
        .then(data => {
            const tbody = document.querySelector("#tablaExpediente tbody"); 
            tbody.innerHTML = ""; // limpio la tabla

            if (data.length === 0) { 
                tbody.innerHTML = '<tr><td colspan="11" class="text-center text-muted">no hay expedientes registrados</td></tr>';
                return;
            }

            data.forEach(e => { // recorro cada expediente
                const fila = `
                <tr>
                    <td>${e.IdExpediente}</td>
                    <td>${e.IdPaciente}</td>
                    <td>${e.IdMedico}</td>
                    <td>${e.FechaConsulta || 'N/A'}</td>
                    <td>${e.Sintomas || 'N/A'}</td>
                    <td>${e.Diagnostico || 'N/A'}</td>
                    <td>${e.Tratamiento || 'N/A'}</td>
                    <td>${e.RecetaMedica || 'N/A'}</td>
                    <td>${e.NotasAdicionales || 'N/A'}</td>
                    <td>${e.ProximaCita || 'N/A'}</td>
                    <td>
                        <button class="btn btn-warning btn-sm me-1" onclick="editarExpediente(${e.IdExpediente})">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarExpediente(${e.IdExpediente})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += fila; // agrego fila
            });
        })
}


// guardar o actualizar expediente
function guardarExpediente(formData) {
    fetch("php/expediente.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {

            if (respuesta.includes("OK")) {
                Swal.fire({
                    icon: "success",
                    title: respuesta.includes("actualizado") ? "expediente actualizado" : "expediente guardado",
                    timer: 1800,
                    showConfirmButton: false
                });

                document.querySelector("#modalExpediente form").reset(); 

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalExpediente"));
                modal.hide();

                cargarExpedientes(); 
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
                text: "no se pudo guardar el expediente"
            });
        });
}


// cargar datos para editar
function editarExpediente(id) {
    fetch(`php/expediente.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(expediente => {

            document.getElementById('modalExpedienteLabel').innerHTML =
                '<i class="fa-solid fa-user-edit me-2"></i>editar expediente';

            // lleno los campos
            document.getElementById('idExpediente').value = expediente.IdExpediente;
            document.getElementById('idExpediente').disabled = true;
            document.getElementById('idPaciente').value = expediente.IdPaciente;
            document.getElementById('idMedico').value = expediente.IdMedico;
            document.getElementById('fechaConsulta').value = expediente.FechaConsulta;
            document.getElementById('sintomas').value = expediente.Sintomas;
            document.getElementById('diagnostico').value = expediente.Diagnostico;
            document.getElementById('tratamiento').value = expediente.Tratamiento;
            document.getElementById('recetaMedica').value = expediente.RecetaMedica;
            document.getElementById('notasAdicionales').value = expediente.NotasAdicionales;
            document.getElementById('proximaCita').value = expediente.ProximaCita;

            // input oculto para indicar que es edición
            let inputEditar = document.querySelector('input[name="idExpedienteEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idExpedienteEditar';
                document.querySelector('#modalExpediente form').appendChild(inputEditar);
            }
            inputEditar.value = expediente.IdExpediente;

            const modal = new bootstrap.Modal(document.getElementById('modalExpediente'));
            modal.show();
        })
        .catch(error => {
            console.error("error al cargar expediente:", error);
            Swal.fire({
                icon: "error",
                title: "error",
                text: "no se pudo cargar la informacion del expediente"
            });
        });
}


// eliminar expediente
function eliminarExpediente(id) {

    Swal.fire({
        title: 'estas seguro?',
        text: `se eliminara el expediente #${id}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'si, eliminar',
        cancelButtonText: 'cancelar'
    }).then((result) => {
        
        if (result.isConfirmed) {

            fetch(`php/expediente.php?accion=eliminar&id=${id}`)
                .then(response => response.text())
                .then(respuesta => {
                    
                    if (respuesta.includes("OK")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'eliminado',
                            text: 'el expediente ha sido eliminado correctamente',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        cargarExpedientes();
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
                        text: 'no se pudo eliminar el expediente'
                    });
                });
        }
    });
}