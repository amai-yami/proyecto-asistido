<?php
// Incluir el archivo de conexión
include('conexion.php');  // Incluir la clase Database

// Activar la visualización de errores para el desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Establecer el encabezado para que se indique que estamos enviando JSON
header('Content-Type: application/json');

// Crear una instancia de la clase Database y obtener la conexión
try {
    $database = new Database();
    $pdo = $database->connect();  // Conectar a la base de datos

    // Verificar si la conexión es válida
    if (!$pdo) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Consulta para obtener el número total de asistencias de cada alumno
    $query = "
        SELECT al.id AS id_alumno, 
               CONCAT(al.nombre, ' ', al.apellido) AS nombre_completo, 
               SUM(a.asistencia) AS numero_asistencias  
        FROM asistencia a
        JOIN alumno al ON a.id_alumno = al.id
        GROUP BY a.id_alumno";  // Agrupamos por alumno

    // Ejecutar la consulta
    $result = $pdo->query($query);

    // Comprobar si la consulta fue exitosa
    if (!$result) {
        throw new Exception("Error en la consulta a la base de datos.");
    }

    // Crear un array para almacenar los resultados
    $asistencias = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $asistencias[] = [
            'id_alumno' => $row['id_alumno'],
            'nombre_completo' => $row['nombre_completo'],
            'numero_asistencias' => $row['numero_asistencias'] // Sumar las asistencias
        ];
    }

    // Verificar si se encontraron asistencias
    if (empty($asistencias)) {
        echo json_encode(['error' => 'No se encontraron asistencias.']);
        exit;
    }

    // Devolver los resultados como JSON
    echo json_encode($asistencias);

} catch (Exception $e) {
    // Registrar el error en el log
    error_log('Error en PHP: ' . $e->getMessage());

    // Devolver un error en formato JSON
    echo json_encode(['error' => 'Error al obtener las asistencias: ' . $e->getMessage()]);
}
?>
