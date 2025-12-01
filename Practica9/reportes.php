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

    <!-- jsPDF para generar PDFs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
    <!-- jsPDF autoTable para tablas en PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

    <!-- SheetJS para generar Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>

<body>

   <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">
            <img src="images/otrogatito (2).png" alt="Logo Clínica" width="60" height="60" class="rounded-circle mb-2">
            <br>Clínica
        </h4>
        
        <!-- Información del usuario -->
        <div class="text-center mb-3 px-3">
            <p class="text-white-50 small mb-1"><?php echo htmlspecialchars($nombreUsuario); ?></p>
            <span class="badge bg-info text-dark"><?php echo ucfirst($rolUsuario); ?></span>
        </div>

        <div class="sidebar-links">
            <a href="dash.php" class="active"><i class="fa-solid fa-house me-2"></i>Inicio</a>
            
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
            <a href="reportes.php"><i class="fa-solid fa-chart-line me-2"></i>Reportes</a>
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
                    <i class="fa-solid fa-chart-line me-2"></i>Reportes de Pagos
                </span>
                <div>
                    <button class="btn btn-danger text-white fw-semibold me-2" onclick="generarReportePDF()">
                        <i class="fa-solid fa-file-pdf me-2"></i>Generar PDF
                    </button>
                    <button class="btn btn-success text-white fw-semibold me-2" onclick="generarReporteExcel()">
                        <i class="fa-solid fa-file-excel me-2"></i>Generar Excel
                    </button>
                    <button class="btn text-white fw-semibold" style="background-color: #2c8888;" data-bs-toggle="modal" data-bs-target="#modalReportes">
                        <i class="fa-solid fa-plus me-2"></i>Agregar Registro
                    </button>
                </div>
            </div>
        </nav>

        <!-- Tarjetas de resumen -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fa-solid fa-file-invoice-dollar fa-3x text-success mb-3"></i>
                        <h3 class="fw-bold" id="totalPagos">0</h3>
                        <p class="text-muted">Total de Pagos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fa-solid fa-dollar-sign fa-3x text-info mb-3"></i>
                        <h3 class="fw-bold" id="montoTotal">$0.00</h3>
                        <p class="text-muted">Monto Total</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fa-solid fa-calendar-check fa-3x text-warning mb-3"></i>
                        <h3 class="fw-bold" id="pagosMes">0</h3>
                        <p class="text-muted">Pagos Este Mes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de reportes -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fa-solid fa-list me-2"></i>Historial de Reportes Generados
                </h5>
                <div class="table-responsive">
                    <table id="tablaReportes" class="table table-hover align-middle text-center">
                        <thead class="table-info">
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Fecha Generación</th>
                                <th>Ruta Archivo</th>
                                <th>Descripción</th>
                                <th>Generado por</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos dinámicos -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="modalReportes" tabindex="-1" aria-labelledby="modalReportesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background-color: #2c8888;">
                    <h5 class="modal-title" id="modalReportesLabel">
                        <i class="fa-solid fa-plus me-2"></i>Agregar Registro de Reporte
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="formReportes">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tipoReporte" class="form-label">Tipo de Reporte</label>
                            <select id="tipoReporte" name="tipoReporte" class="form-select" required>
                                <option value="">Selecciona tipo</option>
                                <option value="Pagos">Pagos</option>
                                <option value="Diagnostico">Diagnóstico</option>
                                <option value="Tratamiento">Tratamiento</option>
                                <option value="Seguimiento">Seguimiento</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="idPaciente" class="form-label">Id Paciente</label>
                            <input type="number" class="form-control" id="idPaciente" name="idPaciente">
                        </div>
                        <div class="mb-3">
                            <label for="idMedico" class="form-label">Id Médico</label>
                            <input type="number" class="form-control" id="idMedico" name="idMedico">
                        </div>
                        <div class="mb-3">
                            <label for="fechaGeneracion" class="form-label">Fecha de Generación</label>
                            <input type="date" class="form-control" id="fechaGeneracion" name="fechaGeneracion" required>
                        </div>
                        <div class="mb-3">
                            <label for="rutaArchivo" class="form-label">Ruta Archivo</label>
                            <input type="text" class="form-control" id="rutaArchivo" name="rutaArchivo">
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="generadoPor" class="form-label">Generado por</label>
                            <input type="text" class="form-control" id="generadoPor" name="generadoPor" value="<?php echo htmlspecialchars($nombreUsuario); ?>">
                        </div>
                    </div>

                    <!-- Botones dentro del form -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-save me-2"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Verificar que las librerías estén cargadas
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Verificando librerías...');
            console.log('jsPDF disponible:', typeof window.jspdf !== 'undefined');
            console.log('XLSX disponible:', typeof XLSX !== 'undefined');
            
            if (typeof window.jspdf === 'undefined') {
                console.error('jsPDF NO está cargado. Verifica la conexión a internet o el CDN.');
            }
            
            if (typeof XLSX === 'undefined') {
                console.error('XLSX NO está cargado. Verifica la conexión a internet o el CDN.');
            }
        });
    </script>
    
    <script src="js/reportes.js"></script>

    <script>
        // Establecer fecha actual por defecto
        document.addEventListener('DOMContentLoaded', function() {
            const hoy = new Date().toISOString().split('T')[0];
            document.getElementById('fechaGeneracion').value = hoy;

            // Cargar estadísticas
            cargarEstadisticas();
        });

        function cargarEstadisticas() {
            fetch('php/reportes.php?accion=datosPagos')
                .then(response => response.json())
                .then(data => {
                    const totalPagos = data.length;
                    const montoTotal = data.reduce((sum, p) => sum + parseFloat(p.Monto), 0);
                    
                    const mesActual = new Date().getMonth();
                    const añoActual = new Date().getFullYear();
                    const pagosMes = data.filter(p => {
                        const fecha = new Date(p.FechaPago);
                        return fecha.getMonth() === mesActual && fecha.getFullYear() === añoActual;
                    }).length;

                    document.getElementById('totalPagos').textContent = totalPagos;
                    document.getElementById('montoTotal').textContent = `$${montoTotal.toFixed(2)}`;
                    document.getElementById('pagosMes').textContent = pagosMes;
                })
                .catch(error => console.error('Error cargando estadísticas:', error));
        }
    </script>
</body>

</html>