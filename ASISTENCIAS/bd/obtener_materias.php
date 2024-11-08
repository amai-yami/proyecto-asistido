<?php
// obtener_materias.php

session_start();
require_once 'conexion.php'; // Archivo de conexión con PDO

// Verificamos que el ID del profesor esté en la sesión
if (isset($_SESSION['id_profesor'])) {
    $idProfesor = $_SESSION['id_profesor'];
    
    try {
        // Consulta para obtener las materias asignadas al profesor
        $stmt = $pdo->prepare("
            SELECT m.id, m.nombre 
            FROM materia AS m
            INNER JOIN profesor_materia AS pm ON m.id = pm.id_materia
            WHERE pm.id_profesor = :idProfesor
        ");
        $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
        $stmt->execute();
        
        // Almacenamos los resultados en un arreglo
        $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enviamos el resultado como JSON
        echo json_encode($materias);

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'No se ha encontrado el ID del profesor en la sesión.']);
}
?>
