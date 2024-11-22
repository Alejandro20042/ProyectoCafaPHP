<?php
// Clave secreta para AES (debe tener al menos 16 caracteres y mantenerse segura)
$secretKey = "clave_secreta_1234"; // Cambia esto por una clave segura

// Función para encriptar texto con AES
function encrypt($plaintext, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc')); // Generar IV aleatorio
    $encrypted = openssl_encrypt($plaintext, 'aes-256-cbc', $key, 0, $iv); // Encriptar texto
    return base64_encode($encrypted . '::' . $iv); // Retornar texto cifrado + IV
}

// Función para desencriptar texto con AES
function decrypt($ciphertext, $key) {
    // Decodificar el texto cifrado y dividir en partes
    $parts = explode('::', base64_decode($ciphertext), 2);
    
    // Verificar que el texto tiene el formato correcto
    if (count($parts) !== 2) {
        return 'Error: El texto cifrado no tiene un formato válido.';
    }

    // Separar el texto cifrado y el IV
    list($encrypted_data, $iv) = $parts;

    // Desencriptar texto
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
}

// Variables para almacenar los resultados
$encryptedText = '';
$decryptedText = '';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si se envió texto para encriptar
    if (isset($_POST['plainText']) && !empty($_POST['plainText'])) {
        $plainText = $_POST['plainText'];
        $encryptedText = encrypt($plainText, $secretKey); // Encriptar texto
    }

    // Si se envió texto para desencriptar
    if (isset($_POST['cipherText']) && !empty($_POST['cipherText'])) {
        $cipherText = $_POST['cipherText'];
        $decryptedText = decrypt($cipherText, $secretKey); // Desencriptar texto
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encriptar y Desencriptar - AES</title>
    <link rel="stylesheet" href="styles.css"> <!-- Opcional: Archivo CSS si lo tienes -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
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
        <form action="aes_tool.php" method="POST">
            <!-- Sección para encriptar -->
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

            <!-- Sección para desencriptar -->
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
    </div>
</body>
</html>
