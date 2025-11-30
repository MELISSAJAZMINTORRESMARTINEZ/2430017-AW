<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" type="image/png" href="images/New Patients.png">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/styleCA.css">
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

         <a href="dash.php"><i class="fa-solid fa-house me-2"></i>Inicio</a>
    <a href="usuarios.php"><i class="fa-solid fa-stethoscope me-2"></i>Usuario</a>
    <a href="pacientes.php"><i class="fa-solid fa-user-injured me-2"></i>Control de pacientes</a>
    <a href="controlAgenda.php" class="active"><i class="fa-solid fa-calendar-days me-2"></i>Control de agenda</a>
    <a href="medicos.php"><i class="fa-solid fa-user-doctor me-2"></i>Control de médicos</a>
    <a href="reportes.php"><i class="fa-solid fa-chart-line me-2"></i>Reportes</a>
    <a href="expediente.php"><i class="fa-solid fa-file-medical me-2"></i>Expediente Clínico</a>
    <a href="pagos.php"><i class="fa-solid fa-money-check-dollar me-2"></i>Pagos</a>
    <a href="tarifas.php"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Gestor de tarifas</a>
    <a href="bitacora.php"><i class="fa-solid fa-book me-2"></i>Bitácoras de usuarios</a>
    <a href="especialidades.php"><i class="fa-solid fa-stethoscope me-2"></i>Especialidades médicas</a>

    <hr>
    <a href="index.php">Cerrar sesión</a>
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

        <!-- Tabla de pacientes -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaPacientes" class="table table-hover align-middle text-center">
                        <thead class="table-info">
                            <tr>
                                <th>Id Bitacora</th>
                                <th>Id Usuario</th>
                                <th>Fecha Acceso</th>
                                <th>Accion Realizada</th>
                                <th>Modulo</th>
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

                <form id="formTarifas">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="idBitacora" class="form-label">Id Bitacora</label>
                            <input type="number" class="form-control" id="idTarifa" name="idTarifa" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcionServicio" class="form-label">Descripción servicio</label>
                            <input type="text" class="form-control" id="descripcionServicio" name="descripcionServicio" required>
                        </div>
                        <div class="mb-3">
                            <label for="costoBase" class="form-label">Costo Base</label>
                            <input type="number" step="0.01" class="form-control" id="costoBase" name="costoBase" required>
                        </div>
                        <div class="mb-3">
                            <label for="especialidadId" class="form-label">Especialidad</label>
                            <select id="especialidadId" name="especialidadId" class="form-select" required>
                                <option value="">Selecciona Especialidad</option>
                                <option value="1">Cardiología</option>
                                <option value="2">Pediatría</option>
                                <option value="3">Dermatología</option>
                                <option value="4">Ginecología</option>
                                <option value="5">Medicina General</option>
                                <option value="6">Neurología</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="estatus" class="form-label">Estatus</label>
                            <select id="estatus" name="estatus" class="form-select" required>
                                <option value="">Selecciona</option>
                                <option value="1">Tarifa pagada</option>
                                <option value="0">Tarifa no pagada</option>
                            </select>
                        </div>
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