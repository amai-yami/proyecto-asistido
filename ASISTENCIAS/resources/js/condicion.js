async function condicionalumnos() {
    try {
        // Capturar los valores de los inputs
        const diasClase = document.getElementById('diasClase').value;
        const porcentajeAsistenciaPromocion = document.getElementById('porcentajeAsistenciaPromocion').value;
        const porcentajeNotaPromocion = document.getElementById('porcentajeNotaPromocion').value;
        const porcentajeAsistenciaRegular = document.getElementById('porcentajeAsistenciaRegular').value;
        const porcentajeNotaRegular = document.getElementById('porcentajeNotaRegular').value;

        // Validar que todos los campos estén completos
        if (!diasClase || !porcentajeAsistenciaPromocion || !porcentajeNotaPromocion || !porcentajeAsistenciaRegular || !porcentajeNotaRegular) {
            mostrarError('Por favor, complete todos los campos.');
            return;
        }

        const datosFormulario = {
            diasClase: diasClase,
            promocion: {
                porcentajeAsistencia: porcentajeAsistenciaPromocion,
                porcentajeNota: porcentajeNotaPromocion
            },
            regularizacion: {
                porcentajeAsistencia: porcentajeAsistenciaRegular,
                porcentajeNota: porcentajeNotaRegular
            }
        };

        const response = await fetch('../bd/condiciones.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datosFormulario)
        });

        // Comprobar si la respuesta del servidor es exitosa
        if (!response.ok) throw new Error('Error en la respuesta del servidor');

        const condicion = await response.json();
        mostrarCondicion(condicion);
    } catch (error) {
        console.error('Error en la solicitud o el procesamiento:', error);
        mostrarError('Ocurrió un error al consultar las condiciones. Por favor, intente más tarde.');
    }
}

function mostrarCondicion(condicion) {
    const notasBody = document.getElementById('notasBody');
    notasBody.innerHTML = `
        <h3 style="text-align: left;">Condición de los Alumnos:</h3>
        <div>
            ${condicion.map(alumno => `
                <div style="display: flex; align-items: center; margin-bottom: 10px; gap: 15px;">
                    <h4 style="margin-bottom: 0;">${alumno.alumno} - ${alumno.condicion}</h4>
                </div>
            `).join('')}
        </div>
    `;
}

function mostrarError(mensaje) {
    const mensajeContainer = document.getElementById('mensajeContainer');
    mensajeContainer.innerHTML = `
        <h3>Error:</h3>
        <p style="color: red;">${mensaje}</p>
    `;
    mensajeContainer.style.display = 'block';
}

function mostrarFormulario() {
    const formContainer = document.getElementById('notasBody');
    
    // Limpiar el contenedor antes de mostrar el formulario
    formContainer.innerHTML = '';

    formContainer.innerHTML = `
        <h3>Condición del Alumno</h3>
        <form>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <label for="diasClase">Días de Clase:</label>
                <input type="number" id="diasClase" name="diasClase" placeholder="Días de Clase" required>

                <h4>Promoción</h4>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <label for="porcentajeAsistenciaPromocion">Porcentaje de Asistencia:</label>
                    <input type="number" id="porcentajeAsistenciaPromocion" name="porcentajeAsistenciaPromocion" placeholder="% Asistencia Promoción" required>

                    <label for="porcentajeNotaPromocion">Nota Mínima:</label>
                    <input type="number" id="porcentajeNotaPromocion" name="porcentajeNotaPromocion" placeholder="% Nota Promoción" required>
                </div>

                <h4>Regularización</h4>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <label for="porcentajeAsistenciaRegular">Porcentaje de Asistencia:</label>
                    <input type="number" id="porcentajeAsistenciaRegular" name="porcentajeAsistenciaRegular" placeholder="% Asistencia Regularización" required>

                    <label for="porcentajeNotaRegular">Nota Mínima:</label>
                    <input type="number" id="porcentajeNotaRegular" name="porcentajeNotaRegular" placeholder="% Nota Regularización" required>
                </div>
            </div>
            <br>
            <button type="button" onclick="condicionalumnos()">Verificar Información</button>
        </form>
    `;
}
