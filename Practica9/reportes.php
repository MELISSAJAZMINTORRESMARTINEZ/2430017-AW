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
                    <i class="fa-solid fa-user-injured me-2"></i>Reportes
                </span>
                <button id="btnAgregarPaciente" class="btn btn-success text-white fw-semibold">
                    <i class="fa-solid fa-user-plus me-2"></i>Agregar Reporte
                </button>
            </div>
        </nav>

        <!-- Tabla de pacientes -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaReportes" class="table table-hover align-middle text-center">
                        <thead class="table-info">
                            <tr>
                                <th>IdReportes</th>
                                <th>Tipo de Reporte</th>
                                <th>Id Paciente</th>
                                <th>Id Medico</th>
                                <th>Fecha de Generacion</th>
                                <th>Ruta Archivo</th>
                                <th>Descripcion</th>
                                <th>Generado por:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!---->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/reportes.js"></script>
</body>

</html>