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
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión</a>
    </div>

  <!-- Contenido -->
  <div class="content" style="margin-left: 230px; padding: 30px;">
    <!-- Navbar superior -->
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <span class="navbar-brand mb-0 h4 fw-bold text-secondary">
          <i class="fa-solid fa-stethoscope me-2"></i>Especialidades Médicas
        </span>
        <button class="btn btn-success text-white fw-semibold" data-bs-toggle="modal" style="background-color: #2c8888;"
          data-bs-target="#modalEspecialidad">
          <i class="fa-solid fa-user-plus me-2"></i>Agregar Especialidad
        </button>
      </div>
    </nav>

    <!-- Tabla de especialidades -->
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="table-responsive">
          <table id="tablaPacientes" class="table table-hover align-middle text-center">
            <thead class="table-info">
              <tr>
                <th>Id Especialidad</th>
                <th>Nombre Especialidad</th>
                <th>Descripción</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="tablaEspecialidades">
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Agregar Especialidad -->
  <div class="modal fade" id="modalEspecialidad" tabindex="-1" aria-labelledby="modalEspecialidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header text-white" style="background-color: #2c8888;">
          <h5 class="modal-title" id="modalEspecialidadLabel">
            <i class="fa-solid fa-user-plus me-2"></i>Agregar Especialidad
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Formulario Agregar -->
        <form action="php/especialidades.php" method="post">
          <input type="hidden" name="accion" value="agregar">
          <div class="modal-body">
            <div class="mb-3">
              <label for="IdEspecialidad" class="form-label">Id Especialidad</label>
              <input type="number" class="form-control" id="IdEspecialidad" name="IdEspecialidad" required>
            </div>
            <div class="mb-3">
              <label for="nombreEspecialidad" class="form-label">Nombre Especialidad</label>
              <select id="nombreEspecialidad" name="nombreEspecialidad" class="form-select" required>
                <option value="">Selecciona Especialidad</option>
                <option>Cardiología</option>
                <option>Pediatría</option>
                <option>Dermatología</option>
                <option>Ginecología</option>
                <option>Medicina General</option>
                <option>Neurología</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="descripcion" class="form-label">Descripción</label>
              <input type="text" class="form-control" id="descripcion" name="descripcion" required>
            </div>
          </div>

          <!-- Botones modal -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Editar Especialidad -->
  <div class="modal fade" id="modalEditarEspecialidad" tabindex="-1" aria-labelledby="modalEditarEspecialidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header text-white" style="background-color: #2c8888;">
          <h5 class="modal-title" id="modalEditarEspecialidadLabel">
            <i class="fa-solid fa-pen-to-square me-2"></i>Editar Especialidad
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Formulario Editar -->
        <form action="php/especialidades.php" method="post">
          <input type="hidden" name="accion" value="editar">
          <input type="hidden" id="editIdEspecialidad" name="IdEspecialidad">
          <div class="modal-body">
            <div class="mb-3">
              <label for="editNombreEspecialidad" class="form-label">Nombre Especialidad</label>
              <select id="editNombreEspecialidad" name="nombreEspecialidad" class="form-select" required>
                <option value="">Selecciona Especialidad</option>
  <option value="Cardiología">Cardiología</option>
  <option value="Pediatría">Pediatría</option>
  <option value="Dermatología">Dermatología</option>
  <option value="Ginecología">Ginecología</option>
  <option value="Medicina">Medicina General</option>
  <option value="Neurología">Neurología</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="editDescripcion" class="form-label">Descripción</label>
              <input type="text" class="form-control" id="editDescripcion" name="descripcion" required>
            </div>
          </div>

          <!-- Botones modal -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-warning text-white">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal Editar Especialidad -->
<div class="modal fade" id="modalEditarEspecialidad" tabindex="-1" aria-labelledby="modalEditarEspecialidadLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header text-white" style="background-color: #2c8888;">
        <h5 class="modal-title" id="modalEditarEspecialidadLabel">
          <i class="fa-solid fa-pen-to-square me-2"></i>Editar Especialidad
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <!-- Formulario Editar -->
      <form action="php/especialidades.php" method="post">
        <input type="hidden" name="accion" value="editar">
        <input type="hidden" id="editIdEspecialidad" name="IdEspecialidad">
        <div class="modal-body">
          <div class="mb-3">
            <label for="editNombreEspecialidad" class="form-label">Nombre Especialidad</label>
            <select id="editNombreEspecialidad" name="nombreEspecialidad" class="form-select" required>
              <option value="">Selecciona Especialidad</option>
              <option value="Cardiología">Cardiología</option>
              <option value="Pediatría">Pediatría</option>
              <option value="Dermatología">Dermatología</option>
              <option value="Ginecología">Ginecología</option>
              <option value="Medicina General">Medicina General</option>
              <option value="Neurología">Neurología</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="editDescripcion" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="editDescripcion" name="descripcion" required>
          </div>
        </div>

        <!-- Botones modal -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning text-white " style="border-radius: 50;">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  </script>
  
  <script src="js/especialidad.js"></script>
  
</body>

</html>