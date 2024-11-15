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
$notaPromocion = (float)($inputData['promocion']['porcentajeNota'] ?? 0);
$notaRegularizacion = (float)($inputData['regularizacion']['porcentajeNota'] ?? 0);
$minimoAsistencias = (int)($inputData['diasClase'] ?? 0);
$porcentajeAsistenciaPromocion = (float)($inputData['promocion']['porcentajeAsistencia'] ?? 0);
$porcentajeAsistenciaRegular = (float)($inputData['regularizacion']['porcentajeAsistencia'] ?? 0);

// Consulta para obtener alumnos, notas y asistencias usando INNER JOIN
$query = "
    SELECT a.id AS id_alumno, 
           CONCAT(a.nombre, ' ', a.apellido) AS nombre_completo, 
           COALESCE(AVG(n.parcial1), 0) AS parcial1,
           COALESCE(AVG(n.parcial2), 0) AS parcial2,
           COALESCE(AVG(n.final), 0) AS final,
           SUM(asis.asistencia) AS asistencias  -- Sumar cualquier valor en asis.asistencia
    FROM alumno a
    INNER JOIN alumno_nota an ON a.id = an.id_alumno
    INNER JOIN nota n ON an.id_nota = n.id
    INNER JOIN asistencia asis ON a.id = asis.id_alumno
    GROUP BY a.id
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar cada alumno y su condición
$resultado = [];
foreach ($alumnos as $alumno) {
    // Cálculo del porcentaje de asistencia
    $porcentajeAsistencia = ($alumno['asistencias'] / max($minimoAsistencias, 1)) * 100;

    // Asegurarse de que el porcentaje de asistencia no supere 100%
    if ($porcentajeAsistencia > 100) {
        $porcentajeAsistencia = 100;
    }

    // Verificar la condición en base a las 3 notas y la asistencia
    $condicion = ""; // Para almacenar la condición del alumno

    // Si las 3 notas son mayores o iguales a la nota de promoción y la asistencia cumple con el mínimo
    if ($alumno['parcial1'] >= $notaPromocion && 
        $alumno['parcial2'] >= $notaPromocion && 
        $alumno['final'] >= $notaPromocion && 
        $porcentajeAsistencia >= $porcentajeAsistenciaPromocion) {
        $condicion = "Promoción";
    }
    // Si las 3 notas son mayores o iguales a la nota de regularización y la asistencia cumple con el mínimo
    elseif ($alumno['parcial1'] >= $notaRegularizacion && 
            $alumno['parcial2'] >= $notaRegularizacion && 
            $alumno['final'] >= $notaRegularizacion && 
            $porcentajeAsistencia >= $porcentajeAsistenciaRegular) {
        $condicion = "Regularización";
    }
    // Si no cumple ninguna de las dos condiciones, es "Libre"
    else {
        $condicion = "Libre";
    }

    // Agregar al resultado el nombre del alumno y su condición
    $resultado[] = [
        "alumno" => $alumno['nombre_completo'],
        "condicion" => $condicion // Condición como texto
    ];
}

// Enviar la respuesta en formato JSON
echo json_encode($resultado);
?>