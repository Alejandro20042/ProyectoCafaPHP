<?php
require '../Conexion/conexionBD.php';
session_start([
    'cookie_lifetime' => 0, // La sesión expira al cerrar el navegador
    'cookie_secure' => true, // Requiere HTTPS
    'cookie_httponly' => true, // Solo accesible por HTTP
    'use_strict_mode' => true, // Previene ataques de sesión
    'use_only_cookies' => true // Solo cookies, no SID en la URL
]);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['registerUser']);
    $correo = filter_var(trim($_POST['registerEmail']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['registerPassword'];

    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Validación del nombre de usuario
    if (empty($username) || strlen($username) > 30) {
        $_SESSION['error'] = 'El nombre de usuario debe contener entre 1 y 30 caracteres.';
        header('Location: ../View/Registro/Registro.php');
        exit();
    }

    // Validación del correo electrónico
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Por favor, ingrese un correo electrónico válido.';
        header('Location: ../View/Registro/Registro.php');
        exit();
    }

    // Validación de la contraseña
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W]/', $password)) {
        $_SESSION['error'] = 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula, un número y un carácter especial.';
        header('Location: ../View/Registro/Registro.php');
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
        $_SESSION['error'] = 'El correo ya está registrado.';
        header('Location: ../View/Registro/Registro.php');
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
            header('Location: ../View/Registro/Registro.php');
            exit();
        } else {
            $_SESSION['error'] = 'Error al crear la cuenta.';
            header('Location: ../View/Registro/Registro.php');
            exit();
        }
    }

    $stmt->close();
    $conn->close();
}
?>
