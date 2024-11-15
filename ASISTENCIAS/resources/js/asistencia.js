function modificarAsistenciasFormulario() {
    fetch('../bd/listar_alumnos.php')  
        .then(response => response.json())
        .then(data => {
            const asistenciasBody = document.getElementById('asistenciasBody');
            asistenciasBody.innerHTML = '';

            if (data.error) {
                mostrarMensaje('No se encontraron alumnos.', 'error');
                return;
            }

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(alumno => {
                    const rowAsistencias = document.createElement('tr');
                    rowAsistencias.innerHTML = `
                        <td>${alumno.nombre} ${alumno.apellido}</td>
                        <td>
                            <input type="number" name="asistencia_${alumno.id}" style="width: 80px;" 
                                   placeholder="Valor de asistencia" step="any" />
                        </td>
                    `;
                    asistenciasBody.appendChild(rowAsistencias);
                });

                const buttonRow = document.createElement('tr');
                buttonRow.innerHTML = ` 
                    <td colspan="2">
                        <button type="button" onclick="modificarAsistencia()">Modificar Asistencias</button>
                    </td>
                `;
                asistenciasBody.appendChild(buttonRow);
            } else {
                mostrarMensaje('No se encontraron alumnos disponibles.', 'error');
            }
        })
        .catch(error => {
            mostrarMensaje('Error al cargar los alumnos: ' + error.message, 'error');
        });
}

function modificarAsistencia() {
    const inputs = document.querySelectorAll('input[type="number"]');
    const asistencias = [];

    inputs.forEach(input => {
        const alumnoId = input.name.split('_')[1];
        let valorAsistencia = input.value.trim();

        if (valorAsistencia === "") {
            return;
        }

        valorAsistencia = parseFloat(valorAsistencia);

        if (isNaN(valorAsistencia)) {
            return;
        }

        asistencias.push({ alumnoId, valorAsistencia });
    });

    if (asistencias.length === 0) {
        mostrarMensaje('No se han registrado asistencias válidas.', 'error');
        return;
    }

    console.log("Asistencias enviadas al servidor:", asistencias);

    fetch('../bd/modificar_asistencia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ asistencias })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje('Asistencias guardadas con éxito', 'success');
        } else {
            mostrarMensaje('Error al guardar las asistencias: ' + (data.message || 'Desconocido'), 'error');
        }
    })
    .catch(error => {
        console.error('Error al modificar las asistencias:', error);
        mostrarMensaje('Error al modificar las asistencias: ' + error.message, 'error');
    });
}







// Buscar alumnos para asistencia
function buscarAlumnoAsistencia() {
    const searchValue = document.getElementById('searchAsistencia').value.trim();

    if (!searchValue) {
        mostrarMensaje('Por favor ingresa un nombre, apellido, DNI o matrícula para buscar.', 'error');
        return;
    }

    fetch(`../bd/buscar_alumno.php?search=${searchValue}`)
        .then(response => response.ok ? response.json() : Promise.reject(`Error ${response.status}`))
        .then(data => {
            const asistenciasBody = document.getElementById('asistenciasBody');
            asistenciasBody.innerHTML = ''; // Limpiar contenido previo

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(alumno => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${alumno.matricula} - ${alumno.dni} - ${alumno.nombre} ${alumno.apellido}</td>
                        <td><input type="checkbox" name="asistencia_${alumno.id}" value="presente"> Presente</td>
                    `;
                    asistenciasBody.appendChild(row);
                });
            } else {
                asistenciasBody.innerHTML = '<tr><td colspan="2">No se encontraron alumnos.</td></tr>';
                mostrarMensaje('No se encontraron alumnos.', 'error');
            }
        })
        .catch(error => mostrarError(asistenciasBody, `Error al buscar alumno: ${error}`));
}
// Cargar alumnos para listado general
function cargarAlumnos() {
    fetch('../bd/listar_alumnos.php')
        .then(response => response.ok ? response.json() : Promise.reject(`Error ${response.status}`))
        .then(data => {
            const asistenciasBody = document.getElementById('asistenciasBody');
            asistenciasBody.innerHTML = ''; // Limpiar filas existentes

            // Agregar input de fecha antes del listado
            const fechaRow = document.createElement('tr');
            fechaRow.innerHTML = `
                <td colspan="2">
                    <label for="fecha">Fecha:</label>
                    <input type="datetime-local" id="fecha" name="fecha" required>
                </td>
            `;
            asistenciasBody.appendChild(fechaRow);

            if (data.error) {
                asistenciasBody.innerHTML += `<tr><td colspan="2">${data.error}</td></tr>`;
                mostrarMensaje(data.error, 'error');
                return;
            }

            // Generar filas para los alumnos
            data.forEach(alumno => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${alumno.nombre} ${alumno.apellido}</td>
                    <td><input type="checkbox" name="asistencia_${alumno.id}" value="presente"> Presente</td>
                `;
                asistenciasBody.appendChild(row);
            });

            // Agregar botón para guardar asistencias después del listado
            const controlsRow = document.createElement('tr');
            controlsRow.innerHTML = `
                <td colspan="2">
                    <button onclick="guardarAsistencias()">Guardar Asistencias</button>
                </td>
            `;
            asistenciasBody.appendChild(controlsRow);
        })
        .catch(error => mostrarError(asistenciasBody, `Error al cargar alumnos: ${error}`));
}



