<?php
require_once 'conexion.php';

$database = new Database();
$db = $database->connect();

try {
    // Obtener los datos de las notas desde $_POST
    $parcial1 = $_POST['parcial1'] ?? []; // Arreglo de notas parciales 1
    $parcial2 = $_POST['parcial2'] ?? []; // Arreglo de notas parciales 2
    $final = $_POST['final'] ?? [];       // Arreglo de notas finales

    // Verificar los datos recibidos
    if (empty($parcial1) || empty($parcial2) || empty($final)) {
        throw new Exception('Faltan datos de notas.');
    }

    // Comenzar transacción
    $db->beginTransaction();

    // Iterar sobre los alumnos y las notas
    foreach ($parcial1 as $id_alumno => $nota) {
        // Verificar si ya existe una nota para este alumno
        $stmtCheckNota = $db->prepare("SELECT id_nota FROM alumno_nota WHERE id_alumno = :id_alumno");
        $stmtCheckNota->execute([':id_alumno' => $id_alumno]);
        $existingNote = $stmtCheckNota->fetch();

        // Obtener las notas parciales 2 y final para el alumno
        $notaParcial2 = isset($parcial2[$id_alumno]) ? $parcial2[$id_alumno] : 0;
        $notaFinal = isset($final[$id_alumno]) ? $final[$id_alumno] : 0;

        if ($existingNote) {
            // Si existe, actualizamos la nota
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
            // Insertar nueva nota si no existe
            $stmtInsert = $db->prepare("INSERT INTO nota (parcial1, parcial2, final) VALUES (:parcial1, :parcial2, :final)");
            $stmtInsert->execute([
                ':parcial1' => $nota,
                ':parcial2' => $notaParcial2,
                ':final' => $notaFinal
            ]);

            // Obtener el ID de la nota recién insertada
            $id_nota = $db->lastInsertId();

            // Relacionar la nueva nota con el alumno
            $stmtInsertRelation = $db->prepare("INSERT INTO alumno_nota (id_alumno, id_nota) VALUES (:id_alumno, :id_nota)");
            $stmtInsertRelation->execute([
                ':id_alumno' => $id_alumno,
                ':id_nota' => $id_nota
            ]);
        }
    }

    // Finalizar la transacción
    $db->commit();
    echo json_encode(['success' => 'Notas guardadas correctamente.']);
} catch (Exception $e) {
    $db->rollBack();
    // Responder con un error en JSON si ocurre una excepción
    echo json_encode(['error' => 'Error al guardar notas: ' . $e->getMessage()]);
}
?>
