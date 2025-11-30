document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");

    if (loginForm) {
        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();

            if (!email || !password) {
                Swal.fire({
                    title: "Campos vacíos",
                    text: "Por favor, ingrese su correo y contraseña",
                    icon: "warning",
                    confirmButtonText: "Aceptar"
                });
                return;
            }

            // Mostrar loader
            Swal.fire({
                title: 'Iniciando sesión...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
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

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        title: "¡Bienvenido!",
                        text: "Inicio de sesión exitoso",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "dash.php";
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: data.error || "Credenciales incorrectas",
                        icon: "error",
                        confirmButtonText: "Intentar de nuevo"
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    title: "Error de conexión",
                    text: "No se pudo conectar con el servidor",
                    icon: "error",
                    confirmButtonText: "Aceptar"
                });
            }
        });
    }
});