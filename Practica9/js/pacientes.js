// solo muestra el modal, sin guardar ni validar
document.addEventListener("DOMContentLoaded", () => {
  const btnAgregar = document.getElementById("btnAgregarPaciente");

  btnAgregar.addEventListener("click", () => {
    const hoy = new Date().toISOString().split("T")[0];

    Swal.fire({
      title: "Agregar Paciente",
      html: `
        <input id="nombre" class="swal2-input" placeholder="Nombre completo">
        <input id="curp" class="swal2-input" placeholder="CURP">
        <input id="fecha" type="date" class="swal2-input" min="${hoy}">
        <select id="sexo" class="swal2-input">
          <option value="">Sexo</option>
          <option>Femenino</option>
          <option>Masculino</option>
        </select>
        <input id="telefono" class="swal2-input" placeholder="Teléfono">
        <input id="correo" type="email" class="swal2-input" placeholder="Correo electrónico">
        <input id="direccion" class="swal2-input" placeholder="Dirección">
        <input id="emergencia" class="swal2-input" placeholder="Teléfono de emergencia">
        <input id="alergias" class="swal2-input" placeholder="Alergias">
      `,
      confirmButtonText: "Agregar",
      showCancelButton: true,
      cancelButtonText: "Cancelar",
      confirmButtonColor: "#17a2b8",
      allowOutsideClick: false,
      didOpen: () => {
        console.log("solo se muestra el modal, sin funcionalidad");
      }
    });
  });
});
