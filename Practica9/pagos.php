<?php require_once 'php/verificar_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pagos</title>
    <link rel="icon" type="image/png" href="images/New Patients.png">
    <link rel="stylesheet" href="css/dashboard.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">
            <img src="images/otrogatito (2).png" alt="Logo Clínica" width="65" height="65" class="rounded-circle mb-2">
            <br>Mi Clínica
        </h4>
        
        <!-- Información del usuario -->
        <div class="text-center mb-3 px-3">
            <p class="text-white small mb-2 fw-500"><?php echo htmlspecialchars($nombreUsuario); ?></p>
            <span class="badge bg-info"><?php echo ucfirst($rolUsuario); ?></span>
        </div>

        <div class="sidebar-links">
            <a href="dash.php"><i class="fa-solid fa-house me-2"></i><span>Inicio</span></a>
            
            <?php if (tienePermiso('usuarios')): ?>
            <a href="usuarios.php"><i class="fa-solid fa-users me-2"></i><span>Usuarios</span></a>
            <?php endif; ?>
            
            <?php if (tienePermiso('pacientes')): ?>
            <a href="pacientes.php"><i class="fa-solid fa-user-injured me-2"></i><span>Control de pacientes</span></a>
            <?php endif; ?>
            
            <?php if (tienePermiso('agenda')): ?>
            <a href="controlAgenda.php"><i class="fa-solid fa-calendar-days me-2"></i><span>Control de agenda</span></a>
            <?php endif; ?>
            
            <?php if (tienePermiso('medicos')): ?>
            <a href="medicos.php"><i class="fa-solid fa-user-doctor me-2"></i><span>Control de médicos</span></a>
            <?php endif; ?>
            
            <?php if (tienePermiso('reportes')): ?>
            <a href="reportes.php"><i class="fa-solid fa-chart-line me-2"></i><span>Reportes</span></a>
            <?php endif; ?>
            
            <?php if (tienePermiso('expedientes')): ?>
            <a href="expediente.php"><i class="fa-solid fa-notes-medical me-2"></i><span>Expediente Clínico</span></a>
            <?php endif; ?>
            
            <?php if (tienePermiso('pagos')): ?>
            <a href="pagos.php" class="active"><i class="fa-solid fa-money-check-dollar me-2"></i><span>Pagos</span></a>
            <?php endif; ?>
            
            <?php if (tienePermiso('tarifas')): ?>
            <a href="tarifas.php"><i class="fa-solid fa-file-invoice-dollar me-2"></i><span>Gestor de tarifas</span></a>
            <?php endif; ?>
            
            <?php if (tienePermiso('bitacoras')): ?>
            <a href="bitacora.php"><i class="fa-solid fa-book me-2"></i><span>Bitácoras de usuarios</span></a>
            <?php endif; ?>
            
            <?php if (tienePermiso('especialidades')): ?>
            <a href="especialidades.php"><i class="fa-solid fa-stethoscope me-2"></i><span>Especialidades médicas</span></a>
            <?php endif; ?>
        </div>

        <hr>
        <a href="logout.php" style="margin: 0 15px;">
            <i class="fa-solid fa-right-from-bracket me-2"></i>
            <span>Cerrar sesión</span>
        </a>
    </div>

    <!-- Contenido -->
    <div class="content">
        <!-- Navbar superior -->
        <div class="top-navbar">
            <div>
                <h5><i class="fa-solid fa-money-check-dollar me-2"></i>Gestión de Pagos</h5>
                <p class="welcome-text mb-0">Administra los pagos de las consultas</p>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalPagos" 
                    style="background: linear-gradient(135deg, #2c8888, #4caeae); border: none;">
                <i class="fa-solid fa-plus me-2"></i>Agregar Pago
            </button>
        </div>

        <!-- Tabla de pagos -->
        <div class="card shadow-sm border-0" style="border-radius: 20px;">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="tablaPagos" class="table table-hover align-middle">
                        <thead style="background: linear-gradient(135deg, #f8fffe 0%, #e8f5f5 100%);">
                            <tr>
                                <th class="text-center">ID Pago</th>
                                <th class="text-center">ID Cita</th>
                                <th class="text-center">ID Paciente</th>
                                <th class="text-center">Monto</th>
                                <th class="text-center">Método Pago</th>
                                <th class="text-center">Fecha Pago</th>
                                <th class="text-center">Referencia</th>
                                <th class="text-center">Estatus</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    <i class="fa-solid fa-spinner fa-spin me-2"></i>Cargando pagos...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar/editar pago -->
    <div class="modal fade" id="modalPagos" tabindex="-1" aria-labelledby="modalPagosLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #2c8888, #4caeae); border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title" id="modalPagosLabel">
                        <i class="fa-solid fa-dollar-sign me-2"></i>Agregar Pago
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="formPagos">
                    <div class="modal-body p-4">
                        <div class="row">
                            <!-- ID Paciente -->
                            <div class="col-md-6 mb-3">
                                <label for="idPaciente" class="form-label">
                                    <i class="fa-solid fa-user me-1 text-primary"></i>ID Paciente *
                                </label>
                                <input type="number" class="form-control" id="idPaciente" name="idPaciente" 
                                       onblur="validarPaciente(this.value)" required>
                                <small class="text-muted">Ingrese el ID del paciente</small>
                            </div>

                            <!-- ID Cita -->
                            <div class="col-md-6 mb-3">
                                <label for="idCita" class="form-label">
                                    <i class="fa-solid fa-calendar me-1 text-success"></i>ID Cita *
                                </label>
                                <input type="number" class="form-control" id="idCita" name="idCita" 
                                       onblur="validarCita(this.value)" required>
                                <small class="text-muted">Ingrese el ID de la cita</small>
                            </div>

                            <!-- Monto -->
                            <div class="col-md-6 mb-3">
                                <label for="monto" class="form-label">
                                    <i class="fa-solid fa-dollar-sign me-1 text-warning"></i>Monto *
                                </label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="monto" name="monto" 
                                       placeholder="0.00" required>
                            </div>

                            <!-- Método de Pago -->
                            <div class="col-md-6 mb-3">
                                <label for="metodoPago" class="form-label">
                                    <i class="fa-solid fa-credit-card me-1 text-info"></i>Método de Pago *
                                </label>
                                <select id="metodoPago" name="metodoPago" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                </select>
                            </div>

                            <!-- Fecha de Pago -->
                            <div class="col-md-6 mb-3">
                                <label for="fechaPago" class="form-label">
                                    <i class="fa-solid fa-calendar-day me-1 text-danger"></i>Fecha de Pago *
                                </label>
                                <input type="date" class="form-control" id="fechaPago" name="fechaPago" required>
                            </div>

                            <!-- Estatus -->
                            <div class="col-md-6 mb-3">
                                <label for="estatusPago" class="form-label">
                                    <i class="fa-solid fa-check-circle me-1 text-success"></i>Estatus *
                                </label>
                                <select id="estatusPago" name="estatusPago" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Completado">Completado</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Cancelado">Cancelado</option>
                                </select>
                            </div>

                            <!-- Referencia -->
                            <div class="col-12 mb-3">
                                <label for="referencia" class="form-label">
                                    <i class="fa-solid fa-hashtag me-1 text-secondary"></i>Referencia / No. Transacción
                                </label>
                                <input type="text" class="form-control" id="referencia" name="referencia" 
                                       placeholder="Opcional" maxlength="100">
                            </div>
                        </div>

                        <div class="alert alert-info d-flex align-items-center mt-3" role="alert">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <small>Los campos marcados con * son obligatorios. 
                                   La cita debe pertenecer al paciente seleccionado.</small>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa-solid fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success" 
                                style="background: linear-gradient(135deg, #2c8888, #4caeae); border: none;">
                            <i class="fa-solid fa-save me-2"></i>Guardar Pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/pagos.js"></script>
</body>

</html>