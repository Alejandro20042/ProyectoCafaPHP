<?php
require '../Conexion/conexionBD.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();

    $correo = $_POST['loginEmail'];
    $password = $_POST['loginPassword'];

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
            header('Location: ../View/inicio/index.php');
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta.'); window.location.href='../Login/login.php';</script>";
        }
    } else {
        echo "<script>alert('Correo no registrado.'); window.location.href='../Login/login.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
