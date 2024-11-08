<?php
session_start();
require_once 'conexion.php';

function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Establecer el encabezado para el tipo de contenido JSON
header('Content-Type: application/json');

// Verificar que se hayan enviado los datos del formulario
if (isset($_POST["nombre"], $_POST["apellido"], $_POST["dni"], $_POST["fecha_nacimiento"], $_POST["email"], $_POST["matricula"])) {
    $nombre = clean_input($_POST["nombre"]);
    $apellido = clean_input($_POST["apellido"]);
    $dni = clean_input($_POST["dni"]);
    $fecha_nacimiento = clean_input($_POST["fecha_nacimiento"]);
    $email = clean_input($_POST["email"]);
    $matricula = clean_input($_POST["matricula"]);

    try {
        $database = new Database();
        $conn = $database->connect();

        $stmt = $conn->prepare('SELECT * FROM alumno WHERE dni = :dni OR correo_electronico = :email');
        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Devolver error en formato JSON
            echo json_encode(['success' => false, 'error' => 'El DNI o el correo ya están registrados.']);
        } else {
            $stmt = $conn->prepare('INSERT INTO alumno (nombre, apellido, matricula, dni, fecha_nacimiento, correo_electronico) VALUES (:nombre, :apellido, :matricula, :dni, :fecha_nacimiento, :email)');
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
            $stmt->bindParam(':matricula', $matricula, PDO::PARAM_STR);
            $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            // Devolver éxito en formato JSON
            echo json_encode(['success' => true, 'message' => 'Registro de alumno exitoso.']);
        }
    } catch (PDOException $e) {
        // Devolver error de conexión en formato JSON
        echo json_encode(['success' => false, 'error' => 'Error de conexión: ' . $e->getMessage()]);
    }
} else {
    // Devolver error si no hay datos en formato JSON
    echo json_encode(['success' => false, 'error' => 'No se recibieron los datos del alumno.']);
}


/*



*/