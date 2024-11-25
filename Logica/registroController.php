<?php
require '../Conexion/conexionBD.php';
session_start(); // Iniciar la sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['registerUser']);
    $correo = trim($_POST['registerEmail']);
    $password = $_POST['registerPassword'];

    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Validación del nombre de usuario (no más de 30 caracteres)
    if (strlen($username) > 30) {
        $_SESSION['error'] = 'El nombre de usuario no puede tener más de 30 caracteres.';
        header('Location: http://localhost/PHP/ProyectoCafaPHP/View/Registro/Registro.php');
        exit();
    }

    // Validación del nombre de usuario (no vacío)
    if (empty($username)) {
        $_SESSION['error'] = 'El nombre de usuario no puede estar vacío.';
        header('Location: http://localhost/PHP/ProyectoCafaPHP/View/Registro/Registro.php');
        exit();
    }

    // Validación del correo electrónico
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Por favor, ingrese un correo electrónico válido.';
        header('Location: http://localhost/PHP/ProyectoCafaPHP/View/Registro/Registro.php');
        exit();
    }

    // Validación de la contraseña
    $passwordErrors = [];
    if (strlen($password) < 8) {
        $passwordErrors[] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $passwordErrors[] = 'La contraseña debe contener al menos una letra mayúscula.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $passwordErrors[] = 'La contraseña debe contener al menos un número.';
    }
    if (!preg_match('/[\W]/', $password)) {
        $passwordErrors[] = 'La contraseña debe contener al menos un carácter especial (por ejemplo, !, ?, @, etc.).';
    }

    if (!empty($passwordErrors)) {
        $_SESSION['error'] = implode(' ', $passwordErrors);
        header('Location: http://localhost/PHP/ProyectoCafaPHP/View/Registro/Registro.php');
        exit();
    }

    // Encriptar la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Verificar si el correo ya existe
    $query = "SELECT * FROM usuarios WHERE correoElectronico = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error al preparar la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El correo ya está registrado
        $_SESSION['error'] = 'El correo ya está registrado.';
        header('Location: http://localhost/PHP/ProyectoCafaPHP/View/Registro/Registro.php');
        exit();
    } else {
        // Insertar usuario en la base de datos
        $insert_query = "INSERT INTO usuarios (nombreUsuario, correoElectronico, contrasena, fechaRegistro) VALUES (?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_query);
        if (!$insert_stmt) {
            die("Error al preparar la inserción: " . $conn->error);
        }
        $insert_stmt->bind_param("sss", $username, $correo, $passwordHash);

        if ($insert_stmt->execute()) {
            $_SESSION['success'] = 'Cuenta creada exitosamente.';
            // Redirigimos de vuelta a la página de registro
            header('Location: http://localhost/PHP/ProyectoCafaPHP/View/Registro/Registro.php');
            exit();
        } else {
            $_SESSION['error'] = 'Error al crear la cuenta.';
            header('Location: http://localhost/PHP/ProyectoCafaPHP/View/Registro/Registro.php');
            exit();
        }
    }

    $stmt->close();
    $conn->close();
}
?>
