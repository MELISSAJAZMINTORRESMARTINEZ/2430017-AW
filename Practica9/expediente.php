<?php require_once 'php/verificar_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente Clínico</title>
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

<body class="bg-light">

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
            <a href="pacientes.html"><i class="fa-solid fa-user-injured me-2"></i>Control de pacientes</a>
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
                    <i class="fa-solid fa-notes-medical me-2"></i>Expediente Clínico
                </span>
                <button class="btn btn-success text-white fw-semibold" data-bs-toggle="modal"
                    style="background-color: #2c8888;" data-bs-target="#modalExpediente">
                    <i class="fa-solid fa-plus me-2"></i>Agregar Expediente
                </button>
            </div>
        </nav>

        <!-- Tabla de expedientes -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaExpediente" class="table table-hover align-middle text-center">
                        <thead class="table-info">
                            <tr>
                                <th>Id Expediente</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Fecha Consulta</th>
                                <th>Síntomas</th>
                                <th>Diagnóstico</th>
                                <th>Tratamiento</th>
                                <th>Receta Médica</th>
                                <th>Notas</th>
                                <th>Próxima Cita</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-end mt-3">
            <a href="controlAgenda.html" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Volver a Agenda
            </a>
        </div>
    </div>

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="modalExpediente" tabindex="-1" aria-labelledby="modalExpedienteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background-color: #2c8888;">
                    <h5 class="modal-title" id="modalExpedienteLabel">
                        <i class="fa-solid fa-notes-medical me-2"></i>Agregar Expediente
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- FORMULARIO DENTRO DEL MODAL (SIN ACTION) -->
                <form id="formExpediente">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="idExpediente" class="form-label">Id Expediente</label>
                                <input type="number" class="form-control" id="idExpediente" name="idExpediente" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="idPaciente" class="form-label">Id Paciente *</label>
                                <input type="number" class="form-control" id="idPaciente" name="idPaciente" required>
                                <small class="text-muted">Ingrese el ID y presione fuera del campo</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="idMedico" class="form-label">Id Médico *</label>
                                <input type="number" class="form-control" id="idMedico" name="idMedico" required>
                                <small class="text-muted">Ingrese el ID y presione fuera del campo</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fechaConsulta" class="form-label">Fecha Consulta</label>
                                <input type="date" class="form-control" id="fechaConsulta" name="fechaConsulta" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="proximaCita" class="form-label">Próxima Cita</label>
                                <input type="date" class="form-control" id="proximaCita" name="proximaCita">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="sintomas" class="form-label">Síntomas</label>
                            <textarea class="form-control" id="sintomas" name="sintomas" rows="2" 
                                placeholder="Describa los síntomas presentados por el paciente"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="diagnostico" class="form-label">Diagnóstico</label>
                            <textarea class="form-control" id="diagnostico" name="diagnostico" rows="2"
                                placeholder="Diagnóstico médico del paciente"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="tratamiento" class="form-label">Tratamiento</label>
                            <textarea class="form-control" id="tratamiento" name="tratamiento" rows="2"
                                placeholder="Tratamiento indicado"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="recetaMedica" class="form-label">Receta Médica</label>
                            <textarea class="form-control" id="recetaMedica" name="recetaMedica" rows="2"
                                placeholder="Medicamentos recetados y dosis"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="notasAdicionales" class="form-label">Notas Adicionales</label>
                            <textarea class="form-control" id="notasAdicionales" name="notasAdicionales" rows="2"
                                placeholder="Cualquier información adicional relevante"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="activo" class="form-label">Activo *</label>
                            <select id="activo" name="activo" class="form-select" required>
                                <option value="">Selecciona</option>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/expediente.js"></script>

</body>

</html>