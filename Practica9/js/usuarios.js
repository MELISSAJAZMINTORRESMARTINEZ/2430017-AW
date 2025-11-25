document.addEventListener("DOMContentLoaded", function () { 
    // espero a que la página termine de cargar antes de ejecutar algo
    
    cargarUsuarios(); // cargo la tabla de usuarios al iniciar

    const form = document.querySelector("#formUsuarios"); // obtengo el formulario
    form.addEventListener("submit", function (e) { 
        e.preventDefault(); // evito que la página se recargue
        
        // validaciones
        if (!validarFormulario()) {
            return;
        }
        
        guardarUsuario(new FormData(form)); // envío los datos al PHP
    });

    // cuando se cierra el modal, limpio todo
    document.getElementById('modalUsuario').addEventListener('hidden.bs.modal', function () {
        document.querySelector("#formUsuarios").reset(); // limpiar formulario
        document.getElementById('modalUsuarioLabel').innerHTML = 
            '<i class="fa-solid fa-user-plus me-2"></i>Agregar Usuario'; 
        
        // si había un input oculto de edición, lo elimino
        const inputEditar = document.querySelector('input[name="idUsuarioEditar"]');
        if (inputEditar) inputEditar.remove();
        
        document.getElementById('idUsuario').disabled = false; // vuelvo a activar el campo ID
        document.getElementById('contrasena').required = true; // contraseña requerida al agregar
        document.getElementById('contrasena').placeholder = ''; // limpio placeholder
        
        // limpiar mensajes de error
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    });
});


// validar formulario
function validarFormulario() {
    let valido = true;
    
    // limpiar errores previos
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    
    // validar ID Usuario
    const idUsuario = document.getElementById('idUsuario');
    if (!idUsuario.disabled && (!idUsuario.value || idUsuario.value <= 0)) {
        mostrarError(idUsuario, 'el ID debe ser un número mayor a 0');
        valido = false;
    }
    
    // validar Usuario
    const usuario = document.getElementById('usuario');
    if (!usuario.value || usuario.value.trim().length < 3) {
        mostrarError(usuario, 'el usuario debe tener al menos 3 caracteres');
        valido = false;
    }
    
    // validar Contraseña (solo si es requerida)
    const contrasena = document.getElementById('contrasena');
    if (contrasena.required && (!contrasena.value || contrasena.value.length < 6)) {
        mostrarError(contrasena, 'la contraseña debe tener al menos 6 caracteres');
        valido = false;
    }
    
    // validar Rol
    const rol = document.getElementById('rol');
    if (!rol.value) {
        mostrarError(rol, 'debe seleccionar un rol');
        valido = false;
    }
    
    // validar Activo
    const activo = document.getElementById('activo');
    if (activo.value === '') {
        mostrarError(activo, 'debe seleccionar si el usuario está activo');
        valido = false;
    }
    
    return valido;
}


// mostrar mensaje de error en un campo
function mostrarError(campo, mensaje) {
    campo.classList.add('is-invalid');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = mensaje;
    campo.parentNode.appendChild(errorDiv);
}


