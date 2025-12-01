document.addEventListener("DOMContentLoaded", function () { 
    // espero a que la página termine de cargar
    
    cargarBitacoras(); // cargo la tabla de bitácoras al iniciar

    const form = document.querySelector("#formBitacora");
    form.addEventListener("submit", function (e) { 
        e.preventDefault();
        
        // validar que el usuario sea válido antes de enviar
        const idUsuario = document.getElementById('idUsuario');
        if (idUsuario.classList.contains('is-invalid') || !idUsuario.classList.contains('is-valid')) {
            Swal.fire({
                icon: 'error',
                title: 'Usuario inválido',
                text: 'Por favor ingrese un ID de usuario válido'
            });
            return;
        }
        
        guardarBitacora(new FormData(form));
    });

    // validación en tiempo real del ID de usuario
    document.getElementById('idUsuario').addEventListener('blur', function() {
        validarUsuario(this.value);
    });

    document.getElementById('idUsuario').addEventListener('input', function() {
        if (this.value) {
            validarUsuario(this.value);
        }
    });

    // establecer fecha y hora actual por defecto
    const ahora = new Date();
    const fechaHoraLocal = new Date(ahora.getTime() - ahora.getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 16);
    document.getElementById('fechaAcceso').value = fechaHoraLocal;

    // cuando se cierra el modal, limpio todo
    document.getElementById('modalBitacora').addEventListener('hidden.bs.modal', function () {
        document.querySelector("#formBitacora").reset();
        document.getElementById('modalBitacoraLabel').innerHTML = 
            '<i class="fa-solid fa-book me-2"></i>Agregar Bitácora'; 
        
        document.querySelectorAll('.text-danger, .text-success').forEach(el => el.remove());
        
        // limpiar clases de validación
        document.getElementById('idUsuario').classList.remove('is-valid', 'is-invalid');
        
        const inputEditar = document.querySelector('input[name="idBitacoraEditar"]');
        if (inputEditar) inputEditar.remove();
        
        // restablecer fecha actual
        const ahora = new Date();
        const fechaHoraLocal = new Date(ahora.getTime() - ahora.getTimezoneOffset() * 60000)
            .toISOString()
            .slice(0, 16);
        document.getElementById('fechaAcceso').value = fechaHoraLocal;
    });
});


// validar si existe el usuario
function validarUsuario(id) {
    if (!id) {
        const input = document.getElementById('idUsuario');
        input.classList.remove('is-valid', 'is-invalid');
        const mensajePrevio = input.parentElement.querySelector('.validacion-msg');
        if (mensajePrevio) mensajePrevio.remove();
        return;
    }
    
    fetch(`php/bitacora.php?accion=validarUsuario&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const input = document.getElementById('idUsuario');
            const mensajePrevio = input.parentElement.querySelector('.validacion-msg');
            if (mensajePrevio) mensajePrevio.remove();
            
            const mensaje = document.createElement('small');
            mensaje.className = 'validacion-msg d-block mt-1';
            
            if (data.error) {
                mensaje.className += ' text-danger';
                mensaje.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i>Usuario no encontrado';
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            } else {
                mensaje.className += ' text-success';
                mensaje.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i>Usuario: ${data.Usuario} (${data.Rol})`;
                input.classList.add('is-valid');
                input.classList.remove('is-invalid');
            }
            
            input.parentElement.appendChild(mensaje);
        })
        .catch(error => {
            console.error('Error validando usuario:', error);
            const input = document.getElementById('idUsuario');
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        });
}


// cargar todas las bitácoras
function cargarBitacoras() {
    fetch("php/bitacora.php?accion=lista")
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector("#tablaBitacora tbody"); 
            tbody.innerHTML = "";

            if (data.length === 0) { 
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">no hay registros de bitácora</td></tr>';
                return;
            }

            data.forEach(b => {
                // formatear fecha
                const fecha = new Date(b.FechaAcceso);
                const fechaFormateada = fecha.toLocaleString('es-MX', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const fila = `
                <tr>
                    <td>${b.IdBitacora}</td>
                    <td>${b.NombreUsuario ?? 'ID: ' + b.IdUsuario}</td>
                    <td>${fechaFormateada}</td>
                    <td>${b.AccionRealizada}</td>
                    <td><span class="badge bg-primary">${b.Modulo}</span></td>
                    <td>
                        <button class="btn btn-warning btn-sm me-1" onclick="editarBitacora(${b.IdBitacora})">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarBitacora(${b.IdBitacora})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += fila;
            });
        })
        .catch(err => {
            console.error("error cargando bitácoras:", err);
            Swal.fire({
                icon: "error",
                title: "error al cargar",
                text: "no se pudieron cargar las bitácoras"
            });
        });
}


// guardar o actualizar bitácora
function guardarBitacora(formData) {
    fetch("php/bitacora.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {

            if (respuesta.includes("OK")) {
                Swal.fire({
                    icon: "success",
                    title: respuesta.includes("actualizada") ? "bitácora actualizada" : "bitácora guardada",
                    timer: 1800,
                    showConfirmButton: false
                });

                document.querySelector("#formBitacora").reset(); 

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalBitacora"));
                modal.hide();

                cargarBitacoras(); 
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
                title: "error en la petición",
                text: "no se pudo guardar la bitácora"
            });
        });
}


// cargar datos para editar
function editarBitacora(id) {
    fetch(`php/bitacora.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(bitacora => {

            document.getElementById('modalBitacoraLabel').innerHTML =
                '<i class="fa-solid fa-edit me-2"></i>Editar Bitácora';

            document.getElementById('idUsuario').value = bitacora.IdUsuario;
            
            // Convertir la fecha del formato MySQL a datetime-local
            const fecha = new Date(bitacora.FechaAcceso);
            const fechaLocal = new Date(fecha.getTime() - fecha.getTimezoneOffset() * 60000)
                .toISOString()
                .slice(0, 16);
            document.getElementById('fechaAcceso').value = fechaLocal;
            
            document.getElementById('accionRealizada').value = bitacora.AccionRealizada;
            document.getElementById('modulo').value = bitacora.Modulo;

            // validar el ID de usuario cargado
            validarUsuario(bitacora.IdUsuario);

            // input oculto para edición
            let inputEditar = document.querySelector('input[name="idBitacoraEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idBitacoraEditar';
                document.getElementById('formBitacora').appendChild(inputEditar);
            }
            inputEditar.value = bitacora.IdBitacora;

            const modal = new bootstrap.Modal(document.getElementById('modalBitacora'));
            modal.show();
        })
        .catch(error => {
            console.error("error al cargar bitácora:", error);
            Swal.fire({
                icon: "error",
                title: "error",
                text: "no se pudo cargar la información de la bitácora"
            });
        });
}


// eliminar bitácora
function eliminarBitacora(id) {

    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Se eliminará este registro de bitácora',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        
        if (result.isConfirmed) {

            fetch(`php/bitacora.php?accion=eliminar&id=${id}`)
                .then(response => response.text())
                .then(respuesta => {
                    
                    if (respuesta.includes("OK")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'La bitácora ha sido eliminada correctamente',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        cargarBitacoras();
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
                        text: 'no se pudo eliminar la bitácora'
                    });
                });
        }
    });
}