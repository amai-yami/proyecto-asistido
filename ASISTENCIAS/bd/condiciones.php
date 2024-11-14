<?php
// Incluir el archivo de la clase Database
include('conexion.php');

// Crear una instancia de la clase Database
$database = new Database();
$pdo = $database->connect();

if (!$pdo) {
    echo json_encode(["error" => "Error en la conexión a la base de datos."]);
    exit;
}

// Obtener los datos enviados por POST (JSON)
$inputData = json_decode(file_get_contents('php://input'), true);
if ($inputData === null) {
    echo json_encode(["error" => "Error al recibir los datos. Asegúrate de que el formato sea JSON."]);
    exit;
}

// Variables del formulario recibidas desde JavaScript
$notaPromocion = $inputData['promocion']['porcentajeNota'];
$notaRegularizacion = $inputData['regularizacion']['porcentajeNota'];
$minimoAsistencias = $inputData['diasClase'];
$porcentajeAsistenciaPromocion = $inputData['promocion']['porcentajeAsistencia'];
$porcentajeAsistenciaRegular = $inputData['regularizacion']['porcentajeAsistencia'];

// Consulta para obtener alumnos, notas y asistencias
$query = "
    SELECT a.id AS id_alumno, 
           CONCAT(a.nombre, ' ', a.apellido) AS nombre_completo, 
           SUM(CASE WHEN n.parcial1 IS NOT NULL THEN n.parcial1 ELSE 0 END) AS parcial1,
           SUM(CASE WHEN n.parcial2 IS NOT NULL THEN n.parcial2 ELSE 0 END) AS parcial2,
           SUM(CASE WHEN n.final IS NOT NULL THEN n.final ELSE 0 END) AS final,
           SUM(CASE WHEN asis.asistencia = 1 THEN 1 ELSE 0 END) AS asistencias
    FROM alumno a
    LEFT JOIN alumno_nota an ON a.id = an.id_alumno
    LEFT JOIN nota n ON an.id_nota = n.id
    LEFT JOIN asistencia asis ON a.id = asis.id_alumno
    GROUP BY a.id
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar cada alumno y su condición
$resultado = [];
foreach ($alumnos as $alumno) {
    // Cálculo del porcentaje de asistencia
    $porcentajeAsistencia = ($alumno['asistencias'] / $minimoAsistencias) * 100;

    // Asegurarse de que el porcentaje de asistencia no supere 100%
    if ($porcentajeAsistencia > 100) {
        $porcentajeAsistencia = 100;
    }

    // Verificar la condición en base a las 3 notas y la asistencia
    $condicion = "";  // Para almacenar la condición del alumno

    // Si las 3 notas son mayores o iguales a la nota de promoción y la asistencia cumple con el mínimo
    if ($alumno['parcial1'] >= $notaPromocion && $alumno['parcial2'] >= $notaPromocion && $alumno['final'] >= $notaPromocion && $porcentajeAsistencia >= $porcentajeAsistenciaPromocion) {
        $condicion = "Promoción";
    }
    // Si las 3 notas son mayores o iguales a la nota de regularización y la asistencia cumple con el mínimo
    elseif ($alumno['parcial1'] >= $notaRegularizacion && $alumno['parcial2'] >= $notaRegularizacion && $alumno['final'] >= $notaRegularizacion && $porcentajeAsistencia >= $porcentajeAsistenciaRegular) {
        $condicion = "Regularización";
    }
    // Si no cumple ninguna de las dos condiciones, es "Libre"
    else {
        $condicion = "Libre";
    }

    // Agregar al resultado el nombre del alumno y su condición
    $resultado[] = [
        "alumno" => $alumno['nombre_completo'],
        "condicion" => $condicion  // Condición como texto
    ];
}

// Enviar la respuesta en formato JSON
echo json_encode($resultado);
?>
