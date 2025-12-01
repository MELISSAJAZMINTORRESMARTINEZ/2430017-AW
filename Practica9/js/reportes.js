// reportes.js
document.addEventListener("DOMContentLoaded", () => {
  const btnAgregar = document.getElementById("btnAgregarPaciente"); // Botón "Agregar Reporte"

  btnAgregar.addEventListener("click", () => {
    Swal.fire({
      title: "Agregar Reporte",
      html: `
        <input id="idReporte" type="number" class="swal2-input" placeholder="ID del Reporte">
        <select id="tipoReporte" class="swal2-input">
          <option value="" disabled selected>Tipo de Reporte</option>
          <option value="Diagnóstico">Diagnóstico</option>
          <option value="Tratamiento">Tratamiento</option>
          <option value="Seguimiento">Seguimiento</option>
          <option value="Otro">Otro</option>
        </select>
        <input id="idPaciente" type="number" class="swal2-input" placeholder="ID del Paciente">
        <input id="idMedico" type="number" class="swal2-input" placeholder="ID del Médico">
        <input id="fechaGeneracion" type="date" class="swal2-input">
        <input id="rutaArchivo" class="swal2-input" placeholder="Ruta del archivo o documento">
        <textarea id="descripcion" class="swal2-textarea" placeholder="Descripción del reporte"></textarea>
        <input id="generadoPor" class="swal2-input" placeholder="Generado por">
      `,
      confirmButtonText: "Guardar Reporte",
      showCancelButton: true,
      cancelButtonText: "Cancelar",
      confirmButtonColor: "#198754",
      customClass: { popup: "custom-alert" },
      width: 420,
    }).then((result) => {
    });
  });
});
