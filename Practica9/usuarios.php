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

    <a href="dash.html"><i class="fa-solid fa-house me-2"></i>Inicio</a>
    <a href="usuarios.html" class="active"><i class="fa-solid fa-stethoscope me-2"></i>Usuario</a>
    <a href="pacientes.html"><i class="fa-solid fa-user-injured me-2"></i>Control de pacientes</a>
    <a href="controlAgenda.html"><i class="fa-solid fa-calendar-days me-2"></i>Control de agenda</a>
    <a href="medicos.html"><i class="fa-solid fa-user-doctor me-2"></i>Control de médicos</a>
    <a href="reportes.html"><i class="fa-solid fa-chart-line me-2"></i>Reportes</a>
    <a href="expediente.html"><i class="fa-solid fa-notes-medical me-2"></i>Expediente Clínico</a>
    <a href="pagos.html"><i class="fa-solid fa-money-check-dollar me-2"></i>Pagos</a>
    <a href="tarifas.html"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Gestor de tarifas</a>
    <a href="bitacora.html"><i class="fa-solid fa-book me-2"></i>Bitácoras de usuarios</a>
    <a href="especialidades.html"><i class="fa-solid fa-stethoscope me-2"></i>Especialidades médicas</a>

    <hr>
    <a href="index.html">Cerrar sesión</a>
  </div>

  <!-- Contenido -->
  <div class="content" style="margin-left: 230px; padding: 30px;">
    <!-- Navbar superior -->
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <span class="navbar-brand mb-0 h4 fw-bold text-secondary">
          <i class="fa-solid fa-user-plus me-2"></i>Control de Usuarios
        </span>
        <button class="btn btn-success text-white fw-semibold" data-bs-toggle="modal" style="background-color: #2c8888;"
          data-bs-target="#modalUsuario">
          <i class="fa-solid fa-user-plus me-2"></i>Agregar Usuario
        </button>
      </div>
    </nav>

    <!-- Tabla -->
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="table-responsive">
          <table id="tablaUsuarios" class="table table-hover align-middle text-center">
            <thead class="table-info">
              <tr>
                <th>IdUsuario</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Contraseña</th>
                <th>Rol</th>
                <th>Médico</th>
                <th>Activo</th>
                <th>Último Acceso</th>
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
  <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header text-white" style="background-color: #2c8888;">
          <h5 class="modal-title" id="modalUsuarioLabel">
            <i class="fa-solid fa-user-plus me-2"></i>Agregar Usuario
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <form id="formUsuarios">
          <div class="modal-body">
            <div class="mb-3">
              <label for="idUsuario" class="form-label">Id Usuario</label>
              <input type="number" class="form-control" id="idUsuario" name="idUsuario" required>
            </div>
            <div class="mb-3">
              <label for="usuario" class="form-label">Usuario</label>
              <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="mb-3">
              <label for="correo" class="form-label">Correo</label>
              <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <div class="mb-3">
              <label for="contrasena" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="contrasena" name="contrasena" required>
              <small class="text-muted">Mínimo 6 caracteres</small>
            </div>
            <div class="mb-3">
              <label for="rol" class="form-label">Rol</label>
              <select id="rol" name="rol" class="form-select" required>
                <option value="">Selecciona Rol</option>
                <option value="Super admin">Super admin</option>
                <option value="Medico">Médico</option>
                <option value="Paciente">Paciente</option>
                <option value="Secretaria">Secretaria</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="idMedico" class="form-label">Id Médico (opcional)</label>
              <input type="number" class="form-control" id="idMedico" name="idMedico">
              <small class="text-muted">Solo si el usuario es médico</small>
            </div>
            <div class="mb-3">
              <label for="activo" class="form-label">Activo</label>
              <select id="activo" name="activo" class="form-select" required>
                <option value="">Selecciona</option>
                <option value="1">Sí</option>
                <option value="0">No</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="ultimoAcceso" class="form-label">Último acceso (opcional)</label>
              <input type="date" class="form-control" id="ultimoAcceso" name="ultimoAcceso">
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

  <!-- JS Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/usuarios.js"></script>
</body>

</html>