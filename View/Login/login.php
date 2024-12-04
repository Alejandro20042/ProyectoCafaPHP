<?php
session_start();

// Si el usuario ya ha iniciado sesión, redirigir a una página protegida
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header("Location: ../inicio/encriptar.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="styles.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                        <div class="password-container">
                            <input type="password" id="loginPassword" name="loginPassword" required>
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                                <span id="eyeIcon">👁️</span>
                            </button>
                        </div>
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

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: '<?php echo (strpos($_SESSION['error'], "bloqueada") !== false) ? "¡Cuenta Bloqueada!" : "¡Error!"; ?>',
                text: '<?php echo htmlspecialchars($_SESSION['error']); ?>',
                confirmButtonText: 'Aceptar'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        function togglePasswordVisibility() {
            const passwordField = document.getElementById('loginPassword');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.textContent = '🙈'; // Cambiar a otro icono
            } else {
                passwordField.type = 'password';
                eyeIcon.textContent = '👁️'; // Volver al icono inicial
            }
        }
    </script>

    <style>
        .password-container {
            display: flex;
            align-items: center;
        }

        .password-container input {
            flex: 1;
        }

        .toggle-password {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2em;
            margin-left: 5px;
        }

        .toggle-password:focus {
            outline: none;
        }
    </style>
</body>

</html>