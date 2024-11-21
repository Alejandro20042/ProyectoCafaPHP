<?php
// Generar claves RSA (esto debería hacerse previamente y guardarse en un lugar seguro)
$privateKey = null;
$publicKey = null;

// Verificar si ya existe una clave privada y pública generada
if (!file_exists("private.key") || !file_exists("public.key")) {
    // Generar nuevas claves
    $res = openssl_pkey_new([
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ]);

    // Comprobamos si la clave fue generada correctamente
    if (!$res) {
        $error_message = 'Error al generar la clave privada: ' . openssl_error_string();
        error_log($error_message);  // Registrar el error en el archivo de log
        die($error_message);  // Mostrar el error y detener la ejecución
    }

    // Exportar la clave privada y guardarla en archivo
    if (!openssl_pkey_export($res, $privateKey)) {
        $error_message = 'Error al exportar la clave privada: ' . openssl_error_string();
        error_log($error_message);  // Registrar el error en el archivo de log
        die($error_message);  // Mostrar el error y detener la ejecución
    }

    // Guardamos la clave privada en un archivo
    file_put_contents("private.key", $privateKey);

    // Obtener la clave pública y guardarla en archivo
    $details = openssl_pkey_get_details($res);
    if (!$details) {
        $error_message = 'Error al obtener detalles de la clave pública: ' . openssl_error_string();
        error_log($error_message);  // Registrar el error en el archivo de log
        die($error_message);  // Mostrar el error y detener la ejecución
    }

    // La clave pública
    $publicKey = $details["key"];
    file_put_contents("public.key", $publicKey);
} else {
    // Leer las claves generadas previamente
    $privateKey = file_get_contents("private.key");
    $publicKey = file_get_contents("public.key");
}

// Encriptar texto
$encryptedText = '';
if (isset($_POST['plainText'])) {
    $plainText = $_POST['plainText'];
    if (!openssl_public_encrypt($plainText, $encryptedText, $publicKey)) {
        $error_message = 'Error al encriptar el texto: ' . openssl_error_string();
        error_log($error_message);  // Registrar el error en el archivo de log
    }
    $encryptedText = base64_encode($encryptedText);
}

// Desencriptar texto
$decryptedText = '';
if (isset($_POST['cipherText'])) {
    $cipherText = base64_decode($_POST['cipherText']);
    if (!openssl_private_decrypt($cipherText, $decryptedText, $privateKey)) {
        $error_message = 'Error al desencriptar el texto: ' . openssl_error_string();
        error_log($error_message);  // Registrar el error en el archivo de log
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encriptar y Desencriptar - RSA</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Encriptar y Desencriptar Texto - RSA</h1>
        <form action="rsa_tool.php" method="POST">
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
    </div>
</body>
</html>