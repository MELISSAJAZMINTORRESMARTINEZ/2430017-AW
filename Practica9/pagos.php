<?php require_once 'php/verificar_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tarifa de Pagos</title>
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
          <i class="fa-solid fa-money-check-dollar me-2"></i>Pagos
        </span>
        <button class="btn btn-success text-white fw-semibold" data-bs-toggle="modal" style="background-color: #2c8888;" data-bs-target="#modalPagos">
          <i class="fa-solid fa-plus me-2"></i>Agregar Pago
        </button>
      </div>
    </nav>

    <!-- Tabla de pacientes -->
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="table-responsive">
          <table id="tablaPagos" class="table table-hover align-middle text-center">
            <thead class="table-info">
              <tr>
                <th>Id Pago</th>
                <th>Id Cita</th>
                <th>Id Paciente</th>
                <th>Monto</th>
                <th>Metodo Pago</th>
                <th>Fecha Pago</th>
                <th>Referencia</th>
                <th>Estatus Pago</th>
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
  <div class="modal fade" id="modalPagos" tabindex="-1" aria-labelledby="modalPagosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header text-white" style="background-color: #2c8888;">
          <h5 class="modal-title" id="modalPagosLabel">
            <i class="fa-solid fa-user-plus me-2"></i>Agregar Pago
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <form id="formPagos">
  <div class="modal-body">
    <!-- Se eliminó el campo IdPago ya que es autoincremental -->
    
    <div class="mb-3">
      <label for="idCita" class="form-label">Id Cita <span class="text-danger">*</span></label>
      <input type="number" class="form-control" id="idCita" name="idCita" required>
    </div>
    
    <div class="mb-3">
      <label for="idPaciente" class="form-label">Id Paciente <span class="text-danger">*</span></label>
      <input type="number" class="form-control" id="idPaciente" name="idPaciente" required>
    </div>
    
    <div class="mb-3">
      <label for="monto" class="form-label">Monto <span class="text-danger">*</span></label>
      <input type="number" step="0.01" class="form-control" id="monto" name="monto" required>
    </div>
    
    <div class="mb-3">
      <label for="metodoPago" class="form-label">Método de Pago <span class="text-danger">*</span></label>
      <select id="metodoPago" name="metodoPago" class="form-select" required>
        <option value="">Selecciona Pago</option>
        <option value="Efectivo">Efectivo</option>
        <option value="Tarjeta">Tarjeta</option>
        <option value="Transferencia">Transferencia</option>
      </select>
    </div>
    
    <div class="mb-3">
      <label for="fechaPago" class="form-label">Fecha Pago <span class="text-danger">*</span></label>
      <input type="date" class="form-control" id="fechaPago" name="fechaPago" required>
    </div>
    
    <div class="mb-3">
      <label for="referencia" class="form-label">Referencia</label>
      <input type="text" class="form-control" id="referencia" name="referencia">
    </div>
    
    <div class="mb-3">
      <label for="estatusPago" class="form-label">Estatus Pago <span class="text-danger">*</span></label>
      <select id="estatusPago" name="estatusPago" class="form-select" required>
        <option value="">Selecciona</option>
        <option value="Pagado">Pagado</option>
        <option value="Pendiente">Pendiente</option>
        <option value="Cancelado">Cancelado</option>
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

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/pagos.js"></script>
</body>

</html>