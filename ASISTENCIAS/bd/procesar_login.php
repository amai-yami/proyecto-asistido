<?php
session_start();
require 'conexion.php'; // Asegúrate de que este archivo exista y esté en la ubicación correcta

// Función para limpiar los datos de entrada
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Respuesta inicial
$response = ['success' => false, 'error' => 'Error desconocido.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = clean_input($_POST["usuario"]);
    $contrasena = clean_input($_POST["contrasena"]);

    try {
        // Crear una instancia de la clase Database
        $database = new Database();
        // Obtener la conexión
        $conn = $database->connect();

        // Preparar la consulta SQL
        $stmt = $conn->prepare('SELECT * FROM usuario WHERE usuario = :usuario');
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->execute();

        // Obtener el resultado
        $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró el usuario y si la contraseña coincide
        if ($usuario_db && password_verify($contrasena, $usuario_db['contrasena'])) {
            // Establecer variables de sesión
            $_SESSION["usuario"] = $usuario_db['nombre']; // Almacenar el nombre en la sesión
            $response['success'] = true;
        } else {
            // Error: Usuario o contraseña inválidos
            $response['error'] = "Usuario o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        $response['error'] = "Error de conexión: " . $e->getMessage();
    }
} else {
    // Error: No se recibieron los valores por POST
    $response['error'] = "No se recibieron los valores por POST.";
}

// Devolver la respuesta en formato JSON
echo json_encode($response);

/* 
web asistencias app


un sistema con login y un registro

esto es para usuarios

nombre
apellido
email
y contraseña



llego al aula y ingreso al aula con iniciar sesion

que revice si encuentra al usuario y si no 

llegar y tomar asistencias

en caso de que no exista el alumno el profesor tendra que solicitar datos al alumno para darlo de alta


apellido nombre  dni  email fecha_nacimiento

tambien se podra dar de alta profesores como administradores

apellido nombre dni legajo 

que la asistencia quede registrada

acceder  a los alumnos particulares

tabla de notas  parcial 1 parcial 2 y final  sacar promedio 

mayor igual a 7 promociona

menor a 7   y mayor igual a 6 queda regular

menor a 6 libre

70 % de asistencia para promocionar

60%  para no quedar libre

y con un color que resalta de el resultado si promociona regulariza y libre

saber diferenciar por materias  ej prog 2 y prog 3


revisar  getter y setter


y libre eleccion

bajar requerimientos    o escribir en criollos  para resolver


y no supongamos  o obviemos algo




3 funciones para la base de datos   
una para el promedio
otra para los dias de clases

y otra 





configurar una tabla para los parametros de ram

 if (nota>=$nota)
 7   70%
 6   60%
 5   50%


 2 maneras   con trait  

 o con una funcion en alumnos


    listar una institucion 

     carreras

     materias

     profesor 
     
     y alumnos
     
*/