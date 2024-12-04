<?php
session_start();

// Limpiar la sesión
$_SESSION = [];
session_destroy();

// Eliminar las cookies de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], true);
}

// Redirigir al login
header("Location: ../View/Login/login.php");
exit();
?>
