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
        <a href="php/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión</a>
    </div>


    <!-- Contenido -->
    <div class="content" style="margin-left: 230px; padding: 30px;">
        <!-- Navbar superior -->
        <nav class="navbar navbar-expand-lg navbar-light mb-4">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <span class="navbar-brand mb-0 h4 fw-bold text-secondary">
                    <i class="fa-solid fa-user-injured me-2"></i>Control de Pacientes
                </span>
                <button class="btn btn-success text-white fw-semibold" data-bs-toggle="modal"
                    style="background-color: #2c8888;" data-bs-target="#modalPaciente">
                    <i class="fa-solid fa-user-plus me-2"></i>Agregar Paciente
                </button>
            </div>
        </nav>

        <!-- Tabla de pacientes -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaPacientes" class="table table-hover align-middle text-center">
                        <thead class="table-info">
                            <tr>
                                <th>IdPaciente</th>
                                <th>Nombre Completo</th>
                                <th>CURP</th>
                                <th>Fecha de Nacimiento</th>
                                <th>Sexo</th>
                                <th>Teléfono</th>
                                <th>Correo Electrónico</th>
                                <th>Dirección</th>
                                <th>Contacto Emergencia</th>
                                <th>Teléfono Emergencia</th>
                                <th>Alergias</th>
                                <th>Antecedentes Medicos</th>
                                <th>Fecha Registro</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--pacientes-->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Bootstrap -->
    <div class="modal fade" id="modalPaciente" tabindex="-1" aria-labelledby="modalPacientelabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background-color: #2c8888;">
                    <h5 class="modal-title" id="modalpacientelabel">
                        <i class="fa-solid fa-user-plus me-2"></i>Agregar Paciente
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- FORMULARIO -->
                <form id="formPaciente" method="post">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="idpaciente" class="form-label">IdPaciente</label>
                            <input type="number" class="form-control" id="idpaciente" name="idpaciente" required>
                        </div>

                        <div class="mb-3">
                            <label for="nombrecompleto" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombrecompleto" name="nombrecompleto" required>
                        </div>

                        <div class="mb-3">
                            <label for="curp" class="form-label">CURP</label>
                            <input type="text" class="form-control" id="curp" name="curp" maxlength="18" required>
                        </div>

                        <div class="mb-3">
                            <label for="fechanacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fechanacimiento" name="fechanacimiento"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select id="sexo" name="sexo" class="form-select" required>
                                <option value="">Selecciona</option>
                                <option value="M">masculino</option>
                                <option value="F">femenino</option>
                                <option>otro</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" maxlength="10">
                        </div>

                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo">
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="contactoemergencia" class="form-label">Contacto de Emergencia</label>
                            <input type="tel" class="form-control" id="contactoemergencia" name="contactoemergencia"
                                maxlength="10">
                        </div>

                        <div class="mb-3">
                            <label for="telefonoemergencia" class="form-label">Teléfono de Emergencia</label>
                            <input type="tel" class="form-control" id="telefonoemergencia" name="telefonoemergencia"
                                maxlength="10">
                        </div>

                        <div class="mb-3">
                            <label for="alergias" class="form-label">Alergias</label>
                            <textarea class="form-control" id="alergias" name="alergias" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="antecedentesmedicos" class="form-label">Antecedentes Médicos</label>
                            <textarea class="form-control" id="antecedentesmedicos" name="antecedentesmedicos"
                                rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="fechaegistro" class="form-label">Fecha Registro</label>
                            <input type="date" class="form-control" id="fecharegistro" name="fecharegistro">
                        </div>

                        <div class="mb-3">
                            <label for="estatus" class="form-label">Estatus</label>
                            <select id="estatus" name="estatus" class="form-select">
                                <option value="">Selecciona</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                    </div>

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
    <script src="js/pacientes.js"></script>
</body>

</html>