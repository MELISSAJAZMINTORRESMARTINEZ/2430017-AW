// 
const formu = document.getElementById("inicio"); 
let usuarios = JSON.parse(localStorage.getItem("usuarios")) || []; // arreglo de usuario guardado en local storage
console.log(localStorage.getItem("Correo"));

if (localStorage.getItem("Correito")) {
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
            alert("no se encontro pendejo");
            return;
        } else {
            localStorage.setItem("Correito", UCorreo);
            window.location.href = "dashboard.html";
        } 
    });
}


if (window.location.pathname.includes("dashboard.html")) {

    // Recuperamos usuario activo
    const usuarioActivo = localStorage.getItem("Correito");
    const bienvenida = document.getElementById("bienvenida");
    if (bienvenida) {
        bienvenida.textContent = "Bienvenido, " + usuarioActivo;
    }

    // Cada usuario tendrá sus tareas guardadas en localStorage
    let tareas = JSON.parse(localStorage.getItem("tareas_" + usuarioActivo)) || [];

}




