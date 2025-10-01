const form = document.getElementById("registro");
let usuarios =[];

form.addEventListener("submit", e =>{
    e.preventDefault();
    
    const nombre = document.getElementById("nombre").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const contraseña = document.getElementById("contraseña").value.trim();

    if(!nombre || !correo || !contraseña){
        alert ("Porfavor ingresa todos los campos");
        return;
    }

    const usuario = {correo, contraseña}
    const vector = {vector: usuarios}

    usuarios.push(usuario);

    form.reset();

    localStorage.setItem("Usuario", JSON.stringify(usuarios))
    window.location.href = "inicio.html";
    console.log(usuarios);
});
