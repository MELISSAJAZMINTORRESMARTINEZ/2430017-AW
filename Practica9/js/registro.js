// js/registro.js

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registroForm");

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const nombre = document.getElementById("nombre").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmar = document.getElementById("confirmar").value.trim();

    // Validaciones básicas
    if (!nombre || !email || !password || !confirmar) {
      Swal.fire({
        icon: "warning",
        title: "Campos incompletos",
        text: "Completa todos los campos para continuar ",
        confirmButtonColor: "#00bfa5"
      });
      return;
    }

    if (password !== confirmar) {
      Swal.fire({
        icon: "error",
        title: "Las contraseñas no coinciden, lo chiento",
        text: "Verifica que sean iguales",
        confirmButtonColor: "#00bfa5"
      });
      return;
    }

    // Verificar si ya existe el usuario
    const usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];
    const existe = usuarios.find(u => u.email === email);

    if (existe) {
      Swal.fire({
        icon: "info",
        title: "El usuario ya existe ",
        text: "Ya hay una cuenta con este correo. Ingresa otro ",
        confirmButtonColor: "#00bfa5"
      });
      return;
    }

    // Guardar nuevo usuario
    usuarios.push({ nombre, email, password });
    localStorage.setItem("usuarios", JSON.stringify(usuarios));

    // Confirmación con SweetAlert
    Swal.fire({
      icon: "success",
      title: "¡Registro exitoso!",
      text: "Tu cuenta se ha creado correctamente.",
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    });

    // Limpiar y redirigir
    form.reset();
    setTimeout(() => {
      window.location.href = "index.html";
    }, 2000);
  });
});
