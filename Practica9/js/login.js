// js/registro.js
document.addEventListener("DOMContentLoaded", () => {
    const formRegistro = document.getElementById("registro");
    
    if (formRegistro) {
        formRegistro.addEventListener("submit", async (e) => {
            e.preventDefault();
            
            const nombre = document.getElementById("nombreR").value.trim();
            const correo = document.getElementById("correoR").value.trim();
            const contrasena = document.getElementById("contraR").value.trim();
            
            // Validación básica
            if (nombre === "" || correo === "" || contrasena === "") {
                Swal.fire({
                    title: "Campos incompletos",
                    text: "Por favor, complete todos los campos",
                    icon: "warning",
                    confirmButtonText: "Aceptar"
                });
                return;
            }
            
            if (contrasena.length < 6) {
                Swal.fire({
                    title: "Contraseña débil",
                    text: "La contraseña debe tener al menos 6 caracteres",
                    icon: "warning",
                    confirmButtonText: "Aceptar"
                });
                return;
            }
            
            // Validar formato de correo
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(correo)) {
                Swal.fire({
                    title: "Correo inválido",
                    text: "Por favor, ingrese un correo electrónico válido",
                    icon: "warning",
                    confirmButtonText: "Aceptar"
                });
                return;
            }
            
            // Mostrar loader
            Swal.fire({
                title: 'Registrando usuario...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            try {
                const response = await fetch('registro.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nombre: nombre,
                        correo: correo,
                        contrasena: contrasena
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        title: "¡Registro exitoso!",
                        text: "Usuario creado correctamente. Ahora puedes iniciar sesión",
                        icon: "success",
                        confirmButtonText: "Continuar"
                    }).then(() => {
                        formRegistro.reset();
                        // Opcional: Cambiar al formulario de login si tienes tabs
                        // document.getElementById("tab-login").click();
                    });
                } else {
                    Swal.fire({
                        title: "Error al registrar",
                        text: data.error || "No se pudo crear el usuario",
                        icon: "error",
                        confirmButtonText: "Reintentar"
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    title: "Error",
                    text: "Error al conectar con el servidor",
                    icon: "error",
                    confirmButtonText: "Aceptar"
                });
            }
        });
    }
});