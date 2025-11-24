document.addEventListener("DOMContentLoaded", function () { 
    // espero a que la página termine de cargar antes de ejecutar algo
    
    cargarTarifas(); // cargo la tabla de tarifas al iniciar

    const form = document.querySelector("#formTarifas"); // obtengo el formulario
    form.addEventListener("submit", function (e) { 
        e.preventDefault(); // evito que la página se recargue
        guardarTarifa(new FormData(form)); // envío los datos al PHP
    });

    // cuando se cierra el modal, limpio todo
    document.getElementById('modalTarifa').addEventListener('hidden.bs.modal', function () {
        document.querySelector("#formTarifas").reset(); // limpiar formulario
        document.getElementById('modalTarifaLabel').innerHTML = 
            '<i class="fa-solid fa-file-invoice-dollar me-2"></i>agregar tarifa'; 
        
        // si había un input oculto de edición, lo elimino
        const inputEditar = document.querySelector('input[name="idTarifaEditar"]');
        if (inputEditar) inputEditar.remove();
        
        document.getElementById('idTarifa').disabled = false; // vuelvo a activar el campo ID
    });
});


// carga todas las tarifas en la tabla
function cargarTarifas() {
    fetch("php/gestortarifas.php?accion=lista") // pido la lista al PHP
        .then(response => response.json()) // convierto respuesta en JSON
        .then(data => {
            const tbody = document.querySelector("#tablaTarifas tbody"); 
            tbody.innerHTML = ""; // limpio la tabla

            if (data.length === 0) { 
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">no hay tarifas registradas</td></tr>';
                return;
            }

            data.forEach(t => { // recorro cada tarifa
                const fila = `
                <tr>
                    <td>${t.IdTarifa}</td>
                    <td>${t.DescripcionServicio}</td>
                    <td>$${parseFloat(t.CostoBase).toFixed(2)}</td>
                    <td>${t.NombreEspecialidad ?? "sin especialidad"}</td>
                    <td>
                        <span class="badge ${t.Estatus == 1 ? 'bg-success' : 'bg-warning'}">
                            ${t.Estatus == 1 ? 'Pagada' : 'No pagada'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm me-1" onclick="editarTarifa(${t.IdTarifa})">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarTarifa(${t.IdTarifa}, '${t.DescripcionServicio}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += fila; // agrego fila
            });
        })
        .catch(err => {
            console.error("error cargando tarifas:", err);
            Swal.fire({
                icon: "error",
                title: "error al cargar",
                text: "no se pudieron cargar las tarifas"
            });
        });
}


// guardar o actualizar tarifa
function guardarTarifa(formData) {
    fetch("php/gestortarifas.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(respuesta => {

            if (respuesta.includes("OK")) {
                Swal.fire({
                    icon: "success",
                    title: respuesta.includes("actualizada") ? "tarifa actualizada" : "tarifa guardada",
                    timer: 1800,
                    showConfirmButton: false
                });

                document.querySelector("#formTarifas").reset(); 

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalTarifa"));
                modal.hide();

                cargarTarifas(); 
            } else {
                Swal.fire({
                    icon: "error",
                    title: "error",
                    text: respuesta
                });
            }
        })
        .catch(error => {
            console.error("error:", error);
            Swal.fire({
                icon: "error",
                title: "error en la peticion",
                text: "no se pudo guardar la tarifa"
            });
        });
}


// cargar datos para editar
function editarTarifa(id) {
    fetch(`php/gestortarifas.php?accion=obtener&id=${id}`)
        .then(response => response.json())
        .then(tarifa => {

            document.getElementById('modalTarifaLabel').innerHTML =
                '<i class="fa-solid fa-edit me-2"></i>editar tarifa';

            // lleno los campos
            document.getElementById('idTarifa').value = tarifa.IdTarifa;
            document.getElementById('idTarifa').disabled = true;
            document.getElementById('descripcionServicio').value = tarifa.DescripcionServicio;
            document.getElementById('costoBase').value = tarifa.CostoBase;
            document.getElementById('especialidadId').value = tarifa.EspecialidadId;
            document.getElementById('estatus').value = tarifa.Estatus;

            // input oculto para indicar que es edición
            let inputEditar = document.querySelector('input[name="idTarifaEditar"]');
            if (!inputEditar) {
                inputEditar = document.createElement('input');
                inputEditar.type = 'hidden';
                inputEditar.name = 'idTarifaEditar';
                document.getElementById('formTarifas').appendChild(inputEditar);
            }
            inputEditar.value = tarifa.IdTarifa;

            const modal = new bootstrap.Modal(document.getElementById('modalTarifa'));
            modal.show();
        })
        .catch(error => {
            console.error("error al cargar tarifa:", error);
            Swal.fire({
                icon: "error",
                title: "error",
                text: "no se pudo cargar la informacion de la tarifa"
            });
        });
}


// eliminar tarifa
function eliminarTarifa(id, descripcion) {

    Swal.fire({
        title: 'estas seguro?',
        text: `se eliminara la tarifa: ${descripcion}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'si, eliminar',
        cancelButtonText: 'cancelar'
    }).then((result) => {
        
        if (result.isConfirmed) {

            fetch(`php/gestortarifas.php?accion=eliminar&id=${id}`)
                .then(response => response.text())
                .then(respuesta => {
                    
                    if (respuesta.includes("OK")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'eliminada',
                            text: 'la tarifa ha sido eliminada correctamente',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        cargarTarifas();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'error',
                            text: respuesta
                        });
                    }
                })
                .catch(error => {
                    console.error("error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'error',
                        text: 'no se pudo eliminar la tarifa'
                    });
                });
        }
    });
}