<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="resources/css/index.css"> <!-- Enlace al archivo CSS -->
   
    <style>
        main {
            text-align: center; /* Centrar contenido dentro del main */
            background-image: url('resources/mesas.jpeg');
            background-size: cover; /* Cubre todo el viewport */
            background-position: center; /* Centra la imagen */
        }
        th {
            background-color: blue;
        }



        
/* Contenedor para el usuario */
#userContainer {
    position: relative;
    display: inline-block;
    cursor: pointer;
    background-color: rgba(255, 255, 255, 0.7);  /* Fondo translúcido */
    padding: 10px 20px;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
}

#userContainer:hover {
    background-color: rgba(255, 255, 255, 1);
}

/* Menú de logout */
#logoutMenu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: #fff;
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    min-width: 150px;
}

/* Estilo para los enlaces del menú de logout */
#logoutMenu a {
    display: block;
    padding: 8px;
    color: #333;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

#logoutMenu a:hover {
    background-color: #f1f1f1;
}

/* Mostrar el menú de logout cuando el contenedor se pasa el cursor */
#userContainer:hover #logoutMenu {
    display: block;
}

/* Estilos para el texto de iniciar sesión */
#userDisplay {
    font-size: 1.1rem;
    font-weight: bold;
    color: #007bff;
    cursor: pointer;
    transition: color 0.3s ease;
}

#userDisplay:hover {
    color: #0056b3;
}

/* Ocultar elementos */
.hidden {
    display: none;
}


        
    </style>
</head>
<body>
    
<header>
    <nav>
        <ul>
            <li id="userContainer">
                <span id="userDisplay" onclick="handleLoginClick()">Iniciar Sesión</span>
                <div id="logoutMenu" class="hidden">
                    <button id="cerrarSesion">Cerrar Sesión</button>
                </div>
            </li>
            <li><a href="#" id="toggleMaterias">Seleccionar Materias</a></li>
            <li><a href="#" id="togglePanel">Alumnos</a></li>
            <li><a href="#" id="toggleAsistencias">Asistencias</a></li>
            <li><a href="#" id="toggleNotas">Notas de la Clase</a></li>
            <li><a href="acerca de.php">Uso de la Página</a></li>
        </ul>
    </nav>
</header>



    <main><!-- Panel para seleccionar materias -->
<div id="materiasPanel" class="panel">
    <h2>Seleccionar Materia</h2>

    <!-- Selección de instituto -->
    <label for="instituto">Seleccionar Instituto:</label>
    <select id="instituto" name="instituto" required>
        <option value="">-- Selecciona un Instituto --</option>
        <!-- Las opciones se cargarán aquí dinámicamente -->
    </select>

    <!-- Contenido del panel de seleccionar materias -->
    <label for="carrera">Seleccionar Carrera/Departamento:</label>
    <select id="carrera" name="carrera" required>
        <option value="">-- Selecciona una Carrera --</option>
        <!-- Las opciones se cargarán aquí dinámicamente -->
    </select>

    <!-- Selección de Año -->
    <label for="anio">Seleccionar Año:</label>
    <select id="anio" name="anio" required>
        <option value="">-- Selecciona un Año --</option>
        <!-- Las opciones se cargarán aquí dinámicamente -->
    </select>

    <!-- Contenido del panel de seleccionar materias -->
    <label for="curso">Seleccionar Curso:</label>
    <select id="curso" name="curso" required>
        <option value="">-- Selecciona un Curso --</option>
        <!-- Las opciones se cargarán aquí dinámicamente -->
    </select>

    <!-- Selección de Materia -->
    <label for="materias">Seleccionar Materia:</label>
    <select id="materias" name="materias" required>
        <option value="">-- Selecciona una Materia --</option>
        <!-- Las opciones se cargarán aquí dinámicamente -->
    </select>
</div>



        <!-- Panel para registrar nuevo alumno -->
<div id="registroPanel" class="panel">
    <h2>Registro de Alumnos</h2>
    <button type="button" onclick="mostrarRegistro()">Registrar Alumno</button>
    <button type="button" onclick="mostrarEliminar()">Eliminar Alumno</button>
    <button type="button" onclick="mostrarModificar()">Modificar Alumno</button>
    <button type="button" onclick="eliminarCurso()">Eliminar Curso</button>
    <div id="formContainer"></div> <!-- Contenedor donde se generarán los inputs -->
    
    <br>
 
