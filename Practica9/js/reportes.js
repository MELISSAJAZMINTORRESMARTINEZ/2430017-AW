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

// generar reporte PDF de pagos
function generarReportePDF() {
    Swal.fire({
        title: 'generando reporte PDF...',
        text: 'por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch("php/reportes.php?accion=datosPagos")
        .then(response => response.json())
        .then(pagos => {
            if (pagos.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'sin datos',
                    text: 'no hay pagos registrados para generar el reporte'
                });
                return;
            }

            // calcular totales
            const totalPagos = pagos.reduce((sum, p) => sum + parseFloat(p.Monto), 0);
            const pagosPagados = pagos.filter(p => p.EstatusPago === 'Pagado').length;
            const pagosPendientes = pagos.filter(p => p.EstatusPago === 'Pendiente').length;

            // crear ventana con contenido HTML para imprimir
            const ventana = window.open('', '_blank');
            ventana.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Reporte de Pagos</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 20px;
                            color: #333;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 30px;
                            border-bottom: 3px solid #2c8888;
                            padding-bottom: 15px;
                        }
                        .header h1 {
                            color: #2c8888;
                            margin: 0;
                        }
                        .fecha-reporte {
                            color: #666;
                            font-size: 14px;
                            margin-top: 5px;
                        }
                        .resumen {
                            background: #f8f9fa;
                            padding: 15px;
                            border-radius: 8px;
                            margin-bottom: 20px;
                            display: flex;
                            justify-content: space-around;
                            text-align: center;
                        }
                        .resumen-item {
                            flex: 1;
                        }
                        .resumen-item h3 {
                            margin: 0;
                            color: #2c8888;
                        }
                        .resumen-item p {
                            margin: 5px 0 0 0;
                            font-size: 24px;
                            font-weight: bold;
                        }
                        table { 
                            width: 100%; 
                            border-collapse: collapse;
                            margin-top: 20px;
                        }
                        th, td { 
                            border: 1px solid #ddd; 
                            padding: 10px; 
                            text-align: left;
                            font-size: 12px;
                        }
                        th { 
                            background-color: #2c8888; 
                            color: white;
                            font-weight: bold;
                        }
                        tr:nth-child(even) {
                            background-color: #f8f9fa;
                        }
                        .badge {
                            padding: 4px 8px;
                            border-radius: 4px;
                            font-size: 11px;
                            font-weight: bold;
                        }
                        .badge-success { background: #28a745; color: white; }
                        .badge-warning { background: #ffc107; color: #333; }
                        .badge-danger { background: #dc3545; color: white; }
                        .badge-info { background: #17a2b8; color: white; }
                        .footer {
                            margin-top: 30px;
                            text-align: center;
                            color: #666;
                            font-size: 12px;
                            border-top: 1px solid #ddd;
                            padding-top: 15px;
                        }
                        @media print {
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Reporte de Pagos</h1>
                        <p class="fecha-reporte">Generado: ${new Date().toLocaleString('es-MX')}</p>
                    </div>

                    <div class="resumen">
                        <div class="resumen-item">
                            <h3>Total Pagos</h3>
                            <p>${pagos.length}</p>
                        </div>
                        <div class="resumen-item">
                            <h3>Pagados</h3>
                            <p style="color: #28a745;">${pagosPagados}</p>
                        </div>
                        <div class="resumen-item">
                            <h3>Pendientes</h3>
                            <p style="color: #ffc107;">${pagosPendientes}</p>
                        </div>
                        <div class="resumen-item">
                            <h3>Monto Total</h3>
                            <p style="color: #2c8888;">${totalPagos.toFixed(2)}</p>
                        </div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Estado</th>
                                <th>Referencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${pagos.map(p => `
                                <tr>
                                    <td>${p.IdPago}</td>
                                    <td>${p.NombrePaciente || 'N/A'}</td>
                                    <td>${p.FechaPago}</td>
                                    <td>${parseFloat(p.Monto).toFixed(2)}</td>
                                    <td><span class="badge badge-info">${p.MetodoPago}</span></td>
                                    <td>
                                        <span class="badge ${
                                            p.EstatusPago === 'Pagado' ? 'badge-success' :
                                            p.EstatusPago === 'Pendiente' ? 'badge-warning' : 'badge-danger'
                                        }">${p.EstatusPago}</span>
                                    </td>
                                    <td>${p.Referencia || '-'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>

                    <div class="footer">
                        <p><strong>Clínica</strong> - Sistema de Gestión de Pagos</p>
                        <p>Este documento es un reporte generado automáticamente</p>
                    </div>

                    <div class="no-print" style="margin-top: 20px; text-align: center;">
                        <button onclick="window.print()" style="
                            background: #2c8888;
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 5px;
                            cursor: pointer;
                            font-size: 16px;
                            margin-right: 10px;
                        ">Imprimir / Guardar como PDF</button>
                        <button onclick="window.close()" style="
                            background: #6c757d;
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 5px;
                            cursor: pointer;
                            font-size: 16px;
                        "> Cerrar</button>
                    </div>
                </body>
                </html>
            `);
            ventana.document.close();

            Swal.close();
            
            Swal.fire({
                icon: 'success',
                title: 'reporte generado',
                text: 'el reporte PDF se ha abierto en una nueva ventana',
                timer: 2000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            console.error("error generando PDF:", error);
            Swal.fire({
                icon: 'error',
                title: 'error',
                text: 'no se pudo generar el reporte PDF'
            });
        });
}

// generar reporte Excel de pagos
function generarReporteExcel() {
    Swal.fire({
        title: 'generando reporte Excel...',
        text: 'por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch("php/reportes.php?accion=datosPagos")
        .then(response => response.json())
        .then(pagos => {
            if (pagos.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'sin datos',
                    text: 'no hay pagos registrados para generar el reporte'
                });
                return;
            }

            // crear tabla HTML para Excel
            let tablaExcel = `
                <table>
                    <thead>
                        <tr>
                            <th>ID Pago</th>
                            <th>ID Cita</th>
                            <th>ID Paciente</th>
                            <th>Nombre Paciente</th>
                            <th>Monto</th>
                            <th>Método de Pago</th>
                            <th>Fecha de Pago</th>
                            <th>Referencia</th>
                            <th>Estado</th>
                            <th>Fecha Cita</th>
                            <th>Motivo Consulta</th>
                            <th>Médico</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            pagos.forEach(p => {
                tablaExcel += `
                    <tr>
                        <td>${p.IdPago}</td>
                        <td>${p.IdCita}</td>
                        <td>${p.IdPaciente}</td>
                        <td>${p.NombrePaciente || 'N/A'}</td>
                        <td>${parseFloat(p.Monto).toFixed(2)}</td>
                        <td>${p.MetodoPago}</td>
                        <td>${p.FechaPago}</td>
                        <td>${p.Referencia || '-'}</td>
                        <td>${p.EstatusPago}</td>
                        <td>${p.FechaCita || 'N/A'}</td>
                        <td>${p.MotivoConsulta || 'N/A'}</td>
                        <td>${p.NombreMedico || 'N/A'}</td>
                    </tr>
                `;
            });

            tablaExcel += `
                    </tbody>
                </table>
            `;

            // crear archivo Excel
            const nombreArchivo = `Reporte_Pagos_${new Date().toISOString().split('T')[0]}.xls`;
            const uri = 'data:application/vnd.ms-excel;base64,';
            const template = `
                <html xmlns:o="urn:schemas-microsoft-com:office:office" 
                      xmlns:x="urn:schemas-microsoft-com:office:excel" 
                      xmlns="http://www.w3.org/TR/REC-html40">
                <head>
                    <meta charset="UTF-8">
                    <!--[if gte mso 9]>
                    <xml>
                        <x:ExcelWorkbook>
                            <x:ExcelWorksheets>
                                <x:ExcelWorksheet>
                                    <x:Name>Reporte de Pagos</x:Name>
                                    <x:WorksheetOptions>
                                        <x:DisplayGridlines/>
                                    </x:WorksheetOptions>
                                </x:ExcelWorksheet>
                            </x:ExcelWorksheets>
                        </x:ExcelWorkbook>
                    </xml>
                    <![endif]-->
                    <style>
                        table { border-collapse: collapse; width: 100%; }
                        th { background-color: #2c8888; color: white; font-weight: bold; border: 1px solid #000; padding: 8px; }
                        td { border: 1px solid #000; padding: 8px; }
                    </style>
                </head>
                <body>
                    <h2>Reporte de Pagos - Clínica</h2>
                    <p>Fecha de generación: ${new Date().toLocaleString('es-MX')}</p>
                    ${tablaExcel}
                </body>
                </html>
            `;

            // descargar archivo
            const link = document.createElement('a');
            link.href = uri + btoa(unescape(encodeURIComponent(template)));
            link.download = nombreArchivo;
            link.click();

            Swal.close();
            
            Swal.fire({
                icon: 'success',
                title: 'reporte generado',
                text: 'el archivo Excel se ha descargado correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            console.error("error generando Excel:", error);
            Swal.fire({
                icon: 'error',
                title: 'error',
                text: 'no se pudo generar el reporte Excel'
            });
        });
}