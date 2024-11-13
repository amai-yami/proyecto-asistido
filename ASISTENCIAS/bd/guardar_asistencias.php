<?php
// Incluir la clase de conexión
include('conexion.php');

class AsistenciaHandler {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function obtenerEdad($fechaNacimiento) {
        $hoy = new DateTime();
        $nacimiento = new DateTime($fechaNacimiento);
        $edad = $hoy->diff($nacimiento);
        return $edad->y; // Devuelve solo los años (edad)
    }

    public function obtenerCumpleanieros($db) {
        $stmt = $db->prepare("SELECT nombre, apellido, fecha_nacimiento FROM alumno");
        $stmt->execute();
        $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $cumpleanieros = [];
        $hoy = new DateTime();
        $mesActual = $hoy->format('m');
        $diaActual = $hoy->format('d');

        foreach ($alumnos as $alumno) {
            $fechaNacimiento = new DateTime($alumno['fecha_nacimiento']);
            $mesNacimiento = $fechaNacimiento->format('m');
            $diaNacimiento = $fechaNacimiento->format('d');

            if ($mesNacimiento === $mesActual && $diaNacimiento === $diaActual) {
                $edad = $this->obtenerEdad($alumno['fecha_nacimiento']);
                $cumpleanieros[] = $alumno['nombre'] . ' ' . $alumno['apellido'] . ' (' . $edad . ' años)';
            }
        }

        return $cumpleanieros;
    }

    public function guardarAsistencias($asistencias) {
        $sql = "INSERT INTO asistencia (id_alumno, asistencia, fecha) VALUES (:alumnoId, :asistencia, NOW())";
        $stmt = $this->db->prepare($sql);
        $exito = true;

        foreach ($asistencias as $asistencia) {
            $alumnoId = $asistencia['alumnoId'];
            $asistenciaValor = isset($asistencia['asistencia']) && $asistencia['asistencia'] ? 1 : 0;

            if ($alumnoId !== null) {
                $stmt->bindParam(':alumnoId', $alumnoId);
                $stmt->bindParam(':asistencia', $asistenciaValor);

                if (!$stmt->execute()) {
                    $exito = false;
                    break;
                }
            }
        }

        return $exito;
    }
}

// Iniciar la conexión a la base de datos y la clase de manejo de asistencia
$database = new Database();
$db = $database->connect();
$asistenciaHandler = new AsistenciaHandler($db);

header('Content-Type: application/json');
$asistencias = json_decode(file_get_contents('php://input'), true);

if (!$asistencias) {
    echo json_encode(['success' => false, 'message' => 'No se enviaron datos válidos']);
    exit;
}

$exito = $asistenciaHandler->guardarAsistencias($asistencias);

if ($exito) {
    $cumpleanieros = $asistenciaHandler->obtenerCumpleanieros($db);
    echo json_encode(['success' => true, 'cumpleanios' => $cumpleanieros]);
} else {
    echo json_encode(['success' => false, 'message' => 'Hubo un error al registrar las asistencias']);
}
?>
