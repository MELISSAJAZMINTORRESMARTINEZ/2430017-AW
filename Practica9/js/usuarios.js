document.addEventListener("DOMContentLoaded", function () { 
    // espero a que la página termine de cargar antes de ejecutar algo
    
    cargarUsuarios(); // cargo la tabla de usuarios al iniciar

    const form = document.querySelector("#formUsuarios"); // obtengo el formulario
    form.addEventListener("submit", function (e) { 
        e.preventDefault(); // evito que la página se recargue
        guardarUsuario(new FormData(form)); // envío los datos al PHP
    });

    // cuando se cierra el modal, limpio todo
    document.getElementById('modalUsuario').addEventListener('hidden.bs.modal', function () {
        document.querySelector("#formUsuarios").reset(); // limpiar formulario
        document.getElementById('modalUsuarioLabel').innerHTML = 
            '<i class="fa-solid fa-user-plus me-2"></i>agregar usuario'; 
        
        // si había un input oculto de edición, lo elimino
        const inputEditar = document.querySelector('input[name="idUsuarioEditar"]');
        if (inputEditar) inputEditar.remove();
        
        document.getElementById('idUsuario').disabled = false; // vuelvo a activar el campo ID
        
        // volver la contraseña requerida
        document.getElementById('contrasena').required = true;
        document.getElementById('contrasena').placeholder = "";
    });
});


// carga todos los usuarios en la tabla
function cargarUsuarios() {
    fetch("php/insertar_usuario.php?accion=lista") // pido la lista al PHP
        .then(response => response.json()) // convierto respuesta en JSON
        .then(data => {
            const tbody = document.querySelector("#tablaUsuarios tbody"); 
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
                    <td>${u.Correo}</td>

                    <td>••••••••</td>
                    <td><span class="badge bg-primary">${u.Rol}</span></td>
                    <td>${u.NombreMedico ?? (u.IdMedico ? 'ID: ' + u.IdMedico : '-')}</td>
                    <td>
                        <span class="badge ${u.Activo == 1 ? 'bg-success' : 'bg-secondary'}">
                            ${u.Activo == 1 ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td>${u.UltimoAcceso ?? 'Nunca'}</td>
                    <td>
                        <button class="btn btn-warning btn-sm me-1" onclick="editarUsuario(${u.IdUsuario})">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(${u.IdUsuario}, '${u.Usuario}')">
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


// guardar o actualizar usuario
function guardarUsuario(formData) {
    fetch("php/insertar_usuario.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {

            if (respuesta.includes("OK")) {
                Swal.fire({
                    icon: "success",
                    title: respuesta.includes("actualizado") ? "usuario actualizado" : "usuario guardado",
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
    fetch(`php/insertar_usuario.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(usuario => {

            document.getElementById('modalUsuarioLabel').innerHTML =
                '<i class="fa-solid fa-user-edit me-2"></i>editar usuario';

            // lleno los campos
            document.getElementById('idUsuario').value = usuario.IdUsuario;
            document.getElementById('idUsuario').disabled = true;
            document.getElementById('usuario').value = usuario.Usuario;

            document.getElementById('correo').value = usuario.Correo;

            
            // la contraseña no se muestra, pero se puede cambiar
            document.getElementById('contrasena').value = '';
            document.getElementById('contrasena').required = false;
            document.getElementById('contrasena').placeholder = 'Dejar vacío para no cambiar';
            
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
function eliminarUsuario(id, nombre) {

    Swal.fire({
        title: 'estas seguro?',
        text: `se eliminara al usuario: ${nombre}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'si, eliminar',
        cancelButtonText: 'cancelar'
    }).then((result) => {
        
        if (result.isConfirmed) {

            fetch(`php/insertar_usuario.php?accion=eliminar&id=${id}`)
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