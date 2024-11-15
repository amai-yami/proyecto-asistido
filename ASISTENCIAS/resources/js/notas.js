// Función para guardar las notas
function guardarNotas() {
    const rows = document.querySelectorAll('#notasBody tr');
    const notas = {};

    // Recorrer las filas para obtener las notas
    rows.forEach(row => {
        const inputs = row.querySelectorAll('input');
        
        // Obtener el id del alumno desde el name del input
        const idAlumno = inputs[0].name.split('_')[1]; // Obtener el id desde el name, ej: parcial1_1 -> id = 1
        
        // Extraer las notas de cada parcial y final
        const parcial1 = inputs[0].value;
        const parcial2 = inputs[1].value;
        const final = inputs[2].value;

        // Validar que las notas sean números válidos y estén en el rango de 0 a 10
        if (parcial1 === '' || parcial2 === '' || final === '' || isNaN(parcial1) || isNaN(parcial2) || isNaN(final)) {
            mostrarMensaje(`Error: Las notas del alumno con ID ${idAlumno} no son válidas. Por favor, ingresa números correctos.`, 'error');
            return;
        }
        
        // Validar que las notas estén dentro del rango de 0 a 10
        if (parcial1 < 0 || parcial1 > 10 || parcial2 < 0 || parcial2 > 10 || final < 0 || final > 10) {
            mostrarMensaje(`Error: Las notas del alumno con ID ${idAlumno} deben estar entre 0 y 10.`, 'error');
            return;
        }

        // Guardar las notas para el alumno
        notas[idAlumno] = {
            parcial1: parseFloat(parcial1),
            parcial2: parseFloat(parcial2),
            final: parseFloat(final)
        };
    });

    // Comprobar si hay notas para guardar
    if (Object.keys(notas).length === 0) {
        mostrarMensaje('No hay notas para guardar. Por favor, ingresa las notas para los alumnos.', 'error');
        return;
    }

    // Preparar datos para enviar
    const formData = new URLSearchParams();
    Object.keys(notas).forEach(id => {
        formData.append(`parcial1[${id}]`, notas[id].parcial1);
        formData.append(`parcial2[${id}]`, notas[id].parcial2);
        formData.append(`final[${id}]`, notas[id].final);
    });

    // Enviar las notas al servidor
    fetch('../bd/guardar_notas.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje(data.success, 'success');
        } else {
            mostrarMensaje('Error al guardar notas: ' + data.error, 'error');
        }
    })
    .catch(error => {
        mostrarMensaje('Error al guardar notas: ' + error.message, 'error');
    });
}

// Función para cargar los alumnos en la tabla
function cargarAlumnosNotas() {
    fetch('../bd/listar_alumnos.php')
        .then(response => response.json())
        .then(data => {
            const notasBody = document.getElementById('notasBody');
            notasBody.innerHTML = '';  // Limpiar la tabla antes de agregar los nuevos alumnos

            if (data.error) {
                // Mostrar mensaje de error si no hay alumnos
                mostrarMensaje('No se encontraron alumnos.', 'error');
                return;
            }

            // Si no hay error y hay alumnos, crear filas en la tabla
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(alumno => {
                    const rowNotas = document.createElement('tr');
                    rowNotas.innerHTML = `
                        <td>${alumno.nombre} ${alumno.apellido}</td>
                        <td><input type="number" name="parcial1_${alumno.id}" min="0" max="10" style="width: 70px; height: 30px; margin: 2px; text-align: center;"></td>
                        <td><input type="number" name="parcial2_${alumno.id}" min="0" max="10" style="width: 70px; height: 30px; margin: 2px; text-align: center;"></td>
                        <td><input type="number" name="final_${alumno.id}" min="0" max="10" style="width: 70px; height: 30px; margin: 2px; text-align: center;"></td>
                    `;
                    // Añadir la fila a la tabla
                    notasBody.appendChild(rowNotas);
                });

                // Verificar si el botón ya existe, para evitar duplicados
                let buttonGuardar = document.getElementById('guardarNotasButton');
                if (!buttonGuardar) {
                    buttonGuardar = document.createElement('button');
                    buttonGuardar.id = 'guardarNotasButton';
                    buttonGuardar.textContent = 'Guardar Notas';
                    buttonGuardar.addEventListener('click', guardarNotas);

                    // Añadir el botón al final de la tabla
                    notasBody.appendChild(buttonGuardar);
                }
            } else {
                // Si no hay alumnos, mostrar un mensaje de error
                mostrarMensaje('No se encontraron alumnos disponibles.', 'error');
            }
        })
        .catch(error => {
            // Si hay un error al cargar los alumnos, mostrar mensaje
            mostrarMensaje('Error al cargar los alumnos: ' + error.message, 'error');
        });
}

