document.addEventListener("DOMContentLoaded", () => {
  const btnAgregarAgenda = document.getElementById("btnAgenda");
  const tabla = document.querySelector(".table");

  // Crear tbody si no existe
  let tbody = tabla.querySelector("tbody");
  if (!tbody) {
    tbody = document.createElement("tbody");
    tabla.appendChild(tbody);
  }

  // Cargar citas guardadas
  let citas = JSON.parse(localStorage.getItem("citas")) || [];

  // Renderizar citas
  function renderizarCitas() {
    tbody.innerHTML = "";

    if (citas.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="10" class="text-center text-muted py-3">
            <i class="fa-solid fa-calendar-xmark me-2"></i>No hay citas registradas
          </td>
        </tr>`;
      return;
    }

    citas.forEach((cita, index) => {
      const fila = document.createElement("tr");
      fila.classList.add("text-center");
      fila.innerHTML = `
        <td>${cita.idCita}</td>
        <td>${cita.idPaciente}</td>
        <td>${cita.idMedico}</td>
        <td>${cita.fechaCita}</td>
        <td>${cita.motivo}</td>
        <td>${cita.estado}</td>
        <td>${cita.observaciones}</td>
        <td>${cita.direccion}</td>
        <td>${cita.fechaRegistro}</td>
        <td>
          <button class="btn btn-sm btn-primary verExpediente" data-index="${index}">
            <i class="fa-solid fa-file-medical"></i>
          </button>
          <button class="btn btn-sm btn-danger eliminarCita" data-index="${index}">
            <i class="fa-solid fa-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(fila);
    });
  }

  // Generar ID automático
  function generarIdCita() {
    return "C" + Math.floor(Math.random() * 100000);
  }

  // Agregar cita con SweetAlert
  btnAgregarAgenda.addEventListener("click", async () => {
    const { value: formValues } = await Swal.fire({
      title: "Agregar nueva cita",
      customClass: "custom-alert",
      html: `
        <input id="idPaciente" class="swal2-input" placeholder="ID del paciente">
        <input id="idMedico" class="swal2-input" placeholder="ID del médico">
        <input id="fechaCita" type="date" class="swal2-input">
        <input id="motivo" class="swal2-input" placeholder="Motivo de la cita">
        <input id="estado" class="swal2-input" placeholder="Estado (Pendiente, Completada...)">
        <input id="observaciones" class="swal2-input" placeholder="Observaciones">
        <input id="direccion" class="swal2-input" placeholder="Dirección">
      `,
      confirmButtonText: "Guardar",
      showCancelButton: true,
      cancelButtonText: "Cancelar",
      focusConfirm: false,
      preConfirm: () => {
        const idPaciente = document.getElementById("idPaciente").value.trim();
        const idMedico = document.getElementById("idMedico").value.trim();
        const fechaCita = document.getElementById("fechaCita").value;
        const motivo = document.getElementById("motivo").value.trim();
        const estado = document.getElementById("estado").value.trim();
        const observaciones = document.getElementById("observaciones").value.trim();
        const direccion = document.getElementById("direccion").value.trim();

        if (!idPaciente || !idMedico || !fechaCita) {
          Swal.showValidationMessage("Por favor completa los campos obligatorios (Paciente, Médico y Fecha)");
          return false;
        }

        return { idPaciente, idMedico, fechaCita, motivo, estado, observaciones, direccion };
      }
    });

    if (formValues) {
      const nuevaCita = {
        idCita: generarIdCita(),
        ...formValues,
        fechaRegistro: new Date().toLocaleDateString(),
      };

      citas.push(nuevaCita);
      localStorage.setItem("citas", JSON.stringify(citas));
      renderizarCitas();

      Swal.fire({
        icon: "success",
        title: "Cita registrada",
        text: `La cita ${nuevaCita.idCita} fue agregada correctamente.`,
        confirmButtonText: "Aceptar",
        customClass: "custom-alert",
      });
    }
  });

  // Eliminar cita
  tbody.addEventListener("click", (e) => {
    const btn = e.target.closest(".eliminarCita");
    if (btn) {
      const index = btn.dataset.index;
      Swal.fire({
        title: "¿Eliminar cita?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        customClass: "custom-alert",
      }).then((result) => {
        if (result.isConfirmed) {
          citas.splice(index, 1);
          localStorage.setItem("citas", JSON.stringify(citas));
          renderizarCitas();

          Swal.fire({
            icon: "success",
            title: "Cita eliminada",
            text: "La cita se eliminó correctamente.",
            customClass: "custom-alert",
          });
        }
      });
    }
  });

  // Ver expediente clínico
  tbody.addEventListener("click", (e) => {
    const btn = e.target.closest(".verExpediente");
    if (btn) {
      const index = btn.dataset.index;
      const cita = citas[index];
      localStorage.setItem("expedienteActual", JSON.stringify(cita));

      Swal.fire({
        icon: "info",
        title: "Abriendo expediente clínico",
        text: `Cita ${cita.idCita}`,
        showConfirmButton: false,
        timer: 1500,
        customClass: "custom-alert",
      }).then(() => {
        window.location.href = "expediente.html";
      });
    }
  });

  // Cargar citas al iniciar
  renderizarCitas();
});
