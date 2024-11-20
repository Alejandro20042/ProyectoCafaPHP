
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="style.css"> <!-- Puedes agregar tu propio estilo CSS -->
</head>
<body>

    <div class="form-container">
        <h2>Registro de Usuario</h2>
        <form id="registerForm" action="ruta_a_tu_php/registro.php" method="POST">
            <label for="registerEmail">Correo Electrónico:</label>
            <input type="email" id="registerEmail" name="registerEmail" required placeholder="Ingresa tu correo electrónico">

            <label for="registerPassword">Contraseña:</label>
            <input type="password" id="registerPassword" name="registerPassword" required placeholder="Ingresa tu contraseña">

            <button type="submit">Registrar</button>
        </form>

        <div id="responseMessage"></div>
    </div>

    <script>
        // Maneja el formulario de registro de manera asíncrona (AJAX)
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            event.preventDefault();

            let formData = new FormData(this);

            fetch('../Logica/registroController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const responseMessage = document.getElementById('responseMessage');
                if (data.status) {
                    responseMessage.style.color = 'green';
                    responseMessage.innerText = data.msg;
                } else {
                    responseMessage.style.color = 'red';
                    responseMessage.innerText = data.msg;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const responseMessage = document.getElementById('responseMessage');
                responseMessage.style.color = 'red';
                responseMessage.innerText = 'Hubo un error al procesar tu solicitud.';
            });
        });
    </script>

</body>
</html>


