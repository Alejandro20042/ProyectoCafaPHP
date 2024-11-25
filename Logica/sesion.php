<?php
//include_once __DIR__ . '/../Conexion/conexionBD.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Evitar el almacenamiento en caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verificar si la sesión está activa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}
?>

