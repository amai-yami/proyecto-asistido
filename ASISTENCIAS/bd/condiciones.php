<?php
include_once('conexion.php'); // Asegúrate de tener la conexión incluida

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Obtener configuración de base de datos desde variables de entorno (o configuración)
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'asistencias';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    enviarRespuesta([
        'error' => true,
        'mensaje' => 'Error de conexión a la base de datos: ' . $e->getMessage()
    ], 500);
}

// Validar si el parámetro id_alumnos está presente y no está vacío
if (!isset($_GET['id_alumnos']) || empty($_GET['id_alumnos'])) {
    enviarRespuesta([
        'error' => true,
        'mensaje' => 'ID de alumno(s) no proporcionado o inválido.'
    ], 400);
}

// Obtener los IDs de los alumnos y validarlos
$id_alumnos = explode(',', $_GET['id_alumnos']);
$id_alumnos = array_map('intval', $id_alumnos); // Aseguramos que los IDs sean enteros
$id_alumnos = array_filter($id_alumnos, fn($id) => $id > 0); // Filtramos IDs negativos o no válidos

if (empty($id_alumnos)) {
    enviarRespuesta([
        'error' => true,
        'mensaje' => 'No se proporcionaron IDs válidos de alumnos.'
    ], 400);
}

// Preparamos la consulta para obtener las condiciones de varios alumnos
$query = "SELECT nombre, apellido, condicion, asistencias FROM alumno WHERE id IN (" . implode(',', array_fill(0, count($id_alumnos), '?')) . ")";
$stmt = $pdo->prepare($query);

// Vinculamos los parámetros
foreach ($id_alumnos as $index => $id_alumno) {
    $stmt->bindValue($index + 1, $id_alumno, PDO::PARAM_INT);
}

try {
    $stmt->execute();
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    enviarRespuesta([
        'error' => true,
        'mensaje' => 'Error al ejecutar la consulta: ' . $e->getMessage()
    ], 500);
}

// Si no se encuentran resultados
if (empty($alumnos)) {
    enviarRespuesta([
        'error' => true,
        'mensaje' => 'No se encontraron datos para los alumnos proporcionados.'
    ], 404);
}

// Responder con los datos de los alumnos
enviarRespuesta($alumnos);

// Función para enviar la respuesta JSON
function enviarRespuesta(array $data, int $status = 200) {
    $json_data = json_encode($data, JSON_PRETTY_PRINT);

    if ($json_data === false) {
        $data = [
            'error' => true,
            'mensaje' => 'Error al codificar la respuesta en JSON: ' . json_last_error_msg()
        ];
        $json_data = json_encode($data, JSON_PRETTY_PRINT);
    }

    echo $json_data;
    http_response_code($status);
    exit();
}
?>
