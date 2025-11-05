// Función para crear los campos de cada materia
function crearFormulario() {
  const numMaterias = parseInt(document.getElementById('numMaterias').value);//obtiene el numero de materias que escribio el usuario
  const contenedor = document.getElementById('formularioMaterias');//busca en el html el contenedro dnd se van a poner los formularios
  contenedor.innerHTML = ''; // lo limpia si ya se habia generado

  if (numMaterias > 100) {
        alert("No se pueden ingresar más de 100 materias.");
        return; // Evita que se generen más campos
      }


  // bucle para crear los campos de cada materia
  for (let i = 1; i <= numMaterias; i++) {
    const div = document.createElement('div');//crea un div que contendra los inputs de cada materia
    div.className = 'materia';//lo que hace este es que asigna la clase css materia para poder poneerle estilo
    div.innerHTML = `
      <h3>Materia ${i}</h3>
      <label>Nombre de la materia:</label>
      <input type="text" id="materia${i}" required><br>
      <label>Unidad 1:</label><input type="number" id="u1_${i}" >
      <label>Unidad 2:</label><input type="number" id="u2_${i}">
      <label>Unidad 3:</label><input type="number" id="u3_${i}">
      <label>Unidad 4:</label><input type="number" id="u4_${i}">
    `;//insertar dentro del div los inputs necesarios para cada materia

    contenedor.appendChild(div);//aagrega el div al contenedor principal

    
  }
  

  //creamos el boton para calcular los promedios
  const boton = document.createElement('button');
  boton.type = 'button';//que no se recargue la pagina
  boton.textContent = 'Calcular Promedios';//texto del boton
  boton.onclick = calcularPromedios;
  contenedor.appendChild(boton);//boton al contenedor principal, se agrega abajo de los formularios
}

//funcion para calcular los promedio de las materias 
function calcularPromedios() {
  const numMaterias = parseInt(document.getElementById('numMaterias').value);
  let resultados = '<h3>Resultados:</h3>';

  for (let i = 1; i <= numMaterias; i++) {
    // obtiene los datos de cada materia y se convierten a numero decimal 
    const nombre = document.getElementById(`materia${i}`).value || `Materia ${i}`;
    const u1 = parseFloat(document.getElementById(`u1_${i}`).value);
    const u2 = parseFloat(document.getElementById(`u2_${i}`).value);
    const u3 = parseFloat(document.getElementById(`u3_${i}`).value);
    const u4 = parseFloat(document.getElementById(`u4_${i}`).value);

    if (u1 < 0 || u2 < 0 || u3 < 0 || u4 < 0) {
          alert("Las calificaciones no pueden ser negativas");
          return;
        }
    

    // calcular proemdio
    let promedio = (u1 + u2 + u3 + u4) / 4;
    let estado = "Aprobado";

    //aqui si alguna unidad es menos de 70 el promedio sera de 60 y sera reprobadoo
    if (u1 < 70 || u2 < 70 || u3 < 70 || u4 < 70) {
      promedio = 60;
      estado = "No aprobado";
    }
  

    // agrega el resultado de la materia al final
    resultados += `
      <p><strong>${nombre}</strong><br>
      Promedio: ${promedio.toFixed(2)}<br>
      Estado: ${estado}</p>
    `;
  }

  // Mostrar resultados en pantalla
  document.getElementById('resultados').innerHTML = resultados;
}
