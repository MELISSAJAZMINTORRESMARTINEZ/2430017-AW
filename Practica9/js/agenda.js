// variable global del calendario
let calendar;

// Variable para almacenar el rol del usuario (se pasa desde PHP)
let rolUsuario = '';

// inicializar cuando carga la página
document.addEventListener('DOMContentLoaded', function () {

    // Obtener el rol del usuario desde el atributo data del body o de una variable global
    const bodyElement = document.querySelector('body');
    rolUsuario = bodyElement.getAttribute('data-rol') || 'invitado';
    
    // configurar el calendario
    const calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        // cargar eventos desde la base de datos
        events: function(info, successCallback, failureCallback) {
            fetch('php/agenda.php?accion=lista')
                .then(response => response.json())
                .then(data => {
                    const eventos = data.map(cita => ({
                        id: cita.IdCita,
                        title: `${cita.NombrePaciente || 'Paciente'} - ${cita.NombreMedico || 'Médico'}`,
                        start: cita.FechaCita,
                        backgroundColor: cita.EstadoCita === 'Programada' ? '#28a745' : 
                                       cita.EstadoCita === 'Atendida' ? '#007bff' : '#dc3545',
                        extendedProps: {
                            paciente: cita.NombrePaciente,
                            medico: cita.NombreMedico,
                            motivo: cita.MotivoConsulta,
                            estatus: cita.EstadoCita
                        }
                    }));
                    successCallback(eventos);
                })
                .catch(error => {
                    console.error('Error cargando eventos:', error);
                    failureCallback(error);
                });
        },

        // click en un evento para ver detalles
        eventClick: function(info) {
            // Determinar qué botones mostrar según el rol
            const esPaciente = rolUsuario.toLowerCase() === 'paciente';
            
            Swal.fire({
                title: info.event.title,
                html: `
                    <p><strong>Paciente:</strong> ${info.event.extendedProps.paciente}</p>
                    <p><strong>Médico:</strong> ${info.event.extendedProps.medico}</p>
                    <p><strong>Motivo:</strong> ${info.event.extendedProps.motivo || 'N/A'}</p>
                    <p><strong>Estado:</strong> <span class="badge bg-info">${info.event.extendedProps.estatus}</span></p>
                    <p><strong>Fecha:</strong> ${info.event.start.toLocaleDateString('es-MX')}</p>
                `,
                showCancelButton: true,
                showDenyButton: !esPaciente, // Pacientes no pueden eliminar
                confirmButtonText: 'Editar',
                denyButtonText: 'Eliminar',
                cancelButtonText: 'Cerrar'
            }).then((result) => {
                if (result.isConfirmed) {
                    editarCita(info.event.id);
                } else if (result.isDenied && !esPaciente) {
                    eliminarCita(info.event.id, info.event.extendedProps.paciente);
                }
            });
        }
    });

    calendar.render();

    // cargar lista de próximas citas
    cargarProximasCitas();

    // configurar el formulario del modal
    const form = document.querySelector("#modalAgenda form");
    if (form) {
        // quitar el action
        form.removeAttribute('action');
        
        form.addEventListener("submit", function (e) { 
            e.preventDefault(); 
            if (validarFormulario()) {
                guardarCita(new FormData(form));
            }
        });
    }

    // Si es paciente, ocultar el campo IdPaciente y deshabilitarlo
    const esPaciente = rolUsuario.toLowerCase() === 'paciente';
    if (esPaciente) {
        const idPacienteDiv = document.getElementById('idPaciente').closest('.mb-3');
        if (idPacienteDiv) {
            idPacienteDiv.style.display = 'none';
        }
    }

    // establecer fecha mínima de hoy
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('fechaCita').setAttribute('min', hoy);
    document.getElementById('fechaCita').value = hoy;

    // cuando se cierra el modal, limpio todo
    document.getElementById('modalAgenda').addEventListener('hidden.bs.modal', function () {
        form.reset();
        document.getElementById('modalAgendaLabel').innerHTML = 
            '<i class="fa-solid fa-calendar-plus me-2"></i>agregar agenda'; 
        
        document.querySelectorAll('.text-danger, .text-success').forEach(el => el.remove());
        
        const inputEditar = document.querySelector('input[name="idCitaEditar"]');
        if (inputEditar) inputEditar.remove();
        
        if (!esPaciente) {
            document.getElementById('idCita').disabled = false;
        }
        document.getElementById('fechaCita').value = hoy;
    });
});


// validar formulario
function validarFormulario() {
    const esPaciente = rolUsuario.toLowerCase() === 'paciente';
    const idPaciente = document.getElementById('idPaciente').value;
    const idMedico = document.getElementById('idMedico').value;
    const fechaCita = document.getElementById('fechaCita').value;
    
    // Si no es paciente, validar que tenga paciente
    if (!esPaciente && !idPaciente) {
        Swal.fire({
            icon: 'warning',
            title: 'campos requeridos',
            text: 'debe ingresar un paciente válido'
        });
        return false;
    }
    
    if (!idMedico) {
        Swal.fire({
            icon: 'warning',
            title: 'campos requeridos',
            text: 'debe ingresar un médico válido'
        });
        return false;
    }

    // validar que la fecha no sea pasada
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const fechaSeleccionada = new Date(fechaCita + 'T00:00:00');
    
    if (fechaSeleccionada < hoy) {
        Swal.fire({
            icon: 'error',
            title: 'fecha inválida',
            text: 'no se pueden agendar citas en fechas pasadas'
        });
        return false;
    }

    return true;
}


