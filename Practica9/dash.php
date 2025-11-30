<?php require_once 'php/verificar_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" type="image/png" href="images/New Patients.png">
    <link rel="stylesheet" href="css/dashboard.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
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
            <a href="pacientes."><i class="fa-solid fa-user-injured me-2"></i>Control de pacientes</a>
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
    <div class="content">

        <!-- Navbar superior -->
        <nav class="navbar navbar-expand-lg navbar-light mb-4">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h5">Panel principal</span>
            </div>
        </nav>

        <!-- Card de bienvenida -->
<div class="welcome-card p-4 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="welcome-icon">
            <i class="fa-solid fa-hand-wave"></i>
        </div>
        <div>
            <h2 class="mb-1">¡Bienvenido(a), <?php echo htmlspecialchars($nombreUsuario); ?>!</h2>
            <p class="mb-0">Holiii, que bueno verte por aqui, espero disfrutes la estancia en la pagina.</p>
        </div>
    </div>
</div>


        <div class="container-fluid">

            <?php if (tienePermiso('reportes')): ?>
            <!-- Tarjetas principales -->
            <div class="row g-4 mb-4">

                <div class="col-md-4">
                    <div class="dash-card">
                        <div class="icon"><i class="fa-solid fa-user-injured"></i></div>
                        <div>
                            <h3 id="pacientesActivos">120</h3>
                            <p>Pacientes activos</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dash-card">
                        <div class="icon"><i class="fa-solid fa-calendar-check"></i></div>
                        <div>
                            <h3 id="citasDelDia">38</h3>
                            <p>Citas del día</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dash-card">
                        <div class="icon"><i class="fa-solid fa-user-doctor"></i></div>
                        <div>
                            <h3 id="medicosDisponibles">12</h3>
                            <p>Médicos disponibles</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Panel inferior -->
            <div class="row g-4">

                <div class="col-lg-8">
                    <div class="dash-panel">
                        <h5><i class="fa-solid fa-chart-line me-2"></i>Actividad reciente</h5>
                        <ul class="recent-list">
                            <li><i class="fa-solid fa-circle-check text-success"></i> Se registró un nuevo paciente.</li>
                            <li><i class="fa-solid fa-circle-check text-success"></i> Se agregó una cita a la agenda.</li>
                            <li><i class="fa-solid fa-circle-check text-success"></i> Se generó un nuevo expediente clínico.</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="dash-panel">
                        <h5><i class="fa-solid fa-bell me-2"></i>Notificaciones</h5>
                        <div class="notif">
                            <p>No hay notificaciones pendientes</p>
                        </div>
                    </div>
                </div>

            </div>
            <?php else: ?>
            <!-- Mensaje de permisos limitados -->
            <div class="alert alert-warning">
                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                No tienes permisos para ver las estadísticas completas.
            </div>
            <?php endif; ?>

            <!-- Sección de permisos del usuario -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="dash-panel">
                        <h5><i class="fa-solid fa-shield-halved me-2"></i>Tus permisos actuales</h5>
                        <ul class="list-group">
                            <?php if (tienePermiso('usuarios')): ?>
                            <li class="list-group-item">
                                <i class="fa-solid fa-check text-success me-2"></i> 
                                Gestión de Usuarios
                            </li>
                            <?php endif; ?>
                            
                            <?php if (tienePermiso('pacientes')): ?>
                            <li class="list-group-item">
                                <i class="fa-solid fa-check text-success me-2"></i> 
                                Control de Pacientes
                            </li>
                            <?php endif; ?>
                            
                            <?php if (tienePermiso('agenda')): ?>
                            <li class="list-group-item">
                                <i class="fa-solid fa-check text-success me-2"></i> 
                                Control de Agenda
                            </li>
                            <?php endif; ?>
                            
                            <?php if (tienePermiso('medicos')): ?>
                            <li class="list-group-item">
                                <i class="fa-solid fa-check text-success me-2"></i> 
                                Control de Médicos
                            </li>
                            <?php endif; ?>
                            
                            <?php if (tienePermiso('reportes')): ?>
                            <li class="list-group-item">
                                <i class="fa-solid fa-check text-success me-2"></i> 
                                Visualización de Reportes
                            </li>
                            <?php endif; ?>
                            
                            <?php if (tienePermiso('expedientes')): ?>
                            <li class="list-group-item">
                                <i class="fa-solid fa-check text-success me-2"></i> 
                                Gestión de Expedientes Clínicos
                            </li>
                            <?php endif; ?>
                            
                            <?php if (tienePermiso('pagos')): ?>
                            <li class="list-group-item">
                                <i class="fa-solid fa-check text-success me-2"></i> 
                                Gestión de Pagos
                            </li>
                            <?php endif; ?>
                            
                            <?php if (tienePermiso('tarifas')): ?>
                            <li class="list-group-item">
                                <i class="fa-solid fa-check text-success me-2"></i> 
                                Gestión de Tarifas
                            </li>
                            <?php endif; ?>
                            
                            <?php if (tienePermiso('bitacoras')): ?>
                            <li class="list-group-item">
                                <i class="fa-solid fa-check text-success me-2"></i> 
                                Visualización de Bitácoras
                            </li>
                            <?php endif; ?>
                            
                            <?php if (tienePermiso('especialidades')): ?>
                            <li class="list-group-item">
                                <i class="fa-solid fa-check text-success me-2"></i> 
                                Especialidades Médicas
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>