let alumnos = [];

// iniciar con dom
document.addEventListener('DOMContentLoaded', function(){
    // Cargar
    if(localStorage.getItem('alumnos')){
        alumnos = JSON.parse(localStorage.getItem('alumnos'));
    }

    document.getElementById('alumnosForm').addEventListener('submit', function(e){
        e.preventDefault(); 
        guardarAlumno();
        
    });

    inicializarTabla();
});

document.getElementById("guardarBtn").addEventListener ("click", function() {
    let matricula = document.getElementById("matricula").value;
    let nombre = document.getElementById("nombre").value;
    let carrera = document.getElementById("carrera").value;
    let email = document.getElementById("email").value;
    let telefono = document.getElementById("telefono").value;


    //validar que no haya matriculas duplicada
    if (alumnos.some(alumno => alumno.matricula === matricula)){
        alert ("La matricula ya existe, ingrese otra");
        return;
    }

    if (alumnos.some(alumno => alumno.telefono === telefono)){
        alert ("Este telefono ya existe. Por favor, ingrese otro");
        return;
    }

    if(alumnos.some(alumno  => alumno.email == email)){
        alert ("Este email ya existe. Por favir, ingreseotro ");
        return;
    }
    //Crear el objeto alumni
    let alumno = { matricula, nombre, carrera, email, telefono};

    //Agrgeamos al vector
    alumnos.push(alumno);


    function actualizarTabla() {
      const tbody = document.getElementById("studentTableBody");
      if (alumnos.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center">No hay datos disponibles</td></tr>`;
        return;
      }
      tbody.innerHTML = alumnos.map(alumno => `
        <tr>
          <td>${alumno.matricula}</td>
          <td>${alumno.nombre}</td>
          <td>${alumno.carrera}</td>
          <td>${alumno.email}</td>
          <td>${alumno.telefono}</td>
        </tr>
      `).join("");
    }

    console.log("Alumno Registrado:", alumno);
    console.log("Todo el vector:", alumnos);

    //se limpia el forumlario
    document.getElementById("formAlumno").reset();
});
    document.getElementById("limpiarBtn").addEventListener("click", function() {
        document.getElementById("formAlumno").reset();
});