// Función para buscar un alumno
function buscarAlumnoNotas() {
    const searchValue = document.getElementById('searchNotas').value;

    if (!searchValue.trim()) {
        mostrarMensaje('Por favor ingresa un nombre o apellido para buscar.', 'error');
        return;
    }

    fetch(`../bd/buscar_alumno.php?search=${searchValue}`)
        .then(response => response.json())
        .then(data => {
            const notasBody = document.getElementById('notasBody');
            notasBody.innerHTML = '';

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(alumno => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${alumno.nombre} ${alumno.apellido}</td>
                        <td><input type="number" name="parcial1_${alumno.id}" min="0" max="10" style="width: 70px; height: 30px; margin: 2px; text-align: center;"></td>
                        <td><input type="number" name="parcial2_${alumno.id}" min="0" max="10" style="width: 70px; height: 30px; margin: 2px; text-align: center;"></td>
                        <td><input type="number" name="final_${alumno.id}" min="0" max="10" style="width: 70px; height: 30px; margin: 2px; text-align: center;"></td>
                    `;
                    notasBody.appendChild(row);
                });
            } else {
                mostrarMensaje('No se encontraron alumnos con ese nombre o apellido.', 'error');
            }
        })
        .catch(error => {
            mostrarMensaje('Error al buscar el alumno: ' + error.message, 'error');
        });
}

// Función para ver las notas
function verNotas() {
    const notasBody = document.getElementById('notasBody');
    notasBody.innerHTML = '<tr><td colspan="4">Cargando notas...</td></tr>';

    fetch('../bd/obtener_notas.php')
        .then(response => response.json())
        .then(data => {
            notasBody.innerHTML = '';

            if (data.length === 0) {
                mostrarMensaje('No se encontraron notas para mostrar.', 'error');
                return;
            }

            data.forEach(alumno => {
                const rowNotas = document.createElement('tr');
                rowNotas.innerHTML = `
                    <td>${alumno.nombre} ${alumno.apellido}</td>
                    <td>${alumno.parcial1 !== null ? alumno.parcial1 : 'N/A'}</td>
                    <td>${alumno.parcial2 !== null ? alumno.parcial2 : 'N/A'}</td>
                    <td>${alumno.final !== null ? alumno.final : 'N/A'}</td>
                `;
                notasBody.appendChild(rowNotas);
            });
        })
        .catch(error => {
            mostrarMensaje('Error al cargar las notas: ' + error.message, 'error');
        });
}

// Función para mostrar mensajes de error o éxito
function mostrarMensaje(mensaje, tipo) {
    // Obtén el contenedor del mensaje dentro del panel de notas
    const mensajeContainer = document.getElementById('mensajeContainer');
    
    mensajeContainer.textContent = mensaje;
    // Aplicar estilo según el tipo de mensaje
    if (tipo === 'error') {
        mensajeContainer.style.backgroundColor = 'red';
        mensajeContainer.style.color = 'white';
    } else if (tipo === 'success') {
        mensajeContainer.style.backgroundColor = 'green';
        mensajeContainer.style.color = 'white';
    }
    // Mostrar el mensaje
    mensajeContainer.style.display = 'block';
    // Ocultar el mensaje después de un tiempo
    setTimeout(() => {
        mensajeContainer.style.display = 'none';
    }, 5000);
}