<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost;dbname=asistencias', 'root', ''); // Cambia estos datos si es necesario

// Obtener los datos enviados en el cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"));

if (isset($data->matricula)) {
    try {
        // Preparar la consulta SQL para actualizar los datos del alumno
        $stmt = $pdo->prepare("UPDATE alumno SET 
            nombre = :nombre,
            apellido = :apellido,
            dni = :dni,
            fecha_nacimiento = :fecha_nacimiento,
            correo_electronico = :email
            WHERE matricula = :matricula");

        // Vincular los parámetros a las variables correspondientes
        $stmt->bindParam(':nombre', $data->nombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $data->apellido, PDO::PARAM_STR);
        $stmt->bindParam(':dni', $data->dni, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_nacimiento', $data->fecha_nacimiento, PDO::PARAM_STR);
        $stmt->bindParam(':email', $data->email, PDO::PARAM_STR);
        $stmt->bindParam(':matricula', $data->matricula, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Verificar si se realizó alguna modificación
        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => "Alumno modificado correctamente"]);
        } else {
            echo json_encode(["error" => "No se encontró el alumno o no se realizaron cambios"]);
        }
    } catch (PDOException $e) {
        // En caso de error, enviar el mensaje de error
        echo json_encode(["error" => "Error al modificar alumno: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Matrícula no proporcionada"]);
}
?>
