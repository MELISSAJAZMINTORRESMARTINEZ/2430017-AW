const form = document.getElementById("registro");
let usuarios = JSON.parse(localStorage.getItem("usuarios")) || []; //arreglo de usuarios guardado en localstorage

form.addEventListener("submit", e =>{ //agregamos un evento que se ejecute cuando el formulario se envia
    e.preventDefault(); //evita que recargue la pagina al enviar el formulrio
    

    //aqui obtenemos los valores que ingresamos en los inputs del html
    const nombre = document.getElementById("nombre").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const contraseña = document.getElementById("contraseña").value.trim();

    if(!nombre || !correo || !contraseña){ //verificamos que todos los campos esten rellenos
        alert("Por favor ingresa todos los campos");
        return;
    }

    const usuario = { correo, contraseña }; //creamos un objeto con los datos del usuario

    usuarios.push(usuario);//agregamos nuevo usuario al arreglo

//guaradmos el arreglo actualizado en localstorgae 
    localStorage.setItem("usuarios", JSON.stringify(usuarios)); // guardamos en la misma clave

    form.reset();//limpiamos el formualrio para que quede sin nada

    window.location.href = "inicio.html"; //aqui estoy redirigiendo al inicio.html (inicio sesion)
});
