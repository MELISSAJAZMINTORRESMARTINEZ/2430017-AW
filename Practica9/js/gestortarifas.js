document.addEventListener("DOMContentLoaded", () => {
  const btnAgregar = document.getElementById("btnAgregarPaciente"); // botón "Agregar Tarifa"

  btnAgregar.addEventListener("click", () => {
    Swal.fire({
      title: "Agregar Tarifa",
      html: `
        <input id="idTarifa" class="swal2-input" placeholder="ID de Tarifa">
        <input id="descripcion" class="swal2-input" placeholder="Descripción del servicio">
        <input id="costo" type="number" class="swal2-input" placeholder="Costo base">
        <input id="especialidad" class="swal2-input" placeholder="Especialidad">
        <select id="estatus" class="swal2-input">
          <option value="">Seleccionar estatus</option>
          <option value="Activo">Activo</option>
          <option value="Inactivo">Inactivo</option>
        </select>
      `,
      confirmButtonText: "Agregar Tarifa",
      showCancelButton: true,
      cancelButtonText: "Cancelar",
      confirmButtonColor: "#17a2b8",
      customClass: { popup: "custom-alert" },
    }).then((result) => {
    });
  });
});
