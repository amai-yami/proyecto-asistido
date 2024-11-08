<?php
require_once 'conexion.php';

// Comprobar si se han enviado datos mediante POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $legajo = $_POST['legajo'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $id_usuario = $_POST['id_usuario']; // Supongo que tienes un campo oculto para el id de usuario

    // Preparar la consulta SQL para insertar el profesor
    $sql = "INSERT INTO profesor (id, legajo, dni, telefono) VALUES (:id_usuario, :legajo, :dni, :telefono)";

    try {
        // Preparar la sentencia
        $stmt = $pdo->prepare($sql);
        
        // Bindear los parámetros
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':legajo', $legajo);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':telefono', $telefono);
        
        // Ejecutar la consulta
        $stmt->execute();
        
        // Redirigir o mostrar un mensaje de éxito
        echo "Profesor registrado con éxito.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

