document.addEventListener("DOMContentLoaded", function () { 
    // espero a que la página termine de cargar
    
    cargarPagos(); // cargo la tabla de pagos al iniciar

    const form = document.querySelector("#formPagos");
    form.addEventListener("submit", function (e) { 
        e.preventDefault();
        
        // validar monto
        const monto = document.getElementById('monto').value;
        if (parseFloat(monto) <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'monto inválido',
                text: 'el monto debe ser mayor a 0'
            });
            return;
        }

        // validar que los campos tengan las clases correctas de validación
        const idPaciente = document.getElementById('idPaciente');
        const idCita = document.getElementById('idCita');

        if (idPaciente.classList.contains('is-invalid') || !idPaciente.classList.contains('is-valid')) {
            Swal.fire({
                icon: 'error',
                title: 'Paciente inválido',
                text: 'Por favor ingrese un ID de paciente válido'
            });
            return;
        }

        if (idCita.classList.contains('is-invalid') || !idCita.classList.contains('is-valid')) {
            Swal.fire({
                icon: 'error',
                title: 'Cita inválida',
                text: 'Por favor ingrese un ID de cita válido'
            });
            return;
        }
        
        guardarPago(new FormData(form));
    });

    // validación en tiempo real del ID de paciente
    document.getElementById('idPaciente').addEventListener('blur', function() {
        validarPaciente(this.value);
    });

    document.getElementById('idPaciente').addEventListener('input', function() {
        if (this.value) {
            validarPaciente(this.value);
        }
    });

    // validación en tiempo real del ID de cita
    document.getElementById('idCita').addEventListener('blur', function() {
        validarCita(this.value);
    });

    document.getElementById('idCita').addEventListener('input', function() {
        if (this.value) {
            validarCita(this.value);
        }
    });

    // establecer fecha de hoy por defecto
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('fechaPago').value = hoy;

    // cuando se cierra el modal, limpio todo
    document.getElementById('modalPagos').addEventListener('hidden.bs.modal', function () {
        document.querySelector("#formPagos").reset();
        document.getElementById('modalPagosLabel').innerHTML = 
            '<i class="fa-solid fa-money-check-dollar me-2"></i>agregar pago'; 
        
        document.querySelectorAll('.text-danger, .text-success').forEach(el => el.remove());
        
        // limpiar clases de validación
        document.getElementById('idPaciente').classList.remove('is-valid', 'is-invalid');
        document.getElementById('idCita').classList.remove('is-valid', 'is-invalid');
        
        const inputEditar = document.querySelector('input[name="idPagoEditar"]');
        if (inputEditar) inputEditar.remove();
        
        document.getElementById('fechaPago').value = hoy;
    });
});


// validar si existe el paciente
function validarPaciente(id) {
    if (!id) {
        const input = document.getElementById('idPaciente');
        input.classList.remove('is-valid', 'is-invalid');
        const mensajePrevio = input.parentElement.querySelector('.validacion-msg');
        if (mensajePrevio) mensajePrevio.remove();
        return;
    }
    
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
        })
        .catch(error => {
            console.error('Error validando paciente:', error);
            const input = document.getElementById('idPaciente');
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        });
}


// validar si existe la cita
function validarCita(id) {
    if (!id) {
        const input = document.getElementById('idCita');
        input.classList.remove('is-valid', 'is-invalid');
        const mensajePrevio = input.parentElement.querySelector('.validacion-msg');
        if (mensajePrevio) mensajePrevio.remove();
        return;
    }
    
    fetch(`php/pagos.php?accion=validarCita&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const input = document.getElementById('idCita');
            const mensajePrevio = input.parentElement.querySelector('.validacion-msg');
            if (mensajePrevio) mensajePrevio.remove();
            
            const mensaje = document.createElement('small');
            mensaje.className = 'validacion-msg d-block mt-1';
            
            if (data.error) {
                mensaje.className += ' text-danger';
                mensaje.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i>Cita no encontrada';
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            } else {
                mensaje.className += ' text-success';
                mensaje.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i>Cita: ${data.FechaCita} - ${data.NombreCompleto || 'Paciente'}`;
                input.classList.add('is-valid');
                input.classList.remove('is-invalid');
            }
            
            input.parentElement.appendChild(mensaje);
        })
        .catch(error => {
            console.error('Error validando cita:', error);
            const input = document.getElementById('idCita');
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        });
}


