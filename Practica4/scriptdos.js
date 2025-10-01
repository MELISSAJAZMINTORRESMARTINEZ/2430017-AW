const formu = document.getElementById("inicio"); 
let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];//arreglo de usuario guardado en local storage

formu.addEventListener("submit", e =>{
    e.preventDefault();

    const UCorreo = document.getElementById("correo").value.trim();
    const UContraseña = document.getElementById("contraseña").value.trim();

    //verificamos que los campos esten rellenos
    if(!UCorreo || !UContraseña){
        alert("Por favor rellena los campos");
        return;
    }
 //buscamos en el arreglo de usuario si existe un usuario con el correoy la contraseña ingresas
    const usuarioValido = usuarios.some(u => u.correo === UCorreo && u.contraseña === UContraseña);
 
    //si se encuentra un usuario valido, mostraremos un msj de bienvenida 
    if(usuarioValido){
        alert("Bienvenido " + UCorreo);
    } 
});
