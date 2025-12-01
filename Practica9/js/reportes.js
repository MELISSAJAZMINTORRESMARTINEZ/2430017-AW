document.addEventListener("DOMContentLoaded", function () {
    cargarReportes();

    const form = document.querySelector("#formReportes");
    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            guardarReporte(new FormData(form));
        });
    }

    // Limpiar modal al cerrar
    document.getElementById('modalReportes').addEventListener('hidden.bs.modal', function () {
        document.querySelector("#formReportes").reset();
        document.getElementById('modalReportesLabel').innerHTML = 
            '<i class="fa-solid fa-plus me-2"></i>Agregar Reporte';
    });
});

// Cargar lista de reportes
function cargarReportes() {
    fetch("php/reportes.php?accion=lista")
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector("#tablaReportes tbody");
            tbody.innerHTML = "";

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No hay reportes generados</td></tr>';
                return;
            }

            data.forEach(r => {
                const fila = `
                <tr>
                    <td>${r.IdReporte}</td>
                    <td><span class="badge bg-info">${r.TipoReporte}</span></td>
                    <td>${r.NombrePaciente || 'ID: ' + r.IdPaciente}</td>
                    <td>${r.NombreMedico || (r.IdMedico ? 'ID: ' + r.IdMedico : 'N/A')}</td>
                    <td>${r.FechaGeneracion}</td>
                    <td><small>${r.RutaArchivo || 'N/A'}</small></td>
                    <td>${r.Descripcion || '-'}</td>
                    <td>${r.GeneradoPor || '-'}</td>
                </tr>`;
                tbody.innerHTML += fila;
            });
        })
        .catch(err => {
            console.error("Error cargando reportes:", err);
        });
}

// Guardar registro de reporte
function guardarReporte(formData) {
    formData.append('accion', 'registrar');
    
    fetch("php/reportes.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {
            if (respuesta.includes("OK")) {
                Swal.fire({
                    icon: "success",
                    title: "Reporte guardado",
                    timer: 1800,
                    showConfirmButton: false
                });

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalReportes"));
                modal.hide();

                cargarReportes();
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
                text: "No se pudo guardar el reporte"
            });
        });
}

// Generar reporte en PDF
function generarReportePDF() {
    Swal.fire({
        title: 'Generar Reporte PDF',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">ID Paciente (opcional)</label>
                    <input type="number" id="pdfIdPaciente" class="form-control" placeholder="Dejar vacío para todos">
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" id="pdfFechaInicio" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" id="pdfFechaFin" class="form-control">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Generar PDF',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            return {
                idPaciente: document.getElementById('pdfIdPaciente').value,
                fechaInicio: document.getElementById('pdfFechaInicio').value,
                fechaFin: document.getElementById('pdfFechaFin').value
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            generarPDF(result.value);
        }
    });
}

function generarPDF(filtros) {
    console.log('Iniciando generación de PDF con filtros:', filtros);
    
    let url = 'php/reportes.php?accion=datosPagos';
    if (filtros.idPaciente) url += `&idPaciente=${filtros.idPaciente}`;
    if (filtros.fechaInicio) url += `&fechaInicio=${filtros.fechaInicio}`;
    if (filtros.fechaFin) url += `&fechaFin=${filtros.fechaFin}`;

    console.log('URL de consulta:', url);

    fetch(url)
        .then(response => {
            console.log('Respuesta recibida:', response);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            
            if (data.length === 0) {
                Swal.fire('Sin datos', 'No hay pagos que coincidan con los filtros', 'info');
                return;
            }

            try {
                // Verificar que jsPDF esté disponible
                if (typeof window.jspdf === 'undefined') {
                    throw new Error('jsPDF no está cargado');
                }

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                // Título
                doc.setFontSize(18);
                doc.text('Reporte de Pagos', 105, 15, { align: 'center' });
                
                doc.setFontSize(10);
                doc.text(`Fecha de generación: ${new Date().toLocaleDateString('es-MX')}`, 105, 22, { align: 'center' });

                // Calcular totales
                const totalMonto = data.reduce((sum, p) => sum + parseFloat(p.Monto), 0);

                doc.setFontSize(11);
                doc.text(`Total de pagos: ${data.length}`, 14, 35);
                doc.text(`Monto total: ${totalMonto.toFixed(2)}`, 14, 42);

                // Preparar datos para autoTable
                const tableData = data.map(pago => [
                    pago.IdPago,
                    (pago.NombrePaciente || 'N/A').substring(0, 25),
                    pago.FechaPago,
                    pago.MetodoPago,
                    `${parseFloat(pago.Monto).toFixed(2)}`,
                    (pago.NombreMedico || 'N/A').substring(0, 20)
                ]);

                // Crear tabla con autoTable si está disponible
                if (typeof doc.autoTable === 'function') {
                    doc.autoTable({
                        startY: 50,
                        head: [['ID', 'Paciente', 'Fecha', 'Método', 'Monto', 'Médico']],
                        body: tableData,
                        theme: 'striped',
                        headStyles: { fillColor: [44, 136, 136] },
                        styles: { fontSize: 9 },
                        columnStyles: {
                            0: { cellWidth: 15 },
                            1: { cellWidth: 45 },
                            2: { cellWidth: 25 },
                            3: { cellWidth: 30 },
                            4: { cellWidth: 25 },
                            5: { cellWidth: 40 }
                        }
                    });
                } else {
                    // Fallback sin autoTable
                    let y = 50;
                    doc.setFontSize(9);

                    // Encabezados
                    doc.setFont(undefined, 'bold');
                    doc.text('ID', 14, y);
                    doc.text('Paciente', 30, y);
                    doc.text('Fecha', 90, y);
                    doc.text('Método', 120, y);
                    doc.text('Monto', 155, y);
                    doc.text('Médico', 180, y);
                    
                    y += 7;
                    doc.setFont(undefined, 'normal');

                    // Datos
                    data.forEach((pago) => {
                        if (y > 270) {
                            doc.addPage();
                            y = 20;
                        }

                        doc.text(pago.IdPago.toString(), 14, y);
                        doc.text((pago.NombrePaciente || 'N/A').substring(0, 25), 30, y);
                        doc.text(pago.FechaPago, 90, y);
                        doc.text(pago.MetodoPago, 120, y);
                        doc.text(`${parseFloat(pago.Monto).toFixed(2)}`, 155, y);
                        doc.text((pago.NombreMedico || 'N/A').substring(0, 15), 180, y);

                        y += 7;
                    });
                }

                // Guardar PDF
                const nombreArchivo = `reporte_pagos_${new Date().getTime()}.pdf`;
                doc.save(nombreArchivo);

                Swal.fire({
                    icon: 'success',
                    title: 'PDF Generado',
                    text: 'El reporte se ha descargado correctamente',
                    timer: 2000
                });

            } catch (error) {
                console.error('Error generando PDF:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al generar PDF',
                    text: error.message || 'No se pudo generar el PDF. Verifica la consola para más detalles.'
                });
            }
        })
        .catch(error => {
            console.error('Error obteniendo datos:', error);
            Swal.fire('Error', 'No se pudieron obtener los datos para el reporte', 'error');
        });
}

