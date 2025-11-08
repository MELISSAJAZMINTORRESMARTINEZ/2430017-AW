// medicos.js
document.addEventListener("DOMContentLoaded", () => {
  const btnAgregar = document.getElementById("btnAgregarMedico");

  btnAgregar.addEventListener("click", () => {
    const hoy = new Date().toISOString().split("T")[0];

    Swal.fire({
      title: "Agregar Médico",
      html: `
        <input id="nombre" class="swal2-input" placeholder="Nombre completo">
        <input id="cedula" class="swal2-input" placeholder="Cédula profesional">
        <input id="fecha" type="date" class="swal2-input" min="${hoy}">
        <input id="especialidad" class="swal2-input" placeholder="Especialidad">
        <input id="telefono" class="swal2-input" placeholder="Teléfono">
        <input id="correo" type="email" class="swal2-input" placeholder="Correo electrónico">
        <label style="font-size: 14px; display: block; margin-top: 5px;">Horario de atención:</label>
        <div style="display: flex; gap: 5px; justify-content: center;">
          <input id="horaInicio" type="time" class="swal2-input" style="width: 45%;">
          <input id="horaFin" type="time" class="swal2-input" style="width: 45%;">
        </div>
      `,
      confirmButtonText: "Agregar Médico",
      showCancelButton: true,
      cancelButtonText: "Cancelar",
      confirmButtonColor: "#17a2b8",
      customClass: { popup: "custom-alert" },
    }).then((result) => {
      // No hace nada al confirmar ni al cancelar
    });
  });
});
