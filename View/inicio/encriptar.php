<?php
// encrypt.php

// Verificar si la sesión no está activa antes de iniciarla
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyectocafa";

$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Generar clave única para cada usuario
$userKey = isset($_SESSION['user_key']) ? $_SESSION['user_key'] : bin2hex(random_bytes(16));
$_SESSION['user_key'] = $userKey; // Almacenar la clave en la sesión

// Inicializar variables
$encryptedText = '';
$error = '';

// Verificar si el formulario de encriptar se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plainText'])) {
    $mensajeOriginal = $_POST['plainText'];

    // Verificar longitud del mensaje
    $maxLength = 500; // Máximo de caracteres permitidos
    if (strlen($mensajeOriginal) > $maxLength) {
        $error = "El texto excede el límite permitido de $maxLength caracteres.";
    } else {
        // Generar un IV aleatorio
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        // Encriptar el mensaje
        $encryptedMessage = openssl_encrypt($mensajeOriginal, 'aes-256-cbc', $userKey, 0, $iv);

        // Codificar el IV y el mensaje encriptado en base64
        $encryptedText = base64_encode($iv . $encryptedMessage);

        // Guardar el mensaje en la base de datos
        $stmt = $conn->prepare("INSERT INTO mensajes (usuario_id, mensajeOriginal, mensajeEncriptado) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $_SESSION['user_id'], $mensajeOriginal, $encryptedText);
        $stmt->execute();
        $stmt->close();

        // Guardar en la sesión para mostrar en la vista
        $_SESSION['encryptedText'] = $encryptedText;

        // Redirigir para evitar reenvíos
        header("Location: encriptar.php");
        exit;
    }
}

// Recuperar el texto encriptado de la sesión
if (isset($_SESSION['encryptedText'])) {
    $encryptedText = $_SESSION['encryptedText'];
    unset($_SESSION['encryptedText']); // Eliminar después de mostrar
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encriptar Texto - AES</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Menú de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Proyecto Cafa</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary me-2" href="desencriptar.php">Desencriptar Texto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary me-2" href="encriptar.php">Encriptar Texto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-danger" href="../../Logica/logout.php">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-5">
        <h1 class="text-center mb-4">Encriptar Texto - AES 256-cbc</h1>
        <form action="encriptar.php" method="POST" class="card p-4 shadow">
            <div class="mb-3">
                <label for="plainText" class="form-label">Texto en Claro (Máximo 500 caracteres):</label>
                <textarea id="plainText" name="plainText" rows="5" class="form-control" maxlength="500" placeholder="Ingresa el texto que deseas encriptar"></textarea>
            </div>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Encriptar</button>
            </div>
        </form>

        <?php if (!empty($encryptedText)): ?>
            <div class="card mt-4 p-4 shadow">
                <h5 class="text-center">Texto Encriptado:</h5>
                <textarea readonly class="form-control mb-3"><?php echo htmlspecialchars($encryptedText); ?></textarea>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS (Opcional, si se usa funcionalidad interactiva) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
