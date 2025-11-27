// configuración de permisos por rol
const PERMISOS = {
  'Super admin': {
    modulos: ['inicio', 'usuarios', 'pacientes', 'agenda', 'medicos', 'reportes', 'expediente', 'pagos', 'tarifas', 'bitacora', 'especialidades'],
    redirect: 'dash.html'
  },
  'Medico': {
    modulos: ['inicio', 'agenda', 'expediente', 'pacientes'],
    redirect: 'dash_medico.html'
  },
  'Secretaria': {
    modulos: ['inicio', 'agenda', 'pacientes', 'pagos'],
    redirect: 'dash_secretaria.html'
  },
  'Paciente': {
    modulos: ['inicio', 'agenda'],
    redirect: 'dash_paciente.html'
  }
};

// verificar si hay sesión activa
function verificarSesion() {
  const usuario = JSON.parse(sessionStorage.getItem('usuarioActivo'));
  
  if (!usuario) {
    Swal.fire({
      icon: 'warning',
      title: 'Sesión no iniciada',
      text: 'Debes iniciar sesión para acceder',
      confirmButtonColor: '#3085d6'
    }).then(() => {
      window.location.href = 'index.html';
    });
    return null;
  }
  
  return usuario;
}

// verificar permisos para un módulo específico
function verificarPermiso(modulo) {
  const usuario = verificarSesion();
  if (!usuario) return false;

  const permisos = PERMISOS[usuario.rol];
  if (!permisos) {
    Swal.fire({
      icon: 'error',
      title: 'Rol no válido',
      text: 'Tu rol no tiene permisos asignados',
      confirmButtonColor: '#d33'
    }).then(() => {
      cerrarSesion();
    });
    return false;
  }

  if (!permisos.modulos.includes(modulo)) {
    Swal.fire({
      icon: 'error',
      title: 'Acceso denegado',
      text: 'No tienes permisos para acceder a este módulo',
      confirmButtonColor: '#d33'
    }).then(() => {
      window.location.href = permisos.redirect;
    });
    return false;
  }

  return true;
}

// construir sidebar según permisos
function construirSidebar() {
  const usuario = verificarSesion();
  if (!usuario) return;

  const permisos = PERMISOS[usuario.rol];
  const sidebar = document.querySelector('.sidebar');
  
  if (!sidebar) return;

  // definir todos los enlaces posibles
  const todosLosEnlaces = {
    'inicio': '<a href="dash.html"><i class="fa-solid fa-house me-2"></i>Inicio</a>',
    'usuarios': '<a href="usuarios.html"><i class="fa-solid fa-stethoscope me-2"></i>Usuario</a>',
    'pacientes': '<a href="pacientes.html"><i class="fa-solid fa-user-injured me-2"></i>Control de pacientes</a>',
    'agenda': '<a href="controlAgenda.html"><i class="fa-solid fa-calendar-days me-2"></i>Control de agenda</a>',
    'medicos': '<a href="medicos.html"><i class="fa-solid fa-user-doctor me-2"></i>Control de médicos</a>',
    'reportes': '<a href="reportes.html"><i class="fa-solid fa-chart-line me-2"></i>Reportes</a>',
    'expediente': '<a href="expediente.html"><i class="fa-solid fa-notes-medical me-2"></i>Expediente Clínico</a>',
    'pagos': '<a href="pagos.html"><i class="fa-solid fa-money-check-dollar me-2"></i>Pagos</a>',
    'tarifas': '<a href="tarifas.html"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Gestor de tarifas</a>',
    'bitacora': '<a href="bitacora.html"><i class="fa-solid fa-book me-2"></i>Bitácoras de usuarios</a>',
    'especialidades': '<a href="especialidades.html"><i class="fa-solid fa-stethoscope me-2"></i>Especialidades médicas</a>'
  };

  // construir HTML del sidebar
  let sidebarHTML = `
    <h4 class="text-center">
      <img src="images/otrogatito (2).png" alt="Logo Clínica" width="60" height="60" class="rounded-circle mb-2">
      <br>Clínica
    </h4>
    <div class="sidebar-links">
  `;

  // agregar solo los enlaces permitidos
  permisos.modulos.forEach(modulo => {
    if (todosLosEnlaces[modulo]) {
      sidebarHTML += todosLosEnlaces[modulo];
    }
  });

  sidebarHTML += `
    </div>
    <hr>
    <a href="#" onclick="cerrarSesion()"><i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión</a>
  `;

  sidebar.innerHTML = sidebarHTML;

  // mostrar nombre del usuario
  const navbarBrand = document.querySelector('.navbar-brand');
  if (navbarBrand) {
    navbarBrand.innerHTML = `<i class="fa-solid fa-user me-2"></i>${usuario.nombre} - ${usuario.rol}`;
  }
}

// cerrar sesión
function cerrarSesion() {
  Swal.fire({
    title: '¿Cerrar sesión?',
    text: '¿Estás seguro de que quieres salir?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, salir',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      sessionStorage.removeItem('usuarioActivo');
      window.location.href = 'index.html';
    }
  });
}

// obtener usuario actual
function getUsuarioActual() {
  return JSON.parse(sessionStorage.getItem('usuarioActivo'));
}