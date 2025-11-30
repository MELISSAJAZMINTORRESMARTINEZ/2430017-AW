// inicializar cuando carga la página
document.addEventListener("DOMContentLoaded", function () {
    cargarPagos();

    const form = document.querySelector("#formPagos");
    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            if (validarFormulario()) {
                guardarPago(new FormData(form));
            }
        });
    }

    // establecer fecha de hoy por defecto
    const hoy = new Date().toISOString().split('T')[0];
    const inputFecha = document.getElementById('fechaPago');
    if (inputFecha) {
        inputFecha.value = hoy;
    }

    // limpiar formulario cuando se cierra el modal
    const modal = document.getElementById('modalPagos');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            form.reset();
            document.getElementById('modalPagosLabel').innerHTML = 
                '<i class="fa-solid fa-dollar-sign me-2"></i>Agregar Pago';
            
            // remover mensajes de validación
            document.querySelectorAll('.validacion-msg').forEach(el => el.remove());
            document.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                el.classList.remove('is-valid', 'is-invalid');
            });
            
            // remover campo de edición si existe
            const inputEditar = document.querySelector('input[name="idPagoEditar"]');
            if (inputEditar) inputEditar.remove();
            
            // habilitar campos
            document.getElementById('idCita').disabled = false;
            document.getElementById('idPaciente').disabled = false;
            
            // restablecer fecha
            if (inputFecha) {
                inputFecha.value = hoy;
            }
        });
    }
});


// cargar todos los pagos
function cargarPagos() {
    fetch("php/pagos.php?accion=lista")
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector("#tablaPagos tbody");
            tbody.innerHTML = "";

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No hay pagos registrados</td></tr>';
                return;
            }

            data.forEach(p => {
                // formatear fecha
                const fechaPago = p.FechaPago ? new Date(p.FechaPago).toLocaleDateString('es-MX') : 'N/A';
                
                // determinar color del badge según estatus
                let badgeClass = 'bg-warning';
                if (p.EstatusPago === 'Completado') badgeClass = 'bg-success';
                if (p.EstatusPago === 'Cancelado') badgeClass = 'bg-danger';
                
                const fila = `
                <tr>
                    <td>${p.IdPago}</td>
                    <td>${p.IdCita}</td>
                    <td>${p.IdPaciente}</td>
                    <td>$${parseFloat(p.Monto).toFixed(2)}</td>
                    <td>${p.MetodoPago}</td>
                    <td>${fechaPago}</td>
                    <td>${p.Referencia || 'N/A'}</td>
                    <td>
                        <span class="badge ${badgeClass}">
                            ${p.EstatusPago}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm me-1" onclick="editarPago(${p.IdPago})" title="Editar">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarPago(${p.IdPago}, '${p.NombrePaciente || 'Paciente'}')" title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += fila;
            });
        })
        .catch(err => {
            console.error("Error cargando pagos:", err);
            Swal.fire({
                icon: "error",
                title: "Error al cargar",
                text: "No se pudieron cargar los pagos"
            });
        });
}


// validar formulario completo
function validarFormulario() {
    const idPaciente = document.getElementById('idPaciente').value;
    const idCita = document.getElementById('idCita').value;
    const monto = document.getElementById('monto').value;
    
    if (!idPaciente || !idCita) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos',
            text: 'Debe ingresar un paciente y una cita válidos'
        });
        return false;
    }

    if (!monto || parseFloat(monto) <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Monto inválido',
            text: 'El monto debe ser mayor a 0'
        });
        return false;
    }

    return true;
}


// validar que existe el paciente
function validarPaciente(id) {
    if (!id) return;
    
    fetch(`php/pagos.php?accion=validarPaciente&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const input = document.getElementById('idPaciente');
            const mensajePrevio = input.parentElement.querySelector('.validacion-msg');
            if (mensajePrevio) mensajePrevio.remove();
            
            const mensaje = document.createElement('small');
            mensaje.className = 'validacion-msg d-block mt-1';
            
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


// validar que la cita existe y pertenece al paciente
function validarCita(idCita) {
    if (!idCita) return;
    
    const idPaciente = document.getElementById('idPaciente').value;
    
    if (!idPaciente) {
        Swal.fire({
            icon: 'info',
            title: 'Atención',
            text: 'Primero debe ingresar un ID de paciente válido'
        });
        document.getElementById('idCita').value = '';
        return;
    }
    
    fetch(`php/pagos.php?accion=validarCita&idCita=${idCita}&idPaciente=${idPaciente}`)
        .then(response => response.json())
        .then(data => {
            const input = document.getElementById('idCita');
            const mensajePrevio = input.parentElement.querySelector('.validacion-msg');
            if (mensajePrevio) mensajePrevio.remove();
            
            const mensaje = document.createElement('small');
            mensaje.className = 'validacion-msg d-block mt-1';
            
            if (data.error) {
                mensaje.className += ' text-danger';
                mensaje.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i>Cita no encontrada o no pertenece al paciente';
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            } else {
                // verificar si ya tiene pago
                verificarPagoExistente(idCita, data);
            }
        });
}


// verificar si la cita ya tiene un pago registrado
function verificarPagoExistente(idCita, dataCita) {
    fetch(`php/pagos.php?accion=verificarPago&idCita=${idCita}`)
        .then(response => response.json())
        .then(data => {
            const input = document.getElementById('idCita');
            const mensajePrevio = input.parentElement.querySelector('.validacion-msg');
            if (mensajePrevio) mensajePrevio.remove();
            
            const mensaje = document.createElement('small');
            mensaje.className = 'validacion-msg d-block mt-1';
            
            if (data.noPago) {
                // no hay pago, todo bien
                mensaje.className += ' text-success';
                mensaje.innerHTML = `
                    <i class="fa-solid fa-circle-check me-1"></i>
                    Cita válida - Paciente: ${dataCita.NombrePaciente}, 
                    Médico: ${dataCita.NombreMedico}
                `;
                input.classList.add('is-valid');
                input.classList.remove('is-invalid');
            } else {
                // ya existe un pago
                mensaje.className += ' text-warning';
                mensaje.innerHTML = `
                    <i class="fa-solid fa-exclamation-triangle me-1"></i>
                    Esta cita ya tiene un pago registrado (ID: ${data.IdPago}, 
                    Monto: $${parseFloat(data.Monto).toFixed(2)}, 
                    Estatus: ${data.EstatusPago})
                `;
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Pago existente',
                    text: 'Esta cita ya tiene un pago registrado. ¿Desea editarlo en lugar de crear uno nuevo?',
                    showCancelButton: true,
                    confirmButtonText: 'Editar pago',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        editarPago(data.IdPago);
                    } else {
                        input.value = '';
                    }
                });
            }
            
            input.parentElement.appendChild(mensaje);
        });
}


