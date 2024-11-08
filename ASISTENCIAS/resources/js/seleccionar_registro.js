document.addEventListener('DOMContentLoaded', function() {
    // Cargar los institutos al cargar la página
    cargarInstitutos();

    // Cargar las carreras cuando se seleccione un instituto
    document.getElementById('instituto').addEventListener('change', cargarCarreras);
    
    // Cargar los años cuando se seleccione una carrera
    document.getElementById('carrera').addEventListener('change', cargarAnios);
    
    // Cargar los cursos cuando se seleccione un año
    document.getElementById('anio').addEventListener('change', cargarCursos);
    
    // Cargar las materias cuando se seleccione un curso
    document.getElementById('curso').addEventListener('change', cargarMaterias);
});

function cargarInstitutos() {
    // Aquí se haría la llamada a PHP para obtener los institutos
    fetch('bd/cargar_institutos.php')
        .then(response => response.json())
        .then(data => {
            const institutoSelect = document.getElementById('instituto');
            data.forEach(instituto => {
                const option = document.createElement('option');
                option.value = instituto.id;
                option.textContent = instituto.nombre;
                institutoSelect.appendChild(option);
            });
        });
}

function cargarCarreras() {
    const institutoId = document.getElementById('instituto').value;
    // Aquí se haría la llamada a PHP para obtener las carreras según el instituto
    fetch(`bd/cargar_carreras.php?instituto_id=${institutoId}`)
        .then(response => response.json())
        .then(data => {
            const carreraSelect = document.getElementById('carrera');
            carreraSelect.innerHTML = '<option value="">-- Selecciona una Carrera --</option>'; // Resetear opciones
            data.forEach(carrera => {
                const option = document.createElement('option');
                option.value = carrera.id;
                option.textContent = carrera.nombre;
                carreraSelect.appendChild(option);
            });
        });
}

function cargarAnios() {
    const carreraId = document.getElementById('carrera').value;
    // Aquí se haría la llamada a PHP para obtener los años según la carrera
    fetch(`bd/cargar_anios.php?carrera_id=${carreraId}`)
        .then(response => response.json())
        .then(data => {
            const anioSelect = document.getElementById('anio');
            anioSelect.innerHTML = '<option value="">-- Selecciona un Año --</option>'; // Resetear opciones
            data.forEach(anio => {
                const option = document.createElement('option');
                option.value = anio.id;
                option.textContent = anio.nombre;
                anioSelect.appendChild(option);
            });
        });
}

function cargarCursos() {
    const anioId = document.getElementById('anio').value;
    // Aquí se haría la llamada a PHP para obtener los cursos según el año
    fetch(`bd/cargar_cursos.php?anio_id=${anioId}`)
        .then(response => response.json())
        .then(data => {
            const cursoSelect = document.getElementById('curso');
            cursoSelect.innerHTML = '<option value="">-- Selecciona un Curso --</option>'; // Resetear opciones
            data.forEach(curso => {
                const option = document.createElement('option');
                option.value = curso.id;
                option.textContent = curso.nombre;
                cursoSelect.appendChild(option);
            });
        });
}
function cargarMaterias() {
    const cursoId = document.getElementById('curso').value;

    // Solicita las materias desde el PHP
    fetch(`bd/cargar_materias.php?curso_id=${cursoId}`)
        .then(response => response.json())
        .then(data => {
            const materiasContainer = document.getElementById('materias');
            materiasContainer.innerHTML = ''; // Limpiar opciones previas

            data.forEach(materia => {
                // Crear un toggle para cada materia
                const label = document.createElement('label');
                label.classList.add('materia-toggle'); // Clase para estilo de toggle

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.value = materia.id;
                checkbox.name = 'materias[]'; // Para enviar al formulario

                const span = document.createElement('span');
                span.textContent = materia.nombre;

                // Agregar los elementos al contenedor
                label.appendChild(checkbox);
                label.appendChild(span);
                materiasContainer.appendChild(label);
            });
        });
}
