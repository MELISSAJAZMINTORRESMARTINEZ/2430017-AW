// obtener el formulario principal
const formu = document.getElementById("inicio");

// obtener lista de usuarios almacenados o crear vacia
let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

// mostrar saludo si hay usuario activo
if (localStorage.getItem("Correito")) {
  const textito = document.getElementById("textito");
  if (textito) {
    textito.innerHTML = "Holita, " + localStorage.getItem("Correito");
  }
}

// si existe el formulario de inicio de sesion
if (formu) {
  formu.addEventListener("submit", e => {
    e.preventDefault();

    // obtener datos del usuario
    const UCorreo = document.getElementById("correo").value.trim();
    const UContraseña = document.getElementById("contraseña").value.trim();

    // verificar campos vacios
    if (!UCorreo || !UContraseña) return alert("por favor rellena los campos");

    // buscar usuario valido en la lista
    const usuarioValido = usuarios.find(u => u.correo === UCorreo && u.contraseña === UContraseña);
    if (!usuarioValido) return alert("usuario o contraseña incorrectos");

    // guardar correo del usuario activo
    localStorage.setItem("Correito", UCorreo);

    // redirigir al dashboard
    window.location.href = "dashboard.html";
  });
}

// --- logica del dashboard ---
if (window.location.pathname.includes("dashboard.html")) {

  // obtener correo del usuario activo
  const usuarioActivo = localStorage.getItem("Correito");

  // referencias de formularios y tablas
  const formTarea = document.getElementById("formTarea");
  const tablaTareas = document.getElementById("tablaTareas");
  const formProyecto = document.getElementById("formProyecto");
  const tablaProyecto = document.getElementById("tablaProyecto");

  // cargar datos del almacenamiento local
  let tareas = JSON.parse(localStorage.getItem("tareas_" + usuarioActivo)) || [];
  let proyectos = JSON.parse(localStorage.getItem("proyectos_" + usuarioActivo)) || [];

  // funcion para mostrar tareas
  function mostrarTareas() {
    tablaTareas.innerHTML = "";

    tareas.forEach((t, index) => {
      const fila = document.createElement("tr");
      fila.draggable = true;
      fila.dataset.index = index;
      fila.dataset.estado = t.estado;

      fila.innerHTML = `
        <td>${index + 1}</td>
        <td>${t.proyecto_id}</td>
        <td>${t.titulo}</td>
        <td>${t.descripcion}</td>
        <td>${t.estado}</td>
        <td>${t.prioridad}</td>
        <td>${t.fecha_vencimiento}</td>
        <td>${t.asignado_a}</td>
        <td><button class="btn btn-danger btn-sm" onclick="eliminarTarea(${index})">🗑️</button></td>
      `;

      // evento para arrastrar
      fila.addEventListener("dragstart", e => {
        e.dataTransfer.setData("text/plain", index);
      });

      tablaTareas.appendChild(fila);
    });
  }

  // agregar nueva tarea
  if (formTarea) {
    formTarea.addEventListener("submit", e => {
      e.preventDefault();

      // obtener datos de la tarea
      const tarea = {
        proyecto_id: document.getElementById("proyecto_id").value.trim(),
        titulo: document.getElementById("nombreTarea").value.trim(),
        descripcion: document.getElementById("descripcionTarea").value.trim(),
        estado: document.getElementById("estadoTarea").value,
        prioridad: document.getElementById("prioridad").value,
        fecha_vencimiento: document.getElementById("fecha_vencimiento").value,
        asignado_a: document.getElementById("asignado_a").value.trim()
      };

      // validar que no haya campos vacios
      if (Object.values(tarea).some(v => !v)) return alert("completa todos los campos");

      // guardar tarea y actualizar vista
      tareas.push(tarea);
      localStorage.setItem("tareas_" + usuarioActivo, JSON.stringify(tareas));
      mostrarTareas();
      formTarea.reset();
    });
  }

  // eliminar tarea
  window.eliminarTarea = function (index) {
    tareas.splice(index, 1);
    localStorage.setItem("tareas_" + usuarioActivo, JSON.stringify(tareas));
    mostrarTareas();
    alert("la tarea ha sido eliminada");
  };

  // funcion para mostrar proyectos
  function mostrarProyectos() {
    tablaProyecto.innerHTML = "";

    proyectos.forEach((p, index) => {
      const fila = document.createElement("tr");
      fila.innerHTML = `
        <td>${index + 1}</td>
        <td>${p.id}</td>
        <td>${p.nombre}</td>
        <td>${p.descripcion}</td>
        <td>${p.estado}</td>
        <td>${p.fecha_inicio}</td>
        <td>${p.fecha_fin}</td>
        <td><button class="btn btn-danger btn-sm" onclick="eliminarProyecto(${index})">🗑️</button></td>
      `;
      tablaProyecto.appendChild(fila);
    });
  }

  // agregar nuevo proyecto
  if (formProyecto) {
    formProyecto.addEventListener("submit", e => {
      e.preventDefault();

      // obtener datos del proyecto
      const proyecto = {
        id: document.getElementById("id").value.trim(),
        nombre: document.getElementById("nombre").value.trim(),
        descripcion: document.getElementById("descripcion").value.trim(),
        estado: document.getElementById("estado").value,
        fecha_inicio: document.getElementById("fecha_inicio").value,
        fecha_fin: document.getElementById("fecha_fin").value
      };

      // validar campos vacios
      if (Object.values(proyecto).some(v => !v)) return alert("completa todos los campos");

      // guardar proyecto y actualizar vista
      proyectos.push(proyecto);
      localStorage.setItem("proyectos_" + usuarioActivo, JSON.stringify(proyectos));
      mostrarProyectos();
      formProyecto.reset();
    });
  }

  // eliminar proyecto
  window.eliminarProyecto = function (index) {
    proyectos.splice(index, 1);
    localStorage.setItem("proyectos_" + usuarioActivo, JSON.stringify(proyectos));
    mostrarProyectos();
    alert("proyecto eliminado");
  };

  // logica de arrastrar y soltar para cambiar estado de tareas
  tablaTareas.addEventListener("dragover", e => e.preventDefault());
  tablaTareas.addEventListener("drop", e => {
    e.preventDefault();
    const index = e.dataTransfer.getData("text/plain");
    const tarea = tareas[index];
    if (!tarea) return;

    // cambiar estado de forma ciclica
    const orden = ["pendiente", "en_proceso", "hecha"];
    const nextEstado = orden[(orden.indexOf(tarea.estado) + 1) % orden.length];
    tarea.estado = nextEstado;

    localStorage.setItem("tareas_" + usuarioActivo, JSON.stringify(tareas));
    mostrarTareas();
  });

  // inicializar tablas al cargar
  mostrarTareas();
  mostrarProyectos();
}

 const addNoteBtn = document.getElementById('addNoteBtn');
    const modal = document.getElementById('modal');
    const saveNote = document.getElementById('saveNote');
    const notesContainer = document.getElementById('notesContainer');

    // Abrir modal
    addNoteBtn.onclick = () => {
      modal.style.display = 'flex';
    };

    // Guardar nota
    saveNote.onclick = () => {
      const title = document.getElementById('noteTitle').value;
      const text = document.getElementById('noteText').value;
      const date = document.getElementById('noteDate').value;

      if (title && text && date) {
        const newNote = document.createElement('div');
        newNote.classList.add('card');
        newNote.innerHTML = `
          <h3>${title}</h3>
          <p>${text}</p>
          <small>${date}</small>
        `;
        notesContainer.appendChild(newNote);
        modal.style.display = 'none';
        document.getElementById('noteTitle').value = '';
        document.getElementById('noteText').value = '';
        document.getElementById('noteDate').value = '';
      } else {
        alert('Completa todos los campos');
      }
    };

    // Cerrar modal al hacer click afuera
    window.onclick = (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    };