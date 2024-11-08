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
            asistencia: checkbox.value === 'presente' // Marcar como 'presente' si el valor del checkbox es 'presente'
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
        return response.text().then(text => {
            try {
                const data = JSON.parse(text); // Intentar parsear la respuesta como JSON
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status} - ${data.message || 'Desconocido'}`);
                }
                return data;
            } catch (error) {
                // Si no es JSON, mostrar el contenido y el error
                console.error('Respuesta del servidor no es JSON:', text);
                throw new Error('La respuesta del servidor no es válida. Verifica el archivo PHP.');
            }
        });
    })
    .then(data => {
        if (data.success) {
            alert('Asistencias registradas con éxito.');
          
          
        } else {
            alert('Hubo un error al registrar las asistencias: ' + (data.message || 'Desconocido'));
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