// carga todos los usuarios en la tabla
function cargarUsuarios() {
    fetch("php/usuarios.php?accion=lista") // pido la lista al PHP
        .then(response => response.json()) // convierto respuesta en JSON
        .then(data => {
            const tbody = document.querySelector("#tablaPacientes tbody"); 
            tbody.innerHTML = ""; // limpio la tabla

            if (data.length === 0) { 
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">no hay usuarios registrados</td></tr>';
                return;
            }

            data.forEach(u => { // recorro cada usuario
                const fila = `
                <tr>
                    <td>${u.IdUsuario}</td>
                    <td>${u.Usuario}</td>
                    <td>
                        <span class="text-muted">
                            <i class="fa-solid fa-lock me-1"></i>
                            ${u.ContraseñaHash ? '••••••••' : 'sin contraseña'}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${getBadgeRol(u.Rol)}">
                            ${u.Rol}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${u.Activo == 1 ? 'bg-success' : 'bg-secondary'}">
                            ${u.Activo == 1 ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td>${u.UltimoAcceso || 'nunca'}</td>
                    <td>
                        <button class="btn btn-warning btn-sm me-1" onclick="editarUsuario(${u.IdUsuario})" title="Editar">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(${u.IdUsuario}, '${u.Usuario}')" title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += fila; // agrego fila
            });
        })
        .catch(err => {
            console.error("error cargando usuarios:", err);
            Swal.fire({
                icon: "error",
                title: "error al cargar",
                text: "no se pudieron cargar los usuarios"
            });
        });
}


// obtener clase de badge según el rol
function getBadgeRol(rol) {
    switch(rol) {
        case 'Super admin':
            return 'bg-danger';
        case 'Medico':
            return 'bg-primary';
        case 'Paciente':
            return 'bg-info';
        case 'Secretaria':
            return 'bg-warning text-dark';
        default:
            return 'bg-secondary';
    }
}


// guardar o actualizar usuario
function guardarUsuario(formData) {
    fetch("php/usuarios.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {

            if (respuesta.includes("OK")) {
                Swal.fire({
                    icon: "success",
                    title: respuesta.includes("actualizado") ? "usuario actualizado" : "usuario guardado",
                    text: respuesta.includes("actualizado") 
                        ? "el usuario se actualizó correctamente" 
                        : "el usuario se registró correctamente",
                    timer: 1800,
                    showConfirmButton: false
                });

                document.querySelector("#formUsuarios").reset(); 

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalUsuario"));
                modal.hide();

                cargarUsuarios(); 
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
                text: "no se pudo guardar el usuario"
            });
        });
}


// cargar datos para editar
function editarUsuario(id) {
    fetch(`php/usuarios.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(usuario => {

            document.getElementById('modalUsuarioLabel').innerHTML =
                '<i class="fa-solid fa-user-edit me-2"></i>editar usuario';

            // lleno los campos
            document.getElementById('idUsuario').value = usuario.IdUsuario;
            document.getElementById('idUsuario').disabled = true;
            document.getElementById('usuario').value = usuario.Usuario;
            
            // la contraseña no se muestra y no es requerida al editar
            document.getElementById('contrasena').value = '';
            document.getElementById('contrasena').required = false;
            document.getElementById('contrasena').placeholder = 'dejar en blanco para mantener la actual';
            
            document.getElementById('rol').value = usuario.Rol;
            document.getElementById('idMedico').value = usuario.IdMedico || '';
            document.getElementById('activo').value = usuario.Activo;
            document.getElementById('ultimoAcceso').value = usuario.UltimoAcceso || '';

            // input oculto para indicar que es edición
            let inputEditar = document.querySelector('input[name="idUsuarioEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idUsuarioEditar';
                document.getElementById('formUsuarios').appendChild(inputEditar);
            }
            inputEditar.value = usuario.IdUsuario;

            const modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
            modal.show();
        })
        .catch(error => {
            console.error("error al cargar usuario:", error);
            Swal.fire({
                icon: "error",
                title: "error",
                text: "no se pudo cargar la informacion del usuario"
            });
        });
}


// eliminar usuario
function eliminarUsuario(id, usuario) {

    Swal.fire({
        title: '¿estás seguro?',
        text: `se eliminará al usuario: ${usuario}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'sí, eliminar',
        cancelButtonText: 'cancelar'
    }).then((result) => {
        
        if (result.isConfirmed) {

            fetch(`php/usuarios.php?accion=eliminar&id=${id}`)
                .then(response => response.text())
                .then(respuesta => {
                    
                    if (respuesta.includes("OK")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'eliminado',
                            text: 'el usuario ha sido eliminado correctamente',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        cargarUsuarios();
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
                        text: 'no se pudo eliminar el usuario'
                    });
                });
        }
    });
}