</div>

<!-- Panel para Asistencias -->
<div id="asistenciasPanel" class="panel">
    <h2>Asistencias</h2>
    <button id="cargarAlumnosBtn" onclick="cargarAlumnos()">Listar Alumnos</button>
    <!-- Nuevo botón "Ver Asistencias" -->
    <button onclick="verAsistencias()">Ver Asistencias</button>
    <!-- Nuevo botón "MODIFICAR ASISTENCIAS" -->
    <button onclick="modificarAsistenciasFormulario()">Modificar</button>

    <!-- Input de fecha para registrar asistencias -->
    <label for="fecha">Fecha:</label>
    <input type="datetime-local" id="fecha" name="fecha">
    
    <label for="searchAsistencia">Buscar Alumno:</label><br>
    <input type="text" id="searchAsistencia" name="searchAsistencia" required>
    <button onclick="buscarAlumnoAsistencia()">Buscar</button><br><br>

    <table>
        <thead>
            <tr>
                <th>Nombre Completo</th> 
                <th>Asistencia</th>
            </tr>
            <!-- Div para mostrar los mensajes al usuario -->
            <div id="mensajeUsuario" class="mensaje-usuario" style="display: none;"></div>
        </thead>
        <tbody id="asistenciasBody">
            <!-- Las filas de asistencia se agregarán aquí dinámicamente -->
        </tbody>
    </table>
    <br><button onclick="guardarAsistencias()">Guardar Asistencias</button>
    <br><br>
</div>


<!-- Panel para Notas -->
<div id="notasPanel" class="panel">
    <h2>Notas de Alumnos</h2>
    
    <!-- Formulario de Búsqueda -->
    <div class="search-container">
        <label for="searchNotas" aria-label="Buscar Alumno">Buscar Alumno:</label>
        <input type="text" id="searchNotas" name="searchNotas" placeholder="Buscar por nombre o apellido" required>
        <button onclick="buscarAlumnoNotas()">Buscar</button><br><br>
    </div>

    <!-- Botones de Acción -->
    <div class="action-buttons">
        <button id="cargarAlumnosBtn" onclick="cargarAlumnosNotas()">Listar Alumnos</button>
        <button id="cargarNotasBtn" onclick="verNotas()">Listar Notas</button>
        <button onclick="condicionalumnos()">Ver Condición</button>
    </div>

    <!-- Formulario para Mostrar Condición del Alumno -->
    <div id="form-container">
        <!-- Aquí se mostrará el nombre y condición del alumno -->
    </div>

    <!-- Tabla de Notas -->
    <table>
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>Parcial 1</th>
                <th>Parcial 2</th>
                <th>Final</th>
            </tr>
        </thead>
        <tbody id="notasBody">
            <!-- Las filas de notas se agregarán aquí dinámicamente -->
        </tbody>
    </table>
    
    <!-- Mensajes de error o éxito (Contenedor de Mensajes) -->
    <br><div id="mensajeContainer" class="mensaje" style="display:none;"></div>
    
    <!-- Botón de Guardado -->
    <br><button onclick="guardarNotas()">Guardar Notas</button>
</div>


<img src="../resources/css/mesas.jpeg" alt="" style="width:100%; height:auto; margin-bottom:600px;">

    </main>
 
    <!-- Pie de página -->
    <footer>
        <p><strong>Asistido</strong>. <br><br> © 2024 Todos los derechos reservados.</p>
    </footer>


    <link rel="stylesheet" href="/resources/css/index.css?v=1.0">

<!-- Scripts necesarios con defer para cargar después de que el HTML se haya procesado -->
<script src="./resources/js/usuario.js" defer></script>
<script src="./resources/js/alumno.js" defer></script>
<script src="./resources/js/notas.js" defer></script>
<script src="./resources/js/asistencia.js" defer></script>
<script src="./resources/js/condicion.js" defer></script>
<script src="./resources/js/model.js" defer></script>
<script src="./resources/js/seleccionar_registro.js" defer></script>

</body>
</html>