// guardar o actualizar pago
function guardarPago(formData) {
    fetch("php/pagos.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {
            if (respuesta.includes("OK")) {
                Swal.fire({
                    icon: "success",
                    title: respuesta.includes("actualizado") ? "Pago actualizado" : "Pago guardado",
                    text: respuesta.includes("actualizado") ? 
                          "El pago se actualizó correctamente" : 
                          "El pago se registró correctamente",
                    timer: 2000,
                    showConfirmButton: false
                });

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalPagos"));
                if (modal) modal.hide();

                cargarPagos();
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
                text: "No se pudo guardar el pago"
            });
        });
}


// editar pago
function editarPago(id) {
    fetch(`php/pagos.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(pago => {
            if (pago.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: pago.error
                });
                return;
            }

            document.getElementById('modalPagosLabel').innerHTML = 
                '<i class="fa-solid fa-edit me-2"></i>Editar Pago';

            // llenar campos
            document.getElementById('idCita').value = pago.IdCita;
            document.getElementById('idCita').disabled = true;
            document.getElementById('idPaciente').value = pago.IdPaciente;
            document.getElementById('idPaciente').disabled = true;
            document.getElementById('monto').value = pago.Monto;
            document.getElementById('metodoPago').value = pago.MetodoPago;
            document.getElementById('fechaPago').value = pago.FechaPago;
            document.getElementById('referencia').value = pago.Referencia || '';
            document.getElementById('estatusPago').value = pago.EstatusPago;

            // crear campo oculto para edición
            let inputEditar = document.querySelector('input[name="idPagoEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idPagoEditar';
                document.getElementById('formPagos').appendChild(inputEditar);
            }
            inputEditar.value = pago.IdPago;

            // mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('modalPagos'));
            modal.show();
        })
        .catch(error => {
            console.error("Error al cargar pago:", error);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "No se pudo cargar la información del pago"
            });
        });
}


// eliminar pago
function eliminarPago(id, nombrePaciente) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Se eliminará el pago del paciente: ${nombrePaciente}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`php/pagos.php?accion=eliminar&id=${id}`)
                .then(response => response.text())
                .then(respuesta => {
                    if (respuesta.includes("OK")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'El pago ha sido eliminado correctamente',
                            timer: 1800,
                            showConfirmButton: false
                        });
                        cargarPagos();
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
                        text: 'No se pudo eliminar el pago'
                    });
                });
        }
    });
}