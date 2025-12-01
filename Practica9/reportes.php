<?php require_once 'php/verificar_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" type="image/png" href="images/New Patients.png">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/styleP.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">
            <img src="images/otrogatito (2).png" width="60" class="rounded-circle mb-2">
            <br>Clínica
        </h4>

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
            <a href="controlAgenda.php"><i class="fa-solid fa-calendar-days me-2"></i>Agenda</a>
            <?php endif; ?>

            <?php if (tienePermiso('medicos')): ?>
            <a href="medicos.php"><i class="fa-solid fa-user-doctor me-2"></i>Médicos</a>
            <?php endif; ?>

            <?php if (tienePermiso('reportes')): ?>
            <a href="reportes.php"><i class="fa-solid fa-chart-line me-2"></i>Reportes</a>
            <?php endif; ?>

            <?php if (tienePermiso('expedientes')): ?>
            <a href="expediente.php"><i class="fa-solid fa-notes-medical me-2"></i>Expediente</a>
            <?php endif; ?>
        </div>

        <hr>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión</a>
    </div>



    <!-- Contenido -->
    <div class="content" style="margin-left: 230px; padding: 30px;">

        <nav class="navbar navbar-light mb-4">
            <span class="navbar-brand h4 fw-bold text-secondary">
                <i class="fa-solid fa-user-injured me-2"></i>Reportes
            </span>

            <div>
                <button class="btn btn-danger me-2" onclick="generarReportePDF()">
                    <i class="fa-solid fa-file-pdf me-2"></i>PDF
                </button>

                <button class="btn btn-success me-2" onclick="generarReporteExcel()">
                    <i class="fa-solid fa-file-excel me-2"></i>Excel
                </button>

                <button class="btn text-white" style="background:#2c8888;" data-bs-toggle="modal"
                    data-bs-target="#modalReportes">
                    <i class="fa-solid fa-plus me-2"></i>Agregar
                </button>
            </div>
        </nav>


        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table id="tablaReportes" class="table table-hover text-center align-middle">
                    <thead class="table-info">
                        <tr>
                            <th>IdReporte</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Ruta</th>
                            <th>Descripción</th>
                            <th>Generado por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="modalReportes" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">

                <div class="modal-header text-white" style="background:#2c8888;">
                    <h5 id="modalReportesLabel">
                        <i class="fa-solid fa-user-plus me-2"></i>Agregar Reporte
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="formUsuarios">
                    <div class="modal-body">

                        <label class="form-label">Id Reporte</label>
                        <input type="number" class="form-control" id="idReporte" name="idReporte" required>

                        <label class="form-label mt-2">Tipo Reporte</label>
                        <select class="form-select" id="tipoReporte" name="tipoReporte" required>
                            <option value="">Selecciona</option>
                            <option value="Pagos">Pagos</option>
                        </select>

                        <label class="form-label mt-2">Fecha</label>
                        <input type="date" class="form-control" id="fechaGeneracion" name="fechaGeneracion">

                        <label class="form-label mt-2">Ruta Archivo</label>
                        <input type="text" class="form-control" id="ruta" name="ruta">

                        <label class="form-label mt-2">Descripción</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion">

                        <label class="form-label mt-2">Generado por</label>
                        <input type="text" class="form-control" id="generado" name="generado">

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-success">
                            <i class="fa-solid fa-save me-2"></i>Guardar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/reportes.js"></script>

</body>
</html>
