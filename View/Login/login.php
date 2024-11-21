<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="card-right">
                <h1 class="title">Inicio de Sesión</h1>
                <h2 class="welcome">¡Bienvenido!</h2>
                <form action="../../Logica/loginController.php" method="POST">
                    <div class="input-group">
                        <label for="loginEmail">Correo Electrónico</label>
                        <input type="email" id="loginEmail" name="loginEmail" required>
                    </div>
                    <div class="input-group">
                        <label for="loginPassword">Contraseña</label>
                        <input type="password" id="loginPassword" name="loginPassword" required>
                    </div>
                    <div class="actions">
                        <button type="submit" class="btn">Iniciar Sesión</button>
                    </div>
                    <div class="create-account">
                        <p>¿No tienes cuenta? <a href="../Registro/Registro.php">Regístrate</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="image-credit">
        Foto de <a href="https://unsplash.com/@katishna">Katie Azi</a> en <a href="https://unsplash.com">Unsplash</a>
    </div>
</body>
</html>
