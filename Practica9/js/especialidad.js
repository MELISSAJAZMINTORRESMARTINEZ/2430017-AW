// cuando la pagina carga se ejecuta todo esto
document.addEventListener("DOMContentLoaded", function () {

    // llama la funcion que muestra las especialidades en la tabla
    cargarEspecialidades()

    // formulario para agregar
    const formAgregar = document.querySelector("#modalEspecialidad form")
    if (formAgregar) {
        formAgregar.addEventListener("submit", function (e) {
            e.preventDefault() // evita que la pagina se recargue
            guardarEspecialidad(new FormData(formAgregar)) // manda el form al php
        })
    }

    // formulario para editar
    const formEditar = document.querySelector("#modalEditarEspecialidad form")
    if (formEditar) {
        formEditar.addEventListener("submit", function (e) {
            e.preventDefault()
            actualizarEspecialidad(new FormData(formEditar))
        })
    }
})


// carga las especialidades y las mete en la tabla
function cargarEspecialidades() {
    fetch("php/especialidades.php?accion=lista")
        .then(response => response.json())
        .then(data => {

            const tbody = document.querySelector("#tablaPacientes tbody")
            tbody.innerHTML = "" // limpia la tabla

            data.forEach(esp => {

                const tr = document.createElement('tr')

                // mete los datos en columnas
                tr.innerHTML = `
                    <td>${esp.IdEspecialidad}</td>
                    <td>${esp.NombreEspecialidad}</td>
                    <td>${esp.Descripcion}</td>
                    <td>
                        <button class="btn btn-sm btn-warning text-white me-2 btn-editar"
                            data-id="${esp.IdEspecialidad}"
                            data-nombre="${esp.NombreEspecialidad}"
                            data-descripcion="${esp.Descripcion}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>

                        <button class="btn btn-sm btn-danger btn-eliminar"
                            data-id="${esp.IdEspecialidad}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                `
                tbody.appendChild(tr)
            })

            // agrega eventos a los botones despues de cargarlos
            agregarEventListeners()
        })
        .catch(error => {
            console.error("error cargando especialidades", error)
            Swal.fire({
                icon: "error",
                title: "error",
                text: "no se pudieron cargar las especialidades"
            })
        })
}


// agrega eventos a los botones de editar y eliminar
function agregarEventListeners() {

    // botones de editar
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id
            const nombre = this.dataset.nombre
            const descripcion = this.dataset.descripcion
            editarEspecialidad(id, nombre, descripcion)
        })
    })

    // botones de eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id
            eliminarEspecialidad(id)
        })
    })
}


// guarda una especialidad nueva
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
                    title: "especialidad guardada",
                    text: "todo bien",
                    timer: 1800,
                    showConfirmButton: false
                })

                document.querySelector("#modalEspecialidad form").reset()

                // cierra el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById("modalEspecialidad"))
                modal.hide()

                cargarEspecialidades()

            } else {
                Swal.fire({
                    icon: "error",
                    title: "error",
                    text: respuesta
                })
            }
        })
}


// abre el modal de editar con los datos del registro
function editarEspecialidad(id, nombre, descripcion) {

    document.getElementById('editIdEspecialidad').value = id
    document.getElementById('editNombreEspecialidad').value = nombre
    document.getElementById('editDescripcion').value = descripcion

    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarEspecialidad'))
    modalEditar.show()
}


// actualiza los datos editados
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
                    title: "especialidad actualizada",
                    text: "se actualizo bien",
                    timer: 1800,
                    showConfirmButton: false
                })

                document.querySelector("#modalEditarEspecialidad form").reset()

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalEditarEspecialidad"))
                modal.hide()

                cargarEspecialidades()

            } else {
                Swal.fire({
                    icon: "error",
                    title: "error",
                    text: respuesta
                })
            }
        })
        .catch(error => {
            console.error("error", error)
            Swal.fire({
                icon: "error",
                title: "error",
                text: "no se pudo actualizar"
            })
        })
}


// elimina una especialidad
function eliminarEspecialidad(id) {

    Swal.fire({
        title: 'seguro',
        text: "esto lo borrara para siempre",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'si eliminar',
        cancelButtonText: 'cancelar'
    }).then((result) => {

        if (result.isConfirmed) {

            const formData = new FormData()
            formData.append('accion', 'eliminar')
            formData.append('IdEspecialidad', id)

            fetch("php/especialidades.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.text())
                .then(respuesta => {
                    if (respuesta.includes("OK")) {

                        Swal.fire({
                            icon: "success",
                            title: "eliminado",
                            text: "se elimino bien",
                            timer: 1800,
                            showConfirmButton: false
                        })

                        cargarEspecialidades()

                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "error",
                            text: respuesta
                        })
                    }
                })
                .catch(error => {
                    console.error("error", error)
                    Swal.fire({
                        icon: "error",
                        title: "error",
                        text: "no se pudo eliminar"
                    })
                })
        }
    })
}
