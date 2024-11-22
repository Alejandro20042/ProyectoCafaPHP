<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro - Woodcraft</title>
    <!-- Enlace al archivo CSS externo -->
    <link rel="stylesheet" href="styles.css">
    <!-- SweetAlert 2 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

</head>

<body>
    <div class="container">
        <div class="login-card">
            <div class="card-right">
                <h1 class="title">Registro</h1>
                <h2 class="welcome"></h2>
                <!-- Logica\registroController.php -->
                <form action="../../Logica/registroController.php" method="POST">
                <div class="input-group">
                        <label for="registerUser">Nombre de Usuario</label>
                        <input type="text" id="registerUser" name="registerUser" maxlength="20" required>
                    </div>
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
                        <div class="create-account">
                            <p>¿Ya tienes cuenta? <a href="../Login/login.php">Inicia sesión</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="image-credit">
        Foto de <a href="https://unsplash.com/@katishna?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash">Katie Azi</a> en <a href="https://unsplash.com/photos/a-laptop-computer-sitting-on-top-of-a-wooden-table-6bEc0U360LA?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash">Unsplash</a>
    </div>
</body>

</html>