// cargar todos los pagos
function cargarPagos() {
    fetch("php/pagos.php?accion=lista")
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector("#tablaPagos tbody"); 
            tbody.innerHTML = "";

            if (data.length === 0) { 
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">no hay pagos registrados</td></tr>';
                return;
            }

            data.forEach(p => {
                const fila = `
                <tr>
                    <td>${p.IdPago}</td>
                    <td>${p.IdCita}</td>
                    <td>${p.NombrePaciente ?? 'ID: ' + p.IdPaciente}</td>
                    <td>$${parseFloat(p.Monto).toFixed(2)}</td>
                    <td><span class="badge bg-info">${p.MetodoPago}</span></td>
                    <td>${p.FechaPago}</td>
                    <td>${p.Referencia ?? '-'}</td>
                    <td>
                        <span class="badge ${p.EstatusPago === 'Pagado' ? 'bg-success' : 
                                            p.EstatusPago === 'Pendiente' ? 'bg-warning' : 'bg-danger'}">
                            ${p.EstatusPago}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm me-1" onclick="editarPago(${p.IdPago})">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarPago(${p.IdPago}, '${p.NombrePaciente}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += fila;
            });
        })
        .catch(err => {
            console.error("error cargando pagos:", err);
            Swal.fire({
                icon: "error",
                title: "error al cargar",
                text: "no se pudieron cargar los pagos"
            });
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
                    title: respuesta.includes("actualizado") ? "pago actualizado" : "pago guardado",
                    timer: 1800,
                    showConfirmButton: false
                });

                document.querySelector("#formPagos").reset(); 

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalPagos"));
                modal.hide();

                cargarPagos(); 
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
                text: "no se pudo guardar el pago"
            });
        });
}


// cargar datos para editar
function editarPago(id) {
    fetch(`php/pagos.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(pago => {

            document.getElementById('modalPagosLabel').innerHTML =
                '<i class="fa-solid fa-edit me-2"></i>editar pago';

            document.getElementById('idCita').value = pago.IdCita;
            document.getElementById('idPaciente').value = pago.IdPaciente;
            document.getElementById('monto').value = pago.Monto;
            document.getElementById('metodoPago').value = pago.MetodoPago;
            document.getElementById('fechaPago').value = pago.FechaPago;
            document.getElementById('referencia').value = pago.Referencia || '';
            document.getElementById('estatusPago').value = pago.EstatusPago;

            // validar los IDs cargados
            validarPaciente(pago.IdPaciente);
            validarCita(pago.IdCita);

            // input oculto para edición
            let inputEditar = document.querySelector('input[name="idPagoEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idPagoEditar';
                document.getElementById('formPagos').appendChild(inputEditar);
            }
            inputEditar.value = pago.IdPago;

            const modal = new bootstrap.Modal(document.getElementById('modalPagos'));
            modal.show();
        })
        .catch(error => {
            console.error("error al cargar pago:", error);
            Swal.fire({
                icon: "error",
                title: "error",
                text: "no se pudo cargar la informacion del pago"
            });
        });
}


// eliminar pago
function eliminarPago(id, paciente) {

    Swal.fire({
        title: 'estas seguro?',
        text: `se eliminara el pago del paciente: ${paciente}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'si, eliminar',
        cancelButtonText: 'cancelar'
    }).then((result) => {
        
        if (result.isConfirmed) {

            fetch(`php/pagos.php?accion=eliminar&id=${id}`)
                .then(response => response.text())
                .then(respuesta => {
                    
                    if (respuesta.includes("OK")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'eliminado',
                            text: 'el pago ha sido eliminado correctamente',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        cargarPagos();
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
                        text: 'no se pudo eliminar el pago'
                    });
                });
        }
    });
}