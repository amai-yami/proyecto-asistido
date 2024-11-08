<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost;dbname=asistencias', 'root', ''); // Cambia estos datos si es necesario

// Obtener datos de la solicitud
$data = json_decode(file_get_contents("php://input"));

if (isset($data->matricula)) {
    try {
        // Obtener el ID del alumno basado en la matrícula
        $stmt = $pdo->prepare("SELECT id FROM alumno WHERE matricula = :matricula");
        $stmt->bindParam(':matricula', $data->matricula, PDO::PARAM_STR);
        $stmt->execute();
        $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($alumno) {
            $id_alumno = $alumno['id'];

            // Eliminar las asistencias del alumno
            $stmt = $pdo->prepare("DELETE FROM asistencia WHERE id_alumno = :id_alumno");
            $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
            $stmt->execute();

            // Eliminar las notas del alumno
            $stmt = $pdo->prepare("DELETE FROM alumno_nota WHERE id_alumno = :id_alumno");
            $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
            $stmt->execute();

            // Ahora eliminar al alumno
            $stmt = $pdo->prepare("DELETE FROM alumno WHERE matricula = :matricula");
            $stmt->bindParam(':matricula', $data->matricula, PDO::PARAM_STR);
            $stmt->execute();

            // Reiniciar AUTO_INCREMENT de la tabla alumno
            $pdo->prepare("ALTER TABLE alumno AUTO_INCREMENT = 1")->execute();

            echo json_encode(["success" => "Alumno, sus notas y asistencias eliminados correctamente"]);
        } else {
            echo json_encode(["error" => "No se encontró el alumno con esa matrícula"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error al eliminar alumno: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Matrícula no proporcionada"]);
}
