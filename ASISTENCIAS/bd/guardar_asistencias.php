<?php
// Incluir la clase de conexión
include('conexion.php');
//require 'Funciones.php';
// Inicializar la clase Database y obtener la conexión
$database = new Database();
$pdo = $database->connect();  // Obtener la conexión a la base de datos

header('Content-Type: application/json'); // Asegurar que la respuesta sea JSON

// Verificar que se reciban los datos correctamente
$asistencias = json_decode(file_get_contents('php://input'), true);

if (!$asistencias) {
    // Devuelve un mensaje de error si no hay datos válidos
    echo json_encode(['success' => false, 'message' => 'No se enviaron datos válidos']);
    exit;
}

// Iniciar un flag para verificar si hubo un error en el proceso
$exito = true;

// Preparar la consulta para insertar la asistencia
$sql = "INSERT INTO asistencia (id_alumno, asistencia, fecha) VALUES (:alumnoId, :asistencia, NOW())";
$stmt = $pdo->prepare($sql);

// Comenzamos el ciclo para procesar cada asistencia
foreach ($asistencias as $asistencia) {
    $alumnoId = $asistencia['alumnoId'];
    $asistenciaValor = isset($asistencia['asistencia']) ? ($asistencia['asistencia'] ? 1 : 0) : 0; // Si no está presente, se marca como 0

    // Solo procesamos la asistencia si el alumno tiene una marca de asistencia
    if ($alumnoId !== null) {
        // Bind de parámetros para cada iteración
        $stmt->bindParam(':alumnoId', $alumnoId);
        $stmt->bindParam(':asistencia', $asistenciaValor);
        
        // Ejecutar la consulta para cada asistencia
        if (!$stmt->execute()) {
            $exito = false; // Si falla, marcar como error
            break; // Salir del ciclo si ocurre un error
        }
    }
}


// Respuesta final dependiendo del resultado
if ($exito) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Hubo un error al registrar las asistencias']);
}
?>
