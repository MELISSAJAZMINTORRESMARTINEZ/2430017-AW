// bitacora.js
document.addEventListener("DOMContentLoaded", () => {
  const btnAgregar = document.getElementById("btnAgregarMedico"); // Botón "Agregar Bitácora"

  btnAgregar.addEventListener("click", () => {
    Swal.fire({
      title: "Agregar Registro de Bitácora",
      html: `
        <input id="idBitacora" type="number" class="swal2-input" placeholder="ID de la bitácora">
        <input id="idUsuario" type="number" class="swal2-input" placeholder="ID del usuario">
        <input id="fechaAcceso" type="datetime-local" class="swal2-input">
        <input id="accion" class="swal2-input" placeholder="Acción realizada">
        <input id="modulo" class="swal2-input" placeholder="Módulo">
      `,
      confirmButtonText: "Guardar Bitácora",
      showCancelButton: true,
      cancelButtonText: "Cancelar",
      confirmButtonColor: "#198754",
      customClass: { popup: "custom-alert" },
    }).then((result) => {
    });
  });
});
