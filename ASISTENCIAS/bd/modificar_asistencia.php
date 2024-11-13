<?php
require_once 'conexion.php';

$database = new Database();
$db = $database->connect();

try {
    // Obtener los datos de las asistencias desde el cuerpo de la solicitud
    $asistencias = json_decode(file_get_contents('php://input'), true)['asistencias'];

    // Verificar los datos recibidos
    if (empty($asistencias)) {
        throw new Exception('Faltan datos de asistencias.');
    }
    
    // Loguear los datos que estamos recibiendo (puedes quitar esto en producción)
    error_log(print_r($asistencias, true)); 

    // Iniciar la transacción
    $db->beginTransaction();

    // Preparar consulta para inserción o actualización
    $stmt = $db->prepare(
        "INSERT INTO asistencia (id_alumno, asistencia) 
         VALUES (:id_alumno, :valorAsistencia)
         ON DUPLICATE KEY UPDATE asistencia = :valorAsistencia"
    );

    // Recorrer todas las asistencias y ejecutarlas
    foreach ($asistencias as $asistencia) {
        $id_alumno = $asistencia['alumnoId'];
        $valorAsistencia = $asistencia['valorAsistencia'];  // El valor recibido es el valor exacto

        // Verificar si estamos recibiendo los valores correctamente
        error_log("Alumno ID: " . $id_alumno . " - Valor Asistencia: " . $valorAsistencia);

        // Verificar si el alumno existe en la base de datos
        $checkAlumno = $db->prepare("SELECT COUNT(*) FROM alumno WHERE id = :id_alumno");
        $checkAlumno->execute([':id_alumno' => $id_alumno]);
        $existeAlumno = $checkAlumno->fetchColumn();

        if ($existeAlumno == 0) {
            throw new Exception("El alumno con ID {$id_alumno} no existe.");
        }

        // Ejecutar la consulta para insertar o actualizar
        $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt->bindParam(':valorAsistencia', $valorAsistencia, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Confirmar la transacción
    $db->commit();
    echo json_encode(['success' => 'Asistencias guardadas correctamente.']);
} catch (Exception $e) {
    // En caso de error, deshacer la transacción
    $db->rollBack();
    echo json_encode(['error' => 'Error al guardar asistencias: ' . $e->getMessage()]);
}
?>
