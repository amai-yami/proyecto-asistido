<?php

require_once 'conexion.php';
require_once '../clases/Usuario.php'; // Subir un nivel para acceder a Usuario.php

// Función para limpiar los datos de entrada
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$response = array('success' => false, 'message' => ''); // Inicializamos la respuesta

// Verificar si se recibieron los valores por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesar el registro
    $usuario = clean_input($_POST["usuario"]);
    $correo = clean_input($_POST["correo"]);
    $contrasena = clean_input($_POST["contrasena"]);
    $nombre = clean_input($_POST["nombre"]);
    $apellido = clean_input($_POST["apellido"]);

    // Validar que los campos no estén vacíos
    if (empty($usuario) || empty($correo) || empty($contrasena) || empty($nombre) || empty($apellido)) {
        $response['message'] = "Error: Todos los campos son obligatorios.";
    } else {
        // Define el rol que se asignará. Asegúrate de que este ID corresponda a un rol existente.
        $id_rol = 1; // Por ejemplo, 1 podría ser el rol de 'Estudiante'.

        try {
            // Crear una instancia de la clase Database
            $database = new Database();
            // Obtener la conexión
            $conn = $database->connect();

            // Verificar si el usuario o el correo ya existen
            $stmt = $conn->prepare('SELECT * FROM usuario WHERE usuario = :usuario OR correo_electronico = :correo');
            $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Si ya existe, respondemos con un mensaje de error
                $response['message'] = "Error: El usuario o el correo ya están registrados.";
            } else {
                // Hashear la contraseña
                $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

                // Modificar la consulta de inserción para incluir id_rol
                $stmt = $conn->prepare('INSERT INTO usuario (usuario, correo_electronico, contrasena, nombre, apellido, id_rol) VALUES (:usuario, :correo, :contrasena, :nombre, :apellido, :id_rol)');
                $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
                $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
                $stmt->bindParam(':contrasena', $hashed_password, PDO::PARAM_STR);
                $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
                $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT); // Añadir este vínculo
                $stmt->execute();

                // Si todo sale bien, respondemos con éxito
                $response['success'] = true;
                $response['message'] = "Registro exitoso.";
            }
        } catch (PDOException $e) {
            // Si ocurre un error, lo registramos
            $response['message'] = "Error de conexión: " . $e->getMessage();
            error_log("Error en el registro de usuario: " . $e->getMessage()); // Esto se registra en los logs del servidor
        }
    }
} else {
    // Si no se recibieron los valores por POST, mostrar un mensaje de error
    $response['message'] = "Error: No se recibieron los valores por POST.";
}

// Enviar la respuesta en formato JSON
echo json_encode($response);
