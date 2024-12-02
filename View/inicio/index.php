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
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Usar un ID de usuario válido

// Inicializar variables
$encryptedText = '';
$decryptedText = '';

// Verificar si el formulario de encriptar se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plainText']) && !empty($_POST['plainText'])) {
    $mensajeOriginal = $_POST['plainText'];

    // Generar un IV aleatorio
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

    // Encriptar el mensaje
    $encryptedMessage = openssl_encrypt($mensajeOriginal, 'aes-256-cbc', $key, 0, $iv);

    // Codificar el IV y el mensaje encriptado en base64
    $encryptedText = base64_encode($iv . $encryptedMessage);

    // Guardar el mensaje en la base de datos
    $stmt = $conn->prepare("INSERT INTO mensajes (usuario_id, mensajeOriginal, mensajeEncriptado) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $mensajeOriginal, $encryptedText);
    $stmt->execute();
    $stmt->close();

    // Guardar en la sesión para mostrar en la vista
    $_SESSION['encryptedText'] = $encryptedText;

    // Redirigir para evitar reenvíos
    header("Location: index.php");
    exit;
}

// Recuperar el texto encriptado de la sesión
if (isset($_SESSION['encryptedText'])) {
    $encryptedText = $_SESSION['encryptedText'];
    unset($_SESSION['encryptedText']); // Eliminar después de mostrar
}

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
    header("Location: index.php");
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
    <title>Encriptar y Desencriptar - AES</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR2i85hzz13fkacRTHYMtxuI4DvD1gMaRaxvw&s');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .container h1 {
            text-align: center;
            color: #333;
        }
        .section {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: none;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .output {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .output strong {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Encriptar y Desencriptar Texto - AES</h1>

        <!-- Encriptar texto -->
        <form action="index.php" method="POST">
            <div class="section">
                <h2>Encriptar Texto</h2>
                <label for="plainText">Texto en Claro:</label>
                <textarea id="plainText" name="plainText" rows="5" placeholder="Ingresa el texto que deseas encriptar"></textarea>
                <button type="submit">Encriptar</button>
                <?php if (!empty($encryptedText)): ?>
                    <div class="output">
                        <strong>Texto Encriptado:</strong>
                        <textarea readonly><?php echo $encryptedText; ?></textarea>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Desencriptar texto -->
            <div class="section">
                <h2>Desencriptar Texto</h2>
                <label for="cipherText">Texto Encriptado:</label>
                <textarea id="cipherText" name="cipherText" rows="5" placeholder="Pega aquí el texto encriptado"></textarea>
                <button type="submit">Desencriptar</button>
                <?php if (!empty($decryptedText)): ?>
                    <div class="output">
                        <strong>Texto Desencriptado:</strong>
                        <p><?php echo htmlspecialchars($decryptedText); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </form>

        <a href="View/Login/login.php">Cerrar sesión</a>
    </div>
</body>
</html>
