// pagos.js
document.addEventListener("DOMContentLoaded", () => {
  const btnAgregar = document.getElementById("btnAgregarPaciente"); // botón "Agregar Pago"

  btnAgregar.addEventListener("click", () => {
    Swal.fire({
      title: "Agregar Pago",
      html: `
        <input id="idCita" type="number" class="swal2-input" placeholder="ID de la cita">
        <input id="idPaciente" type="number" class="swal2-input" placeholder="ID del paciente">
        <input id="monto" type="number" class="swal2-input" placeholder="Monto del pago">
        <select id="metodo" class="swal2-input">
          <option value="" disabled selected>Método de pago</option>
          <option value="Efectivo">Efectivo</option>
          <option value="Tarjeta">Tarjeta</option>
          <option value="Transferencia">Transferencia</option>
        </select>
        <input id="fechaPago" type="date" class="swal2-input">
        <input id="referencia" class="swal2-input" placeholder="Referencia (opcional)">
        <select id="estatus" class="swal2-input">
          <option value="Completado">Completado</option>
          <option value="Pendiente">Pendiente</option>
          <option value="Cancelado">Cancelado</option>
        </select>
      `,
      confirmButtonText: "Guardar Pago",
      showCancelButton: true,
      cancelButtonText: "Cancelar",
      customClass: { popup: "custom-alert" },
    }).then((result) => {
    });
  });
});
