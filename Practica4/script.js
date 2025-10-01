const form = document.getElementById("registro");
let usuarios = JSON.parse(localStorage.getItem("usuarios")) || []; 

form.addEventListener("submit", e =>{
    e.preventDefault();
    
    const nombre = document.getElementById("nombre").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const contraseña = document.getElementById("contraseña").value.trim();

    if(!nombre || !correo || !contraseña){
        alert("Por favor ingresa todos los campos");
        return;
    }

    const usuario = { correo, contraseña };

    usuarios.push(usuario);

    localStorage.setItem("usuarios", JSON.stringify(usuarios)); // guardamos en la misma clave

    form.reset();

    window.location.href = "inicio.html";
});
