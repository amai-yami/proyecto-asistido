// Mostrar formulario para registrar alumno
function mostrarRegistro() { 
    const formContainer = document.getElementById('formContainer');
    formContainer.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos inputs

    const formHtml = `
        <form id="formRegistrar" onsubmit="event.preventDefault(); registrarAlumno();">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" required>
            <label for="dni">DNI:</label>
            <input type="number" id="dni" name="dni" required>
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required placeholder="ejemplo@dominio.com">
            <label for="matricula">Matrícula:</label>
            <input type="text" id="matricula" name="matricula" required>
            <button type="submit">Registrar Alumno</button>
            <div id="mensajeRegistro" style="margin-top: 10px;"></div> <!-- Div para mostrar mensajes -->
        </form>
    `;
    formContainer.innerHTML = formHtml; // Agregar el formulario al contenedor
}

// Registrar alumno
function registrarAlumno() {
    const form = document.getElementById('formRegistrar');
    const formData = new FormData(form);

    fetch('../bd/insert_alumno.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const mensajeRegistro = document.getElementById('mensajeRegistro');
        const mensajePanel = document.getElementById('mensajePanel');
        if (data.success) {
            mensajeRegistro.textContent = data.message;
            mensajePanel.textContent = 'Alumno registrado correctamente';
            form.reset(); // Limpiar el formulario después de registrar
        } else {
            mensajeRegistro.textContent = 'Error: ' + data.error;
            mensajePanel.textContent = 'No se pudo registrar el alumno';
        }
    })
    .catch(error => {
        console.error('Error al registrar alumno:', error);
        const mensajePanel = document.getElementById('mensajePanel');
        mensajePanel.textContent = 'Error al registrar alumno';
    });
}

// Mostrar formulario para eliminar alumno
function mostrarEliminar() {
    const formContainer = document.getElementById('formContainer');
    formContainer.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos inputs

    const formHtml = `
        <form id="formEliminar">
            <label for="matriculaEliminar">Matrícula del Alumno a Eliminar:</label>
            <input type="text" id="matriculaEliminar" name="matriculaEliminar" required>
            <button type="button" onclick="eliminarAlumno()">Eliminar Alumno</button>
        </form>
    `;
    formContainer.innerHTML = formHtml; // Agregar el formulario al contenedor
}

// Eliminar alumno
function eliminarAlumno() {
    const matricula = document.getElementById('matriculaEliminar').value;

    fetch('../bd/eliminar_alumno.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ matricula })
    })
    .then(response => response.json())
    .then(data => {
        const mensajePanel = document.getElementById('mensajePanel');
        if (data.success) {
            mensajePanel.textContent = 'Alumno eliminado correctamente';
            document.getElementById('formEliminar').reset(); // Limpiar el formulario después de eliminar
        } else {
            mensajePanel.textContent = 'Error: ' + data.error;
        }
    })
    .catch(error => {
        console.error('Error al eliminar alumno:', error);
        const mensajePanel = document.getElementById('mensajePanel');
        mensajePanel.textContent = 'Error al eliminar alumno';
    });
}

// Mostrar formulario para modificar alumno
function mostrarModificar() {
    const formContainer = document.getElementById('formContainer');
    formContainer.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos inputs

    const formHtml = `
        <form id="formModificar">
            <label for="nombreModificar">Nuevo Nombre:</label>
            <input type="text" id="nombreModificar" name="nombreModificar" required>
            <label for="apellidoModificar">Nuevo Apellido:</label>
            <input type="text" id="apellidoModificar" name="apellidoModificar" required>
            <label for="dniModificar">Nuevo DNI:</label>
            <input type="number" id="dniModificar" name="dniModificar" required>
            <label for="fecha_nacimientoModificar">Nueva Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimientoModificar" name="fecha_nacimientoModificar" required>
            <label for="emailModificar">Nuevo Correo Electrónico:</label>
            <input type="email" id="emailModificar" name="emailModificar" required placeholder="ejemplo@dominio.com">
            <label for="matriculaModificar">Matrícula del Alumno a Modificar:</label>
            <input type="text" id="matriculaModificar" name="matriculaModificar" required>
            <button type="button" onclick="modificarAlumno()">Modificar Alumno</button>
        </form>
    `;
    formContainer.innerHTML = formHtml; // Agregar el formulario al contenedor
}

// Modificar alumno
function modificarAlumno() {
    const datosModificar = {
        matricula: document.getElementById('matriculaModificar').value,
        nombre: document.getElementById('nombreModificar').value,
        apellido: document.getElementById('apellidoModificar').value,
        dni: document.getElementById('dniModificar').value,
        fecha_nacimiento: document.getElementById('fecha_nacimientoModificar').value,
        email: document.getElementById('emailModificar').value
    };

    fetch('../bd/modificar_alumno.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosModificar) 
    })
    .then(response => response.json())
    .then(data => {
        const mensajePanel = document.getElementById('mensajePanel');
        if (data.success) {
            mensajePanel.textContent = 'Alumno modificado correctamente';
            document.getElementById('formModificar').reset(); // Limpiar el formulario después de modificar
        } else {
            mensajePanel.textContent = 'Error: ' + data.error;
        }
    })
    .catch(error => {
        console.error('Error al modificar alumno:', error);
        const mensajePanel = document.getElementById('mensajePanel');
        mensajePanel.textContent = 'Error al modificar alumno';
    });
}

// Eliminar curso
function eliminarCurso() {
    if (confirm("¿Estás seguro de que deseas eliminar todos los alumnos y sus cursos? Esta acción no se puede deshacer.")) {
        fetch('../bd/eliminar_curso.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            const mensajePanel = document.getElementById('mensajePanel');
            mensajePanel.textContent = data.success 
                ? 'Curso eliminado correctamente' 
                : 'Error: ' + data.error;
        })
        .catch(error => {
            console.error('Error al eliminar curso:', error);
            document.getElementById('mensajePanel').textContent = 'Error al eliminar curso';
        });
    }
}
