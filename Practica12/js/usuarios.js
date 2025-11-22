document.addEventListener("DOMContentLoaded", () => {
  const tabla = document.querySelector("#tablaPacientes tbody");
  const btnGuardar = document.getElementById("btnGuardar");
  const form = document.getElementById("formUsuario");

  btnGuardar.addEventListener("click", () => {
    const idUsuario = document.getElementById("idUsuario").value;
    const usuario = document.getElementById("usuario").value;
    const contrasena = document.getElementById("contrasena").value;
    const rol = document.getElementById("rol").value;
    const idMedico = document.getElementById("idMedico").value;
    const activo = document.getElementById("activo").value;
    const ultimoAcceso = document.getElementById("ultimoAcceso").value;

    if (!usuario || !contrasena || !rol) {
      Swal.fire("Error", "Completa los campos obligatorios", "warning");
      return;
    }

    const fila = document.createElement("tr");
    fila.innerHTML = `
      <td>${idUsuario}</td>
      <td>${usuario}</td>
      <td>${contrasena}</td>
      <td>${rol}</td>
      <td>${idMedico}</td>
      <td>${activo}</td>
      <td>${ultimoAcceso}</td>
    `;

    tabla.appendChild(fila);
    Swal.fire("Éxito", "Usuario agregado correctamente", "success");

    form.reset();
    const modal = bootstrap.Modal.getInstance(document.getElementById("modalUsuario"));
    modal.hide();
  });
});
