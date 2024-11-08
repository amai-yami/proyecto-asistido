<?php
require 'conexion.php'; // Asegúrate de que este archivo esté correctamente configurado para conectarse a la base de datos

$sql = "SELECT id, direccion FROM institucion"; // Ajusta los campos según lo que necesites
$stmt = $pdo->prepare($sql);
$stmt->execute();

$institutos = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($institutos);
?>
