<?php
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

// Definir la clave de encriptación
$key = 'mi_clave_secreta_de_32_bytes'; // Debe ser de 32 bytes para AES-256-CBC

// Iniciar sesión
session_start();
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Usar un ID de usuario válido por defecto (1)

// Inicializar variables
$decryptedText = '';

// Verificar si el formulario de desencriptar se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cipherText']) && !empty($_POST['cipherText'])) {
    $cipherText = base64_decode($_POST['cipherText']);

    // Separar el IV y el texto encriptado
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($cipherText, 0, $ivLength);
    $encryptedMessage = substr($cipherText, $ivLength);

    // Desencriptar el texto
    $decryptedText = openssl_decrypt($encryptedMessage, 'aes-256-cbc', $key, 0, $iv);

    // Validar si el mensaje fue desencriptado correctamente
    if ($decryptedText === false) {
        $decryptedText = "No se pudo desencriptar el mensaje. Verifica la clave y el texto encriptado.";
    }

    // Guardar en la sesión para mostrar después de redirigir
    $_SESSION['decryptedText'] = $decryptedText;

    // Redirigir para evitar reenvío del formulario (PRG)
    header("Location: desencriptar.php");
    exit;
}

// Recuperar el texto desencriptado de la sesión
if (isset($_SESSION['decryptedText'])) {
    $decryptedText = $_SESSION['decryptedText'];
    unset($_SESSION['decryptedText']); // Limpiar después de mostrar
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desencriptar Texto - AES</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .decrypt-form {
            flex: 2;
        }
    </style>
</head>

<body>
    <!-- Menú de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Proyecto Cafa</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary me-2" href="encriptar.php">Encriptar Texto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary me-2" href="desencriptar.php">Desencriptar Texto</a>
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
        <!-- Formulario para desencriptar -->
        <div class="decrypt-form">
            <h1 class="text-center mb-4">Desencriptar Texto - AES 256-CBC</h1>
            <form action="desencriptar.php" method="POST" class="card p-4 shadow">
                <div class="mb-3">
                    <label for="cipherText" class="form-label">Texto Encriptado:</label>
                    <textarea id="cipherText" name="cipherText" rows="5" class="form-control" placeholder="Pega aquí el texto encriptado"></textarea>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary">Desencriptar</button>
                </div>
            </form>

            <!-- Mostrar el texto desencriptado -->
            <?php if (!empty($decryptedText)): ?>
                <div class="card mt-4 p-4 shadow">
                    <h5 class="text-center">Mensaje Original Desencriptado:</h5>
                    <p class="text-center text-muted"><?php echo htmlspecialchars($decryptedText); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
