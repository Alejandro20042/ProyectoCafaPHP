<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//IMPORTANTE PONER EN LA VISTA ESTE INCLUDE include '../controller/sesion.php';


if (!isset($_SESSION['user_id'])) {
    // Evitar la redirección infinita
    $currentFile = basename($_SERVER['PHP_SELF']);
    if ($currentFile !== 'login.php' && $currentFile !== 'registro.php') {
        header("Location: ../View/login.php");
        exit();
    }
} else {
    // Solo ejecuta esta parte si el usuario ha iniciado sesión y 'user_id' está en la sesión
    include_once '../Conexion/conexionBD.php';
  

    $userId = $_SESSION['user_id'];
    $query = "SELECT Admin FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($isAdmin);
    $stmt->fetch();
    $stmt->close();

    // Guardar el valor de Admin en la sesión
    $_SESSION['Admin'] = $isAdmin;
}
?>
