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
    
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            margin: 20px 0;
        }
        
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .metric-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .metric-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .metric-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .metric-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .metric-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .metric-change {
            font-size: 0.85rem;
            margin-top: 10px;
        }
        
        .metric-change.positive {
            color: #a8ffb8;
        }
        
        .metric-change.negative {
            color: #ffb8b8;
        }
    </style>
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
            
            <!-- Métricas principales con gradientes -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">
                            <i class="fa-solid fa-user-injured me-2"></i>Pacientes Activos
                        </div>
                        <div class="metric-value" id="pacientesActivos">120</div>
                        <div class="metric-change positive">
                            <i class="fa-solid fa-arrow-up me-1"></i>+12% vs mes anterior
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="metric-card success">
                        <div class="metric-label">
                            <i class="fa-solid fa-calendar-check me-2"></i>Citas del Día
                        </div>
                        <div class="metric-value" id="citasDelDia">38</div>
                        <div class="metric-change positive">
                            <i class="fa-solid fa-arrow-up me-1"></i>+5 más que ayer
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="metric-card warning">
                        <div class="metric-label">
                            <i class="fa-solid fa-user-doctor me-2"></i>Médicos Disponibles
                        </div>
                        <div class="metric-value" id="medicosDisponibles">12</div>
                        <div class="metric-change">
                            <i class="fa-solid fa-minus me-1"></i>Sin cambios
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="metric-card info">
                        <div class="metric-label">
                            <i class="fa-solid fa-money-bill-wave me-2"></i>Ingresos del Mes
                        </div>
                        <div class="metric-value">$45K</div>
                        <div class="metric-change positive">
                            <i class="fa-solid fa-arrow-up me-1"></i>+18% vs mes anterior
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficas -->
            <div class="row g-4 mb-4">
                <!-- Gráfica de pastel: Estado de citas -->
                <div class="col-lg-4">
                    <div class="dash-panel">
                        <h5><i class="fa-solid fa-chart-pie me-2"></i>Estado de Citas</h5>
                        <div class="chart-container">
                            <canvas id="chartEstadoCitas"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfica de pastel: Distribución por especialidad -->
                <div class="col-lg-4">
                    <div class="dash-panel">
                        <h5><i class="fa-solid fa-chart-pie me-2"></i>Citas por Especialidad</h5>
                        <div class="chart-container">
                            <canvas id="chartEspecialidades"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfica de dona: Género de pacientes -->
                <div class="col-lg-4">
                    <div class="dash-panel">
                        <h5><i class="fa-solid fa-chart-pie me-2"></i>Distribución por Género</h5>
                        <div class="chart-container">
                            <canvas id="chartGenero"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfica de barras: Citas por día de la semana -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="dash-panel">
                        <h5><i class="fa-solid fa-chart-bar me-2"></i>Citas por Día de la Semana</h5>
                        <div class="chart-container" style="height: 350px;">
                            <canvas id="chartCitasSemana"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="dash-panel">
                        <h5><i class="fa-solid fa-bell me-2"></i>Notificaciones</h5>
                        <div class="notif">
                            <div class="alert alert-info mb-2">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                <small>5 citas pendientes de confirmar</small>
                            </div>
                            <div class="alert alert-warning mb-2">
                                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                <small>2 pacientes sin expediente actualizado</small>
                            </div>
                            <div class="alert alert-success mb-0">
                                <i class="fa-solid fa-check-circle me-2"></i>
                                <small>Sistema funcionando correctamente</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel inferior -->
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="dash-panel">
                        <h5><i class="fa-solid fa-chart-line me-2"></i>Actividad Reciente</h5>
                        <ul class="recent-list">
                            <li><i class="fa-solid fa-circle-check text-success"></i> Se registró un nuevo paciente: Juan Pérez</li>
                            <li><i class="fa-solid fa-circle-check text-success"></i> Se agregó una cita para mañana a las 10:00 AM</li>
                            <li><i class="fa-solid fa-circle-check text-success"></i> Se generó un nuevo expediente clínico</li>
                            <li><i class="fa-solid fa-circle-info text-info"></i> Dr. García actualizó su disponibilidad</li>
                            <li><i class="fa-solid fa-circle-check text-success"></i> Se procesó un pago de $1,200 MXN</li>
                        </ul>
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

    <script>
        // Configuración de colores
        const colors = {
            primary: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b'],
            success: ['#11998e', '#38ef7d'],
            warning: ['#f093fb', '#f5576c'],
            info: ['#4facfe', '#00f2fe']
        };

        // Gráfica de Estado de Citas (Pastel)
        const ctxEstado = document.getElementById('chartEstadoCitas').getContext('2d');
        new Chart(ctxEstado, {
            type: 'pie',
            data: {
                labels: ['Confirmadas', 'Pendientes', 'Canceladas', 'Completadas'],
                datasets: [{
                    data: [45, 15, 8, 32],
                    backgroundColor: [
                        '#38ef7d',
                        '#f5576c',
                        '#ff6b6b',
                        '#4facfe'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfica de Especialidades (Pastel)
        const ctxEsp = document.getElementById('chartEspecialidades').getContext('2d');
        new Chart(ctxEsp, {
            type: 'pie',
            data: {
                labels: ['Cardiología', 'Pediatría', 'Dermatología', 'Traumatología', 'Otros'],
                datasets: [{
                    data: [25, 30, 15, 20, 10],
                    backgroundColor: [
                        '#667eea',
                        '#764ba2',
                        '#f093fb',
                        '#4facfe',
                        '#43e97b'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfica de Género (Dona)
        const ctxGenero = document.getElementById('chartGenero').getContext('2d');
        new Chart(ctxGenero, {
            type: 'doughnut',
            data: {
                labels: ['Femenino', 'Masculino', 'Otro'],
                datasets: [{
                    data: [58, 40, 2],
                    backgroundColor: [
                        '#f093fb',
                        '#4facfe',
                        '#43e97b'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfica de Citas por Día (Barras)
        const ctxSemana = document.getElementById('chartCitasSemana').getContext('2d');
        new Chart(ctxSemana, {
            type: 'bar',
            data: {
                labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                datasets: [{
                    label: 'Citas',
                    data: [45, 52, 38, 48, 55, 28],
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>