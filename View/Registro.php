<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro - Woodcraft</title>
    <!-- Enlace al archivo CSS externo -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Contenedor principal para centrar la tarjeta de login en la pantalla -->
    <div class="container">
        <!-- Tarjeta de registro con la imagen y el formulario -->
        <div class="login-card">
            <!-- Lado izquierdo de la tarjeta con la imagen decorativa -->
            <div class="card-left">
                <div class="image-cover">
                </div>
            </div>

            <!-- Lado derecho de la tarjeta con el formulario de registro -->
            <div class="card-right">
                <h1 class="title">Registro</h1>
                <h2 class="welcome">¡Bienvenido!</h2>
                <form action="registro.php" method="POST">
                    <div class="input-group">
                        <label for="registerEmail">Correo Electrónico</label>
                        <input type="email" id="registerEmail" name="registerEmail" required>
                    </div>
                    <div class="input-group">
                        <label for="registerPassword">Contraseña</label>
                        <input type="password" id="registerPassword" name="registerPassword" required>
                    </div>
                    <div class="actions">
                        <button type="submit" class="btn">Registrar</button>
                    </div>                    
                    <div class="create-account">
                        <p>¿Nuevo en Woodcraft? <a href="#">Crea una cuenta</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Crédito de la imagen en la parte inferior de la página -->
    <div class="image-credit">
        Foto de <a href="https://unsplash.com/@katishna?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash">Katie Azi</a> en <a href="https://unsplash.com/photos/a-laptop-computer-sitting-on-top-of-a-wooden-table-6bEc0U360LA?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash">Unsplash</a>
    </div>
</body>
</html>
