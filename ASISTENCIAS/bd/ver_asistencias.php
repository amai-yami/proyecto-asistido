<?php
// Incluir el archivo de conexión
include('conexion.php');  // Incluir la clase Database

// Activar la visualización de errores para el desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Establecer el encabezado para que se indique que estamos enviando JSON
header('Content-Type: application/json');

// Crear una instancia de la clase Database y obtener la conexión
$database = new Database();
$pdo = $database->connect();  // Conectar a la base de datos

// Verificar si la conexión es válida
if (!$pdo) {
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

// Consulta para obtener el número total de asistencias de cada alumno
$query = "SELECT al.id AS id_alumno, CONCAT(al.nombre, ' ', al.apellido) AS nombre_completo, 
                 COUNT(a.asistencia) AS numero_asistencias
          FROM asistencia a
          JOIN alumno al ON a.id_alumno = al.id
          WHERE a.asistencia = 1  -- Contamos solo las asistencias
          GROUP BY a.id_alumno";  // Agrupamos por alumno

try {
    // Ejecutar la consulta
    $result = $pdo->query($query);

    // Verificar si la consulta fue exitosa
    if (!$result) {
        throw new Exception("Error en la consulta a la base de datos.");
    }

    // Crear un array para almacenar los resultados
    $asistencias = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $asistencias[] = $row;
    }

    // Verificar si hay datos en el array
    if (empty($asistencias)) {
        echo json_encode(['error' => 'No se encontraron asistencias.']);
        exit;
    }

    // Devolver las asistencias como JSON
    echo json_encode($asistencias);

} catch (Exception $e) {
    // Registrar el error en el log
    error_log('Error en PHP: ' . $e->getMessage());

    // Si hay un error, devolver un JSON con el error
    echo json_encode(['error' => 'Error al obtener las asistencias: ' . $e->getMessage()]);
}
?>
