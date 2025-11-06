// pacientes.js

document.addEventListener("DOMContentLoaded", () => {//esperamos a que el contenido del DOM cargue 
    //referencia al boton d agrear paciente 
  const btnAgregar = document.getElementById("btnAgregarPaciente");
  //ref al cuerpo de la tabla 
  const tabla = document.querySelector("#tablaPacientes tbody");

  //se creo un contador para poder asignar un I
  let id = 1;

  btnAgregar.addEventListener("click", () => {
    Swal.fire({
        //se mostrara un formulario emergente con campos para el nuevo paciente 
      title: "Agregar Paciente",
      html: `
        <input id="nombre" class="swal2-input" placeholder="Nombre completo">
        <input id="curp" class="swal2-input" placeholder="CURP">
        <input id="fecha" type="date" class="swal2-input">
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
      confirmButtonText: "Agregar Paciente",
      showCancelButton: true,
      cancelButtonText: "Cancelar",
      confirmButtonColor: "#17a2b8",
      preConfirm: () => {
        //se obtienen los valores ingresados en los campos del form
        const nombre = document.getElementById("nombre").value.trim();
        const curp = document.getElementById("curp").value.trim();
        const fecha = document.getElementById("fecha").value.trim();
        const sexo = document.getElementById("sexo").value;
        const telefono = document.getElementById("telefono").value.trim();
        const correo = document.getElementById("correo").value.trim();
        const direccion = document.getElementById("direccion").value.trim();
        const emergencia = document.getElementById("emergencia").value.trim();
        const alergias = document.getElementById("alergias").value.trim();

        //una pequeña validacion para q los campos sean obligatorios 
        if (!nombre || !curp || !fecha || !sexo) {
          alert("Por favor, completa los campos obligatorios");
          return false;
        }

        //se devuelve un objeto con los datos del paciente
        return {
          nombre,
          curp,
          fecha,
          sexo,
          telefono,
          correo,
          direccion,
          emergencia,
          alergias,
        };
      },
    }).then((result) => {
      if (result.isConfirmed) {
        const datos = result.value;

        // agregamos una nueva fila 
        const fila = document.createElement("tr");
        fila.innerHTML = `
          <td>${id++}</td>
          <td>${datos.nombre}</td>
          <td>${datos.curp}</td>
          <td>${datos.fecha}</td>
          <td>${datos.sexo}</td>
          <td>${datos.telefono}</td>
          <td>${datos.correo}</td>
          <td>${datos.direccion}</td>
          <td>${datos.emergencia}</td>
          <td>${datos.alergias}</td>
          <td>${new Date().toLocaleDateString()}</td> `;
        tabla.appendChild(fila);

        //se muestra un sweet alert de que se agrego correctamente el paciente 
        Swal.fire({
           title: '¡Paciente agregado!',
  text: 'El registro se completó exitosamente.',
  icon: 'success',
  confirmButtonText: 'Aceptar',
  customClass: {
    popup: 'custom-alert'  // Aplica el estilo que definimos en CSS
  }
        });
      }
    });
  });
});
