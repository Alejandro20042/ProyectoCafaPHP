<?php
// decrypt.php
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
    // Guardar en la sesión para mostrar en la vista
    $_SESSION['decryptedText'] = $decryptedText;

    // Redirigir para evitar reenvíos
    header("Location: desencriptar.php");
    exit;
}

// Recuperar el texto desencriptado de la sesión
if (isset($_SESSION['decryptedText'])) {
    $decryptedText = $_SESSION['decryptedText'];
    unset($_SESSION['decryptedText']); // Limpiar después de mostrar
}
?>

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

// Iniciar sesión
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
    $key = 'mi_clave_secreta_de_32_bytes'; // Cambiar si usas una clave diferente
    $decryptedText = openssl_decrypt($encryptedMessage, 'aes-256-cbc', $key, 0, $iv);
    // Guardar en la sesión para mostrar en la vista
    $_SESSION['decryptedText'] = $decryptedText;

    // Redirigir para evitar reenvíos
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
        .main-content {
            display: flex;
            justify-content: space-between;
        }

        .messages-list {
            flex: 1;
            margin-right: 20px;
        }

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
                        <a class="nav-link btn btn-outline-danger" href="../../Logica/lagout.php">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-5">
        <div class="main-content">
            <!-- Lista de mensajes encriptados -->
            <div class="messages-list card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Mensajes Encriptados </h5>
                    <div>
                        <button class="btn btn-sm btn-outline-primary" id="showAll">Mostrar Todos</button>
                        <button class="btn btn-sm btn-outline-secondary" id="hideAll">Ocultar Todos</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="accordion" id="messagesAccordion">
                        <?php
                        // Consulta para obtener los mensajes encriptados por usuario
                        $stmt = $conn->prepare("SELECT id, mensajeEncriptado FROM mensajes WHERE usuario_id = ? ORDER BY fechaEnvio DESC");
                        $stmt->bind_param("i", $userId);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                        ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?php echo $row['id']; ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $row['id']; ?>" aria-expanded="false" aria-controls="collapse<?php echo $row['id']; ?>">
                                            Mensaje Encriptado
                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo $row['id']; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $row['id']; ?>" data-bs-parent="#messagesAccordion">
                                        <div class="accordion-body">
                                            <?php echo htmlspecialchars($row['mensajeEncriptado']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            endwhile;
                        else:
                            ?>
                            <p class="text-center text-muted">No hay mensajes encriptados disponibles para este usuario.</p>
                        <?php
                        endif;

                        $stmt->close();
                        ?>
                    </div>
                </div>
            </div>

            <!-- Formulario para desencriptar -->
            <div class="decrypt-form">
                <h1 class="text-center mb-4">Desencriptar Texto - AES</h1>
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
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('showAll').addEventListener('click', function() {
            const collapses = document.querySelectorAll('.accordion-collapse');
            collapses.forEach(collapse => collapse.classList.add('show'));
        });

        document.getElementById('hideAll').addEventListener('click', function() {
            const collapses = document.querySelectorAll('.accordion-collapse');
            collapses.forEach(collapse => collapse.classList.remove('show'));
        });
    </script>
</body>

</html>