<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost;dbname=asistencias', 'root', ''); // Cambia estos datos si es necesario

try {
    // Primero, eliminar las relaciones en la tabla alumno_nota que hacen referencia a las notas
    $stmt = $pdo->prepare("DELETE FROM alumno_nota");
    $stmt->execute();

    // Luego, eliminar todas las asistencias asociadas a los alumnos
    $stmt = $pdo->prepare("DELETE FROM asistencia");
    $stmt->execute();

    // Después, eliminar todas las notas asociadas a los alumnos
    $stmt = $pdo->prepare("DELETE FROM nota");
    $stmt->execute();

    // Finalmente, eliminar todos los registros de alumnos
    $stmt = $pdo->prepare("DELETE FROM alumno");
    $stmt->execute();

   // Reiniciar AUTO_INCREMENT de las tablas: alumno, asistencia y alumno_nota
$pdo->prepare("ALTER TABLE alumno AUTO_INCREMENT = 1")->execute();
$pdo->prepare("ALTER TABLE asistencia AUTO_INCREMENT = 1")->execute();
$pdo->prepare("ALTER TABLE nota AUTO_INCREMENT = 1")->execute();

    // Verificar si se eliminaron registros
    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => "Todos los alumnos, sus notas, asistencias y registros de notas han sido eliminados correctamente"]);
    } else {
        echo json_encode(["error" => "No se encontraron alumnos para eliminar"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al eliminar curso: " . $e->getMessage()]);
}
?>
