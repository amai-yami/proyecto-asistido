<?php
require 'conexion.php'; // Asegúrate de que este archivo esté correctamente configurado para conectarse a la base de datos

if (isset($_GET['instituto_id']) && isset($_GET['profesor_id'])) {
    $instituto_id = $_GET['instituto_id'];
    $profesor_id = $_GET['profesor_id'];

    // Ajusta la consulta según tus relaciones
    $sql = "SELECT m.id, m.nombre 
            FROM materia m 
            JOIN profesor_materia pm ON m.id = pm.id_materia 
            JOIN profesor p ON pm.id_profesor = p.id 
            JOIN profesor_institucion pi ON p.id = pi.id_profesor 
            WHERE pi.id_institucion = :instituto_id AND p.id = :profesor_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':instituto_id', $instituto_id);
    $stmt->bindParam(':profesor_id', $profesor_id);
    $stmt->execute();

    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($materias);
} else {
    echo json_encode([]);
}
?>
