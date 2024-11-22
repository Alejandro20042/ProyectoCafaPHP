<?php
include '../../Logica/sesion.php';

// Obtener el user_id de la sesión
if (!isset($_SESSION['user_id'])) {
    die("Error: Usuario no autenticado.");
}

$userId = $_SESSION['user_id']; // Aquí obtienes el user_id
// Definir la clave secreta para AES (esto debería ser almacenado de manera segura)
$key = '1234567890123456'; // Clave de 16 bytes para AES-128

// Inicializar variables
$encryptedText = '';
$decryptedText = '';

// Encriptar texto
if (isset($_POST['plainText']) && !empty($_POST['plainText'])) {
    $plainText = $_POST['plainText'];

    // Generar un IV aleatorio
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));

    // Encriptar con AES
    $encryptedText = openssl_encrypt($plainText, 'aes-128-cbc', $key, 0, $iv);
    
    // Codificar el IV y el texto encriptado en base64 para pasarlos a través de HTTP
    $encryptedText = base64_encode($iv . $encryptedText); // Concatenar IV y el texto encriptado
}

// Desencriptar texto
if (isset($_POST['cipherText']) && !empty($_POST['cipherText'])) {
    $cipherText = base64_decode($_POST['cipherText']);

    // Separar el IV y el texto encriptado
    $ivLength = openssl_cipher_iv_length('aes-128-cbc');
    $iv = substr($cipherText, 0, $ivLength);
    $encryptedText = substr($cipherText, $ivLength);

    // Desencriptar con AES
    $decryptedText = openssl_decrypt($encryptedText, 'aes-128-cbc', $key, 0, $iv);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encriptar y Desencriptar - AES</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR2i85hzz13fkacRTHYMtxuI4DvD1gMaRaxvw&s');
            background-size: cover; /* Asegura que la imagen cubra toda la pantalla */
            background-position: center; /* Centra la imagen de fondo */
            background-attachment: fixed; /* Fija el fondo cuando se hace scroll */
            font-family: 'Roboto', sans-serif; /* Establece la fuente para todo el cuerpo */
            }
    </style>
</head>
<body>
    <script>
        // Pasar el user_id de PHP a JavaScript
        const userId = <?php echo json_encode($userId); ?>;

        // Mostrar el user_id en la consola
        console.log("User ID:", userId);
    </script>
    
    <div class="container">
        <h1>Encriptar y Desencriptar Texto - AES</h1>
        <form action="aes_tool.php" method="POST">
            <div class="section">
                <h2>Encriptar Texto</h2>
                <label for="plainText">Texto en Claro:</label>
                <textarea id="plainText" name="plainText" rows="5" placeholder="Ingresa el texto que deseas encriptar"></textarea>
                <button type="submit" class="btn">Encriptar</button>
                <?php if (!empty($encryptedText)): ?>
                    <div class="output">
                        <strong>Texto Encriptado:</strong>
                        <textarea readonly><?php echo $encryptedText; ?></textarea>
                    </div>
                <?php endif; ?>
            </div>
            <div class="section">
                <h2>Desencriptar Texto</h2>
                <label for="cipherText">Texto Encriptado:</label>
                <textarea id="cipherText" name="cipherText" rows="5" placeholder="Pega aquí el texto encriptado"></textarea>
                <button type="submit" class="btn">Desencriptar</button>
                <?php if (!empty($decryptedText)): ?>
                    <div class="output">
                        <strong>Texto Desencriptado:</strong>
                        <p><?php echo htmlspecialchars($decryptedText); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </form>
        <a href="../../Logica/lagout.php">Cerrar sesión</a>
    </div>
</body>
</html>
