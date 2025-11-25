//inicializar calendario
document.addEventListener('DOMContentLoaded', function () {

  const calendarEl = document.getElementById('calendar');

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    height: 'auto',

    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
  });

  calendar.render();


  //boton para agregar cita
  const btnAgregar = document.getElementById('btnAgenda');

  btnAgregar.addEventListener('click', () => {
    const todayISO = new Date().toISOString().slice(0, 10);

    Swal.fire({
      title: 'Agregar cita',
      html: `
        <input id="cita" class="swal2-input" placeholder="Id Cita">
        <input id="paciente" class="swal2-input" placeholder="Id Paciente">
        <input id="medico" class="swal2-input" placeholder="Id Medico">
        <input id="motivo" class="swal2-input" placeholder="Motivo">
        <div style="display:flex; gap:10px;">
           <input id="fecha" type="date" class="swal2-input" style="flex:1;" value="${todayISO}" min="${todayISO}">
           <input id="hora" type="time" class="swal2-input" style="flex:1;" value="09:00">
        </div>
        <input id="estado" class="swal2-input" placeholder="Estado Cita">
        <input id="observacion" class="swal2-input" placeholder="Observacion">


      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Agregar',
      cancelButtonText: 'Cancelar',
      customClass: { popup: 'custom-alert' }
    }).then(result => {

      if (result.isConfirmed) {

        // obtener datos
        const pac = document.getElementById('paciente').value;
        const fecha = document.getElementById('fecha').value;
        const hora = document.getElementById('hora').value;

        if (!pac || !fecha || !hora) {
          Swal.fire("Error", "Debe llenar los campos obligatorios.", "error");
          return;
        }

        // agregar evento al calendario
        calendar.addEvent({
          title: pac,
          start: `${fecha}T${hora}`,
        });

        Swal.fire("Éxito", "Cita agregada correctamente.", "success");
      }
    });
  });

});
