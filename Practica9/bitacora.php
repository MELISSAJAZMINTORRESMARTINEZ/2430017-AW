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
                    <i class="fa-solid fa-book me-2"></i>Bitacora de Usuarios
                </span>
                <button class="btn btn-success text-white fw-semibold" data-bs-toggle="modal" style="background-color: #2c8888;" data-bs-target="#modalBitacora">
                    <i class="fa-solid fa-plus me-2"></i>Agregar Bitacora
                </button>
            </div>
        </nav>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaBitacora" class="table table-hover align-middle text-center">
                        <thead class="table-info">
                            <tr>
                                <th>Id Bitacora</th>
                                <th>Id Usuario</th>
                                <th>Fecha Acceso</th>
                                <th>Accion Realizada</th>
                                <th>Modulo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="modalBitacora" tabindex="-1" aria-labelledby="modalTarifaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background-color: #2c8888;">
                    <h5 class="modal-title" id="modalBitacoraLabel">
                        <i class="fa-solid fa-file-invoice-dollar me-2"></i>Agregar Bitacora
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="formBitacora">
                        <div class="mb-3">
                            <label for="idUsuario" class="form-label">Id Usuario</label>
                            <input type="text" class="form-control" id="idUsuario" name="idUsuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="fechaAcceso" class="form-label">Fecha Acceso</label>
                            <input type="date" class="form-control" id="fechaAcceso" name="fechaAcceso" required>
                        </div>
                         <div class="mb-3">
                        <label for="accionRealizada" class="form-label">Acción Realizada <span class="text-danger">*</span></label>
                        <select id="accionRealizada" name="accionRealizada" class="form-select" required>
                            <option value="">Selecciona una acción</option>
                            <option value="Inicio de sesión">Inicio de sesión</option>
                            <option value="Cierre de sesión">Cierre de sesión</option>
                            <option value="Creación de registro">Creación de registros</option>
                            <option value="Modificación de registro">Modificación de registros</option>
                            <option value="Eliminación de registro">Eliminación de registros</option>
                            <option value="Exportación de datos">Exportación de datos</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="modulo" class="form-label">Módulo <span class="text-danger">*</span></label>
                        <select id="modulo" name="modulo" class="form-select" required>
                            <option value="">Selecciona un módulo</option>
                            <option value="Usuarios">Usuarios</option>
                            <option value="Pacientes">Pacientes</option>
                            <option value="Agenda">Agenda</option>
                            <option value="Médicos">Médicos</option>
                            <option value="Expedientes">Expedientes</option>
                            <option value="Pagos">Pagos</option>
                            <option value="Tarifas">Tarifas</option>
                            <option value="Reportes">Reportes</option>
                            <option value="Bitácoras">Bitácoras</option>
                            <option value="Especialidades">Especialidades</option>
                        </select>
                    </div>
                    <div class="mb-3">
                            <label for="acciones" class="form-label">Acciones</label>
                            <input type="text" class="form-control" id="acciones" name="acciones" required>             
                    </div>

                    <!-- Botones dentro del form -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bitacora.js"></script>

</body>

</html>