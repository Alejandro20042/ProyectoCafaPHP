<?php
session_start(); // Iniciar la sesión
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro - Woodcraft</title>
    <!-- Enlace al archivo CSS externo -->
    <link rel="stylesheet" href="styles.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
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

<body>
    <div class="container">
        <div class="login-card">
            <div class="card-right">
                <h1 class="title">Registro</h1>
                <form action="../../Logica/registroController.php" method="POST" autocomplete="off">
                    <div class="input-group">
                        <label for="registerUser">Nombre de Usuario</label>
                        <input type="text" id="registerUser" name="registerUser" maxlength="30" required autocomplete="off">
                    </div>
                    <div class="input-group">
                        <label for="registerEmail">Correo Electrónico</label>
                        <input type="email" id="registerEmail" name="registerEmail" required autocomplete="off">
                    </div>
                    <div class="input-group">
                        <label for="registerPassword">Contraseña</label>
                        <div class="password-container">
                            <input type="password" id="registerPassword" name="registerPassword" required
                                pattern="(?=.*[A-Z])(?=.*\d)(?=.*\W).{8,}"
                                title="Mínimo 8 caracteres, incluyendo una mayúscula, un número y un carácter especial."
                                autocomplete="new-password">
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                                <span id="eyeIcon">👁️</span>
                            </button>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="submit" class="btn">Registrar</button>
                    </div>
                    <div class="create-account">
                        <p>¿Ya tienes cuenta? <a href="../Login/login.php">Inicia sesión</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('registerPassword');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.textContent = '🙈'; // Cambiar a otro icono
            } else {
                passwordField.type = 'password';
                eyeIcon.textContent = '👁️'; // Volver al icono inicial
            }
        }

        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '<?php echo htmlspecialchars($_SESSION['error']); ?>',
                confirmButtonText: 'Aceptar'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '<?php echo htmlspecialchars($_SESSION['success']); ?>',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                window.location.href = '../Login/login.php';
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </script>
</body>

</html>
