<?php
session_start();

// Si el usuario está logueado, devuelve el nombre
if (isset($_SESSION['usuario'])) {
    echo json_encode(['usuario' => $_SESSION['usuario']]);
} else {
    // Si no está logueado, devuelve null
    echo json_encode(['usuario' => null]);
}
?>
