document.addEventListener("DOMContentLoaded", function () { 
    // espero a que la página termine de cargar antes de ejecutar algo
    
    cargarReportes(); // cargo la tabla de reportes al iniciar

    const form = document.querySelector("#formUsuarios"); // obtengo el formulario
    form.addEventListener("submit", function (e) { 
        e.preventDefault(); // evito que la página se recargue
        guardarReporte(new FormData(form)); // envío los datos al PHP
    });

    // cuando se cierra el modal, limpio todo
    document.getElementById('modalReportes').addEventListener('hidden.bs.modal', function () {
        document.querySelector("#formUsuarios").reset(); // limpiar formulario
        document.getElementById('modalReportesLabel').innerHTML = 
            '<i class="fa-solid fa-user-plus me-2"></i>Agregar Reporte'; 
        
        // si había un input oculto de edición, lo elimino
        const inputEditar = document.querySelector('input[name="idReporteEditar"]');
        if (inputEditar) inputEditar.remove();
        
        document.getElementById('idReporte').disabled = false; // vuelvo a activar el campo ID
    });
});


// carga todos los reportes en la tabla
function cargarReportes() {
    fetch("php/reportes.php?accion=lista") // pido la lista al PHP
        .then(response => response.json()) // convierto respuesta en JSON
        .then(data => {
            const tbody = document.querySelector("#tablaReportes tbody"); 
            tbody.innerHTML = ""; // limpio la tabla

            if (data.length === 0) { 
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">no hay reportes registrados</td></tr>';
                return;
            }

            data.forEach(r => { // recorro cada reporte
                const fila = `
                <tr>
                    <td>${r.IdReporte}</td>
                    <td>${r.TipoReporte}</td>
                    <td>${r.IdPaciente}</td>
                    <td>${r.IdMedico}</td>
                    <td>${r.FechaGeneracion}</td>
                    <td>${r.RutaArchivo ?? 'sin ruta'}</td>
                    <td>${r.Descripcion ?? 'sin descripción'}</td>
                    <td>${r.GeneradoPor ?? 'no especificado'}</td>
                    <td>
                        <button class="btn btn-warning btn-sm me-1" onclick="editarReporte(${r.IdReporte})">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarReporte(${r.IdReporte})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += fila; // agrego fila
            });
        })
        .catch(err => {
            console.error("error cargando reportes:", err);
            Swal.fire({
                icon: "error",
                title: "error al cargar",
                text: "no se pudieron cargar los reportes"
            });
        });
}


// guardar o actualizar reporte
function guardarReporte(formData) {
    fetch("php/reportes.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {

            if (respuesta.includes("OK")) {
                Swal.fire({
                    icon: "success",
                    title: respuesta.includes("actualizado") ? "reporte actualizado" : "reporte guardado",
                    timer: 1800,
                    showConfirmButton: false
                });

                document.querySelector("#formUsuarios").reset(); 

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalReportes"));
                modal.hide();

                cargarReportes(); 
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
                text: "no se pudo guardar el reporte"
            });
        });
}


// cargar datos para editar
function editarReporte(id) {
    fetch(`php/reportes.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(reporte => {

            document.getElementById('modalReportesLabel').innerHTML =
                '<i class="fa-solid fa-edit me-2"></i>editar reporte';

            // lleno los campos
            document.getElementById('idReporte').value = reporte.IdReporte;
            document.getElementById('idReporte').disabled = true;
            document.getElementById('tipoReporte').value = reporte.TipoReporte;
            document.getElementById('idPaciente').value = reporte.IdPaciente;
            document.getElementById('idMedico').value = reporte.IdMedico;
            document.getElementById('fechaGeneracion').value = reporte.FechaGeneracion;
            document.getElementById('ruta').value = reporte.RutaArchivo;
            document.getElementById('descripcion').value = reporte.Descripcion;
            document.getElementById('generado').value = reporte.GeneradoPor;

            // input oculto para indicar que es edición
            let inputEditar = document.querySelector('input[name="idReporteEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idReporteEditar';
                document.getElementById('formUsuarios').appendChild(inputEditar);
            }
            inputEditar.value = reporte.IdReporte;

            const modal = new bootstrap.Modal(document.getElementById('modalReportes'));
            modal.show();
        })
        .catch(error => {
            console.error("error al cargar reporte:", error);
            Swal.fire({
                icon: "error",
                title: "error",
                text: "no se pudo cargar la informacion del reporte"
            });
        });
}


// eliminar reporte
function eliminarReporte(id) {

    Swal.fire({
        title: 'estas seguro?',
        text: `se eliminara el reporte con ID: ${id}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'si, eliminar',
        cancelButtonText: 'cancelar'
    }).then((result) => {
        
        if (result.isConfirmed) {

            fetch(`php/reportes.php?accion=eliminar&id=${id}`)
                .then(response => response.text())
                .then(respuesta => {
                    
                    if (respuesta.includes("OK")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'eliminado',
                            text: 'el reporte ha sido eliminado correctamente',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        cargarReportes();
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
                        text: 'no se pudo eliminar el reporte'
                    });
                });
        }
    });
}

// funciones vacías para los botones de PDF y Excel
function generarReportePDF() {
    Swal.fire({
        icon: 'info',
        title: 'Función no implementada',
        text: 'La generación de PDF aún no está disponible'
    });
}

function generarReporteExcel() {
    Swal.fire({
        icon: 'info',
        title: 'Función no implementada',
        text: 'La generación de Excel aún no está disponible'
    });
}