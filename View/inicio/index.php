<?php
// Generar claves RSA (esto debería hacerse previamente y guardarse en un lugar seguro)
$privateKey = null;
$publicKey = null;

// Verificar si ya existe una clave privada y pública generada
if (!file_exists("private.key") || !file_exists("public.key")) {
    $res = openssl_pkey_new([
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ]);

    // Exportar la clave privada y guardar en archivo
    openssl_pkey_export($res, $privateKey);
    file_put_contents("private.key", $privateKey);

    // Obtener la clave pública y guardar en archivo
    $publicKey = openssl_pkey_get_details($res)["key"];
    file_put_contents("public.key", $publicKey);
} else {
    // Leer claves generadas previamente
    $privateKey = file_get_contents("private.key");
    $publicKey = file_get_contents("public.key");
}

// Encriptar texto
$encryptedText = '';
if (isset($_POST['plainText'])) {
    $plainText = $_POST['plainText'];
    openssl_public_encrypt($plainText, $encryptedText, $publicKey);
    $encryptedText = base64_encode($encryptedText);
}

// Desencriptar texto
$decryptedText = '';
if (isset($_POST['cipherText'])) {
    $cipherText = base64_decode($_POST['cipherText']);
    openssl_private_decrypt($cipherText, $decryptedText, $privateKey);
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