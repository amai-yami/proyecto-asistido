<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="resources/css/registrar.css">
    <title>Registrarse</title>
</head>
<body>
    
<form id="registro-form">
    <h2>Registrarse</h2>
    <h4>Usuario:</h4>
    <input type="text" id="usuario" name="usuario" required>
    
    <h4>Correo:</h4>
    <input type="text" id="email" name="correo" required>
    
    <h4>Contraseña:</h4>
    <input type="password" id="pass" name="contrasena" required>
    
    <h4>Nombre:</h4>
    <input type="text" id="nom" name="nombre" required>
    
    <h4>Apellido:</h4>
    <input type="text" id="ape" name="apellido" required>
    
    <div class="button-container">
        <button id="login" type="button" onclick="window.location.href='login.php'">Volver a iniciar sesión</button>
        <button id="enviar" type="submit">Registrarse</button>
    </div>
    <div id="error-message" style="color: red; margin-top: 10px;"></div>
</form>

<script src="./resources/js/usuario.js"></script>
</body>
</html>
