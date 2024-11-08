<?php
require_once 'conexion.php';

$database = new Database();
$db = $database->connect();

$search = $_GET['search'] ?? '';
$search = "%$search%"; // Agregar comodines para la búsqueda

$query = "SELECT a.id, a.nombre, a.apellido, n.parcial1, n.parcial2, n.final 
          FROM alumno a
          LEFT JOIN alumno_nota an ON a.id = an.id_alumno
          LEFT JOIN nota n ON an.id_nota = n.id
          WHERE a.nombre LIKE :search OR a.apellido LIKE :search";

$stmt = $db->prepare($query);
$stmt->execute(['search' => $search]);
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($alumnos);

