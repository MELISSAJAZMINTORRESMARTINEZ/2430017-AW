document.addEventListener("DOMContentLoaded", () => {

    cargarReportes();

    const form = document.querySelector("#formUsuarios");
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        guardarReporte(new FormData(form));
    });

    document.getElementById('modalReportes').addEventListener('hidden.bs.modal', () => {
        form.reset();
        document.getElementById('idReporte').disabled = false;
        document.getElementById('modalReportesLabel').innerHTML =
            '<i class="fa-solid fa-user-plus me-2"></i>Agregar Reporte';

        const hidden = document.querySelector('input[name="idReporteEditar"]');
        if (hidden) hidden.remove();
    });
});


function cargarReportes() {

    fetch("php/reportes.php?accion=lista")
        .then(r => r.json())
        .then(data => {

            const tbody = document.querySelector("#tablaReportes tbody");
            tbody.innerHTML = "";

            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-muted">No hay reportes</td></tr>`;
                return;
            }

            data.forEach(r => {

                tbody.innerHTML += `
                <tr>
                    <td>${r.IdReporte}</td>
                    <td>${r.TipoReporte}</td>
                    <td>${r.FechaGeneracion}</td>
                    <td>${r.RutaArchivo ?? 'N/A'}</td>
                    <td>${r.Descripcion ?? 'Sin descripción'}</td>
                    <td>${r.GeneradoPor ?? 'N/A'}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editarReporte(${r.IdReporte})">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarReporte(${r.IdReporte})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });
        });
}


function guardarReporte(formData) {

    fetch("php/reportes.php", {
        method: "POST",
        body: formData
    })
        .then(r => r.text())
        .then(res => {

            if (!res.includes("OK")) {
                Swal.fire("Error", res, "error");
                return;
            }

            Swal.fire({
                icon: "success",
                title: res.includes("actualizado") ? "Reporte actualizado" : "Reporte guardado",
                timer: 1800,
                showConfirmButton: false
            });

            bootstrap.Modal.getInstance(document.getElementById("modalReportes")).hide();
            cargarReportes();
        });
}


function editarReporte(id) {

    fetch(`php/reportes.php?accion=obtener&id=${id}`)
        .then(r => r.json())
        .then(r => {

            document.getElementById('modalReportesLabel').innerHTML =
                '<i class="fa-solid fa-edit me-2"></i>Editar Reporte';

            document.getElementById('idReporte').value = r.IdReporte;
            document.getElementById('idReporte').disabled = true;

            document.getElementById('tipoReporte').value = r.TipoReporte;
            document.getElementById('fechaGeneracion').value = r.FechaGeneracion;
            document.getElementById('ruta').value = r.RutaArchivo;
            document.getElementById('descripcion').value = r.Descripcion;
            document.getElementById('generado').value = r.GeneradoPor;

            let hidden = document.createElement('input');
            hidden.type = "hidden";
            hidden.name = "idReporteEditar";
            hidden.value = r.IdReporte;

            document.getElementById('formUsuarios').appendChild(hidden);

            new bootstrap.Modal(document.getElementById('modalReportes')).show();
        });
}


function eliminarReporte(id) {

    Swal.fire({
        icon: "warning",
        title: "¿Eliminar?",
        text: "Esta acción no se puede deshacer",
        showCancelButton: true,
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar",
    })
        .then(result => {

            if (!result.isConfirmed) return;

            fetch(`php/reportes.php?accion=eliminar&id=${id}`)
                .then(r => r.text())
                .then(res => {

                    if (res.includes("OK")) {
                        Swal.fire("Eliminado", "El reporte fue eliminado", "success");
                        cargarReportes();
                    }
                });
        });
}


function generarReportePDF() {
    Swal.fire("PDF generado (demo)", "", "success");
}

function generarReporteExcel() {
    Swal.fire("Excel generado (demo)", "", "success");
}
