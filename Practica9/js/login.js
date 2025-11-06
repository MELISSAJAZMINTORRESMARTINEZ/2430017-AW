//login.js
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    // recupera los usuarios guardados en localStorage
    const usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

//busca si hay algun usuario que coincida con el correo y la contraseña ingresados
    const usuarioValido =
      usuarios.find((u) => u.email === email && u.password === password) ||
      (email === "admin@correo.com" && password === "admin" ? {nombre: "admin", email: "admin@correo.com"} : null);



    if (usuarioValido) {
      // guardamos al usuario 
      localStorage.setItem("usuarioActivo", JSON.stringify(usuarioValido));

      // mostrar mensaje de bienvenida con SweetAlert
      Swal.fire({
        title: "¡Inicio de sesión exitoso!",
        text: `Bienvenido ${usuarioValido.nombre}`,
        icon: "success",
        confirmButtonText: "Continuar",
        confirmButtonColor: "#3085d6",
      }).then(() => {
        //redirigimos a dash.html
        window.location.href = "dash.html";
      });
    } else {
      alert(" Correo o contraseña incorrectos. Intenta nuevamente.");
    }
  });
});

