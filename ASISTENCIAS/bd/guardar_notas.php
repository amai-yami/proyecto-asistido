<?php
require_once 'conexion.php';

const NOTA_MIN = 0;
const NOTA_MAX = 10;

$database = new Database();
$db = $database->connect();

try {
    // Obtener los datos de las notas desde $_POST
    $parcial1 = $_POST['parcial1'] ?? [];
    $parcial2 = $_POST['parcial2'] ?? [];
    $final = $_POST['final'] ?? [];

    if (empty($parcial1) || empty($parcial2) || empty($final)) {
        throw new Exception('Faltan datos de notas.');
    }

    $db->beginTransaction();

    foreach ($parcial1 as $id_alumno => $nota) {
        $notaParcial2 = $parcial2[$id_alumno] ?? 0;
        $notaFinal = $final[$id_alumno] ?? 0;

        // Validación de rango de notas
        if ((!is_numeric($nota) || $nota < NOTA_MIN || $nota > NOTA_MAX) ||
            (!is_numeric($notaParcial2) || $notaParcial2 < NOTA_MIN || $notaParcial2 > NOTA_MAX) ||
            (!is_numeric($notaFinal) || $notaFinal < NOTA_MIN || $notaFinal > NOTA_MAX)) {
            throw new Exception("Error: Notas fuera de rango para el alumno con ID $id_alumno.");
        }

        // Verificar si ya existe una nota para este alumno
        $stmtCheckNota = $db->prepare("SELECT id_nota FROM alumno_nota WHERE id_alumno = :id_alumno");
        $stmtCheckNota->execute([':id_alumno' => $id_alumno]);
        $existingNote = $stmtCheckNota->fetch();

        if ($existingNote) {
            $id_nota = $existingNote['id_nota'];
            $stmtUpdate = $db->prepare("
                UPDATE nota SET 
                    parcial1 = :parcial1, 
                    parcial2 = :parcial2, 
                    final = :final 
                WHERE id = :id_nota
            ");
            $stmtUpdate->execute([
                ':parcial1' => $nota,
                ':parcial2' => $notaParcial2,
                ':final' => $notaFinal,
                ':id_nota' => $id_nota
            ]);
        } else {
            $stmtInsert = $db->prepare("INSERT INTO nota (parcial1, parcial2, final) VALUES (:parcial1, :parcial2, :final)");
            $stmtInsert->execute([
                ':parcial1' => $nota,
                ':parcial2' => $notaParcial2,
                ':final' => $notaFinal
            ]);

            $id_nota = $db->lastInsertId();
            $stmtInsertRelation = $db->prepare("INSERT INTO alumno_nota (id_alumno, id_nota) VALUES (:id_alumno, :id_nota)");
            $stmtInsertRelation->execute([
                ':id_alumno' => $id_alumno,
                ':id_nota' => $id_nota
            ]);
        }
    }

    $db->commit();
    echo json_encode(['success' => 'Notas guardadas correctamente.']);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['error' => 'Error al guardar notas: ' . $e->getMessage()]);
}
?>
