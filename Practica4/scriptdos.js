const formu = document.getElementById("inicio"); 
let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

formu.addEventListener("submit", e =>{
    e.preventDefault();

    const UCorreo = document.getElementById("correo").value.trim();
    const UContraseña = document.getElementById("contraseña").value.trim();

    if(!UCorreo || !UContraseña){
        alert("Por favor rellena los campos");
        return;
    }

    const usuarioValido = usuarios.find(u => u.correo === UCorreo && u.contraseña === UContraseña);

    if(usuarioValido){

        const guardamos = {correo: usuarioValido.correo};

        localStorage.setItem("guardamos", JSON.stringify(guardamos))
        window.location.href = "nuevo.html"
    } 
});
