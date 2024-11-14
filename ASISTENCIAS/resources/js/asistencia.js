function buscarAlumnoAsistencia() {
    const searchValue = document.getElementById('searchAsistencia').value;

    if (!searchValue.trim()) {
        alert('Por favor ingresa un nombre, apellido, DNI o matrícula para buscar.');
        return;
    }

    fetch(`../bd/buscar_alumno.php?search=${searchValue}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta de la red: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const asistenciasBody = document.getElementById('asistenciasBody');
            asistenciasBody.innerHTML = ''; // Limpiar el contenido previo

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
                asistenciasBody.innerHTML = `<tr><td colspan="2">No se encontraron alumnos.</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const asistenciasBody = document.getElementById('asistenciasBody');
            asistenciasBody.innerHTML = `<tr><td colspan="2">Error al buscar alumno: ${error.message}</td></tr>`;
        });
}






function cargarAlumnos() {
    fetch('../bd/listar_alumnos.php') // Ajusta la ruta según tu estructura de carpetas
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta de la red');
            }
            return response.json();
        })
        .then(data => {
            const asistenciasBody = document.getElementById('asistenciasBody');
            asistenciasBody.innerHTML = ''; // Limpiar filas existentes

            if (data.error) {
                // Manejo de error
                asistenciasBody.innerHTML = `<tr><td colspan="2">${data.error}</td></tr>`;
                return;
            }

            data.forEach(alumno => {
                const rowAsistencias = document.createElement('tr');
                rowAsistencias.innerHTML = `
                    <td>${alumno.nombre} ${alumno.apellido}</td>
                    <td><input type="checkbox" name="asistencia_${alumno.id}" value="presente"> Presente</td>
                `;
                asistenciasBody.appendChild(rowAsistencias);
            });
        })
        .catch(error => {
            console.error('Error al cargar alumnos:', error);
            const asistenciasBody = document.getElementById('asistenciasBody');
            asistenciasBody.innerHTML = `<tr><td colspan="2">Error al cargar alumnos: ${error.message}</td></tr>`;
        });
}


function guardarAsistencias() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
    const asistencias = [];
    let fecha = document.getElementById('fecha').value; // Obtener el valor del input de fecha

    // Verificar si se ha seleccionado una fecha
    if (!fecha) {
        alert('Por favor, selecciona una fecha.');
        return;
    }

    // Asegurarse de que la fecha incluya los segundos
    if (fecha.length === 16) { // Si la fecha tiene formato "YYYY-MM-DDTHH:MM" (sin segundos)
        fecha += ":00"; // Agregar los segundos como "00"
    }

    // Asegurarse de que la fecha esté en el formato adecuado (YYYY-MM-DD HH:MM:SS)
    // Si la fecha contiene el carácter 'T' (lo que implica un formato de tipo ISO)
    // Reemplazamos 'T' por un espacio para cumplir con el formato 'YYYY-MM-DD HH:MM'
    if (fecha.includes('T')) {
        fecha = fecha.replace('T', ' '); // Reemplazamos 'T' por un espacio
    }

    // Verificar si se han seleccionado alumnos
    if (checkboxes.length === 0) {
        alert('No se seleccionaron alumnos.');
        return;
    }

    // Recopilar los datos de los alumnos seleccionados
    checkboxes.forEach(checkbox => {
        const alumnoId = checkbox.name.split('_')[1]; // Obtener el ID del alumno del nombre del checkbox
        asistencias.push({
            alumnoId: alumnoId,
            asistencia: checkbox.value === 'presente', // Marcar como 'presente' si el valor del checkbox es 'presente'
            fecha: fecha // Incluir la fecha seleccionada con segundos
        });
    });

    // Enviar las asistencias al servidor
    fetch('../bd/guardar_asistencias.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' // Asegurar que el cuerpo se envíe como JSON
        },
        body: JSON.stringify(asistencias) // Convertir el array a JSON
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text); // Intentar parsear la respuesta como JSON
            if (data.success) {
                alert('Asistencias registradas con éxito.');

                // Verificar si hay cumpleaños
                if (data.cumpleanios && data.cumpleanios.length > 0) {
                    // Crear un mensaje con la lista de cumpleaños
                    const cumpleaniosMensaje = '¡Hoy es el cumpleaños de: ' + data.cumpleanios.join(', ') + '!';
                    alert(cumpleaniosMensaje); // Mostrar el mensaje de cumpleaños
                }
            } else {
                alert('Hubo un error al registrar las asistencias: ' + (data.message || 'Desconocido'));
            }
        } catch (error) {
            console.error('Error al procesar la respuesta del servidor:', error);
            alert('Error al procesar la respuesta del servidor. Verifica los detalles en la consola para más información.');
        }
    })
    .catch(error => {
        console.error('Error al guardar asistencias:', error);
        alert('Error al guardar las asistencias. Verifica los detalles en la consola para más información.');
    });
}





