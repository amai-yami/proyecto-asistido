<?php
require_once 'conexion.php';

$database = new Database();
$db = $database->connect();

try {
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    if (empty($search)) {
        echo json_encode(['error' => 'El parámetro de búsqueda está vacío']);
        exit;
    }

    // Actualiza la consulta para buscar por nombre, apellido, dni o matrícula
    $stmt = $db->prepare("SELECT id, matricula, dni, nombre, apellido FROM alumno WHERE 
        nombre LIKE :search OR apellido LIKE :search OR matricula LIKE :search OR dni LIKE :search");
    $stmt->execute(['search' => "%$search%"]);
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($alumnos);

} catch (PDOException $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Error al ejecutar la consulta: ' . $e->getMessage()]);
} finally {
    $db = null;
}
?>
