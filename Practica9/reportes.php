<?php require_once 'php/verificar_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
    <link rel="icon" type="image/png" href="images/New Patients.png">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/styleP.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

   <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">
            <img src="images/otrogatito (2).png" alt="Logo Clínica" width="60" height="60" class="rounded-circle mb-2">
            <br>Clínica
        </h4>
        
        <div class="text-center mb-3 px-3">
            <p class="text-white-50 small mb-1"><?php echo htmlspecialchars($nombreUsuario); ?></p>
            <span class="badge bg-info text-dark"><?php echo ucfirst($rolUsuario); ?></span>
        </div>

        <div class="sidebar-links">
            <a href="dash.php"><i class="fa-solid fa-house me-2"></i>Inicio</a>
            
            <?php if (tienePermiso('usuarios')): ?>
            <a href="usuarios.php"><i class="fa-solid fa-stethoscope me-2"></i>Usuario</a>
            <?php endif; ?>
            
             <?php if (tienePermiso('pacientes')): ?>
            <a href="pacientes.php"><i class="fa-solid fa-user-injured me-2"></i>Control de pacientes</a>
            <?php endif; ?>
            
            <?php if (tienePermiso('agenda')): ?>
            <a href="controlAgenda.php"><i class="fa-solid fa-calendar-days me-2"></i>Control de agenda</a>
            <?php endif; ?>
            
            <?php if (tienePermiso('medicos')): ?>
            <a href="medicos.php"><i class="fa-solid fa-user-doctor me-2"></i>Control de médicos</a>
            <?php endif; ?>
            
            <?php if (tienePermiso('reportes')): ?>
            <a href="reportes.php" class="active"><i class="fa-solid fa-chart-line me-2"></i>Reportes</a>
            <?php endif; ?>
            
            <?php if (tienePermiso('expedientes')): ?>
            <a href="expediente.php"><i class="fa-solid fa-notes-medical me-2"></i>Expediente Clínico</a>
            <?php endif; ?>
            
            <?php if (tienePermiso('pagos')): ?>
            <a href="pagos.php"><i class="fa-solid fa-money-check-dollar me-2"></i>Pagos</a>
            <?php endif; ?>
            
            <?php if (tienePermiso('tarifas')): ?>
            <a href="tarifas.php"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Gestor de tarifas</a>
            <?php endif; ?>
            
            <?php if (tienePermiso('bitacoras')): ?>
            <a href="bitacora.php"><i class="fa-solid fa-book me-2"></i>Bitácoras de usuarios</a>
            <?php endif; ?>
            
            <?php if (tienePermiso('especialidades')): ?>
            <a href="especialidades.php"><i class="fa-solid fa-stethoscope me-2"></i>Especialidades médicas</a>
            <?php endif; ?>
        </div>

        <hr>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión</a>
    </div>

    <!-- Contenido -->
    <div class="content" style="margin-left: 230px; padding: 30px;">
        <!-- Navbar superior -->
        <nav class="navbar navbar-expand-lg navbar-light mb-4">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <span class="navbar-brand mb-0 h4 fw-bold text-secondary">
                    <i class="fa-solid fa-chart-line me-2"></i>Historial de Reportes
                </span>
                <div>
                    <button class="btn btn-danger text-white fw-semibold me-2" onclick="generarReportePDF()">
                        <i class="fa-solid fa-file-pdf me-2"></i>Generar PDF
                    </button>
                    <button class="btn btn-success text-white fw-semibold" onclick="generarReporteExcel()">
                        <i class="fa-solid fa-file-excel me-2"></i>Generar Excel
                    </button>
                </div>
            </div>
        </nav>

        <!-- Filtros -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header" style="background-color: #2c8888;">
                <h5 class="text-white mb-0">
                    <i class="fa-solid fa-filter me-2"></i>Filtros de Búsqueda
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tipo de Reporte</label>
                        <select class="form-select" id="filtroTipo" onchange="filtrarReportes()">
                            <option value="">Todos</option>
                            <option value="Pagos">Pagos</option>
                            <option value="Citas">Citas</option>
                            <option value="Pacientes">Pacientes</option>
                            <option value="Médicos">Médicos</option>
                            <option value="Financiero">Financiero</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha Desde</label>
                        <input type="date" class="form-control" id="filtroFechaDesde" onchange="filtrarReportes()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha Hasta</label>
                        <input type="date" class="form-control" id="filtroFechaHasta" onchange="filtrarReportes()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Generado Por</label>
                        <input type="text" class="form-control" id="filtroGeneradoPor" placeholder="Nombre..." onkeyup="filtrarReportes()">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-secondary" onclick="limpiarFiltros()">
                            <i class="fa-solid fa-eraser me-2"></i>Limpiar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Historial -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaReportes" class="table table-hover align-middle">
                        <thead class="table-info">
                            <tr>
                                <th>ID</th>
                                <th>Tipo de Reporte</th>
                                <th>ID Paciente</th>
                                <th>ID Médico</th>
                                <th>Fecha de Generación</th>
                                <th>Ruta Archivo</th>
                                <th>Descripción</th>
                                <th>Generado por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Reporte (opcional) -->
    <div class="modal fade" id="modalEditarReporte" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #2c8888;">
                    <h5 class="modal-title text-white">
                        <i class="fa-solid fa-edit me-2"></i>Editar Reporte
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarReporte">
                        <input type="hidden" name="idReporteEditar" id="idReporteEditar">
                        
                        <div class="mb-3">
                            <label for="editTipoReporte" class="form-label">Tipo de Reporte</label>
                            <select id="editTipoReporte" name="tipoReporte" class="form-select" required>
                                <option value="Pagos">Pagos</option>
                                <option value="Citas">Citas</option>
                                <option value="Pacientes">Pacientes</option>
                                <option value="Médicos">Médicos</option>
                                <option value="Financiero">Financiero</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="editIdPaciente" class="form-label">ID Paciente</label>
                            <input type="number" class="form-control" id="editIdPaciente" name="idPaciente">
                        </div>

                        <div class="mb-3">
                            <label for="editIdMedico" class="form-label">ID Médico</label>
                            <input type="number" class="form-control" id="editIdMedico" name="idMedico">
                        </div>

                        <div class="mb-3">
                            <label for="editFechaGeneracion" class="form-label">Fecha de Generación</label>
                            <input type="date" class="form-control" id="editFechaGeneracion" name="fechaGeneracion">
                        </div>

                        <div class="mb-3">
                            <label for="editRuta" class="form-label">Ruta Archivo</label>
                            <input type="text" class="form-control" id="editRuta" name="ruta">
                        </div>

                        <div class="mb-3">
                            <label for="editDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="editGenerado" class="form-label">Generado por</label>
                            <input type="text" class="form-control" id="editGenerado" name="generado">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn text-white" style="background-color: #2c8888;" onclick="guardarEdicion()">
                        <i class="fa-solid fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/reportes.js"></script>
    
    <script>
        // Variable global para almacenar todos los reportes
        let todosLosReportes = [];

        // Cargar reportes al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarReportes();
        });

        // Función de filtrado
        function filtrarReportes() {
            const tipo = document.getElementById('filtroTipo').value.toLowerCase();
            const fechaDesde = document.getElementById('filtroFechaDesde').value;
            const fechaHasta = document.getElementById('filtroFechaHasta').value;
            const generadoPor = document.getElementById('filtroGeneradoPor').value.toLowerCase();

            const tbody = document.querySelector("#tablaReportes tbody");
            tbody.innerHTML = "";

            const reportesFiltrados = todosLosReportes.filter(r => {
                let cumpleTipo = !tipo || r.TipoReporte.toLowerCase().includes(tipo);
                let cumpleFechaDesde = !fechaDesde || r.FechaGeneracion >= fechaDesde;
                let cumpleFechaHasta = !fechaHasta || r.FechaGeneracion <= fechaHasta;
                let cumpleGeneradoPor = !generadoPor || (r.GeneradoPor && r.GeneradoPor.toLowerCase().includes(generadoPor));

                return cumpleTipo && cumpleFechaDesde && cumpleFechaHasta && cumpleGeneradoPor;
            });

            if (reportesFiltrados.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No se encontraron reportes con los filtros aplicados</td></tr>';
                return;
            }

            reportesFiltrados.forEach(r => {
                const colorBadge = {
                    'Pagos': 'bg-success',
                    'Citas': 'bg-primary',
                    'Pacientes': 'bg-info',
                    'Médicos': 'bg-warning',
                    'Financiero': 'bg-danger'
                };

                const fila = `
                <tr>
                    <td>${r.IdReporte}</td>
                    <td><span class="badge ${colorBadge[r.TipoReporte] || 'bg-secondary'}">${r.TipoReporte}</span></td>
                    <td>${r.IdPaciente || '-'}</td>
                    <td>${r.IdMedico || '-'}</td>
                    <td>${r.FechaGeneracion}</td>
                    <td>${r.RutaArchivo ? '<i class="fa-solid fa-file text-primary"></i> Disponible' : '<span class="text-muted">Sin ruta</span>'}</td>
                    <td>${r.Descripcion || 'Sin descripción'}</td>
                    <td>${r.GeneradoPor || 'No especificado'}</td>
                    <td>
                        ${r.RutaArchivo ? `<button class="btn btn-sm btn-outline-primary me-1" onclick="descargarReporte('${r.RutaArchivo}')" title="Descargar">
                            <i class="fa-solid fa-download"></i>
                        </button>` : ''}
                        <button class="btn btn-sm btn-outline-warning me-1" onclick="editarReporte(${r.IdReporte})" title="Editar">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarReporte(${r.IdReporte})" title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += fila;
            });
        }

        // Limpiar filtros
        function limpiarFiltros() {
            document.getElementById('filtroTipo').value = '';
            document.getElementById('filtroFechaDesde').value = '';
            document.getElementById('filtroFechaHasta').value = '';
            document.getElementById('filtroGeneradoPor').value = '';
            filtrarReportes();
        }

        // Función para descargar reporte
        function descargarReporte(ruta) {
            if (ruta) {
                window.open(ruta, '_blank');
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin archivo',
                    text: 'Este reporte no tiene archivo asociado'
                });
            }
        }

        // Guardar edición
        function guardarEdicion() {
            const formData = new FormData(document.getElementById('formEditarReporte'));
            guardarReporte(formData);
        }

        // Sobrescribir la función cargarReportes original para usar filtros
        const cargarReportesOriginal = window.cargarReportes;
        window.cargarReportes = function() {
            fetch("php/reportes.php?accion=lista")
                .then(response => response.json())
                .then(data => {
                    todosLosReportes = data;
                    filtrarReportes();
                })
                .catch(err => {
                    console.error("error cargando reportes:", err);
                    Swal.fire({
                        icon: "error",
                        title: "Error al cargar",
                        text: "No se pudieron cargar los reportes"
                    });
                });
        };
    </script>
</body>
</html>