<?php
session_start();
session_unset();  // Limpiar las variables de sesión
session_destroy();  // Destruir la sesión
echo json_encode(["status" => "success"]);  // Respuesta en formato JSON
exit();
