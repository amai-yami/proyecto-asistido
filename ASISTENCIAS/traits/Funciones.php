<?php

require_once 'conexion.php'; // Asegúrate de incluir la conexión a la base de datos
/* 


if($cumple !=null){
    alert('el cumpleaños es');
}

*/

class cumpleanios{
    function fiesta(){
$fecha= "SELECT fecha_nacimiento 
          FROM alumno "; 

$dia=date("Y-m-d");
$cumpleanio=date($fecha);
$cumple=$dia-$cumpleanio;

return $cumple;

}}

echo json_encode(['cumpleaños'. $cumple]);

