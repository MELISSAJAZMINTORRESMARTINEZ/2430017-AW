document.addEventListener("DOMContentLoaded", function () {
    cargarEspecialidades();

    // Formulario de AGREGAR
    const formAgregar = document.querySelector("#modalEspecialidad form");
    if (formAgregar) {
        formAgregar.addEventListener("submit", function (e) {
            e.preventDefault();
            guardarEspecialidad(new FormData(formAgregar));
        });
    }

    // Formulario de EDITAR
    const formEditar = document.querySelector("#modalEditarEspecialidad form");
    if (formEditar) {
        formEditar.addEventListener("submit", function (e) {
            e.preventDefault();
            actualizarEspecialidad(new FormData(formEditar));
        });
    }
});

// Cargar especialidades en la tabla
function cargarEspecialidades() {
    fetch("php/especialidades.php?accion=lista")
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector("#tablaPacientes tbody");
            tbody.innerHTML = "";

            data.forEach(esp => {
                const tr = document.createElement('tr');
                
                tr.innerHTML = `
                    <td>${esp.IdEspecialidad}</td>
                    <td>${esp.NombreEspecialidad}</td>
                    <td>${esp.Descripcion}</td>
                    <td>
                        <button class="btn btn-sm btn-warning text-white me-2 btn-editar" 
                                data-id="${esp.IdEspecialidad}"
                                data-nombre="${esp.NombreEspecialidad}"
                                data-descripcion="${esp.Descripcion}"
                                title="Editar">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-eliminar" 
                                data-id="${esp.IdEspecialidad}"
                                title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                `;
                
                tbody.appendChild(tr);
            });

            // Agregar event listeners a los botones
            agregarEventListeners();
        })
        .catch(err => {
            console.error("Error cargando especialidades:", err);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "No se pudieron cargar las especialidades"
            });
        });
}

// Agregar event listeners a botones de editar y eliminar
function agregarEventListeners() {
    // Botones de editar
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            const descripcion = this.dataset.descripcion;
            editarEspecialidad(id, nombre, descripcion);
        });
    });

    // Botones de eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            eliminarEspecialidad(id);
        });
    });
}

// Guardar nueva especialidad
function guardarEspecialidad(formData) {
    fetch("php/especialidades.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {
            if (respuesta.includes("OK")) {
                Swal.fire({
                    icon: "success",
                    title: "Especialidad guardada",
                    text: "Se agregó correctamente",
                    timer: 1800,
                    showConfirmButton: false
                });

                document.querySelector("#modalEspecialidad form").reset();

                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById("modalEspecialidad"));
                modal.hide();

                cargarEspecialidades();
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
                text: "No se pudo guardar la especialidad"
            });
        });
}

// Función para abrir modal de edición con datos
function editarEspecialidad(id, nombre, descripcion) {
    // Llenar los campos del modal
    document.getElementById('editIdEspecialidad').value = id;
    document.getElementById('editNombreEspecialidad').value = nombre;
    document.getElementById('editDescripcion').value = descripcion;
    
    // Abrir modal de edición
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarEspecialidad'));
    modalEditar.show();
}

// Actualizar especialidad editada
function actualizarEspecialidad(formData) {
    fetch("php/especialidades.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {
            if (respuesta.includes("OK")) {
                Swal.fire({
                    icon: "success",
                    title: "Especialidad actualizada",
                    text: "Se actualizó correctamente",
                    timer: 1800,
                    showConfirmButton: false
                });

                document.querySelector("#modalEditarEspecialidad form").reset();

                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById("modalEditarEspecialidad"));
                modal.hide();

                cargarEspecialidades();
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
                text: "No se pudo actualizar la especialidad"
            });
        });
}

// Función para eliminar especialidad
function eliminarEspecialidad(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción eliminará la especialidad permanentemente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('accion', 'eliminar');
            formData.append('IdEspecialidad', id);
            
            fetch("php/especialidades.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.text())
                .then(respuesta => {
                    if (respuesta.includes("OK")) {
                        Swal.fire({
                            icon: "success",
                            title: "Eliminado",
                            text: "La especialidad ha sido eliminada",
                            timer: 1800,
                            showConfirmButton: false
                        });
                        cargarEspecialidades();
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
                        text: "No se pudo eliminar la especialidad"
                    });
                });
        }
    });
}