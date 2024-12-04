<?php
require '../Conexion/conexionBD.php';
session_start([
    'cookie_lifetime' => 0,
    'cookie_secure' => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'use_only_cookies' => true
]);

$maxAttempts = 5;
$lockoutTime = 15 * 60; // 15 minutos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = filter_var(trim($_POST['loginEmail']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['loginPassword'];

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Por favor, ingrese un correo electrónico válido.';
        header('Location: ../View/Login/login.php');
        exit();
    }

    // Verificar intentos fallidos
    $query = "SELECT attempts, last_attempt, blocked_until FROM login_attempts WHERE correoElectronico = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $blockedUntil = $row['blocked_until'] ? strtotime($row['blocked_until']) : 0;

        // Registrar en logs para depuración
        error_log("Correo: $correo");
        error_log("Intentos: " . $row['attempts']);
        error_log("Último intento: " . $row['last_attempt']);
        error_log("Blocked Until: " . $row['blocked_until']);

        // Verificar si el usuario está bloqueado
        if (time() < $blockedUntil) {
            $waitTime = ceil(($blockedUntil - time()) / 60); // Tiempo restante en minutos
            $_SESSION['error'] = "Tu cuenta está bloqueada temporalmente. Intenta de nuevo en $waitTime minutos.";
            header('Location: ../View/Login/login.php');
            exit();
        } elseif ($row['attempts'] >= $maxAttempts) {
            // Bloquear al usuario
            $blockedUntil = date('Y-m-d H:i:s', time() + $lockoutTime);
            error_log("Bloqueando al usuario. blocked_until será: $blockedUntil");

            $updateQuery = "UPDATE login_attempts SET blocked_until = ?, attempts = 0 WHERE correoElectronico = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ss", $blockedUntil, $correo);

            if ($updateStmt->execute()) {
                if ($updateStmt->affected_rows > 0) {
                    error_log("Bloqueo exitoso. blocked_until actualizado para $correo.");
                } else {
                    error_log("El UPDATE no afectó ninguna fila. Verifica el valor de correoElectronico.");
                }
            } else {
                error_log("Error al bloquear al usuario: " . $updateStmt->error);
            }

            $_SESSION['error'] = "Demasiados intentos fallidos. Tu cuenta está bloqueada temporalmente.";
            header('Location: ../View/Login/login.php');
            exit();
        } else {
            error_log("Usuario no bloqueado. Continuando con el proceso de autenticación.");
        }
    } else {
        // Si no hay registro en login_attempts, inicializarlo
        $query = "INSERT INTO login_attempts (correoElectronico, attempts, last_attempt) VALUES (?, 0, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $correo);

        if (!$stmt->execute()) {
            error_log("Error al inicializar registro en login_attempts: " . $stmt->error);
        }
    }

    // Consultar usuario
    $query = "SELECT ID, contrasena FROM usuarios WHERE correoElectronico = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['contrasena'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['ID'];

            // Reiniciar intentos fallidos
            $deleteQuery = "DELETE FROM login_attempts WHERE correoElectronico = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("s", $correo);

            if (!$deleteStmt->execute()) {
                error_log("Error al reiniciar intentos fallidos: " . $deleteStmt->error);
            }

            header('Location: ../View/inicio/encriptar.php');
            exit();
        }
    }

    // Si el usuario no está bloqueado pero las credenciales son incorrectas
    // Si el usuario no está bloqueado pero las credenciales son incorrectas
    $query = "INSERT INTO login_attempts (correoElectronico, attempts, last_attempt)
    VALUES (?, 1, NOW())
    ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $correo);


    if (!$stmt->execute()) {
        error_log("Error al registrar intento fallido: " . $stmt->error);
    }

    // Mostrar el error después de registrar el intento fallido
    $_SESSION['error'] = 'Correo o contraseña incorrectos.';
    header('Location: ../View/Login/login.php');
    exit();
}