// Guardar asistencias seleccionadas
function guardarAsistencias() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
    const asistencias = [];
    let fecha = document.getElementById('fecha').value;

    if (!fecha) {
        mostrarMensaje('Por favor, selecciona una fecha.', 'error');
        return;
    }

    fecha = ajustarFechaFormato(fecha);

    if (checkboxes.length === 0) {
        mostrarMensaje('No se seleccionaron alumnos.', 'error');
        return;
    }

    checkboxes.forEach(checkbox => {
        const alumnoId = checkbox.name.split('_')[1];
        asistencias.push({ alumnoId, asistencia: checkbox.value === 'presente', fecha });
    });

    fetch('../bd/guardar_asistencias.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(asistencias)
    })
    .then(response => response.ok ? response.json() : Promise.reject(`Error HTTP ${response.status}`))
    .then(data => {
        if (data.success) {
            mostrarMensaje('Asistencias registradas con éxito.', 'success');
            if (data.cumpleanios?.length) {
                mostrarMensaje(`¡Hoy es el cumpleaños de: ${data.cumpleanios.join(', ')}!`, 'info');
            }
        } else {
            mostrarMensaje(`Error al registrar asistencias: ${data.message || 'Desconocido'}`, 'error');
        }
    })
    .catch(error => mostrarMensaje(`Error al guardar asistencias: ${error}`, 'error'));
}

// Ver asistencias registradas
function verAsistencias() {
    fetch('../bd/ver_asistencias.php')
        .then(response => response.ok ? response.json() : Promise.reject(`Error ${response.status}`))
        .then(data => {
            if (data.error) {
                mostrarMensaje(data.error, 'error');
                return;
            }

            const asistenciasBody = document.getElementById('asistenciasBody');
            asistenciasBody.innerHTML = '';

            data.forEach(asistencia => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${asistencia.nombre_completo}</td>
                    <td>Asistió a ${asistencia.numero_asistencias} clases</td>
                `;
                asistenciasBody.appendChild(row);
            });
        })
        .catch(error => mostrarMensaje(`Error al cargar las asistencias: ${error}`, 'error'));
}

// Función auxiliar para ajustar formato de fecha
function ajustarFechaFormato(fecha) {
    if (fecha.includes('T')) {
        fecha = fecha.replace('T', ' ');
    }
    return fecha.length === 16 ? fecha + ':00' : fecha;
}

// Mostrar mensajes al usuario
function mostrarMensaje(mensaje, tipo) {
    const mensajeContainer = document.getElementById('mensajeUsuario');
    mensajeContainer.style.display = 'block';
    mensajeContainer.innerHTML = `<div class="${tipo}">${mensaje}</div>`;
    setTimeout(() => mensajeContainer.style.display = 'none', 5000);
}

// Mostrar errores en el DOM
function mostrarError(container, mensaje) {
    container.innerHTML = `<tr><td colspan="2">${mensaje}</td></tr>`;
    mostrarMensaje(mensaje, 'error');
}
