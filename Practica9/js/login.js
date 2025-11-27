document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    // enviar datos al PHP
    fetch("php/login.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // guardar datos del usuario en sessionStorage
          sessionStorage.setItem('usuarioActivo', JSON.stringify({
            nombre: data.nombre,
            rol: data.rol,
            idMedico: data.idMedico
          }));

          Swal.fire({
            title: "¡Inicio de sesión exitoso!",
            text: `Bienvenido ${data.nombre}`,
            icon: "success",
            confirmButtonText: "Continuar",
            confirmButtonColor: "#3085d6",
            timer: 2000
          }).then(() => {
            // redirigir según el rol
            switch(data.rol) {
              case 'Super admin':
                window.location.href = "dash.html";
                break;
              case 'Medico':
                window.location.href = "dash_medico.html";
                break;
              case 'Secretaria':
                window.location.href = "dash_secretaria.html";
                break;
              case 'Paciente':
                window.location.href = "dash_paciente.html";
                break;
              default:
                window.location.href = "dash.html";
            }
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error de inicio de sesión",
            text: data.mensaje,
            confirmButtonColor: "#d33"
          });
        }
      })
      .catch(error => {
        console.error("Error:", error);
        Swal.fire({
          icon: "error",
          title: "Error de conexión",
          text: "No se pudo conectar con el servidor",
          confirmButtonColor: "#d33"
        });
      });
  });
});