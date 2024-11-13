<?php
require_once __DIR__ . '/../bd/conexion.php';


trait CumpleaniosTrait {
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
                $cumpleanieros[] = $alumno['nombre'] . ' ' . $alumno['apellido'];
            }
        }

        return $cumpleanieros;
    }
}
?>
no hace nada esta funcion 