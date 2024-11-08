<?php
// Incluir el archivo de conexión
require_once 'conexion.php';

// Crear una instancia de la clase Database
$database = new Database();
$db = $database->connect();

try {
    // Consulta SQL para obtener todos los alumnos
    $stmt = $db->query("SELECT id, nombre, apellido FROM alumno");
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($alumnos);

} catch (PDOException $e) {
    // Manejo de errores
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Error al ejecutar la consulta: ' . $e->getMessage()]);
}
?>
