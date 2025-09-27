let alumnos = [];

// iniciar con dom
document.addEventListener('DOMContentLoaded', function(){
    // Cargar datos del localStorage
    if(localStorage.getItem('alumnos')){
        alumnos = JSON.parse(localStorage.getItem('alumnos'));
    }

    // Event listener para el botón guardar
    document.getElementById('guardarBtn').addEventListener('click', function() {
        guardarAlumno();
    });

    // Event listener para el botón limpiar
    document.getElementById('limpiarBtn').addEventListener('click', function() {
        document.getElementById('formAlumno').reset();
    });

    inicializarTabla();
});

//funcion para poder guardar a el alumno

function guardarAlumno(){
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
    //Crear el objeto alumni
    let alumno = { matricula, nombre, carrera, email, telefono};

    //Agrgeamos al vector
    alumnos.push(alumno);
    guardarEnLocalStorage();
    actualizarTabla();

    console.log("Alumno Registrado:", alumno);
    console.log("Todo el vector:", alumnos);

    document ("formAlumno").reset();

    alert ("Alumno registradro:3");

}
//inicializa la tablaal inicio
function inicializarTabla(){
    actualizarTabla();
}
// Funcin para actualizar la tabla HTML
//si no hay algumos mostramos un msj
function actualizarTabla(){
    const tbody = document.querySelector("tbody");

    if(alumnos.length === 0){//revisa si el arreglo esta vacio y dvuelve cuantos alumnos hay en la lista
        tbody.innerHTML = ` <tr>
        <td colspan="6" class="text-muted">No hay alumnos registrados</td>
            </tr>`;
            return;
    }
    //si hay alumnos los mostramos en la tabla 
    tbody.innerHTML = alumnos.map ((alumno, index) => `
      <tr>
            <td>${alumno.matricula}</td>
            <td>${alumno.nombre}</td>
            <td>${alumno.carrera}</td>
            <td>${alumno.email}</td>
            <td>${alumno.telefono}</td>
            <td>
     `).join('');
}

function guardarEnLocalStorage(){
    localStorage.setItem('alumnos', JSON.stringify(alumnos));
}

