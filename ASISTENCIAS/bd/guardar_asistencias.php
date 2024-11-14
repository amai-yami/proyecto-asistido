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

    public function convertirFecha($fecha) {
        // Reemplazar 'T' por espacio para asegurar el formato adecuado para MySQL
        $fecha = str_replace("T", " ", $fecha); 
    
        // Verificar si la fecha tiene el formato esperado
        $fechaFormateada = date("Y-m-d H:i:s", strtotime($fecha));
    
        // Si la conversión no es válida, podemos devolver una fecha por defecto o manejar el error
        if ($fechaFormateada === false) {
            throw new Exception("La fecha tiene un formato inválido.");
        }
    
        return $fechaFormateada; // Convertir a formato correcto
    }
    
    public function guardarAsistencias($asistencias) {
        // Usar una transacción para asegurar que todas las asistencias se registren correctamente
        $this->db->beginTransaction();

        $sql = "INSERT INTO asistencia (id_alumno, asistencia, fecha) VALUES (:alumnoId, :asistencia, :fecha)";
        $stmt = $this->db->prepare($sql);
        $exito = true;

        foreach ($asistencias as $asistencia) {
            $alumnoId = $asistencia['alumnoId'];
            $asistenciaValor = isset($asistencia['asistencia']) && $asistencia['asistencia'] ? 1 : 0;
            $fecha = $this->convertirFecha($asistencia['fecha']); // Usar la función para convertir la fecha

            if ($alumnoId !== null) {
                $stmt->bindParam(':alumnoId', $alumnoId);
                $stmt->bindParam(':asistencia', $asistenciaValor);
                $stmt->bindParam(':fecha', $fecha); // Enviar la fecha al query

                if (!$stmt->execute()) {
                    $exito = false;
                    break;
                }
            }
        }

        if ($exito) {
            // Si todo ha ido bien, hacer commit de la transacción
            $this->db->commit();
        } else {
            // Si hay algún error, hacer rollback de la transacción
            $this->db->rollBack();
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
