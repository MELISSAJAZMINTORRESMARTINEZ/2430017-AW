// login.js
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    // usuario por defecto
    const adminEmail = "admin@correo.com";
    const adminPass = "admin";

    let usuarioValido = null;

    // validar
    if (email === adminEmail && password === adminPass) {
      usuarioValido = { nombre: "admin", email: adminEmail };
    }

    if (usuarioValido) {
      // mostrar mensaje de bienvenida con SweetAlert
      Swal.fire({
        title: "¡Inicio de sesión exitoso!",
        text: `Bienvenido ${usuarioValido.nombre}`,
        icon: "success",
        confirmButtonText: "Continuar",
        confirmButtonColor: "#3085d6",
      }).then(() => {
        window.location.href = "dash.php";
      });
    } else {
      alert("Correo o contraseña incorrectos. Intenta nuevamente.");
    }
  });
});
