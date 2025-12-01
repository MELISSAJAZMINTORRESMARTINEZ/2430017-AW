// variable global del calendario
let calendar;

// inicializar cuando carga la página
document.addEventListener('DOMContentLoaded', function () {

    // configurar el calendario
    const calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth', // vista inicial por mes
        locale: 'es',                // idioma español
        height: 'auto',              // altura automática del calendario

        // configuración del header (menú superior)
        headerToolbar: {
            left: 'prev,next today',                     // botones de navegación
            center: 'title',                             // título (mes actual)
            right: 'dayGridMonth,timeGridWeek,timeGridDay' // cambiar vista
        },

        // cargar eventos desde la base de datos
        events: function(info, successCallback, failureCallback) {
            // llamada al servidor para obtener citas
            fetch('php/agenda.php?accion=lista')
                .then(response => response.json()) // convertir la respuesta en JSON
                .then(data => {
                    // convertir cada cita en un evento del calendario
                    const eventos = data.map(cita => ({
                        id: cita.IdCita, // ID único de la cita
                        title: `${cita.NombrePaciente || 'Paciente'} - ${cita.NombreMedico || 'Médico'}`, // título que se muestra
                        start: cita.FechaCita, // fecha del evento

                        // color dependiendo del estado de la cita
                        backgroundColor: cita.EstadoCita === 'Programada' ? '#28a745' : 
                                       cita.EstadoCita === 'Atendida' ? '#007bff' : '#dc3545',

                        // datos adicionales que usaremos en el modal
                        extendedProps: {
                            paciente: cita.NombrePaciente,
                            medico: cita.NombreMedico,
                            motivo: cita.MotivoConsulta,
                            estatus: cita.EstadoCita
                        }
                    }));

                    // enviar eventos al calendario
                    successCallback(eventos);
                })
                .catch(error => {
                    console.error('Error cargando eventos:', error);
                    failureCallback(error); // reporta el error al calendario
                });
        },

        // cuando se hace clic en un evento
        eventClick: function(info) {
            Swal.fire({
                title: info.event.title,
                html: `
                    <p><strong>Paciente:</strong> ${info.event.extendedProps.paciente}</p>
                    <p><strong>Médico:</strong> ${info.event.extendedProps.medico}</p>
                    <p><strong>Motivo:</strong> ${info.event.extendedProps.motivo || 'N/A'}</p>
                    <p><strong>Estado:</strong> <span class="badge bg-info">${info.event.extendedProps.estatus}</span></p>
                    <p><strong>Fecha:</strong> ${info.event.start.toLocaleDateString('es-MX')}</p>
                `,
                showCancelButton: true,  // botón cerrar
                showDenyButton: true,    // botón eliminar
                confirmButtonText: 'Editar',
                denyButtonText: 'Eliminar',
                cancelButtonText: 'Cerrar'
            }).then((result) => {

                // SI EDITA
                if (result.isConfirmed) {
                    editarCita(info.event.id);

                // SI ELIMINA
                } else if (result.isDenied) {
                    eliminarCita(info.event.id, info.event.extendedProps.paciente);
                }
            });
        }
    });

    calendar.render(); // mostrar calendario

    // cargar lista de próximas citas
    cargarProximasCitas();

    // configurar el formulario del modal
    const form = document.querySelector("#modalAgenda form");
    if (form) {
        form.removeAttribute('action'); // se manejará solo con JS
        
        form.addEventListener("submit", function (e) { 
            e.preventDefault(); // evitar envío tradicional

            if (validarFormulario()) {
                guardarCita(new FormData(form)); // enviar datos al backend
            }
        });
    }

    // establecer fecha mínima (hoy)
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('fechaCita').setAttribute('min', hoy);
    document.getElementById('fechaCita').value = hoy;

    // al cerrar el modal restaurar valores
    document.getElementById('modalAgenda').addEventListener('hidden.bs.modal', function () {

        form.reset(); // limpiar formulario
        document.getElementById('modalAgendaLabel').innerHTML = 
            '<i class="fa-solid fa-calendar-plus me-2"></i>agregar agenda'; 
        
        // eliminar mensajes de validación
        document.querySelectorAll('.text-danger, .text-success').forEach(el => el.remove());
        
        const inputEditar = document.querySelector('input[name="idCitaEditar"]');
        if (inputEditar) inputEditar.remove(); // quitar campo oculto

        document.getElementById('idCita').disabled = false;
        document.getElementById('fechaCita').value = hoy;
    });
});


// validar formulario antes de enviar
function validarFormulario() {
    const idPaciente = document.getElementById('idPaciente').value;
    const idMedico = document.getElementById('idMedico').value;
    const fechaCita = document.getElementById('fechaCita').value;
    
    // validar campos obligatorios
    if (!idPaciente || !idMedico) {
        Swal.fire({
            icon: 'warning',
            title: 'campos requeridos',
            text: 'debe ingresar un paciente y un médico válidos'
        });
        return false;
    }

    // validar fecha no pasada
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


// validar paciente por ID
function validarPaciente(id) {
    if (!id) return;
    
    fetch(`php/agenda.php?accion=validarPaciente&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const input = document.getElementById('idPaciente');

            // eliminar mensaje previo
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


// validar médico por ID
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


// cargar lista de próximas citas
function cargarProximasCitas() {
    fetch("php/agenda.php?accion=lista")
        .then(response => response.json())
        .then(data => {
            const lista = document.getElementById('upcomingList');
            lista.innerHTML = "";

            // filtrar citas futuras y solo programadas
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
        method: "POST",  // se envía POST
        body: formData   // datos del formulario
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

                // cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById("modalAgenda"));
                if (modal) modal.hide();

                // recargar calendario y listado
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



// cargar datos de cita para editar
function editarCita(id) {
    fetch(`php/agenda.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(cita => {

            document.getElementById('modalAgendaLabel').innerHTML =
                '<i class="fa-solid fa-edit me-2"></i>editar cita';

            // llenar campos del formulario
            document.getElementById('idCita').value = cita.IdCita;
            document.getElementById('idCita').disabled = true;
            document.getElementById('idPaciente').value = cita.IdPaciente;
            document.getElementById('idMedico').value = cita.IdMedico;
            document.getElementById('fechaCita').value = cita.FechaCita;
            document.getElementById('motivoConsulta').value = cita.MotivoConsulta || '';
            document.getElementById('estatus').value = cita.EstadoCita;
            document.getElementById('observaciones').value = cita.Observaciones || '';
            document.getElementById('fechaRegistro').value = cita.FechaRegistro || '';

            // crear input oculto si no existe
            let inputEditar = document.querySelector('input[name="idCitaEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idCitaEditar';
                document.querySelector('#modalAgenda form').appendChild(inputEditar);
            }
            inputEditar.value = cita.IdCita;

            // mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('modalAgenda'));
            modal.show();
        });
}


// eliminar cita
function eliminarCita(id, paciente) {
    Swal.fire({
        title: 'estas seguro?',
        text: `se eliminara la cita del paciente: ${paciente}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',   // rojo
        cancelButtonColor: '#3085d6', // azul
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

                        // recargar calendario y lista
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
