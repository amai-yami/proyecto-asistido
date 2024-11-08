<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="resources/css/login.css"> <!-- Vincula el archivo CSS -->
    <title>Formulario de Login</title>
</head>
<body>

    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form id="loginForm">
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <button type="submit">Ingresar</button>
            <button type="button" onclick="window.location.href='registrar.php'">Registrarse</button>
        </form>
    </div>
    <script src="./resources/js/usuario.js" defer></script>
</body>
</html>
