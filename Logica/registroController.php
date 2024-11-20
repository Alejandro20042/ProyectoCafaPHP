<?php
require '../Conexion/conexionBD.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['registerEmail'];
    $password = password_hash($_POST['registerPassword'], PASSWORD_DEFAULT);

    // Verifica si el correo ya está registrado en la tabla usuarios
    $query = "SELECT * FROM usuarios WHERE correoElectronico = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El correo ya está registrado
        echo json_encode(array('status' => false, 'msg' => 'El correo ya está registrado.'));
    } else {
        // Si el correo no está registrado, crea la cuenta
        $insert_query = "INSERT INTO usuarios (nombreUsuario, correoElectronico, contrasena, fechaRegistro) VALUES (?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("sss", $correo, $correo, $password); // Usamos $correo como nombreUsuario

        if ($insert_stmt->execute()) {
            // Cuenta creada con éxito
            echo json_encode(array('status' => true, 'msg' => 'Cuenta creada exitosamente.'));
        } else {
            // Error al crear la cuenta
            echo json_encode(array('status' => false, 'msg' => 'Error al crear la cuenta.'));
        }
    }

    $stmt->close();
    $conn->close();
}
?>