// Generar reporte en Excel
function generarReporteExcel() {
    Swal.fire({
        title: 'Generar Reporte Excel',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">ID Paciente (opcional)</label>
                    <input type="number" id="excelIdPaciente" class="form-control" placeholder="Dejar vacío para todos">
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" id="excelFechaInicio" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" id="excelFechaFin" class="form-control">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Generar Excel',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            return {
                idPaciente: document.getElementById('excelIdPaciente').value,
                fechaInicio: document.getElementById('excelFechaInicio').value,
                fechaFin: document.getElementById('excelFechaFin').value
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            generarExcel(result.value);
        }
    });
}

function generarExcel(filtros) {
    console.log('Iniciando generación de Excel con filtros:', filtros);
    
    let url = 'php/reportes.php?accion=datosPagos';
    if (filtros.idPaciente) url += `&idPaciente=${filtros.idPaciente}`;
    if (filtros.fechaInicio) url += `&fechaInicio=${filtros.fechaInicio}`;
    if (filtros.fechaFin) url += `&fechaFin=${filtros.fechaFin}`;

    console.log('URL de consulta:', url);

    fetch(url)
        .then(response => {
            console.log('Respuesta recibida:', response);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            
            if (data.length === 0) {
                Swal.fire('Sin datos', 'No hay pagos que coincidan con los filtros', 'info');
                return;
            }

            try {
                // Verificar que XLSX esté disponible
                if (typeof XLSX === 'undefined') {
                    throw new Error('SheetJS (XLSX) no está cargado');
                }

                // Preparar datos para Excel
                const datosExcel = data.map(pago => ({
                    'ID Pago': pago.IdPago,
                    'Paciente': pago.NombrePaciente || 'N/A',
                    'Teléfono': pago.TelefonoPaciente || 'N/A',
                    'Correo': pago.CorreoPaciente || 'N/A',
                    'Médico': pago.NombreMedico || 'N/A',
                    'Especialidad': pago.NombreEspecialidad || 'N/A',
                    'Fecha Cita': pago.FechaCita || 'N/A',
                    'Motivo': pago.MotivoConsulta || 'N/A',
                    'Monto': parseFloat(pago.Monto).toFixed(2),
                    'Método de Pago': pago.MetodoPago,
                    'Fecha de Pago': pago.FechaPago,
                    'Referencia': pago.Referencia || 'N/A',
                    'Estatus': pago.EstatusPago
                }));

                // Calcular totales
                const totalMonto = data.reduce((sum, p) => sum + parseFloat(p.Monto), 0);

                // Agregar fila de totales
                datosExcel.push({
                    'ID Pago': '',
                    'Paciente': '',
                    'Teléfono': '',
                    'Correo': '',
                    'Médico': '',
                    'Especialidad': '',
                    'Fecha Cita': '',
                    'Motivo': 'TOTAL',
                    'Monto': totalMonto.toFixed(2),
                    'Método de Pago': '',
                    'Fecha de Pago': '',
                    'Referencia': '',
                    'Estatus': ''
                });

                // Crear libro de Excel
                const ws = XLSX.utils.json_to_sheet(datosExcel);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Pagos");

                // Ajustar ancho de columnas
                const colWidths = [
                    { wch: 10 }, { wch: 30 }, { wch: 15 }, { wch: 25 },
                    { wch: 25 }, { wch: 20 }, { wch: 12 }, { wch: 30 },
                    { wch: 12 }, { wch: 15 }, { wch: 12 }, { wch: 15 }, { wch: 12 }
                ];
                ws['!cols'] = colWidths;

                // Descargar archivo
                const nombreArchivo = `reporte_pagos_${new Date().getTime()}.xlsx`;
                XLSX.writeFile(wb, nombreArchivo);

                Swal.fire({
                    icon: 'success',
                    title: 'Excel Generado',
                    text: 'El reporte se ha descargado correctamente',
                    timer: 2000
                });

            } catch (error) {
                console.error('Error generando Excel:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al generar Excel',
                    text: error.message || 'No se pudo generar el archivo Excel. Verifica la consola para más detalles.'
                });
            }
        })
        .catch(error => {
            console.error('Error obteniendo datos:', error);
            Swal.fire('Error', 'No se pudieron obtener los datos para el reporte', 'error');
        });
}