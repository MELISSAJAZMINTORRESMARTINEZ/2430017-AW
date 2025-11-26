// variable global del calendario
// aqui guardo la instancia del calendario para usarla en todo el archivo
let calendar;

// se ejecuta cuando carga la pagina
document.addEventListener('DOMContentLoaded', function () {

    // agarro el div donde va el calendario
    const calendarEl = document.getElementById('calendar');

    // creo el calendario con sus opciones
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth', // vista inicial del mes
        locale: 'es', // idioma
        height: 'auto', // que se adapte a la pantalla

        // botones de arriba del calendario
        headerToolbar: {
            left: 'prev,next today',  // botones de navegacion
            center: 'title',          // titulo del mes
            right: 'dayGridMonth,timeGridWeek,timeGridDay'  // vistas
        },

        // aqui cargo las citas desde el backend
        events: function(info, successCallback, failureCallback) {
            // pido las citas al archivo agenda.php
            fetch('php/agenda.php?accion=lista')
                .then(response => response.json())
                .then(data => {
                    // aqui transformo la respuesta para que fullcalendar la entienda
                    const eventos = data.map(cita => ({
                        id: cita.IdCita, // id de la cita
                        title: `${cita.NombrePaciente || 'Paciente'} - ${cita.NombreMedico || 'Medico'}`, // texto del evento
                        start: cita.FechaCita, // fecha de la cita

                        // colores segun el estado
                        backgroundColor:
                            cita.EstadoCita === 'Programada' ? '#28a745' :
                            cita.EstadoCita === 'Atendida' ? '#007bff' : '#dc3545',

                        // datos extra que mostrare en el modal
                        extendedProps: {
                            paciente: cita.NombrePaciente,
                            medico: cita.NombreMedico,
                            motivo: cita.MotivoConsulta,
                            estatus: cita.EstadoCita
                        }
                    }));
                    successCallback(eventos); // envio los eventos al calendario
                })
                .catch(error => {
                    console.error('Error cargando eventos:', error);
                    failureCallback(error); // si falla mando error
                });
        },

        // cuando doy click en una cita, sale un modal con su info
        eventClick: function(info) {
            Swal.fire({
                title: info.event.title,
                html: `
                    <p><strong>Paciente:</strong> ${info.event.extendedProps.paciente}</p>
                    <p><strong>Medico:</strong> ${info.event.extendedProps.medico}</p>
                    <p><strong>Motivo:</strong> ${info.event.extendedProps.motivo || 'N/A'}</p>
                    <p><strong>Estado:</strong> <span class="badge bg-info">${info.event.extendedProps.estatus}</span></p>
                    <p><strong>Fecha:</strong> ${info.event.start.toLocaleDateString('es-MX')}</p>
                `,
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Editar',
                denyButtonText: 'Eliminar',
                cancelButtonText: 'Cerrar'
            }).then((result) => {
                if (result.isConfirmed) {
                    editarCita(info.event.id); // aqui abro modal para editar
                } else if (result.isDenied) {
                    eliminarCita(info.event.id, info.event.extendedProps.paciente); // aqui elimino
                }
            });
        }
    });

    // dibujo el calendario
    calendar.render();

    // cargo las proximas citas en la lista lateral
    cargarProximasCitas();

    // agarro el formulario del modal  
    const form = document.querySelector("#modalAgenda form");

    if (form) {
        // le quito el action pa que no recargue la pagina
        form.removeAttribute('action');
        
        // cuando le doy submit al modal
        form.addEventListener("submit", function (e) { 
            e.preventDefault(); // no deja recargar pagina

            if (validarFormulario()) { // si esta todo ok
                guardarCita(new FormData(form)); // mando los datos a php
            }
        });
    }

    // validacion rapida del id del paciente
    document.getElementById('idPaciente').addEventListener('blur', function() {
        validarPaciente(this.value);
    });

    // validacion rapida del id del medico
    document.getElementById('idMedico').addEventListener('blur', function() {
        validarMedico(this.value);
    });

    // aqui pongo la fecha minima del dia actual
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('fechaCita').setAttribute('min', hoy);
    document.getElementById('fechaCita').value = hoy;

    // cuando cierro el modal limpio todo
    document.getElementById('modalAgenda').addEventListener('hidden.bs.modal', function () {
        form.reset(); // limpio todos los campos

        document.getElementById('modalAgendaLabel').innerHTML =
            '<i class="fa-solid fa-calendar-plus me-2"></i>agregar agenda';

        // borro mensajes de validacion
        document.querySelectorAll('.text-danger, .text-success').forEach(el => el.remove());

        // borro input oculto de edicion si existe
        const inputEditar = document.querySelector('input[name="idCitaEditar"]');
        if (inputEditar) inputEditar.remove();

        document.getElementById('idCita').disabled = false;
        document.getElementById('fechaCita').value = hoy;
    });
});



// ------------------------------
// validacion de formulario
// ------------------------------

function validarFormulario() {
    const idPaciente = document.getElementById('idPaciente').value;
    const idMedico = document.getElementById('idMedico').value;
    const fechaCita = document.getElementById('fechaCita').value;
    
    // si faltan paciente o medico
    if (!idPaciente || !idMedico) {
        Swal.fire({
            icon: 'warning',
            title: 'campos requeridos',
            text: 'debe ingresar un paciente y un medico validos'
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
            title: 'fecha invalida',
            text: 'no se pueden agendar citas en fechas pasadas'
        });
        return false;
    }

    return true; // si todo esta bien
}



