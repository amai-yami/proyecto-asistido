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
            <input type="email" id="email" name="email" required placeholder="ejemplo@dominio.com" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%; max-width: 150px;">

            <label for="matricula">Matrícula:</label>
            <input type="text" id="matricula" name="matricula" required>

            <br><br>
            <button type="submit">Registrar Alumno</button>
            <div id="mensajeRegistro" style="margin-top: 10px;"></div> <!-- Div para mostrar mensajes -->
        </form>
    `;
    
    formContainer.innerHTML = formHtml; // Agregar el formulario al contenedor
}


function registrarAlumno() {
    const form = document.getElementById('formRegistrar');
    const formData = new FormData(form);
    
    fetch('../bd/insert_alumno.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const mensajeRegistro = document.getElementById('mensajeRegistro'); // Obtén el div donde se mostrará el mensaje
        if (data.success) {
            mensajeRegistro.textContent = data.message; // Muestra el mensaje de éxito
            form.reset(); // Limpia el formulario después de registrar
        } else {
            mensajeRegistro.textContent = 'Error: ' + data.error; // Muestra el error
        }
    })
    .catch(error => {
        console.error('Error al registrar alumno:', error);
        const mensajeRegistro = document.getElementById('mensajeRegistro');
        mensajeRegistro.textContent = 'Error al registrar alumno'; // Muestra el mensaje de error
    });
}







function mostrarEliminar() {
    const formContainer = document.getElementById('formContainer');
    formContainer.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos inputs

    const formHtml = `
        <form id="formEliminar">
            <label for="matriculaEliminar">Matrícula del Alumno a Eliminar:</label>
            <input type="text" id="matriculaEliminar" name="matriculaEliminar" required>

            <br><br>
            <button type="button" onclick="eliminarAlumno()">Eliminar Alumno</button>
        </form>
    `;
    
    formContainer.innerHTML = formHtml; // Agregar el formulario al contenedor
}





function eliminarAlumno() {
    const matricula = document.getElementById('matriculaEliminar').value;

    fetch('../bd/eliminar_alumno.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ matricula }) // Envía la matrícula en formato JSON
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.success);
            document.getElementById('formEliminar').reset(); // Limpiar el formulario después de eliminar
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error al eliminar alumno:', error);
        alert('Error al eliminar alumno');
    });
}

function mostrarModificar() {
    const formContainer = document.getElementById('formContainer');
    formContainer.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos inputs

    const formHtml = `
        <form id="formModificar">
            <label for="matriculaModificar">Nuevo Nombre:</label>
            <input type="text" id="nombreModificar" name="nombreModificar" required>

            <label for="apellidoModificar">Nuevo Apellido:</label>
            <input type="text" id="apellidoModificar" name="apellidoModificar" required>

            <label for="dniModificar">Nuevo DNI:</label>
            <input type="number" id="dniModificar" name="dniModificar" required>

            <label for="fecha_nacimientoModificar">Nueva Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimientoModificar" name="fecha_nacimientoModificar" required>

            <label for="emailModificar">Nuevo Correo Electrónico:</label>
            <input type="email" id="emailModificar" name="emailModificar" required placeholder="ejemplo@dominio.com" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; width: 100%; max-width: 150px;">

            <label for="matriculaModificar">Matrícula del Alumno a Modificar:</label>
            <input type="text" id="matriculaModificar" name="matriculaModificar" required>

            <br><br>
            <button type="button" onclick="modificarAlumno()">Modificar Alumno</button>
        </form>
    `;
    
    formContainer.innerHTML = formHtml; // Agregar el formulario al contenedor
}





function modificarAlumno() {
    const matricula = document.getElementById('matriculaModificar').value;
    const nombre = document.getElementById('nombreModificar').value;
    const apellido = document.getElementById('apellidoModificar').value;
    const dni = document.getElementById('dniModificar').value;
    const fechaNacimiento = document.getElementById('fecha_nacimientoModificar').value;
    const email = document.getElementById('emailModificar').value;

    const datosModificar = {
        matricula,
        nombre,
        apellido,
        dni,
        fecha_nacimiento: fechaNacimiento,
        email
    };

    fetch('../bd/modificar_alumno.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datosModificar) // Envía los datos en formato JSON
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.success);
            document.getElementById('formModificar').reset(); // Limpiar el formulario después de modificar
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error al modificar alumno:', error);
        alert('Error al modificar alumno');
    });
}

function eliminarCurso() {
    // Solicitar confirmación antes de proceder con la eliminación de todos los alumnos y sus cursos
    const confirmacion = confirm("¿Estás seguro de que deseas eliminar todos los alumnos y sus cursos? Esta acción no se puede deshacer.");

    if (confirmacion) {
        // Realizar la eliminación sin necesidad de solicitar matrícula
        fetch('../bd/eliminar_curso.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({}) // Enviar un cuerpo vacío ya que no es necesario la matrícula
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.success);
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error al eliminar curso:', error);
            alert('Error al eliminar curso');
        });
    }
}
