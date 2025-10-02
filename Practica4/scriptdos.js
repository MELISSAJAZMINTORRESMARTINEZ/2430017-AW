const formu = document.getElementById("inicio"); //formulario de inicio de sesion
let usuarios = JSON.parse(localStorage.getItem("usuarios")) || []; // arreglo de usuario guardado en local storage
console.log(localStorage.getItem("Correo"));

if (localStorage.getItem("Correito")) {//si hay usuarios activos, mostramos un msj de bienvendia
    const textito = document.getElementById("textito");
    if (textito) {
        textito.innerHTML = "Holita, " + localStorage.getItem("Correito");
    }
}

if (formu) {
    formu.addEventListener("submit", e => {
        e.preventDefault();

        const UCorreo = document.getElementById("correo").value.trim();
        const UContraseña = document.getElementById("contraseña").value.trim();

        // verificamos que los campos esten rellenos
        if (!UCorreo || !UContraseña) {
            alert("Por favor rellena los campos");
            return;
        }

        // buscamos en el arreglo de usuario si existe un usuario con el correo y la contraseña ingresada
        const usuarioValido = usuarios.find(u => u.correo === UCorreo && u.contraseña === UContraseña);

        // si se encuentra un usuario valido, mostraremos un msj de bienvenida 
        if (!usuarioValido) {
            alert("no se encontro");
            return;
        } else {//si existe, guardamos el usuario activo y redirigimos al dashboard
            localStorage.setItem("Correito", UCorreo);
            window.location.href = "dashboard.html";
        } 
    });
}
//solo se ejecutara si estamos en el dahboard
if (window.location.pathname.includes("dashboard.html")) {

    // usuario actvio
    const usuarioActivo = localStorage.getItem("Correito");
    //msj de bienvenida
    const bienvenida = document.getElementById("bienvenida");

    // recuperamos las tarea desde el localstorage
    let tareas = JSON.parse(localStorage.getItem("tareas_" + usuarioActivo)) || [];

    // ref
    const formTarea = document.getElementById("formTarea");
    const tablaTareas = document.getElementById("tablaTareas");

    // recoremos cada tarea y mostramos la tabla
    function Tareas() {
        tablaTareas.innerHTML = "";

        tareas.forEach((tarea, index) => {
            const fila = document.createElement("tr");

            fila.innerHTML = `
                <td>${index + 1}</td>
                <td>${tarea.nombre}</td>
                <td>${tarea.descripcion}</td>
                <td><button onclick="eliminarTarea(${index})">Eliminar</button></td>
            `;

            tablaTareas.appendChild(fila);
        });
    }

    // agregar tarea
    if (formTarea) {
        formTarea.addEventListener("submit", e => {
            e.preventDefault();

            const nombre = document.getElementById("nombreTarea").value.trim();
            const descripcion = document.getElementById("descripcionTarea").value.trim();

            if (!nombre || !descripcion) {
                alert("Por favor rellena todos los campos");
                return;
            }

            // guardamos la tarea
            tareas.push({ nombre, descripcion });
            localStorage.setItem("tareas_" + usuarioActivo, JSON.stringify(tareas));

            // mostramos de nuevo
            Tareas();l
            // limpiamos formulario
            formTarea.reset();
        });
    }

    // eliminar la tarea
    window.eliminarTarea = function(index) {
        tareas.splice(index, 1);
        //actualizamos localstorage
        localStorage.setItem("tareas_" + usuarioActivo, JSON.stringify(tareas));
        //volvemos a mostrar la tabla
        Tareas();
        //por ultimo mostramos una alerta de que la tarea fue eliminada
        alert("La tarea ha sido elimininada"); //ya no puse el de editar tarea pq son las 3 de la mañana y tengo sueño
    };

    // inicializamos la tabla al cargar
    if (tablaTareas) {
        Tareas();
    }
}