// ------------------------------
// validar paciente por ID
// ------------------------------

function validarPaciente(id) {
    if (!id) return;

    // pido al backend que verifique si existe
    fetch(`php/agenda.php?accion=validarPaciente&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const input = document.getElementById('idPaciente');

            // borro msg viejo
            const mensajePrevio = input.parentElement.querySelector('.validacion-msg');
            if (mensajePrevio) mensajePrevio.remove();
            
            const mensaje = document.createElement('small');
            mensaje.className = 'validacion-msg';
            
            // si no existe
            if (data.error) {
                mensaje.className += ' text-danger';
                mensaje.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i>Paciente no encontrado';
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            } else {
                // si existe muestro su nombre
                mensaje.className += ' text-success';
                mensaje.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i>Paciente: ${data.NombreCompleto}`;
                input.classList.add('is-valid');
                input.classList.remove('is-invalid');
            }
            
            input.parentElement.appendChild(mensaje);
        });
}



// ------------------------------
// validar medico por ID
// ------------------------------
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
                mensaje.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i>Medico no encontrado';
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            } else {
                mensaje.className += ' text-success';
                mensaje.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i>Medico: ${data.NombreCompleto}`;
                input.classList.add('is-valid');
                input.classList.remove('is-invalid');
            }
            
            input.parentElement.appendChild(mensaje);
        });
}



// ------------------------------
// cargar proximas citas
// ------------------------------

function cargarProximasCitas() {
    fetch("php/agenda.php?accion=lista")
        .then(response => response.json())
        .then(data => {
            const lista = document.getElementById('upcomingList');
            lista.innerHTML = "";

            // filtro citas futuras y ordeno por fecha
            const proximasCitas = data
                .filter(c => new Date(c.FechaCita) >= new Date() && c.EstadoCita === 'Programada')
                .sort((a, b) => new Date(a.FechaCita) - new Date(b.FechaCita))
                .slice(0, 5);

            // si no hay citas
            if (proximasCitas.length === 0) {
                lista.innerHTML = '<li class="text-muted">No hay proximas citas</li>';
                return;
            }

            // agrego cada cita a la lista
            proximasCitas.forEach(c => {
                const fecha = new Date(c.FechaCita).toLocaleDateString('es-MX');
                const item = document.createElement('li');
                item.className = 'mb-2 pb-2 border-bottom';
                item.innerHTML = `
                    <small class="text-muted">${fecha}</small><br>
                    <strong>${c.NombrePaciente || 'Paciente'}</strong><br>
                    <small>${c.NombreMedico || 'Medico'}</small>
                `;
                lista.appendChild(item);
            });
        });
}



// ------------------------------
// guardar o editar cita
// ------------------------------

function guardarCita(formData) {
    fetch("php/agenda.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {
            if (respuesta.includes("OK")) {
                // mensaje de exito
                Swal.fire({
                    icon: "success",
                    title: respuesta.includes("actualizada") ? "cita actualizada" : "cita guardada",
                    timer: 1800,
                    showConfirmButton: false
                });

                // cierro el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById("modalAgenda"));
                if (modal) modal.hide();

                // recargo eventos
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



// ------------------------------
// editar cita
// ------------------------------

function editarCita(id) {
    fetch(`php/agenda.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(cita => {

            // titulo del modal
            document.getElementById('modalAgendaLabel').innerHTML =
                '<i class="fa-solid fa-edit me-2"></i>editar cita';

            // aqui cargo los datos en el modal
            document.getElementById('idCita').value = cita.IdCita;
            document.getElementById('idCita').disabled = true;
            document.getElementById('idPaciente').value = cita.IdPaciente;
            document.getElementById('idMedico').value = cita.IdMedico;
            document.getElementById('fechaCita').value = cita.FechaCita;
            document.getElementById('motivoConsulta').value = cita.MotivoConsulta || '';
            document.getElementById('estatus').value = cita.EstadoCita;
            document.getElementById('observaciones').value = cita.Observaciones || '';
            document.getElementById('fechaRegistro').value = cita.FechaRegistro || '';
            document.getElementById('activo').value = cita.Activo;

            // input oculto para saber que es edicion
            let inputEditar = document.querySelector('input[name="idCitaEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idCitaEditar';
                document.querySelector('#modalAgenda form').appendChild(inputEditar);
            }
            inputEditar.value = cita.IdCita;

            // muestro el modal
            const modal = new bootstrap.Modal(document.getElementById('modalAgenda'));
            modal.show();
        });
}



// ------------------------------
// eliminar cita
// ------------------------------

function eliminarCita(id, paciente) {
    Swal.fire({
        title: 'estas seguro?',
        text: `se eliminara la cita del paciente: ${paciente}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2c8888',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'si, eliminar',
        cancelButtonText: 'cancelar'
    }).then((result) => {

        // si confirmo, borro
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

                        calendar.refetchEvents(); // recargo calendario
                        cargarProximasCitas();     // recargo lista lateral
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
