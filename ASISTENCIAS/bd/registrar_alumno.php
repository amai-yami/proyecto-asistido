<?php
session_start();
require 'conexion.php';

function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = clean_input($_POST["nombre"]);
    $apellido = clean_input($_POST["apellido"]);
    $dni = clean_input($_POST["dni"]);
    $fecha_nacimiento = clean_input($_POST["fecha_nacimiento"]);
    $email = clean_input($_POST["email"]);
    $matricula = clean_input($_POST["matricula"]);

    $_SESSION["alumno_data"] = [
        "nombre" => $nombre,
        "apellido" => $apellido,
        "dni" => $dni,
        "fecha_nacimiento" => $fecha_nacimiento,
        "correo_electronico" => $email,
        "matricula" => $matricula,
    ];

    header("Location: insert_alumno.php");
    exit();
} else {
    echo "Error: No se recibieron los valores por POST.";
}
