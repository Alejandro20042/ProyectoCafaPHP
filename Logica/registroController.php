<?php
require '../Conexion/conexionBD.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['registerUser'];
    $correo = $_POST['registerEmail'];
    $password = password_hash($_POST['registerPassword'], PASSWORD_DEFAULT);

    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Validación del nombre de usuario (no más de 20 caracteres)
    if (strlen($username) > 20) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'El nombre de usuario no puede tener más de 20 caracteres.',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    return false;
                })
              </script>";
        exit();
    }

    // Validación del correo electrónico para asegurar que contiene '@' y '.com'
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strpos($correo, '@') === false || substr($correo, -4) !== '.com') {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Por favor, ingrese un correo electrónico válido que contenga @ y .com.',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    return true;
                })
              </script>";
        exit();
    }

    // Verificar si el correo ya existe
    $query = "SELECT * FROM usuarios WHERE correoElectronico = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error al preparar la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El correo ya está registrado
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'El correo ya está registrado.',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    return false;
                })
              </script>";
    } else {
        // Insertar usuario en la base de datos
        $insert_query = "INSERT INTO usuarios (nombreUsuario, correoElectronico, contrasena, fechaRegistro) VALUES (?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_query);
        if (!$insert_stmt) {
            die("Error al preparar la inserción: " . $conn->error);
        }
        $insert_stmt->bind_param("sss", $username, $correo, $password);

        if ($insert_stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Cuenta creada exitosamente.',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        return false;
                    })
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'Error al crear la cuenta.',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        return false;
                    })
                  </script>";
        }
    }

    $stmt->close();
    $conn->close();
}
?>
