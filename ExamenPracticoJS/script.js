const form = document.getElementById("form");

form.addEventListener("submit", e =>{
    

    const nombre = document.getElementById("nombre").value.trim;
    const correo = document.getElementById("correo").value.trim;
    const contra = document.getElementById("contra").value.trim;
    const contraC = document.getElementById("contraC").value.trim;

      if(!nombre || !correo || !contra || !contraC){ 
        alert("Ingresa todos los campos requeridos");
        return;
    }

    if(!contra || !contraC < 6){
        alert("La contraseña debe ser menor a 6");
    } 
    if(!contra == !contraC){
        alert("Registro exitoso");
    } else {
        alert("las contraseñas no coinciden")
    }

    if(!nombre){
        
    }

    
    


});
