document.addEventListener("DOMContentLoaded", () => {
    // aqui esperamos a que cargue todo el html antes de correr el script
    const loginForm = document.getElementById("loginForm");

    // si existe el formulario de login entonces agregamos el evento
    if (loginForm) {

        loginForm.addEventListener("submit", async (e) => {

            // se evita que el formulario recargue la pagina
            e.preventDefault();

            // se toman el correo y contrasena del formulario
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();

            // si alguno esta vacio mostramos alerta
            if (!email || !password) {
                Swal.fire({
                    title: "campos vacios",
                    text: "por favor ingrese su correo y contrasena",
                    icon: "warning",
                    confirmButtonText: "aceptar"
                });
                return;
            }

            // mostramos un loading bonito mientras procesa
            Swal.fire({
                title: 'iniciando sesion...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // mandamos los datos al archivo php por fetch
                const response = await fetch('php/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        correo: email,
                        contrasena: password
                    })
                });

                // esperamos la respuesta del servidor
                const data = await response.json();

                // si todo salio bien se muestra mensaje de exito
                if (data.success) {
                    Swal.fire({
                        title: "bienvenido",
                        text: "inicio de sesion exitoso",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // redirige al dashboard despues de la alerta
                        window.location.href = "dash.php";
                    });

                } else {
                    // si hubo error de login se muestra alerta
                    Swal.fire({
                        title: "error",
                        text: data.error || "credenciales incorrectas",
                        icon: "error",
                        confirmButtonText: "intentar de nuevo"
                    });
                }

            } catch (error) {

                // si hubo un problema en la conexion se muestra esto
                console.error('error', error);
                
                Swal.fire({
                    title: "error de conexion",
                    text: "no se pudo conectar con el servidor",
                    icon: "error",
                    confirmButtonText: "aceptar"
                });
            }
        });
    }
});
