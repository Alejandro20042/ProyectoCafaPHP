<?php
require '../Conexion/conexionBD.php';
session_start(); // Iniciar la sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['loginEmail'];
    $password = $_POST['loginPassword'];

    // Validación del correo electrónico
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strpos($correo, '@') === false ) {
        $_SESSION['error'] = 'Por favor, ingrese un correo electrónico válido que contenga @';
        header('Location: http://localhost/PHP/ProyectoCafaPHP/View/Login/login.php');
        exit();
    }

    // Verifica si el correo y la contraseña son correctos
    $query = "SELECT ID, contrasena FROM usuarios WHERE correoElectronico = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['contrasena'])) {
            // Guardar el user_id en la sesión
            $_SESSION['user_id'] = $user['ID'];

            // Redirigir al inicio
            header('Location: http://localhost/PHP/ProyectoCafaPHP/View/inicio/index.php');
            exit();
        } else {
            $_SESSION['error'] = 'Contraseña o correo  incorrecto';
            header('Location: http://localhost/PHP/ProyectoCafaPHP/View/Login/login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'Correo no registrado. Si no tienes cuenta, por favor regístrate.';
        header('Location: http://localhost/PHP/ProyectoCafaPHP/View/Login/login.php');
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