function verAsistencias() {
    fetch('../bd/ver_asistencias.php')
        .then(response => {
            // Verifica que la respuesta sea exitosa (status 200)
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.statusText}`);
            }
            // Verifica que la respuesta sea JSON
            return response.json().catch(err => {
                throw new Error('La respuesta no es un JSON válido');
            });
        })
        .then(data => {
            console.log(data);  // Muestra los datos para depuración

            // Verifica si hay un error en los datos
            if (data.error) {
                console.error(data.error);
                return;
            }

            const asistenciasBody = document.getElementById("asistenciasBody");
            asistenciasBody.innerHTML = "";  // Limpiar la tabla antes de agregar los nuevos datos

            // Mostrar todas las asistencias en la tabla
            data.forEach(asistencia => {
                const tr = document.createElement("tr");
                tr.innerHTML = `<td>${asistencia.nombre_completo}</td>
                                <td>Asistió a ${asistencia.numero_asistencias} clases</td>`;
                asistenciasBody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Error al cargar las asistencias:', error.message);
        });
}

function modificarAsistenciasFormulario() {
    fetch('../bd/listar_alumnos.php')  
        .then(response => response.json())
        .then(data => {
            const asistenciasBody = document.getElementById('asistenciasBody');
            asistenciasBody.innerHTML = '';  // Limpiar la tabla antes de agregar los nuevos alumnos

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
        const alumnoId = input.name.split('_')[1];  // Extrae el ID del alumno
        let valorAsistencia = input.value.trim();  // Obtiene el valor tal cual lo escribió el usuario

        // Si el valor está vacío, no lo enviamos
        if (valorAsistencia === "") {
            return;  // No agregar este campo si está vacío
        }

        // Convertir el valor a número flotante para asegurarnos de que es un número
        valorAsistencia = parseFloat(valorAsistencia);  

        // Si el valor no es un número válido (NaN), lo descartamos
        if (isNaN(valorAsistencia)) {
            return; // No agregar si no es un número válido
        }

        // Agregar la asistencia al array con el valor exacto
        asistencias.push({ alumnoId, valorAsistencia });
    });

    // Si no se han registrado asistencias válidas, mostrar mensaje
    if (asistencias.length === 0) {
        mostrarMensaje('No se han registrado asistencias válidas.', 'error');
        alert('No se han registrado asistencias válidas.');
        return;
    }

    // Verificar los datos antes de enviarlos al servidor (para depuración)
    console.log("Asistencias enviadas al servidor:", asistencias);

    // Enviar todos los valores tal cual los escribió el usuario al servidor
    fetch('../bd/modificar_asistencia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ asistencias })  // Enviamos el array con todos los valores tal cual
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje('Asistencias guardadas con éxito', 'success');
            alert('Asistencias guardadas con éxito.');
        } else {
            mostrarMensaje('Error al guardar las asistencias: ' + (data.message || 'Desconocido'), 'error');
            alert('Error al guardar las asistencias: ' + (data.message || 'Desconocido'));
        }
    })
    .catch(error => {
        console.error('Error al guardar las asistencias:', error);
        mostrarMensaje('Error al guardar las asistencias.', 'error');
        alert('Error al guardar las asistencias.');
    });
}

