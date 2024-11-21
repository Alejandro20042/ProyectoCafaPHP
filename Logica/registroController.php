<?php
require '../Conexion/conexionBD.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['registerUser'];
    $correo = $_POST['registerEmail'];
    $password = password_hash($_POST['registerPassword'], PASSWORD_DEFAULT);

    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error());
    }

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
        echo "<script>alert('El correo ya está registrado.'); window.location.href='registro.php';</script>";
    } else {
        $insert_query = "INSERT INTO usuarios (nombreUsuario, correoElectronico, contrasena, fechaRegistro) VALUES (?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_query);
        if (!$insert_stmt) {
            die("Error al preparar la inserción: " . $conn->error);
        }
        $insert_stmt->bind_param("sss", $username, $correo, $password);

        if ($insert_stmt->execute()) {
            echo "<script>alert('Cuenta creada exitosamente.'); window.location.href='../View/Login/login.php';</script>";
        } else {
            echo "<script>alert('Error al crear la cuenta.'); window.location.href='registro.php';</script>";
        }
    }
    $stmt->close();
    $conn->close();
}
