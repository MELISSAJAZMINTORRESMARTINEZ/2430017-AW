// Requiere: SweetAlert2
document.addEventListener('DOMContentLoaded', function () {
  const btnAgregar = document.getElementById('btnAgenda');

  btnAgregar.addEventListener('click', () => {
    const todayISO = new Date().toISOString().slice(0, 10);

    Swal.fire({
      title: 'Agregar cita',
      html: `
        <input id="paciente" class="swal2-input" placeholder="Nombre del paciente">
        <input id="motivo" class="swal2-input" placeholder="Motivo (opcional)">
        <div style="display:flex; gap:10px;">
          <input id="fecha" type="date" class="swal2-input" style="flex:1;" value="${todayISO}" min="${todayISO}">
          <input id="hora" type="time" class="swal2-input" style="flex:1;" value="09:00">
        </div>
        <input id="direccion" class="swal2-input" placeholder="Dirección (opcional)">
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Agregar',
      cancelButtonText: 'Cancelar',
      customClass: { popup: 'custom-alert' }
    }).then(result => {
      // ✅ No hace nada funcional: simplemente se cierra el modal.
      // Si presiona "Agregar" o "Cancelar", no se guarda ni muestra alerta.
      if (result.isConfirmed || result.isDismissed) {
        // Modal se cierra automáticamente, sin acción adicional.
      }
    });
  });
});
