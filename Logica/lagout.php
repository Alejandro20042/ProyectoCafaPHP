<?php
session_start(); // Iniciar la sesión
session_unset(); // Eliminar todas las variables de sesión
session_destroy(); // Destruir la sesión en el servidor

// Eliminar la cookie de sesión del navegador
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Depuración: Muestra el directorio actual y la ruta de redirección
echo "Directorio actual: " . __DIR__; // Muestra el directorio actual
echo "<br>Redirigiendo a: ../View/Login/login.php"; // Muestra la URL de redirección

// Redirigir al usuario al login u otra página
header("Location: ../View/Login/login.php");
exit();
?>