// validar si existe el paciente
function validarPaciente(id) {
    if (!id) return;
    
    fetch(`php/agenda.php?accion=validarPaciente&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const input = document.getElementById('idPaciente');
            const mensajePrevio = input.parentElement.querySelector('.validacion-msg');
            if (mensajePrevio) mensajePrevio.remove();
            
            const mensaje = document.createElement('small');
            mensaje.className = 'validacion-msg';
            
            if (data.error) {
                mensaje.className += ' text-danger';
                mensaje.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i>Paciente no encontrado';
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            } else {
                mensaje.className += ' text-success';
                mensaje.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i>Paciente: ${data.NombreCompleto}`;
                input.classList.add('is-valid');
                input.classList.remove('is-invalid');
            }
            
            input.parentElement.appendChild(mensaje);
        });
}


// validar si existe el médico
function validarMedico(id) {
    if (!id) return;
    
    fetch(`php/agenda.php?accion=validarMedico&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const input = document.getElementById('idMedico');
            const mensajePrevio = input.parentElement.querySelector('.validacion-msg');
            if (mensajePrevio) mensajePrevio.remove();
            
            const mensaje = document.createElement('small');
            mensaje.className = 'validacion-msg';
            
            if (data.error) {
                mensaje.className += ' text-danger';
                mensaje.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i>Médico no encontrado';
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            } else {
                mensaje.className += ' text-success';
                mensaje.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i>Médico: ${data.NombreCompleto}`;
                input.classList.add('is-valid');
                input.classList.remove('is-invalid');
            }
            
            input.parentElement.appendChild(mensaje);
        });
}


// cargar próximas citas en la lista lateral
function cargarProximasCitas() {
    fetch("php/agenda.php?accion=lista")
        .then(response => response.json())
        .then(data => {
            const lista = document.getElementById('upcomingList');
            if (!lista) return; // Si no existe el elemento, salir
            
            lista.innerHTML = "";

            // filtrar solo citas futuras y programadas
            const proximasCitas = data
                .filter(c => new Date(c.FechaCita) >= new Date() && c.EstadoCita === 'Programada')
                .sort((a, b) => new Date(a.FechaCita) - new Date(b.FechaCita))
                .slice(0, 5);

            if (proximasCitas.length === 0) {
                lista.innerHTML = '<li class="text-muted">No hay próximas citas</li>';
                return;
            }

            proximasCitas.forEach(c => {
                const fecha = new Date(c.FechaCita).toLocaleDateString('es-MX');
                const item = document.createElement('li');
                item.className = 'mb-2 pb-2 border-bottom';
                item.innerHTML = `
                    <small class="text-muted">${fecha}</small><br>
                    <strong>${c.NombrePaciente || 'Paciente'}</strong><br>
                    <small>${c.NombreMedico || 'Médico'}</small>
                `;
                lista.appendChild(item);
            });
        });
}


// guardar o actualizar cita
function guardarCita(formData) {
    fetch("php/agenda.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {
            if (respuesta.includes("OK")) {
                Swal.fire({
                    icon: "success",
                    title: respuesta.includes("actualizada") ? "cita actualizada" : "cita guardada",
                    timer: 1800,
                    showConfirmButton: false
                });

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalAgenda"));
                if (modal) modal.hide();

                // recargar calendario y lista
                calendar.refetchEvents();
                cargarProximasCitas();
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
                text: "no se pudo guardar la cita"
            });
        });
}


// editar cita
function editarCita(id) {
    const esPaciente = rolUsuario.toLowerCase() === 'paciente';
    
    fetch(`php/agenda.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(cita => {
            if (cita.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: cita.error
                });
                return;
            }
            
            document.getElementById('modalAgendaLabel').innerHTML =
                '<i class="fa-solid fa-edit me-2"></i>editar cita';

            if (!esPaciente) {
                document.getElementById('idCita').value = cita.IdCita;
                document.getElementById('idCita').disabled = true;
            }
            
            document.getElementById('idPaciente').value = cita.IdPaciente;
            document.getElementById('idMedico').value = cita.IdMedico;
            document.getElementById('fechaCita').value = cita.FechaCita;
            document.getElementById('motivoConsulta').value = cita.MotivoConsulta || '';
            document.getElementById('estatus').value = cita.EstadoCita;
            document.getElementById('observaciones').value = cita.Observaciones || '';
            
            if (document.getElementById('fechaRegistro')) {
                document.getElementById('fechaRegistro').value = cita.FechaRegistro || '';
            }

            // Si es paciente, deshabilitar el campo de paciente
            if (esPaciente) {
                document.getElementById('idPaciente').disabled = true;
            }

            let inputEditar = document.querySelector('input[name="idCitaEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idCitaEditar';
                document.querySelector('#modalAgenda form').appendChild(inputEditar);
            }
            inputEditar.value = cita.IdCita;

            const modal = new bootstrap.Modal(document.getElementById('modalAgenda'));
            modal.show();
        });
}


// eliminar cita
function eliminarCita(id, paciente) {
    const esPaciente = rolUsuario.toLowerCase() === 'paciente';
    
    if (esPaciente) {
        Swal.fire({
            icon: 'warning',
            title: 'Acción no permitida',
            text: 'Los pacientes no pueden eliminar citas. Contacte con la recepción para cancelar.'
        });
        return;
    }
    
    Swal.fire({
        title: 'estas seguro?',
        text: `se eliminara la cita del paciente: ${paciente}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'si, eliminar',
        cancelButtonText: 'cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`php/agenda.php?accion=eliminar&id=${id}`)
                .then(response => response.text())
                .then(respuesta => {
                    if (respuesta.includes("OK")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'eliminada',
                            text: 'la cita ha sido eliminada correctamente',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        calendar.refetchEvents();
                        cargarProximasCitas();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'error',
                            text: respuesta
                        });
                    }
                });
        }
    });
}