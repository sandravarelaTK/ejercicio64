<?php
include 'db.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if ($nombre == '' || $email == '' || $password == '') {
        $error = 'Completa todos los campos.';
    } else {
        $sql = "SELECT id FROM usuarios WHERE nombre='$nombre' OR email='$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $error = 'Usuario o correo ya existe.';
        } else {
            mysqli_query($conn, "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$password')");
            mysqli_query($conn, "INSERT INTO users (name, email) VALUES ('$nombre', '$email')");
            $success = 'Usuario registrado. Ve a login.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Registro de Usuario</h2>

    <?php if ($error != '') { echo "<p class='error-message'>$error</p>"; } ?>
    <?php if ($success != '') { echo "<p class='success-message'>$success</p>"; } ?>

    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrar</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
</div>

</body>
</html>
