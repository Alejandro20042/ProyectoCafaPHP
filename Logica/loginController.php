<?php
require '../Conexion/conexionBD.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();
    $correo = $_POST['loginEmail'];
    $password = $_POST['loginPassword'];

    // Verifica si el correo y la contrasena son correctos
    $query = "SELECT ID, contrasena FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['contrasena'])) {
            $_SESSION['user_id'] = $user['ID'];
            echo json_encode(array('status' => true, 'msg' => 'Inicio de sesión exitoso.'));
        } else {
            echo json_encode(array('status' => false, 'msg' => 'contrasena incorrecta.'));
        }
    } else {
        echo json_encode(array('status' => false, 'msg' => 'Correo no registrado.'));
    }

    $stmt->close();
    $conn->close();
}
?>
