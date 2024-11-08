async function condicionalumnos(ids_alumnos) {
    try {
        // Asegúrate de que ids_alumnos sea un array y contenga datos
        if (!Array.isArray(ids_alumnos) || ids_alumnos.length === 0) {
            throw new Error("El array de IDs de alumnos no es válido o está vacío.");
        }

        console.log("IDs de alumnos: ", ids_alumnos); // Verifica que los datos sean correctos

        // Convertir el array de IDs a un string que se pueda enviar en la URL
        const idsParam = ids_alumnos.join(',');

        const response = await fetch(`../bd/condiciones.php?id_alumnos=${idsParam}`);

        if (!response.ok) {
            throw new Error(`Error en la respuesta del servidor: ${response.statusText}`);
        }

        const data = await response.json();

        // Verificar si la respuesta contiene un mensaje de error
        if (data.mensaje) {
            alert(data.mensaje);
            return;
        }

        // Asegurarse de que data sea un array, incluso si es un solo alumno
        if (!Array.isArray(data)) {
            data = [data]; // Convertirlo en un array si es necesario
        }

        // Mostrar la condición del alumno(s) en el formulario
        mostrarFormulario(data);

    } catch (error) {
        console.error('Error en la solicitud o el procesamiento:', error);

        let errors = JSON.parse(localStorage.getItem('errorLog')) || [];
        errors.push({
            timestamp: new Date().toISOString(),
            error: error.message
        });
        localStorage.setItem('errorLog', JSON.stringify(errors));

        alert('Ocurrió un error al consultar las condiciones. Por favor, intente más tarde.');
    }
}



function mostrarFormulario(data) {
    const formContainer = document.getElementById('form-container');
    
    // Limpiar el contenedor antes de mostrar los datos
    formContainer.innerHTML = '';

    data.forEach(alumno => {
        formContainer.innerHTML += `
            <h3>Condición de: ${alumno.nombre} ${alumno.apellido}</h3>
            <p>Condición: ${alumno.condicion}</p>
            <p>Asistencias: ${alumno.asistencias}</p>
            <form>
                <label for="nombre">Nombre del Alumno:</label>
                <input type="text" id="nombre" value="${alumno.nombre}" disabled><br><br>

                <label for="condicion">Condición:</label>
                <input type="text" id="condicion" value="${alumno.condicion}" disabled><br><br>

                <button type="submit">Enviar</button>
            </form>
        `;
    });
}
