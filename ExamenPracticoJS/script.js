   const form = document.getElementById("formRegistro");

    form.addEventListener("submit", function(e) {
      e.preventDefault();

      // limpiar errores
      document.getElementById("errorNombre").textContent = "";
      document.getElementById("errorCorreo").textContent = "";
      document.getElementById("errorPassword").textContent = "";
      document.getElementById("errorConfirmar").textContent = "";

      const nombre = document.getElementById("nombre").value.trim();
      const correo = document.getElementById("correo").value.trim();
      const password = document.getElementById("password").value;
      const confirmar = document.getElementById("confirmar").value;
      let valido = true;

      if (nombre === "") {
        document.getElementById("errorNombre").textContent = "El nombre es obligatorio";
        valido = false;
      }

      if (correo === "") {
        document.getElementById("errorCorreo").textContent = "El correo es obligatorio";
        valido = false;
      } else if (correo.indexOf("@") === -1 || correo.indexOf(".") === -1) {
        document.getElementById("errorCorreo").textContent = "Correo no válido";
        valido = false;
      }

      if (password === "") {
        document.getElementById("errorPassword").textContent = "La contraseña es obligatoria";
        valido = false;
      } else if (password.length < 6) {
        document.getElementById("errorPassword").textContent = "Debe tener al menos 6 caracteres";
        valido = false;
      }

      if (confirmar === "") {
        document.getElementById("errorConfirmar").textContent = "Confirma la contraseña";
        valido = false;
      } else if (password !== confirmar) {
        document.getElementById("errorConfirmar").textContent = "Las contraseñas no coinciden";
        valido = false;
      }

      if (valido) {
        alert("¡Registro exitoso!");
        form.reset();
      }
    });