// especialidades.js
document.addEventListener("DOMContentLoaded", () => {
  const btnAgregar = document.getElementById("btnAgregarMedico"); // botón "Agregar Especialidad"

  btnAgregar.addEventListener("click", () => {
    Swal.fire({
      title: "Agregar Especialidad Médica",
      html: `
        <input id="nombre" class="swal2-input" placeholder="Nombre de la especialidad">
        <textarea id="descripcion" class="swal2-textarea" placeholder="Descripción de la especialidad" style="height: 100px; resize: none;"></textarea>
      `,
      confirmButtonText: "Guardar Especialidad",
      showCancelButton: true,
      cancelButtonText: "Cancelar",
      confirmButtonColor: "#198754",
      customClass: { popup: "custom-alert" },
    }).then((result) => {
    });
  });
});
